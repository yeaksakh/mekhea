<?php


namespace Modules\Connector\Http\Controllers\Api;
use App\Contact;
use App\Business;
use App\Utils\Util;
use App\ReferenceCount;
use App\Utils\ModuleUtil;
use App\Utils\ContactUtil;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;
use Modules\Crm\Utils\CrmUtil;
use App\Utils\NotificationUtil;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Connector\Http\Controllers\Api\ApiController;

class ImportContactController extends ApiController
{
  protected $commonUtil;

  protected $contactUtil;

  protected $transactionUtil;

  protected $moduleUtil;

  protected $notificationUtil;

  protected $crmUtil;

  /**
   * Constructor
   *
   * @param  Util  $commonUtil
   * @return void
   */
  public function __construct(
    Util $commonUtil,
    ModuleUtil $moduleUtil,
    TransactionUtil $transactionUtil,
    NotificationUtil $notificationUtil,
    ContactUtil $contactUtil,
    CrmUtil $crmUtil
  ) {
    $this->commonUtil = $commonUtil;
    $this->contactUtil = $contactUtil;
    $this->moduleUtil = $moduleUtil;
    $this->transactionUtil = $transactionUtil;
    $this->notificationUtil = $notificationUtil;
    $this->crmUtil = $crmUtil;
  }


  public function downloadTemplate()
  {
    $filePath = asset('files/import_contacts_csv_template.xls');

    if (file_exists(public_path('files/import_contacts_csv_template.xls'))) {
      return response()->json([
        'success' => 1,
        'url' => $filePath,
        'msg' => 'Template URL generated successfully'
      ]);
    } else {
      return response()->json(['success' => 0, 'msg' => 'Template file not found'], 404);
    }
  }
  public function postImportContacts(Request $request)
  {
    try {
      // Handle file upload and processing
      if ($request->hasFile('contacts_csv')) {
        $file = $request->file('contacts_csv');
        $parsed_array = Excel::toArray([], $file);
        $imported_data = array_splice($parsed_array[0], 1); // Remove header row

        // Use business_id and user_id directly from the request or auth
        $business_id = $request->input('business_id');  // Assuming business_id is passed in the request
        $user_id = auth()->id();

        $formated_data = [];
        $is_valid = true;
        $error_msg = '';

        DB::beginTransaction();

        foreach ($imported_data as $key => $value) {
          // Check if 27 columns exist
          if (count($value) != 27) {
            return response()->json(['success' => 0, 'msg' => 'Number of columns mismatch'], 422);
          }

          $row_no = $key + 1;
          $contact_array = [];

          // Process each row (similar logic as before)
          $contact_type = '';
          $contact_types = [
            1 => 'customer',
            2 => 'supplier',
            3 => 'both',
          ];

          if (!empty($value[0])) {
            $contact_type = strtolower(trim($value[0]));
            if (in_array($contact_type, [1, 2, 3])) {
              $contact_array['type'] = $contact_types[$contact_type];
            } else {
              return response()->json(['success' => 0, 'msg' => "Invalid contact type $contact_type in row no. $row_no"], 422);
            }
          } else {
            return response()->json(['success' => 0, 'msg' => "Contact type is required in row no. $row_no"], 422);
          }

          $contact_array['prefix'] = $value[1];

          if (!empty($value[2])) {
            $contact_array['first_name'] = $value[2];
          } else {
            return response()->json(['success' => 0, 'msg' => "First name is required in row no. $row_no"], 422);
          }

          $contact_array['middle_name'] = $value[3];
          $contact_array['last_name'] = $value[4];
          $contact_array['name'] = implode(' ', [$contact_array['prefix'], $contact_array['first_name'], $contact_array['middle_name'], $contact_array['last_name']]);

          if (!empty(trim($value[5]))) {
            $contact_array['supplier_business_name'] = $value[5];
          }

          if (in_array($contact_type, ['supplier', 'both'])) {
            if (trim($value[9]) != '') {
              $contact_array['pay_term_number'] = trim($value[9]);
            } else {
              return response()->json(['success' => 0, 'msg' => "Pay term is required in row no. $row_no"], 422);
            }

            $pay_term_type = strtolower(trim($value[10]));
            if (in_array($pay_term_type, ['days', 'months'])) {
              $contact_array['pay_term_type'] = $pay_term_type;
            } else {
              return response()->json(['success' => 0, 'msg' => "Pay term period is required in row no. $row_no"], 422);
            }
          }

          if (!empty(trim($value[6]))) {
            $count = Contact::where('business_id', $business_id)
              ->where('contact_id', $value[6])
              ->count();

            if ($count == 0) {
              $contact_array['contact_id'] = $value[6];
            } else {
              return response()->json(['success' => 0, 'msg' => "Contact ID already exists in row no. $row_no"], 422);
            }
          }

          if (!empty(trim($value[7]))) {
            $contact_array['tax_number'] = $value[7];
          }

          if (!empty(trim($value[8])) && $value[8] != 0) {
            $contact_array['opening_balance'] = trim($value[8]);
          }

          if (trim($value[11]) != '' && in_array($contact_type, ['customer', 'both'])) {
            $contact_array['credit_limit'] = trim($value[11]);
          }

          if (!empty(trim($value[12]))) {
            if (filter_var(trim($value[12]), FILTER_VALIDATE_EMAIL)) {
              $contact_array['email'] = $value[12];
            } else {
              return response()->json(['success' => 0, 'msg' => "Invalid email id in row no. $row_no"], 422);
            }
          }

          if (!empty(trim($value[13]))) {
            $contact_array['mobile'] = $value[13];
          } else {
            return response()->json(['success' => 0, 'msg' => "Mobile number is required in row no. $row_no"], 422);
          }

          $contact_array['alternate_number'] = $value[14];
          $contact_array['landline'] = $value[15];
          $contact_array['city'] = $value[16];
          $contact_array['state'] = $value[17];
          $contact_array['country'] = $value[18];
          $contact_array['address_line_1'] = $value[19];
          $contact_array['address_line_2'] = $value[20];
          $contact_array['zip_code'] = $value[21];
          $contact_array['dob'] = $value[22];
          $contact_array['custom_field1'] = $value[23];
          $contact_array['custom_field2'] = $value[24];
          $contact_array['custom_field3'] = $value[25];
          $contact_array['custom_field4'] = $value[26];

          $formated_data[] = $contact_array;
        }

        if (!$is_valid) {
          throw new \Exception($error_msg);
        }

        if (!empty($formated_data)) {
          foreach ($formated_data as $contact_data) {
            $ref_count = $this->setAndGetReferenceCount('contacts', $business_id);

            if (empty($contact_data['contact_id'])) {
              $contact_data['contact_id'] = $this->generateReferenceNumber('contacts', $ref_count, $business_id);
            }

            $opening_balance = 0;
            if (isset($contact_data['opening_balance'])) {
              $opening_balance = $contact_data['opening_balance'];
              unset($contact_data['opening_balance']);
            }

            $contact_data['business_id'] = $business_id;
            $contact_data['created_by'] = $user_id;

            // Log data before insertion for debugging
            \Log::info('Inserting contact:', $contact_data);

            $contact = Contact::create($contact_data);

            if (!empty($opening_balance)) {
              $this->transactionUtil->createOpeningBalanceTransaction($business_id, $contact->id, $opening_balance, $user_id, false);
            }

            $this->activityLog($contact, 'imported');
          }
        }

        $output = [
          'success' => 1,
          'msg' => __('product.file_imported_successfully'),
        ];

        DB::commit();

        return response()->json($output, 200);
      } else {
        return response()->json(['success' => 0, 'msg' => 'No file uploaded'], 400);
      }
    } catch (\Exception $e) {
      
      \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

      return response()->json(['success' => 0, 'msg' => $e->getMessage()], 500);
    }
  }


  public function setAndGetReferenceCount($type, $business_id = null)
  {
    // Ensure that business_id is provided
    if (empty($business_id)) {
      throw new \Exception("Business ID is required");
    }

    // Find the reference count for the given type and business ID
    $ref = ReferenceCount::where('ref_type', $type)
      ->where('business_id', $business_id)
      ->first();

    if (!empty($ref)) {
      // Increment the reference count if it exists
      $ref->ref_count += 1;
      $ref->save();

      return $ref->ref_count;
    } else {
      // Create a new reference count if it doesn't exist
      $new_ref = ReferenceCount::create([
        'ref_type' => $type,
        'business_id' => $business_id,
        'ref_count' => 1,
      ]);

      return $new_ref->ref_count;
    }
  }
  public function generateReferenceNumber($type, $ref_count, $business_id = null, $default_prefix = null)
  {
    $prefix = '';

    // Fetch prefix from business ID if provided
    if (!empty($business_id)) {
      $business = Business::find($business_id);
      if ($business && !empty($business->ref_no_prefixes) && !empty($business->ref_no_prefixes[$type])) {
        $prefix = $business->ref_no_prefixes[$type];
      }
    }

    // Override with default prefix if provided
    if (!empty($default_prefix)) {
      $prefix = $default_prefix;
    }

    // Generate the reference digits
    $ref_digits = str_pad($ref_count, 4, '0', STR_PAD_LEFT);

    // Append the year and prefix for types that require it
    if (!in_array($type, ['contacts', 'business_location', 'username'])) {
      $ref_year = \Carbon\Carbon::now()->year;
      $ref_number = $prefix . $ref_year . '/' . $ref_digits;
    } else {
      $ref_number = $prefix . $ref_digits;
    }

    return $ref_number;
  }
  public function activityLog($on, $action = null, $before = null, $properties = [], $log_changes = true, $business_id = null)
  {
    // Log changes if required
    if ($log_changes) {
      $log_properties = $on->log_properties ?? [];
      foreach ($log_properties as $property) {
        if (isset($on->$property)) {
          $properties['attributes'][$property] = $on->$property;
        }

        if (!empty($before) && isset($before->$property)) {
          $properties['old'][$property] = $before->$property;
        }
      }
    }

    // Ensure business_id is set
    if (empty($business_id)) {
      if (!empty($on->business_id)) {
        $business_id = $on->business_id;
      } else {
        throw new \Exception("Business ID is required");
      }
    }

    // Fetch business data based on the business_id
    $business = Business::find($business_id);

    if (!$business) {
      throw new \Exception("Business not found");
    }

    // Set the timezone for logging
    date_default_timezone_set($business->time_zone);

    // Log the activity
    $activity = activity()
      ->performedOn($on)
      ->withProperties($properties)
      ->log($action);

    // Attach business_id to the activity log
    $activity->business_id = $business_id;
    $activity->save();
  }


}