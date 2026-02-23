<?php

namespace Modules\ExpenseAutoFill\Http\Controllers;

use App\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Modules\ExpenseAutoFill\Entities\TelegramOcrData;
use Modules\ExpenseAutoFill\Entities\TelegramExpenseImageData;
use Modules\ExpenseAutoFill\Services\ExpenseInvoiceOcrService;
use Yajra\DataTables\DataTables;
use App\BusinessLocation;
use App\Contact;
use App\CustomerGroup;
use App\TaxRate;
use App\User;
use App\Transaction;
use App\ExpenseCategory;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\ProductUtil;
use App\Account;

use DB;
use Modules\ExpenseAutoFill\Entities\ExpenseAutoFillSocial;

class BotImageController extends Controller
{
    // Static token - replace with your actual bot token
    protected $botToken;
    protected $ocrService;
    protected $business_id;

    public function __construct(ExpenseInvoiceOcrService $ocrService)
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

    // /**
    //  * Display the Telegram images in a table view
    //  */
    // public function index(Request $request)
    // {

    //     $this->business_id  = request()->session()->get('user.business_id');
    //     $this->botToken = ExpenseAutoFillSocial::where('business_id', $business_id)
    //         ->value('social_token');

    //     // Permission check - adjust permissions as needed
    //     // if (!auth()->user()->can('expenseautofill.view') && !auth()->user()->can('expenseautofill.create')) {
    //     //     abort(403, 'Unauthorized action.');
    //     // }

    //     if ($request->ajax()) {
    //         // Query the TelegramExpenseImageData table instead of TelegramOcrData
    //         $query = TelegramExpenseImageData::where('business_id', $business_id)
    //             ->select([
    //                 'id',
    //                 'telegram_file_id',
    //                 'telegram_user_first_name',
    //                 'telegram_user_last_name',
    //                 'telegram_date',
    //                 'status',
    //                 'total_amount',
    //                 'telegram_file_size',
    //                 'telegram_width',
    //                 'telegram_height',
    //                 'file_path', // Changed from image_path to file_path
    //                 'supplier',
    //                 'transaction_date',
    //                 'location',
    //                 'category',
    //                 'ref_no'
    //             ]);

    //         // Apply filters
    //         if (!empty(request()->status)) { // Changed from ocr_status to status
    //             $query->where('status', request()->status);
    //         }

    //         // if (!empty(request()->from_date) && !empty(request()->to_date)) {
    //         //     $fromDate = request()->from_date . ' 00:00:00';
    //         //     $toDate = request()->to_date . ' 23:59:59';
    //         //     $query->whereBetween('telegram_date', [$fromDate, $toDate]);
    //         // }

    //         dd(request()->start_date, request()->end_date);


    //         if (! empty(request()->start_date) && ! empty(request()->end_date)) {
    //             $start = request()->start_date;
    //             $end = request()->end_date;
    //             $query->whereBetween('telegram_date', [$start, $end]);
    //         }


    //         return DataTables::of($query)
    //             ->addColumn('action', function ($row) {
    //                 $html = '<div class="btn-group">
    // <button type="button" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown">
    //     Actions <span class="caret"></span>
    // </button>
    // <ul class="dropdown-menu dropdown-menu-left" role="menu">';

    //                 if (auth()->user()->can('expenseautofill.view')) {
    //                     $html .= '<li><a href="#" class="view-image" data-id="' . $row->id . '">
    //     <i class="fas fa-eye"></i> View
    // </a></li>';
    //                 }

    //                 if (auth()->user()->can('expenseautofill.delete')) {
    //                     $html .= '<li><a href="#" class="delete-image" data-id="' . $row->id . '">
    //     <i class="fas fa-trash"></i> Delete
    // </a></li>';
    //                 }

    //                 if (auth()->user()->can('expenseautofill.prefill')) {
    //                     $url = route('expenseautofill.prefill', $row->id);
    //                     $html .= '<li><a href="' . $url . '" class="accept-ocr">
    //     <i class="fa fa-plug"></i> Set Prefill
    // </a></li>';
    //                 }

    //                 $html .= '</ul></div>';
    //                 return $html;
    //             })
    //             ->addColumn('telegram_from', function ($row) {
    //                 // Combine first_name and last_name to display the full name
    //                 return $row->telegram_user_first_name . ' ' . ($row->telegram_user_last_name ?? '');
    //             })
    //             ->editColumn('image', function ($row) {
    //                 if (!$row->file_path) { // Changed from image_path to file_path
    //                     return '<div style="display: flex;">No Image</div>';
    //                 }

    //                 $url = asset($row->file_path); // Changed from image_path to file_path
    //                 return '<div style="display: flex;"><img src="' . $url . '" alt="Telegram Image" class="product-thumbnail-small"></div>';
    //             })
    //             ->editColumn('status', function ($row) { // Changed from ocr_status to status
    //                 $statusClass = '';
    //                 switch ($row->status) {
    //                     case 'stored':
    //                         $statusClass = 'bg-yellow';
    //                         break;
    //                     case 'processing':
    //                         $statusClass = 'bg-blue';
    //                         break;
    //                     case 'processed':
    //                         $statusClass = 'bg-green';
    //                         break;
    //                     case 'failed':
    //                         $statusClass = 'bg-red';
    //                         break;
    //                     default:
    //                         $statusClass = 'bg-gray';
    //                 }
    //                 return '<span class="label ' . $statusClass . '">' . ucfirst($row->status) . '</span>';
    //             })
    //             ->editColumn('telegram_date', '{{@format_datetime($telegram_date)}}')
    //             ->editColumn('total_amount', '<span class="total_amount" data-orig-value="{{$total_amount}}">@format_currency($total_amount)</span>') // Changed from final_total to total_amount
    //             ->editColumn('telegram_file_size', function ($row) {
    //                 return $this->formatFileSize($row->telegram_file_size);
    //             })
    //             ->addColumn('dimensions', function ($row) {
    //                 return $row->telegram_width . 'x' . $row->telegram_height;
    //             })
    //             ->addColumn('supplier_info', function ($row) {
    //                 return $row->supplier ?? 'N/A';
    //             })
    //             ->addColumn('ref_info', function ($row) {
    //                 return $row->ref_no ?? 'N/A';
    //             })
    //             // Remove columns that are not needed in the final table output
    //             ->removeColumn('file_path') // Changed from image_path to file_path
    //             ->removeColumn('telegram_width')
    //             ->removeColumn('telegram_height')
    //             ->removeColumn('telegram_user_first_name')
    //             ->removeColumn('telegram_user_last_name')
    //             ->removeColumn('supplier')
    //             ->removeColumn('ref_no')
    //             ->setRowAttr([
    //                 'data-href' => function ($row) {
    //                     if (auth()->user()->can('expenseautofill.view')) {
    //                         return url("/expenseautofill/bot-image/{$row->id}"); // Use DB ID
    //                     } else {
    //                         return '';
    //                     }
    //                 }
    //             ])
    //             ->rawColumns(['action', 'image', 'status', 'total_amount']) // Changed from ocr_status to status and final_total to total_amount
    //             ->make(true);
    //     }

    //     // For non-ajax requests, prepare data for the view
    //     $statuses = [ // Changed from ocrStatuses to statuses
    //         '' => 'All Status',
    //         'stored' => 'Stored', // Changed from pending to stored
    //         'processing' => 'Processing',
    //         'processed' => 'Completed', // Changed from completed to processed
    //         'failed' => 'Failed',
    //     ];

    //     return view('expenseautofill::ExpenseAutoFill.bot-images')
    //         ->with(compact('statuses')); // Changed from ocrStatuses to statuses
    // }

    /**
     * Display the image from the bot using file ID
     */
    public function showImage($id) // <-- Changed the parameter name to $id for clarity
    {
        dd("here");
        // 1. Find the image record in the database using its primary key ID.
        // Using find() is the most efficient way to look up by primary key.
        $imageRecord = TelegramExpenseImageData::find($id);

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
        if (! auth()->user()->can('expenseautofill.create')) {
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
                $ocrResult = $this->ocrService->extractInvoiceData($base64Image, $this->botToken);

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
        if (! auth()->user()->can('expenseautofill.create')) {
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
            $ocrResult = $this->ocrService->extractInvoiceData($base64Image, $this->botToken);

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
        if (! auth()->user()->can('expenseautofill.delete')) {
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
        if (! auth()->user()->can('expenseautofill.view')) {
            abort(403, 'Unauthorized action.');
        }

        // Get the business_id of the logged-in user
        $business_id = request()->session()->get('user.business_id');

        // CRITICAL: Find the record, but ONLY if it belongs to the current user's business
        $ocrData = TelegramExpenseImageData::where('business_id', $business_id)->find($id);

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
        if (!auth()->user()->can('expenseautofill.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            // Find the image record, but ONLY if it belongs to the current user's business
            $ocrData = TelegramExpenseImageData::where('business_id', $business_id)->find($id);

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


    // public function prefillForm($id)
    // {
    //     if (! auth()->user()->can('purchase.create')) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     $business_id = request()->session()->get('user.business_id');

    //     $moduleUtil = app(ModuleUtil::class);

    //     //Check if subscribed or not
    //     if (! $moduleUtil->isSubscribed($business_id)) {
    //         return $moduleUtil->expiredResponse();
    //     }

    //     $taxes = TaxRate::where('business_id', $business_id)
    //         ->ExcludeForTaxGroup()
    //         ->get();
    //     $prodcutUtil = app(ProductUtil::class);
    //     $orderStatuses = $prodcutUtil->orderStatuses();
    //     $business_locations = BusinessLocation::forDropdown($business_id, false, true);
    //     $bl_attributes = $business_locations['attributes'];
    //     $business_locations = $business_locations['locations'];

    //     $transactionUtil = app(TransactionUtil::class);

    //     $currency_details = $transactionUtil->purchaseCurrencyDetails($business_id);

    //     $ocr = TelegramOcrData::find($id);

    //     // Set static default purchase status
    //     $default_purchase_status = 'received'; // Static value

    //     // Set static default transaction date (today's date in the format expected by the form)
    //     $default_transaction_date = $ocr->transaction_date;

    //     $supplier_business_name = $ocr->supplier_name;
    //     // $supplier_business_name = 'ក្រុមហ៊ុន​អាឌូម៉ាស ABA THIN '; 

    //     // Get the image path from OCR data
    //     $ocr_image_path = $ocr->image_path;

    //     // Set static default business location (you need to replace '1' with the actual ID of your desired location)
    //     $default_location_id = 17; // Static value - replace with your actual location ID

    //     $types = [];
    //     if (auth()->user()->can('supplier.create')) {
    //         $types['supplier'] = __('report.supplier');
    //     }
    //     if (auth()->user()->can('customer.create')) {
    //         $types['customer'] = __('report.customer');
    //     }
    //     if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
    //         $types['both'] = __('lang_v1.both_supplier_customer');
    //     }
    //     $customer_groups = CustomerGroup::forDropdown($business_id);

    //     $businessUtil = app(BusinessUtil::class);
    //     $business_details = $businessUtil->getDetails($business_id);
    //     $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

    //     $payment_line = $this->dummyPaymentLine;
    //     $payment_types = $prodcutUtil->payment_types(null, true, $business_id);

    //     //Accounts
    //     $accounts = $moduleUtil->accountsDropdown($business_id, true);

    //     $common_settings = ! empty(session('business.common_settings')) ? session('business.common_settings') : [];

    //     return view('expenseautofill::ExpenseAutoFill.prefill_form')
    //         ->with(compact(
    //             'taxes',
    //             'orderStatuses',
    //             'business_locations',
    //             'currency_details',
    //             'default_purchase_status',
    //             'customer_groups',
    //             'types',
    //             'shortcuts',
    //             'payment_line',
    //             'payment_types',
    //             'accounts',
    //             'bl_attributes',
    //             'common_settings',
    //             'default_transaction_date',
    //             'default_location_id',
    //             'supplier_business_name',
    //             'ocr_image_path'
    //         ));
    // }



    public function prefillForm($id)
    {
        if (! auth()->user()->can('expense.add')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $moduleUtil = app(ModuleUtil::class);

        //Check if subscribed or not
        if (! $moduleUtil->isSubscribed($business_id)) {
            return $moduleUtil->expiredResponse(action([\App\Http\Controllers\ExpenseController::class, 'index']));
        }

        $business_locations = BusinessLocation::forDropdown($business_id, false, true);

        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];

        $expense_categories = ExpenseCategory::where('business_id', $business_id)
            ->whereNull('parent_id')
            ->pluck('name', 'id');
        $users = User::forDropdown($business_id, true, true);

        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);

        $payment_line = $this->dummyPaymentLine;

        $transactionUtil = app(transactionUtil::class);

        $payment_types = $transactionUtil->payment_types(null, false, $business_id);

        $contacts = Contact::contactDropdown($business_id, false, false);

        $prefill = TelegramExpenseImageData::find($id);

        $location = $prefill->location;
        $expense_group = $prefill->category;
        $transaction_date = $prefill->transaction_date;
        $who_expense = $prefill->expense_for;
        $supplier = $prefill->supplier;
        $note = $prefill->notes;
        $employee_name = $prefill->employee_name;
        $total = $prefill->total_amount;
        $image_path = $prefill->file_path;


        //Accounts
        $accounts = [];
        if ($moduleUtil->isModuleEnabled('account')) {
            $accounts = Account::forDropdown($business_id, true, false, true);
        }

        $image_id = $id;

        $extraVars = compact('image_id', 'location', 'total', 'image_path', 'expense_group', 'transaction_date', 'who_expense', 'supplier', 'note', 'employee_name');

        if (request()->get('request')) {
            if (request()->ajax()) {
                return view('expense_request.add_expense_modal')
                    ->with($extraVars + compact('expense_categories', 'business_locations', 'users', 'taxes', 'payment_line', 'payment_types', 'accounts', 'bl_attributes', 'contacts'));
            }
            return view('expense_request.create')
                ->with($extraVars + compact('expense_categories', 'business_locations', 'users', 'taxes', 'payment_line', 'payment_types', 'accounts', 'bl_attributes', 'contacts'));
        } else {
            if (request()->ajax()) {
                return view('expense.add_expense_modal')
                    ->with($extraVars + compact('expense_categories', 'business_locations', 'users', 'taxes', 'payment_line', 'payment_types', 'accounts', 'bl_attributes', 'contacts'));
            }
            return view('expenseautofill::ExpenseAutoFill.prefill_form')
                ->with($extraVars + compact('expense_categories', 'business_locations', 'users', 'taxes', 'payment_line', 'payment_types', 'accounts', 'bl_attributes', 'contacts'));
        }
    }
}
