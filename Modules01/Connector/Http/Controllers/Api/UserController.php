<?php

namespace Modules\Connector\Http\Controllers\Api;

use App\User;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Connector\Notifications\NewPassword;
use Modules\Connector\Transformers\CommonResource;
use App\Media;

/**
 * @group User management
 * @authenticated
 *
 * APIs for managing users
 */
class UserController extends ApiController
{
    /**
     * All Utils instance.
     */
    protected $commonUtil;

    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    /**
     * List users
     *
     * @queryParam service_staff boolean Filter service staffs from users list (0, 1)
     *
     * @response {
            "data": [
                {
                    "id": 1,
                    "user_type": "user",
                    "surname": "Mr",
                    "first_name": "Admin",
                    "last_name": null,
                    "username": "admin",
                    "email": "admin@example.com",
                    "language": "en",
                    "contact_no": null,
                    "address": null,
                    "business_id": 1,
                    "max_sales_discount_percent": null,
                    "allow_login": 1,
                    "essentials_department_id": null,
                    "essentials_designation_id": null,
                    "status": "active",
                    "crm_contact_id": null,
                    "is_cmmsn_agnt": 0,
                    "cmmsn_percent": "0.00",
                    "selected_contacts": 0,
                    "dob": null,
                    "gender": null,
                    "marital_status": null,
                    "blood_group": null,
                    "contact_number": null,
                    "fb_link": null,
                    "twitter_link": null,
                    "social_media_1": null,
                    "social_media_2": null,
                    "permanent_address": null,
                    "current_address": null,
                    "guardian_name": null,
                    "custom_field_1": null,
                    "custom_field_2": null,
                    "custom_field_3": null,
                    "custom_field_4": null,
                    "bank_details": null,
                    "id_proof_name": null,
                    "id_proof_number": null,
                    "deleted_at": null,
                    "created_at": "2018-01-04 02:15:19",
                    "updated_at": "2018-01-04 02:15:19"
                }
            ]
        }
     */
    public function index()
    {
        $user = Auth::user();

        $business_id = $user->business_id;

        if (! empty(request()->service_staff) && request()->service_staff == 1) {
            $users = $this->commonUtil->getServiceStaff($business_id);
        } else {
            $users = User::where('business_id', $business_id)->get();
        }

        $users->each(function ($user) {
            $user->role_name = $user->role_name; // Accessor to get the role name
        });

        return CommonResource::collection($users);
    }

    /**
     * Get the specified user
     *
     * @response {
            "data": [
                {
                    "id": 1,
                    "user_type": "user",
                    "surname": "Mr",
                    "first_name": "Admin",
                    "last_name": null,
                    "username": "admin",
                    "email": "admin@example.com",
                    "language": "en",
                    "contact_no": null,
                    "address": null,
                    "business_id": 1,
                    "max_sales_discount_percent": null,
                    "allow_login": 1,
                    "essentials_department_id": null,
                    "essentials_designation_id": null,
                    "status": "active",
                    "crm_contact_id": null,
                    "is_cmmsn_agnt": 0,
                    "cmmsn_percent": "0.00",
                    "selected_contacts": 0,
                    "dob": null,
                    "gender": null,
                    "marital_status": null,
                    "blood_group": null,
                    "contact_number": null,
                    "fb_link": null,
                    "twitter_link": null,
                    "social_media_1": null,
                    "social_media_2": null,
                    "permanent_address": null,
                    "current_address": null,
                    "guardian_name": null,
                    "custom_field_1": null,
                    "custom_field_2": null,
                    "custom_field_3": null,
                    "custom_field_4": null,
                    "bank_details": null,
                    "id_proof_name": null,
                    "id_proof_number": null,
                    "deleted_at": null,
                    "created_at": "2018-01-04 02:15:19",
                    "updated_at": "2018-01-04 02:15:19"
                }
            ]
        }
     * @urlParam user required comma separated ids of the required users Example: 1
     */
    public function show($user_ids)
    {
        $user = Auth::user();

        $business_id = $user->business_id;
        $user_ids = explode(',', $user_ids);

        $users = User::where('business_id', $business_id)
            ->whereIn('id', $user_ids)->get();

        $users->each(function ($user) {
            $user->role_name = $user->role_name; // This will call the accessor
        });
        
        return CommonResource::collection($users);
    }

    /**
     * Get the loggedin user details.
     *
     * @response {
            "data":{
                "id": 1,
                "user_type": "user",
                "surname": "Mr",
                "first_name": "Admin",
                "last_name": null,
                "username": "admin",
                "email": "admin@example.com",
                "language": "en",
                "contact_no": null,
                "address": null,
                "business_id": 1,
                "max_sales_discount_percent": null,
                "allow_login": 1,
                "essentials_department_id": null,
                "essentials_designation_id": null,
                "status": "active",
                "crm_contact_id": null,
                "is_cmmsn_agnt": 0,
                "cmmsn_percent": "0.00",
                "selected_contacts": 0,
                "dob": null,
                "gender": null,
                "marital_status": null,
                "blood_group": null,
                "contact_number": null,
                "fb_link": null,
                "twitter_link": null,
                "social_media_1": null,
                "social_media_2": null,
                "permanent_address": null,
                "current_address": null,
                "guardian_name": null,
                "custom_field_1": null,
                "custom_field_2": null,
                "custom_field_3": null,
                "custom_field_4": null,
                "bank_details": null,
                "id_proof_name": null,
                "id_proof_number": null,
                "deleted_at": null,
                "created_at": "2018-01-04 02:15:19",
                "updated_at": "2018-01-04 02:15:19"
            }
        }
     */
    public function loggedin()
    {
        $user = Auth::user();
        $user->is_admin = $this->commonUtil->is_admin($user);

        if (! $user->is_admin) {
            $user->all_permissions = $user->getAllPermissions()->pluck('name');
        }
        $user->business_logo = $user->business ? $user->business->logo : null;
        unset($user->permissions);
        unset($user->roles);

        return new CommonResource($user);
    }

    /**
     * Update user password.
     *
     * @bodyParam current_password string required Current password of the user
     * @bodyParam new_password string required New password of the user
     *
     * @response {
            "success":1,
            "msg":"Password updated successfully"
        }
     */
    public function updatePassword(Request $request)
    {
        try {
            $user = Auth::user();

            if (! empty($request->input('current_password')) && ! empty($request->input('new_password'))) {
                if (Hash::check($request->input('current_password'), $user->password)) {
                    $user->password = Hash::make($request->input('new_password'));
                    $user->save();
                    $output = [
                        'success' => 1,
                        'msg' => __('lang_v1.password_updated_successfully'),
                    ];
                } else {
                    $output = [
                        'success' => 0,
                        'msg' => __('lang_v1.u_have_entered_wrong_password'),
                    ];
                }
            } else {
                $output = [
                    'success' => 0,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        if ($output['success']) {
            return $this->respond($output);
        } else {
            return $this->otherExceptions($output['msg']);
        }
    }

    /**
     * Register User
     *
     * @bodyParam surname string prefix like Mr, Mrs,Dr
     * @bodyParam first_name string required
     * @bodyParam last_name string
     * @bodyParam email string required
     * @bodyParam is_active string required 'active', 'inactive', 'terminated'
     * @bodyParam user_type string required 'user_customer' for contact/customer login & 'user' for general user
     * @bodyParam crm_contact_id integer if user_type is 'user_customer' then required
     * @bodyParam allow_login boolean 1 to allow login & 0 to disable login
     * @bodyParam username string minimum 5 characters
     * @bodyParam password string minimum 6 characters & required if 'allow_login' is 1
     * @bodyParam role integer id of role to be assigned to user & required if user_type is 'user'
     * @bodyParam access_all_locations boolean 1 if user has access all location else 0 & required if user_type is 'user'
     * @bodyParam location_permissions array array of location ids to be assigned to user & required if user_type is 'user' and 'access_all_locations' is 0
     * @bodyParam cmmsn_percent decimal
     * @bodyParam max_sales_discount_percent decimal
     * @bodyParam selected_contacts boolean 1 or 0
     * @bodyParam selected_contact_ids array array of contact ids & required if 'selected_contacts' is 1
     * @bodyParam dob date dob of user in "Y-m-d" format Ex: 1997-10-29
     * @bodyParam gender string if user is 'male', 'female', 'others'
     * @bodyParam marital_status string if user is 'married', 'unmarried', 'divorced'
     * @bodyParam blood_group string
     * @bodyParam contact_number string
     * @bodyParam alt_number string
     * @bodyParam family_number string
     * @bodyParam fb_link string
     * @bodyParam twitter_link string
     * @bodyParam social_media_1 string
     * @bodyParam social_media_2 string
     * @bodyParam custom_field_1 string
     * @bodyParam custom_field_2 string
     * @bodyParam custom_field_3 string
     * @bodyParam custom_field_4 string
     * @bodyParam guardian_name string
     * @bodyParam id_proof_name string ID proof of user like Adhar No.
     * @bodyParam id_proof_number string Id Number like adhar number
     * @bodyParam permanent_address string
     * @bodyParam current_address string
     * @bodyParam bank_details.*.account_holder_name string
     * @bodyParam bank_details.*.account_number string
     * @bodyParam bank_details.*.bank_name string
     * @bodyParam bank_details.*.bank_code string
     * @bodyParam bank_details.*.branch string
     * @bodyParam bank_details.*.tax_payer_id string
     *
     * @response {
        "success": 1,
        "msg": "User added successfully",
        "user": {
            "surname": "Mr",
            "first_name": "Test",
            "last_name": "kumar",
            "email": "test@example.com",
            "user_type": "user_customer",
            "crm_contact_id": "2",
            "allow_login": 1,
            "username": "0017",
            "cmmsn_percent": "25",
            "max_sales_discount_percent": "52",
            "dob": "1997-10-12",
            "gender": "male",
            "marital_status": "unmarried",
            "blood_group": "0+",
            "contact_number": "4578451245",
            "alt_number": "7474747474",
            "family_number": "7474147414",
            "fb_link": "fb.com/username",
            "twitter_link": "twitter.com/username",
            "social_media_1": "test",
            "social_media_2": "test",
            "custom_field_1": "test",
            "custom_field_2": "test",
            "custom_field_3": "test",
            "custom_field_4": "test",
            "guardian_name": "test",
            "id_proof_name": "uid",
            "id_proof_number": "747845120124",
            "permanent_address": "test permanent adrress",
            "current_address": "test current address",
            "bank_details": "{\"account_holder_name\":\"test\",\"account_number\":\"test\",\"bank_name\":\"test\",\"bank_code\":\"test\",\"branch\":\"test\",\"tax_payer_id\":\"test\"}",
            "selected_contacts": "1",
            "status": "active",
            "business_id": 1,
            "updated_at": "2021-08-12 18:03:58",
            "created_at": "2021-08-12 18:03:58",
            "id": 140
        }
    }
     */
    public function registerUser(Request $request)
    {
        $request->validate([
            'username' => 'unique:users',
            'email' => 'required|unique:users',
            'user_type' => 'required',
        ]);

        try {
            $user = $this->commonUtil->createUser($request);

            $output = [
                'success' => 1,
                'msg' => __('user.user_added'),
                'user' => $user,
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        if ($output['success']) {
            return $this->respond($output);
        } else {
            return $this->otherExceptions($output['msg']);
        }
    }

    public function generateRandomString($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ#@_$&%*(){}[]!^?><=';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * Recover forgotten password.
     *
     * @bodyParam email string required Users email id
     *
     * @response {
            "success":1,
            "msg":"New password sent to user@example.com successfully"
        }
     */
    public function forgetPassword(Request $request)
    {
        try {
            $user = Auth::user();

            if (! empty($request->input('email'))) {
                $forgotten_user = User::where('business_id', $user->business_id)
                    ->where('email', $request->input('email'))
                    ->first();
                if (! empty($forgotten_user)) {
                    $new_password = $this->generateRandomString();
                    $forgotten_user->password = Hash::make($new_password);
                    $forgotten_user->save();

                    $forgotten_user->notify(new NewPassword($new_password));
                    $output = [
                        'success' => 1,
                        'msg' => "New password sent to {$forgotten_user->email} successfully",
                    ];
                } else {
                    $output = [
                        'success' => 0,
                        'msg' => 'User not found',
                    ];
                }
            } else {
                $output = [
                    'success' => 0,
                    'msg' => 'Email Required',
                ];
            }
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage(),
            ];
        }

        if ($output['success']) {
            return $this->respond($output);
        } else {
            return $this->otherExceptions($output['msg']);
        }
    }

    public function edit(Request $request, $user_id)
    {
        $authUser = Auth::user();
        $business_id = $authUser->business_id;

        try {
            // Find the user by ID and business ID
            $user = User::where('business_id', $business_id)->findOrFail($user_id);

            // Updating user attributes
            $user->surname = $request->input('surname', $user->surname);
            $user->first_name = $request->input('first_name', $user->first_name);
            $user->last_name = $request->input('last_name', $user->last_name);
            $user->email = $request->input('email', $user->email);
            $user->allow_login = $request->input('allow_login', $user->allow_login);
            $user->language = $request->input('language', $user->language);
            $user->contact_number = $request->input('contact_number', $user->contact_number);
            $user->address = $request->input('address', $user->address);
            $user->dob = $request->input('dob', $user->dob);
            $user->gender = $request->input('gender', $user->gender);
            $user->marital_status = $request->input('marital_status', $user->marital_status);
            $user->blood_group = $request->input('blood_group', $user->blood_group);
            $user->fb_link = $request->input('fb_link', $user->fb_link);
            $user->twitter_link = $request->input('twitter_link', $user->twitter_link);
            $user->social_media_1 = $request->input('social_media_1', $user->social_media_1);
            $user->social_media_2 = $request->input('social_media_2', $user->social_media_2);
            $user->permanent_address = $request->input('permanent_address', $user->permanent_address);
            $user->current_address = $request->input('current_address', $user->current_address);
            $user->guardian_name = $request->input('guardian_name', $user->guardian_name);
            $user->custom_field_1 = $request->input('custom_field_1', $user->custom_field_1);
            $user->custom_field_2 = $request->input('custom_field_2', $user->custom_field_2);
            $user->custom_field_3 = $request->input('custom_field_3', $user->custom_field_3);
            $user->custom_field_4 = $request->input('custom_field_4', $user->custom_field_4);
            $user->bank_details = $request->input('bank_details', $user->bank_details);
            $user->id_proof_name = $request->input('id_proof_name', $user->id_proof_name);
            $user->id_proof_number = $request->input('id_proof_number', $user->id_proof_number);

            // Save updated user attributes
            $user->save();

            // Handle profile photo upload if provided
            if ($request->hasFile('profile_photo')) {
                // Check if an old photo exists
                if ($user->media()->where('model_media_type', 'profile_photo')->exists()) {
                    $oldMedia = $user->media()->where('model_media_type', 'profile_photo')->first();
                    if ($oldMedia) {
                        // Build the full path to the old file
                        $oldFilePath = public_path('uploads/media/' . $oldMedia->file_name);

                        // Delete the old file
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }

                        // Delete the media record from the database
                        $oldMedia->delete();
                    }
                }

                // Upload the new photo
                Media::uploadMedia($user->business_id, $user, $request, 'profile_photo', true, 'profile_photo');
            }

            // Return success response with updated user data
            $output = [
                'success' => 1,
                'msg' => __('user.user_updated_successfully'),
                'user' => $user,
            ];
        } catch (\Exception $e) {
            // Log any exceptions and return an error message
            \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        // Return the response
        if ($output['success']) {
            return response()->json($output, 200);
        } else {
            return response()->json($output, 500);
        }
    }
}