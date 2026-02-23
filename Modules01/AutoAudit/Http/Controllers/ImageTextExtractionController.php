<?php

namespace Modules\AutoAudit\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\AutoAudit\Services\GoogleVisionService;

class ImageTextExtractionController extends Controller
{
    protected $googleVisionService;

    public function __construct(GoogleVisionService $googleVisionService)
    {
        $this->googleVisionService = $googleVisionService;
    }

    public function index()
    {
        return view('AutoAudit::image_text_extraction.index');
    }

    public function extract(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240', // Max 10MB
            'image_content' => 'required|string',
            'rotation' => 'nullable|numeric'
        ]);

        try {
            $result = $this->googleVisionService->extractText($request->image_content);
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process image: ' . $e->getMessage()
            ], 500);
        }
    }
} 