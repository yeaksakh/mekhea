<?php

namespace Modules\PurchaseAutoFill\Http\Controllers;

use App\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Modules\PurchaseAutoFill\Entities\TelegramOcrData;
use Modules\PurchaseAutoFill\Services\PurchaseInvoiceOcrService;
use Yajra\DataTables\DataTables;
use App\BusinessLocation;
use App\Contact;
use App\CustomerGroup;
use App\TaxRate;
use App\Transaction;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use DB;

class BotImageController extends Controller
{
    // Static token - replace with your actual bot token
    private $botToken = '7946624031:AAFcNbAZqfqGej0W7BLCcE_85Ytc6MaMAgk';


    // OCR service instance
    protected $ocrService;

    public function __construct(PurchaseInvoiceOcrService $ocrService)
    {
        $this->ocrService = $ocrService;
        $this->dummyPaymentLine = [
            'method' => 'cash',
            'amount' => 0,
            'note' => '',
            'card_transaction_number' => '',
            'card_number' => '',
            'card_type' => '',
            'card_holder_name' => '',
            'card_month' => '',
            'card_year' => '',
            'card_security' => '',
            'cheque_number' => '',
            'bank_account_number' => '',
            'is_return' => 0,
            'transaction_no' => '',
        ];
    }

    /**
     * Display the Telegram images in a table view
     */
    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        // Permission check - adjust permissions as needed
        if (!auth()->user()->can('purchaseautofill.view') && !auth()->user()->can('purchaseautofill.create')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            // Query the database instead of the Telegram API
            $query = TelegramOcrData::where('business_id', $business_id)
                ->select([
                    'id',
                    'telegram_file_id',
                    'telegram_from',
                    'telegram_date',
                    'ocr_status',
                    'final_total',
                    'telegram_file_size',
                    'telegram_width',
                    'telegram_height',
                    'image_path' // Add image_path to use for preview
                ]);

            // Apply filters
            if (!empty(request()->ocr_status)) {
                $query->where('ocr_status', request()->ocr_status);
            }

            if (!empty(request()->from_date) && !empty(request()->to_date)) {
                $fromDate = request()->from_date . ' 00:00:00';
                $toDate = request()->to_date . ' 23:59:59';
                $query->whereBetween('telegram_date', [$fromDate, $toDate]);
            }

            return DataTables::of($query)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
        <button type="button" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown">
            Actions <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    if (auth()->user()->can('purchaseautofill.view')) {
                        $html .= '<li><a href="#" class="view-image" data-id="' . $row->id . '">
            <i class="fas fa-eye"></i> View
        </a></li>';
                    }

                    if (auth()->user()->can('purchaseautofill.delete')) {
                        $html .= '<li><a href="#" class="delete-image" data-id="' . $row->id . '">
            <i class="fas fa-trash"></i> Delete
        </a></li>';
                    }

                    if (auth()->user()->can('purchaseautofill.prefill')) {
                        $url = route('purchaseautofill.prefill', $row->id);
                        $html .= '<li><a href="' . $url . '" class="accept-ocr">
            <i class="fa fa-plug"></i> Set Prefill
        </a></li>';
                    }

                    $html .= '</ul></div>';
                    return $html;
                })
                ->editColumn('image', function ($row) {
                    if (!$row->image_path) {
                        return '<div style="display: flex;">No Image</div>';
                    }

                    $url = asset($row->image_path);
                    // dd($url);
                    return '<div style="display: flex;"><img src="' . $url . '" alt="Telegram Image" class="product-thumbnail-small"></div>';
                })
                ->editColumn('ocr_status', function ($row) {
                    $statusClass = '';
                    switch ($row->ocr_status) {
                        case 'pending':
                            $statusClass = 'bg-yellow';
                            break;
                        case 'processing':
                            $statusClass = 'bg-blue';
                            break;
                        case 'completed':
                            $statusClass = 'bg-green';
                            break;
                        case 'failed':
                            $statusClass = 'bg-red';
                            break;
                        default:
                            $statusClass = 'bg-gray';
                    }
                    return '<span class="label ' . $statusClass . '">' . ucfirst($row->ocr_status) . '</span>';
                })
                ->editColumn('telegram_date', '{{@format_datetime($telegram_date)}}')
                ->editColumn('final_total', '<span class="final_total" data-orig-value="{{$final_total}}">@format_currency($final_total)</span>')
                ->editColumn('telegram_file_size', function ($row) {
                    return $this->formatFileSize($row->telegram_file_size);
                })
                ->addColumn('dimensions', function ($row) {
                    return $row->telegram_width . 'x' . $row->telegram_height;
                })
                // Remove columns that are not needed in the final table output
                ->removeColumn('image_path')
                ->removeColumn('telegram_width')
                ->removeColumn('telegram_height')
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('purchaseautofill.view')) {
                            return url("/purchaseautofill/bot-image/{$row->id}"); // Use DB ID
                        } else {
                            return '';
                        }
                    }
                ])
                ->rawColumns(['action', 'image', 'ocr_status', 'final_total'])
                ->make(true);
        }

        // For non-ajax requests, prepare data for the view
        $ocrStatuses = [
            '' => 'All Status',
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
        ];

        return view('purchaseautofill::PurchaseAutoFill.bot-images')
            ->with(compact('ocrStatuses'));
    }


    /**
     * Display the image from the bot using file ID
     */
    public function showImage($id) // <-- Changed the parameter name to $id for clarity
    {
        // 1. Find the image record in the database using its primary key ID.
        // Using find() is the most efficient way to look up by primary key.
        $imageRecord = TelegramOcrData::find($id);

        // 2. Check if the record actually exists.
        // If not, return a 404 "Not Found" error.
        if (!$imageRecord) {
            // You can return a JSON error or a 404 page
            abort(404, 'Image record not found.');
            // Or: return response()->json(['error' => 'Image not found for this ID.'], 404);
        }

        // 3. Get the path to the image from the record.
        $imagePath = $imageRecord->image_path;

        // 4. Check if the file actually exists in your storage.
        if (!Storage::disk('public')->exists($imagePath)) {
            abort(404, 'Image file does not exist on server.');
            // Or: return response()->json(['error' => 'Image file does not exist on server.'], 404);
        }

        // 5. Get the file's contents and its MIME type.
        $imageContents = Storage::disk('public')->get($imagePath);
        $mimeType = Storage::disk('public')->mimeType($imagePath);

        // 6. Return the image as a response.
        return response($imageContents)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . basename($imagePath) . '"');
    }

    /**
     * Just display the image without storing
     */
    public function displayImageOnly($fileId)
    {
        return $this->showImage($fileId);
    }

    /**
     * Download the image
     */
    public function downloadImage($fileId)
    {
        $imageRecord = TelegramOcrData::find($fileId);

        if (!$imageRecord) {
            abort(404, 'Image record not found.');
        }

        $imagePath = $imageRecord->image_path;

        if (!Storage::disk('public')->exists($imagePath)) {
            abort(404, 'Image file does not exist on server.');
        }

        return Storage::disk('public')->download($imagePath);
    }

    /**
     * Accept image, store to database, and process with OCR
     */
    public function acceptImage(Request $request)
    {
        // Permission check
        if (! auth()->user()->can('purchaseautofill.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $fileId = $request->file_id;
            $updateId = $request->update_id;
            $messageId = $request->message_id;
            $chatId = $request->chat_id;

            // Get file information and download the image
            $fileInfoUrl = "https://api.telegram.org/bot{$this->botToken}/getFile?file_id={$fileId}";
            $fileInfoResponse = Http::get($fileInfoUrl);

            if (!$fileInfoResponse->successful()) {
                return response()->json(['success' => false, 'message' => 'Failed to get file info']);
            }

            $fileInfo = $fileInfoResponse->json();
            if (!$fileInfo['ok']) {
                return response()->json(['success' => false, 'message' => 'Invalid file info']);
            }

            $filePath = $fileInfo['result']['file_path'];
            $imageUrl = "https://api.telegram.org/file/bot{$this->botToken}/{$filePath}";
            $imageResponse = Http::get($imageUrl);

            if (!$imageResponse->successful()) {
                return response()->json(['success' => false, 'message' => 'Failed to download image']);
            }

            // Store the image
            $filename = 'telegram_' . $fileId . '_' . time() . '.jpg';
            $storedPath = 'telegram_images/' . $filename;
            Storage::disk('public')->put($storedPath, $imageResponse->body());

            // Get additional data from Telegram update
            $updatesUrl = "https://api.telegram.org/bot{$this->botToken}/getUpdates";
            $updatesResponse = Http::get($updatesUrl);

            $telegramFrom = 'Unknown';
            $telegramDate = now();
            $fileSize = 0;
            $width = 0;
            $height = 0;

            if ($updatesResponse->successful()) {
                $updates = $updatesResponse->json();
                if ($updates['ok']) {
                    $update = collect($updates['result'])->firstWhere('update_id', $updateId);
                    if ($update && isset($update['message'])) {
                        $telegramFrom = $update['message']['from']['first_name'] . ' ' . ($update['message']['from']['last_name'] ?? '');
                        $telegramDate = date('Y-m-d H:i:s', $update['message']['date']);

                        if (isset($update['message']['photo'])) {
                            $photo = $update['message']['photo'][count($update['message']['photo']) - 1];
                            $fileSize = $photo['file_size'] ?? 0;
                            $width = $photo['width'] ?? 0;
                            $height = $photo['height'] ?? 0;
                        }
                    }
                }
            }

            // Create database record with initial status
            $ocrData = TelegramOcrData::create([
                'business_id' => $business_id,
                'telegram_file_id' => $fileId,
                'telegram_file_unique_id' => $fileInfo['result']['file_unique_id'] ?? '',
                'telegram_file_size' => $fileSize,
                'telegram_width' => $width,
                'telegram_height' => $height,
                'telegram_from' => $telegramFrom,
                'telegram_date' => $telegramDate,
                'telegram_message_id' => $messageId,
                'telegram_chat_id' => $chatId,
                'image_path' => $storedPath,
                'ocr_status' => 'processing', // Set to processing immediately
                'status' => 'pending'
            ]);

            // Now process with OCR in the background
            try {
                // Get the image content for OCR
                $imageContent = Storage::disk('public')->get($storedPath);

                // Convert to base64 for API
                $base64Image = base64_encode($imageContent);

                // Process with OCR service
                $ocrResult = $this->ocrService->extractInvoiceData($base64Image);

                if ($ocrResult['success']) {

                    // Prepare update data
                    $updateData = [
                        'ocr_data' => $ocrResult['data'],
                        'ocr_status' => 'completed'
                    ];

                    // Map OCR data to individual columns
                    $ocrFields = [
                        'contact_id',
                        'supplier_name',  // Added
                        'company_name',   // Added
                        'ref_no',
                        'transaction_date',
                        'status',
                        'location_id',
                        'exchange_rate',
                        'pay_term_number',
                        'pay_term_type',
                        'document',
                        'custom_field_1',
                        'custom_field_2',
                        'custom_field_3',
                        'custom_field_4',
                        'purchase_order_ids',
                        'product',
                        'discount_type',
                        'discount_amount',
                        'tax_id',
                        'tax_amount',
                        'additional_notes',
                        'shipping_details',
                        'shipping_charges',
                        'final_total',
                        'advance_balance'
                    ];

                    foreach ($ocrFields as $field) {
                        if (isset($ocrResult['data'][$field])) {
                            $updateData[$field] = $ocrResult['data'][$field];
                        }
                    }

                    // Special mapping: If supplier_name is not in OCR data but contact_id is,
                    // use contact_id as supplier_name
                    if (!isset($updateData['supplier_name']) && isset($ocrResult['data']['contact_id'])) {
                        $updateData['supplier_name'] = $ocrResult['data']['contact_id'];
                    }

                    // Update the record
                    $ocrData->update($updateData);

                    return response()->json([
                        'success' => true,
                        'message' => 'Image accepted and OCR processed successfully',
                        'data' => $ocrData,
                        'ocr_data' => $ocrResult['data']
                    ]);
                } else {
                    // Update status to failed
                    $ocrData->ocr_status = 'failed';
                    $ocrData->ocr_error = $ocrResult['message'];
                    $ocrData->save();

                    return response()->json([
                        'success' => false,
                        'message' => 'Image stored but OCR processing failed: ' . $ocrResult['message'],
                        'data' => $ocrData
                    ]);
                }
            } catch (\Exception $e) {
                // Update status to failed if OCR processing fails
                $ocrData->ocr_status = 'failed';
                $ocrData->ocr_error = $e->getMessage();
                $ocrData->save();

                return response()->json([
                    'success' => false,
                    'message' => 'Image stored but OCR processing failed: ' . $e->getMessage(),
                    'data' => $ocrData
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process OCR for an existing image (manual trigger)
     */
    public function processOcr(Request $request)
    {
        // Permission check
        if (! auth()->user()->can('purchaseautofill.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $imageId = $request->id;

            // Get the image record
            $ocrData = TelegramOcrData::find($imageId);

            if (!$ocrData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found'
                ]);
            }

            // Update status to processing
            $ocrData->ocr_status = 'processing';
            $ocrData->save();

            // Get the image content
            $imageContent = Storage::disk('public')->get($ocrData->image_path);

            // Convert to base64 for API
            $base64Image = base64_encode($imageContent);

            // Process with OCR service
            $ocrResult = $this->ocrService->extractInvoiceData($base64Image);

            if ($ocrResult['success']) {
                // Prepare update data
                $updateData = [
                    'ocr_data' => $ocrResult['data'],
                    'ocr_status' => 'completed'
                ];

                // Map OCR data to individual columns
                $ocrFields = [
                    'contact_id',
                    'supplier_name',  // Added
                    'company_name',   // Added
                    'ref_no',
                    'transaction_date',
                    'status',
                    'location_id',
                    'exchange_rate',
                    'pay_term_number',
                    'pay_term_type',
                    'document',
                    'custom_field_1',
                    'custom_field_2',
                    'custom_field_3',
                    'custom_field_4',
                    'purchase_order_ids',
                    'product',
                    'discount_type',
                    'discount_amount',
                    'tax_id',
                    'tax_amount',
                    'additional_notes',
                    'shipping_details',
                    'shipping_charges',
                    'final_total',
                    'advance_balance'
                ];

                foreach ($ocrFields as $field) {
                    if (isset($ocrResult['data'][$field])) {
                        $updateData[$field] = $ocrResult['data'][$field];
                    }
                }

                // Special mapping: If supplier_name is not in OCR data but contact_id is,
                // use contact_id as supplier_name
                if (!isset($updateData['supplier_name']) && isset($ocrResult['data']['contact_id'])) {
                    $updateData['supplier_name'] = $ocrResult['data']['contact_id'];
                }

                // Update the record
                $ocrData->update($updateData);

                return response()->json([
                    'success' => true,
                    'message' => 'OCR processed successfully',
                    'data' => $ocrResult['data']
                ]);
            } else {
                // Update status to failed
                $ocrData->ocr_status = 'failed';
                $ocrData->ocr_error = $ocrResult['message'];
                $ocrData->save();

                return response()->json([
                    'success' => false,
                    'message' => 'OCR processing failed: ' . $ocrResult['message']
                ]);
            }
        } catch (\Exception $e) {
            // Update status to failed if we have the record
            if (isset($ocrData)) {
                $ocrData->ocr_status = 'failed';
                $ocrData->ocr_error = $e->getMessage();
                $ocrData->save();
            }

            return response()->json([
                'success' => false,
                'message' => 'Error processing OCR: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Decline image (delete from Telegram updates)
     */
    public function declineImage(Request $request)
    {
        // Permission check
        if (! auth()->user()->can('purchaseautofill.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $updateId = $request->update_id;
            $chatId = $request->chat_id;

            // Note: Telegram doesn't provide a direct way to delete getUpdates messages
            // We acknowledge receipt by setting offset past this update
            // Or optionally send a message to the user

            // Optional: Send notification to user
            if ($chatId) {
                $messageUrl = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
                Http::post($messageUrl, [
                    'chat_id' => $chatId,
                    'text' => '❌ Your image was declined.'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Image declined successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get image details for modal
     */
    public function getImageDetails($id) // <-- The $id is now passed directly from the route
    {
        // Permission check
        if (! auth()->user()->can('purchaseautofill.view')) {
            abort(403, 'Unauthorized action.');
        }

        // Get the business_id of the logged-in user
        $business_id = request()->session()->get('user.business_id');

        // CRITICAL: Find the record, but ONLY if it belongs to the current user's business
        $ocrData = TelegramOcrData::where('business_id', $business_id)->find($id);

        if (!$ocrData) {
            // Return a 404 Not Found status, which is more accurate
            return response()->json(['success' => false, 'message' => 'Image not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $ocrData]);
    }

    /**
     * Format file size helper
     */
    private function formatFileSize($bytes)
    {
        if ($bytes === 0 || $bytes === null || $bytes === 'undefined') {
            return '0 Bytes';
        }

        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));

        return number_format($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }


    // ====================
    // ADD THIS METHOD TO YOUR BotImageController.php
    // ====================

    /**
     * Delete image and its database record
     */
    public function destroy($id)
    {
        // Permission check
        if (!auth()->user()->can('purchaseautofill.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            // Find the image record, but ONLY if it belongs to the current user's business
            $ocrData = TelegramOcrData::where('business_id', $business_id)->find($id);

            if (!$ocrData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found or you do not have permission to delete it'
                ], 404);
            }

            // Delete the physical file if it exists
            if ($ocrData->image_path && Storage::disk('public')->exists($ocrData->image_path)) {
                Storage::disk('public')->delete($ocrData->image_path);
            }

            // Delete the database record
            $ocrData->delete();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting image: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Get OCR data from telegram_ocr_data table
     * 
     * @param int $telegram_id
     * @return object|null
     */
    private function getTelegramOcrData($telegram_id)
    {
        return \DB::table('telegram_ocr_data')
            ->where('id', $telegram_id)
            ->where('business_id', request()->session()->get('user.business_id'))
            ->where('ocr_status', 'completed')
            ->first();
    }


    public function prefillForm($id)
    {
        if (! auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $moduleUtil = app(ModuleUtil::class);

        //Check if subscribed or not
        if (! $moduleUtil->isSubscribed($business_id)) {
            return $moduleUtil->expiredResponse();
        }

        $taxes = TaxRate::where('business_id', $business_id)
            ->ExcludeForTaxGroup()
            ->get();
        $prodcutUtil = app(ProductUtil::class);
        $orderStatuses = $prodcutUtil->orderStatuses();
        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];

        $transactionUtil = app(TransactionUtil::class);

        $currency_details = $transactionUtil->purchaseCurrencyDetails($business_id);

        $ocr = TelegramOcrData::find($id);

        // Set static default purchase status
        $default_purchase_status = 'received'; // Static value

        // Set static default transaction date (today's date in the format expected by the form)
        $default_transaction_date = $ocr->transaction_date;

        $supplier_business_name = $ocr->supplier_name;
        // $supplier_business_name = 'ក្រុមហ៊ុន​អាឌូម៉ាស ABA THIN '; 

        // Get the image path from OCR data
        $ocr_image_path = $ocr->image_path;

        // Set static default business location (you need to replace '1' with the actual ID of your desired location)
        $default_location_id = 17; // Static value - replace with your actual location ID

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = CustomerGroup::forDropdown($business_id);

        $businessUtil = app(BusinessUtil::class);
        $business_details = $businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $payment_line = $this->dummyPaymentLine;
        $payment_types = $prodcutUtil->payment_types(null, true, $business_id);

        //Accounts
        $accounts = $moduleUtil->accountsDropdown($business_id, true);

        $common_settings = ! empty(session('business.common_settings')) ? session('business.common_settings') : [];

        return view('purchaseautofill::PurchaseAutoFill.prefill_form')
            ->with(compact(
                'taxes',
                'orderStatuses',
                'business_locations',
                'currency_details',
                'default_purchase_status',
                'customer_groups',
                'types',
                'shortcuts',
                'payment_line',
                'payment_types',
                'accounts',
                'bl_attributes',
                'common_settings',
                'default_transaction_date',
                'default_location_id',
                'supplier_business_name',
                'ocr_image_path'
            ));
    }

   
}
