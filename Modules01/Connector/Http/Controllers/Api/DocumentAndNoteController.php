<?php

namespace Modules\Connector\Http\Controllers\Api;

use logs;
use App\Media;
use Exception;
use App\Business;
use App\DocumentAndNote;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Arcanedev\LogViewer\Entities\Log;
use Modules\Connector\Transformers\CommonResource;
use Modules\Connector\Http\Controllers\Api\ApiController;

class DocumentAndNoteController extends ApiController
{
    /**
     * All Utils instance.
     */
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }


    // show all document and note by costumer_id
    public function index($contact_id){
        $user = Auth::user();
        $business_id = $user->business_id; 
        
        //model id like project_id, user_id
        //contact_id=notable_id
        
       $notable_id = $contact_id;
            //model name like App\User
            $notable_type = 'App\Contact';

        // $test=123;
        $document_notes = DocumentAndNote::where('business_id', $business_id)
            ->where('notable_id', $notable_id)
            ->where('notable_type', $notable_type)
            ->with('media', 'createdBy')
            ->get();
       return response()->json(['document_notes' =>$document_notes]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * 
     * this function store document and note data api 
     *  that combine store and postmedia function by gpt
     */
   public function store(Request $request, $contact_id)
{
    $user = Auth::user();
    $business_id = $user->business_id; 
    // Retrieve notable_id and notable_type from the request
    $notable_id = $contact_id;
    $notable_type = 'App\Contact';

    // Extract specific fields from the request
    $input = $request->only('heading', 'description', 'is_private');
    $input['business_id'] = $business_id;
    $input['created_by'] = $request->user()->id;

    // Begin a database transaction
    DB::beginTransaction();

    try {
        // Disable logging if the note is private
        if (!empty($input['is_private'])) {
            activity()->disableLogging();
        }

        // Find the notable model and create a new note associated with it
        $model = $notable_type::where('business_id', $business_id)
            ->findOrFail($notable_id);

        $model_note = $model->documentsAndnote()->create($input);

        // Handle file uploads if any
        $file_names = [];
        if ($request->hasFile('file')) {
            $files = $request->file('file');
            foreach ($files as $file) {
                $file_name = Media::uploadFile($file);
                $file_names[] = $file_name;
            }

            // Attach the uploaded files to the model note
            Media::attachMediaToModel($model_note, $business_id, $file_names);
        }

        // Commit the transaction
        DB::commit();

        // Prepare a success response
        $output = [
            'success' => true,
            'msg' => __('lang_v1.success'),
            'data' => $model_note,
            'file_names' => $file_names,
        ];
    } catch (Exception $e) {
        
        \Log::emergency('File:'.$e->getFile().' Line:'.$e->getLine().' Message:'.$e->getMessage());

        $output = [
            'success' => false,
            'msg' => __('messages.something_went_wrong'),
        ];
    }

    return response()->json($output);
}





   /**
 * Display the specified resource.
 *
 * @param  int  $id
 * @return \Illuminate\Http\JsonResponse
 */
public function show($id)
{
    // Get business_id from the authenticated user's session
    $user = Auth::user();
    $business_id = $user->business_id;

    try {
        // Find the document note based on the provided parameters
        $document_note = DocumentAndNote::where('business_id', $business_id)
            ->with('media', 'createdBy')
            ->findOrFail($id);

        // Extract notable_id and notable_type from the found document note
        $notable_id = $document_note->notable_id;
        $notable_type = $document_note->notable_type;

        return response()->json([
            'success' => true,
            'data' => $document_note,
        ], 200);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Document note not found',
        ], 404);
    }
}

    /**
 * Update the specified resource in storage via API.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  int  $note_id
 * @return \Illuminate\Http\JsonResponse
 */
public function update(Request $request, $id)
{
    $user = Auth::user();
    $business_id = $user->business_id;

    // Extract specific fields from the request
    $input = $request->only('heading', 'description', 'is_private');
    $input['business_id'] = $business_id;
    $input['updated_by'] = $request->user()->id;

    // Begin a database transaction
    DB::beginTransaction();

    try {
        // Disable logging if the note is private
        if (!empty($input['is_private'])) {
            activity()->disableLogging();
        }

        // Find the note to be updated
        $model_note = DocumentAndNote::where('business_id', $business_id)
            ->findOrFail($id);

        // Log the current data
        \Log::info('Current Note Data:', $model_note->toArray());

        // Update the note with the new data
        $model_note->update($input);

        // Log the updated data
        \Log::info('Updated Note Data:', $model_note->toArray());

        // Handle file uploads if any
        $file_names = [];
        if ($request->hasFile('file')) {
            $files = $request->file('file');
            foreach ($files as $file) {
                $file_name = Media::uploadFile($file);
                $file_names[] = $file_name;
            }

            // Attach the uploaded files to the model note
            Media::attachMediaToModel($model_note, $business_id, $file_names);
        }

        // Commit the transaction
        DB::commit();

        // Prepare a success response
        $output = [
            'success' => true,
            'msg' => __('lang_v1.success'),
            'data' => $model_note,
            'file_names' => $file_names,
        ];
    } catch (Exception $e) {
        
        \Log::emergency('File: '.$e->getFile().' Line: '.$e->getLine().' Message: '.$e->getMessage());

        $output = [
            'success' => false,
            'msg' => __('messages.something_went_wrong'),
        ];
    }

    return response()->json($output);
}


  /**
 * Remove the specified resource from storage.
 *
 * @param  int  $id
 * @return \Illuminate\Http\JsonResponse
 */
public function delete(Request $request, $id)
{
    $user = Auth::user();
    $business_id = $user->business_id;

    // Retrieve notable_id and notable_type from the request
    $notable_id = $request->input('notable_id'); // Use input() for retrieving request data
    $notable_type = 'App\Contact'; // Ensure this is set in the request

    // Begin a database transaction
    DB::beginTransaction();

    try {
        // Find the document note to be deleted
        $document_note = DocumentAndNote::where('business_id', $business_id)
            ->where('notable_id', $notable_id)
            ->where('notable_type', $notable_type) // Use variable here
            ->findOrFail($id);

        // Delete the document note
        $document_note->delete();

        // Delete associated media, if any
        $document_note->media()->delete();

        // Commit the transaction
        DB::commit();

        // Prepare a success response
        $output = [
            'success' => true,
            'msg' => __('lang_v1.success'),
        ];
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        
        
        // Log the exception details
        \Log::emergency('File: '.$e->getFile().' Line: '.$e->getLine().' Message: '.$e->getMessage());

        // Prepare an error response
        $output = [
            'success' => false,
            'msg' => __('messages.something_went_wrong'),
        ];
    }

    return response()->json($output);
}



    /**
     * upload documents in app
     *
     * @return \Illuminate\Http\Response
     */
  

    /**
     * get docus & note index page
     * through ajax
     *
     * @return \Illuminate\Http\Response
     */
    public function getDocAndNoteIndexPage(Request $request)
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $notable_type = $request->get('notable_type');
            $notable_id = $request->get('notable_id');
            $permissions = $this->__getPermission($business_id, $notable_id, $notable_type);

            return view('documents_and_notes.index')
                ->with(compact('permissions', 'notable_type', 'notable_id'));
        }
    }

  }