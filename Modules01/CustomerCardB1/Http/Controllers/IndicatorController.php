<?php

namespace Modules\CustomerCardB1\Http\Controllers;

use App\User;
use App\Contact;
use App\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\CustomerCardB1\Entities\VisaAppraisal;
use Modules\CustomerCardB1\Entities\VisaIndicator;
use Modules\CustomerCardB1\Entities\VisaCompetency;
use Modules\CustomerCardB1\Entities\VisaAppraisalScore;

class IndicatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        // Fetch indicators with the associated department
        $indicators = VisaIndicator::with('department')->where('business_id', $business_id);
        if(request()->ajax()){
            return DataTables::of($indicators)
        ->addColumn('department', function ($indicator) {
            return $indicator->department ? $indicator->department->name : 'N/A';
        })
        ->addColumn('action', function ($indicator) {
            $html = '<a href="' . route('customercardb1.visa.indicator.view', $indicator->id) . '" class="btn btn-xs btn-info"><i class="fas fa-eye"></i> ' . __('customercardb1::visa.view') . '</a>' .
                    '&nbsp;' .
                    '<a href="' . route('customercardb1.visa.indicator.edit', $indicator->id) . '" class="btn btn-xs btn-primary"><i class="fas fa-pencil-alt"></i> ' . __('customercardb1::visa.edit') . '</a>' .
                    '&nbsp;' .
                    '<a href="#" data-href="' . route('customercardb1.visa.indicator.delete', $indicator->id) . '" class="btn btn-xs btn-danger delete_indicator"><i class="fas fa-trash"></i> ' . __('customercardb1::visa.delete') . '</a>';

            return $html;
        })
        ->editColumn('created_at', '{{@format_datetime($created_at)}}')
        ->rawColumns(['action'])
        ->make(true);
        }
    
        // Pass the indicators to the view
        return view('customercardb1::Indicator.index', compact('indicators'));
    }

    /**
     * Show the form for creating a new indicator.
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');

        $department = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_department')
            ->pluck('name', 'id');

        $designation = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_designation')
            ->pluck('name', 'id');

        return view('customercardb1::Indicator.create', compact('department', 'designation'));
    }

    /**
     * Store a newly created indicator in storage.
     */
    public function store(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        // Validate the request input
        $request->validate([
            'title' => 'required|string|max:255',
            'technical_indicators.*.name' => 'required|string',
            'technical_indicators.*.value' => 'nullable|string', // Allow value to be nullable
            'technical_indicators.*.score' => 'nullable|string', // Allow score to be nullable
            'behavioral_indicators.*.name' => 'required|string',
            'behavioral_indicators.*.value' => 'nullable|string', // Allow value to be nullable
            'behavioral_indicators.*.score' => 'nullable|string', // Allow score to be nullable
        ]);

        // Create the indicator
        $indicator = VisaIndicator::create([
            'title' => $request->input('title'),
            'business_id' => $business_id,
        ]);

        // Store technical competencies
        if ($request->has('technical_indicators')) {
            foreach ($request->input('technical_indicators') as $technical) {
                VisaCompetency::create([
                    'indicator_id' => $indicator->id,
                    'type' => 'technical',
                    'name' => $technical['name'],
                    'value' => $technical['value'] ?? null, // Allow null values
                    'score' => $technical['score'] ?? null, // Allow null scores
                    'business_id' => $business_id,
                ]);
            }
        }

        // Store behavioral competencies
        if ($request->has('behavioral_indicators')) {
            foreach ($request->input('behavioral_indicators') as $behavioral) {
                VisaCompetency::create([
                    'indicator_id' => $indicator->id,
                    'type' => 'behavioral',
                    'name' => $behavioral['name'],
                    'value' => $behavioral['value'] ?? null, // Allow null values
                    'score' => $behavioral['score'] ?? null, // Allow null scores
                    'business_id' => $business_id,
                ]);
            }
        }

        return redirect()->route('customercardb1.visa.indicator.index')->with('success', 'Indicator created successfully');
    }

    public function view($id)
    {
        $business_id = request()->session()->get('user.business_id');

        // Fetch the indicator along with its related competencies
        $indicator = VisaIndicator::with('competencies')->find($id);
        if (!$indicator) {
            return redirect()->route('customercardb1.visa.indicator.index')->with('error', 'Indicator not found');
        }

        // Fetch department and designation
        $department = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_department')
            ->pluck('name', 'id');

        $designation = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_designation')
            ->pluck('name', 'id');

        return view('customercardb1::Indicator.view_indicator', compact('indicator', 'department', 'designation'));
    }

    /**
     * Show the form for editing the specified indicator.
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');

        // Fetch the indicator along with its related competencies
        $indicator = VisaIndicator::with('competencies')->find($id);
        if (!$indicator) {
            return redirect()->route('customercardb1.visa.indicator.index')->with('error', 'Indicator not found');
        }

        // Fetch department and designation
        $department = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_department')
            ->pluck('name', 'id');

        $designation = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_designation')
            ->pluck('name', 'id');

        return view('customercardb1::Indicator.edit', compact('indicator', 'department', 'designation'));
    }

    /**
     * Update the specified indicator in storage.
     */
    public function update(Request $request, $id)
    {
        $business_id = $request->session()->get('user.business_id');
    
        // Validate the request input
        $request->validate([
            'title' => 'required|string|max:255',
            'technical_indicators.*.name' => 'required|string',
            'technical_indicators.*.value' => 'nullable|string',
            'technical_indicators.*.score' => 'nullable|string',
            'behavioral_indicators.*.name' => 'required|string',
            'behavioral_indicators.*.value' => 'nullable|string',
            'behavioral_indicators.*.score' => 'nullable|string',
        ]);
    
        // Find the existing indicator
        $indicator = VisaIndicator::find($id);
        if (!$indicator) {
            return redirect()->route('customercardb1.visa.indicator.index')->with('error', 'Indicator not found');
        }
    
        // Update the indicator's basic details
        $indicator->update([
            'title' => $request->input('title'),
            'business_id' => $business_id,
        ]);
    
        // Function to handle competencies
        $updateCompetencies = function ($data, $type) use ($indicator, $business_id) {
            $existingCompetencies = $indicator->competencies()->where('type', $type)->get()->keyBy('id');
    
            foreach ($data as $id => $details) {
                if (is_numeric($id) && $existingCompetencies->has($id)) {
                    // Update existing competency
                    $existingCompetencies[$id]->update([
                        'name' => $details['name'], 
                        'value' => $details['value'] ?? null,
                        'score' => $details['score'] ?? null,
                    ]);
                } else {
                    // Add new competency
                    VisaCompetency::create([
                        'indicator_id' => $indicator->id,
                        'type' => $type,
                        'name' => $details['name'],
                        'value' => $details['value'] ?? null,
                        'score' => $details['score'] ?? null,
                        'business_id' => $business_id,
                    ]);
                }
            }
            
            // Delete any competencies not included in the request
            $submittedIds = array_keys($data);
            $existingCompetencies->reject(function ($competency) use ($submittedIds) {
                return in_array($competency->id, $submittedIds);
            })->each->delete();
        };
    
        // Update technical competencies
        if ($request->has('technical_indicators')) {
            $updateCompetencies($request->technical_indicators, 'technical');
        }
        
        // Update behavioral competencies
        if ($request->has('behavioral_indicators')) {
            $updateCompetencies($request->behavioral_indicators, 'behavioral');
        }
    
        return redirect()->route('customercardb1.visa.indicator.index')->with('success', 'Indicator updated successfully');
    }
    


    /**
     * Show the form for giving an appraisal for a specific indicator.
     */
    public function give_appraisal($id, Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        // Load the indicator and its competencies
        $indicator = VisaIndicator::with('competencies')->find($id);
        if (!$indicator) {
            return redirect()->route('customercardb1.visa.indicator.index')->with('error', 'Indicator not found');
        }

        $department = Category::where('business_id', $business_id)->where('category_type', 'hrm_department')->pluck('name', 'id');
        $designation = Category::where('business_id', $business_id)->where('category_type', 'hrm_designation')->pluck('name', 'id');
        $employee = User::forDropdown($business_id, false);

        // Retrieve the employee and appraisal month from the request
        $employeeId = $request->get('employee');
        $contact = Contact::contactDropdown($business_id, false, false);
        $appraisalMonth = $request->get('appraisal_month');

        $appraisalss = null;
        $scores = [];

        // Case when both employee and appraisal month are provided
        if ($employeeId && $appraisalMonth) {
            $appraisalss = VisaAppraisal::where('employee_id', $employeeId)
                ->where('business_id', $business_id)
                ->where('appraisal_month', $appraisalMonth)
                ->where('indicator_id', $id)
                ->first();

            if ($appraisalss) {
                $appraisalScores = VisaAppraisalScore::where('appraisal_id', $appraisalss->id)->get();

                foreach ($appraisalScores as $score) {
                    $scores[$score->competency_id] = $score->score;
                }
            }
        }
        // Case when only the appraisal month is provided (no employee)
        elseif ($appraisalMonth) {
            $appraisalss = VisaAppraisal::where('appraisal_month', $appraisalMonth)
                ->where('business_id', $business_id)
                ->where('indicator_id', $id)
                ->get();

            foreach ($appraisalss as $appraisal) {
                $appraisalScores = VisaAppraisalScore::where('appraisal_id', $appraisal->id)->get();
                foreach ($appraisalScores as $score) {
                    $scores[$score->competency_id] = $scores[$score->competency_id] ?? $score->score;
                }
            }
        }

        return view('customercardb1::Indicator.give_appraisal', compact('contact','indicator', 'department', 'designation', 'employee', 'appraisalss', 'scores'));
    }

    /**
     * Store the appraisal and the competency scores for a given indicator.
     */
    public function store_appraisal(Request $request, $id)
    {
        $business_id = $request->session()->get('user.business_id');

        // Load the indicator with its competencies
        $indicator = VisaIndicator::with('competencies')->find($id);
        if (!$indicator) {
            return redirect()->route('customercardb1.visa.indicator.index')->with('error', 'Indicator not found');
        }

        // Validate the request input
        $request->validate([
            'employee' => 'nullable|exists:users,id',
            'technical.*.actual_value' => 'required|numeric',
            'technical.*.note' => 'nullable|string',
        ]);

        // Get the employeeId and appraisalMonth from the request
        $employeeId = $request->input('employee');
        $appraisalMonth = $request->input('appraisal_month');

        // Build the conditions array
        $conditions = [
            'indicator_id' => $indicator->id,
            'appraisal_month' => $appraisalMonth,
        ];

        // Conditionally add the employee_id if it's provided
        if (!empty($employeeId)) {
            $conditions['employee_id'] = $employeeId;
        }

        // Create or update the appraisal
        $appraisal = VisaAppraisal::updateOrCreate(
            $conditions,
            ['business_id' => $business_id]
        );

        // Store technical competency scores
        foreach ($request->input('technical') as $competencyId => $technicalInput) {
            $competency = $indicator->competencies->where('id', $competencyId)->where('type', 'technical')->first();
            if ($competency) {
                VisaAppraisalScore::updateOrCreate(
                    [
                        'appraisal_id' => $appraisal->id,
                        'competency_id' => $competency->id,
                        'business_id' => $business_id,
                    ],
                    [
                        'actual_value' => $technicalInput['actual_value'],
                        'note' => $technicalInput['note'] ?? null,
                    ]
                );
            }
        }

        // Redirect with success message
        return redirect()->route('customercardb1.visa.indicator.index')->with('success', 'Appraisal saved successfully');
    }

    public function appraisal(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        // Fetch indicators with the associated department
        $indicators = VisaIndicator::where('business_id', $business_id)
            ->pluck('title', 'id');

        // Get the selected indicator from the request (if any)
        $selectedIndicator = $request->get('indicator');

        // Load the indicator and its competencies based on selected indicator
        $indicator = null;
        if ($selectedIndicator) {
            $indicator = VisaIndicator::with('competencies')->find($selectedIndicator);
        }

        $department = Category::where('business_id', $business_id)->where('category_type', 'hrm_department')->pluck('name', 'id');
        $designation = Category::where('business_id', $business_id)->where('category_type', 'hrm_designation')->pluck('name', 'id');
        $employee = User::forDropdown($business_id, false);
        $contact = Contact::contactDropdown($business_id, false, false);

        // Retrieve the employee and appraisal month from the request
        $employeeId = $request->get('employee'); // Can be null
        $contactId = $request->get('contact'); // Can be null
        $appraisalMonth = $request->get('appraisal_month');

        $appraisalss = null;
        $scores = [];

        $query = VisaAppraisal::where('appraisal_month', $appraisalMonth)
            ->where('business_id', $business_id)
            ->where('indicator_id', $selectedIndicator);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }
        if ($contactId) {
            $query->where('contact_id', $contactId);
        }

        // Fetch the appraisal(s)
        $appraisalss = $query->get();

        // If appraisals exist, load the scores
        if ($appraisalss->isNotEmpty()) {
            foreach ($appraisalss as $appraisal) {
                $appraisalScores = VisaAppraisalScore::where('appraisal_id', $appraisal->id)->get();
                foreach ($appraisalScores as $score) {
                    $scores[$score->competency_id] = $score;
                }
            }
        }

        return view('customercardb1::Indicator.appraisal', compact('contact','indicators', 'indicator', 'department', 'designation', 'employee', 'appraisalss', 'scores', 'selectedIndicator'));
    }

    public function appraisal_store(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $userId = $request->session()->get('user.id');
    
        $indicator = VisaIndicator::with('competencies')->find($request->input('indicator_id'));
        if (!$indicator) {
            return redirect()->route('customercardb1.visa.indicator.index')->with('error', 'Indicator not found');
        }
    
        // Validate that all actual_score fields are provided
        $request->validate([
            'technical.*.actual_value' => 'required|numeric',
            'technical.*.actual_score' => 'required|numeric',
        ]);

        $employeeId = $request->input('employee');
        $contactId = $request->input('contact');
        $appraisalMonth = $request->input('appraisal_month');
        $appraisalId = $request->input('appraisal_id');
    
        // Check if appraisal exists with the provided appraisal_id
        $existingAppraisal = $appraisalId ? VisaAppraisal::find($appraisalId) : null;
    
        DB::beginTransaction();
        try {
            // Update if appraisal exists and matches appraisal_id, otherwise create new
            if ($existingAppraisal) {
                // Update existing appraisal
                $existingAppraisal->update([
                    'indicator_id' => $indicator->id,
                    'appraisal_month' => $appraisalMonth,
                    'contact_id' => $contactId,
                    'business_id' => $business_id,
                    'created_by' => $userId,
                ]);
                $appraisal = $existingAppraisal;
            } else {
                // Create new appraisal
                $appraisal = VisaAppraisal::create([
                    'indicator_id' => $indicator->id,
                    'appraisal_month' => $appraisalMonth,
                    'contact_id' => $contactId,
                    'business_id' => $business_id,
                    'created_by' => $userId,

                ]);
            }
             // Store technical competencies
        foreach ($request->input('technical', []) as $competencyId => $technicalInput) {
            $existingScore = VisaAppraisalScore::where('appraisal_id', $appraisal->id)
            ->where('competency_id', $competencyId)
            ->first();
    
            if ($existingScore) {
                // Update existing score
                $existingScore->update([
                    'business_id' => $business_id,
                    'expect_value' => $technicalInput['expect_value'] ?? null,
                    'expect_score' => $technicalInput['expect_score'] ?? null,
                    'actual_value' => $technicalInput['actual_value'] ?? null,
                    'actual_score' => $technicalInput['actual_score'] ?? null,
                    'note' => $technicalInput['note'] ?? null,
                ]);
            } else {
                // Create new score
                VisaAppraisalScore::create([
                    'appraisal_id' => $appraisal->id,
                    'competency_id' => $competencyId,
                    'business_id' => $business_id,
                    'expect_value' => $technicalInput['expect_value'] ?? null,
                    'expect_score' => $technicalInput['expect_score'] ?? null,
                    'actual_value' => $technicalInput['actual_value'] ?? null,
                    'actual_score' => $technicalInput['actual_score'] ?? null,
                    'note' => $technicalInput['note'] ?? null,
                ]);
            }
        }


    
            DB::commit();
            return redirect()->route('customercardb1.visa.appraisal.list')->with('success', 'Appraisal saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('customercardb1.visa.appraisal.list')->with('error', 'Error saving appraisal score.');
        }
    }

    // public function appraisal_store(Request $request)
    // {
    //     $business_id = $request->session()->get('user.business_id');
    //     $userId = $request->session()->get('user.id');
    

    //     $indicator = VisaIndicator::with('competencies')->find($request->input('indicator_id'));
    //     if (!$indicator) {
    //         return redirect()->route('visa.indicator.index')->with('error', 'Indicator not found');
    //     }

    //     $employeeId = $request->input('employee');
    //     $contactId = $request->input('contact');
    //     $appraisalMonth = $request->input('appraisal_month');


    //     // Create or update the appraisal record
    //     $appraisal = VisaAppraisal::updateOrCreate(
    //         [
    //             'id' => $request->input('appraisal_id'),
    //             'indicator_id' => $indicator->id,
    //             'appraisal_month' => $appraisalMonth,
    //             'contact_id' => $contactId,
    //         ],
    //         [
    //             'business_id' => $business_id,
    //             'created_by' => $userId,
    //         ]
    //     );
        

    //     DB::beginTransaction();
    //     try {
    //         // Store technical competencies
    //         foreach ($request->input('technical', []) as $competencyId => $technicalInput) {
    //             VisaAppraisalScore::updateOrCreate(
    //                 [
    //                     'appraisal_id' => $appraisal->id,
    //                     'competency_id' => $competencyId,
    //                 ],
    //                 [
    //                     'business_id' => $business_id,
    //                     'expect_value' => $technicalInput['expect_value'] ?? null,
    //                     'expect_score' => $technicalInput['expect_score'] ?? null,
    //                     'actual_value' => $technicalInput['actual_value'] ?? null,
    //                     'actual_score' => $technicalInput['actual_score'] ?? null,
    //                     'note' => $technicalInput['note'] ?? null,
    //                 ]
    //             );
    //         }

    //         DB::commit();
    //         return redirect()->route('visa.appraisal.list')->with('success', 'Appraisal saved successfully.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return redirect()->route('visa.appraisal.list')->with('error', 'Error saving appraisal score.');
    //     }
    // }





    public function appraisal_list(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $query = VisaAppraisal::with(['contact', 'indicator.department', 'scores'])->where('business_id', $business_id);

            // Apply year and month filters if provided
            if ($request->has('month') && $request->has('year')) {
                // Create a date string like '2024-10' based on the selected year and month
                $appraisalMonth = $request->year . '-' . str_pad($request->month, 2, '0', STR_PAD_LEFT);
                $query->where('appraisal_month', $appraisalMonth);
            }

            $appraisals = $query->get();

            return DataTables::of($appraisals)
                ->addIndexColumn()
                ->addColumn('contact', function ($row) {
                    return isset($row->contact)
                        ? $row->contact->name
                        : 'N/A';
                })

                ->addColumn('department', function ($row) {
                    return $row->indicator->department->name ?? 'N/A';
                })
                ->addColumn('appraisal_month', function ($row) {
                    return $row->appraisal_month ? \Carbon\Carbon::parse($row->appraisal_month)->format('d F Y') : '-';
                })
                ->addColumn('actual_score', function ($row) {
                    return $row->scores->sum(function ($score) {

                        return $score->actual_score ? floatval($score->actual_score) : 0;
                    });
                })
                ->addColumn('actual_value', function ($row) {
                    return $row->scores->sum(function ($score) {
                        return $score->actual_value ? floatval($score->actual_value) : 0;
                    });
                })

                ->addColumn('created_by', function ($row) {
                    return isset($row->createdBy)
                        ? $row->createdBy->first_name . ' ' . $row->createdBy->last_name
                        : 'N/A';
                })

                ->addColumn('action', function ($row) {
                    $viewUrl = route('customercardb1.visa.appraisal.view', ['id' => $row->id]);
                    $editUrl = route('customercardb1.visa.appraisal.store') . '?indicator=' . $row->indicator_id . '&appraisal_month=' . $row->appraisal_month .'&appraisal_id=' . $row->id;
                    if ($row->contact) {
                        $editUrl .= '&contact=' . $row->contact->id;
                    }
                    $deleteUrl = route('customercardb1.visa.appraisal.delete', ['appraisal_id' => $row->id]);
        
                    $html = '<div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">' .
                                __('messages.actions') .
                                '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                    <li><a href="javascript:void(0);" data-href="' . $viewUrl . '" class="btn-modal" data-container=".visa_modal">
                                        <i class="fas fa-eye"></i> ' . __('messages.view') . '</a></li>
                                    <li><a href="' . $editUrl . '">
                                         <i class="fas fa-edit"></i> ' . __('messages.edit') . '</a></li>
                                    <li><a href="javascript:void(0);" data-href="' . $deleteUrl . '" class="delete-visa">
                                        <i class="fas fa-trash"></i> ' . __('messages.delete') . '</a></li>
                                </ul>
                            </div>';
        
                    return $html;
                })
                ->make(true);
        }

        return view('customercardb1::Indicator.list_appraisal');
    }


    public function appraisal_view(Request $request, $id)
{
    $business_id = $request->session()->get('user.business_id');
    
    $appraisalData = VisaAppraisal::from('visa_appraisals as a')
            ->where('a.id', $id)
            ->join('visa_appraisal_scores as ascore', 'ascore.appraisal_id', '=', 'a.id')
            ->join('visa_competencies as c', 'ascore.competency_id', '=', 'c.id')
            ->join('visa_indicators as i', 'c.indicator_id', '=', 'i.id')
            ->leftJoin('categories as dept', 'i.department_id', '=', 'dept.id')
            ->leftJoin('categories as desig', 'i.designation_id', '=', 'desig.id')
            ->leftJoin('contacts as con', function ($join) {
                $join->on('a.contact_id', '=', 'con.id');
            })
            ->where('a.business_id', $business_id)
            ->select([
                'a.id as appraisal_id',
                'a.contact_id',
                DB::raw("COALESCE(con.name, '') as contact_name"),
                'a.appraisal_month',
                'c.type as competency_type',
                'c.name as competency_name',
                'ascore.expect_value',
                'ascore.expect_score',
                'ascore.actual_value',
                'ascore.actual_score',
                'ascore.note',
                'i.title as indicator_title',
            ])
            ->get();

        if ($appraisalData->isEmpty()) {
            return response()->json([
                'error' => 'No appraisal scores found for the specified appraisal ID'
            ], 404);
        }

        return view('customercardb1::customer.partials.card.view_appraisal', compact('appraisalData'));
}

    public function appraisal_delete($id)
    {
        try {
            DB::beginTransaction();
            VisaAppraisalScore::where('appraisal_id', $id)->delete();
            VisaAppraisal::where('id', $id)->delete();
            DB::commit();
            return response()->json(['success' => 'Appraisal deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to delete appraisal'], 500);
        }
    }

    public function appraisal_report(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        if (!$business_id) {
            return response()->json(['error' => 'Business ID not found'], 404);
        }

        if ($request->ajax()) {
            $query = VisaAppraisal::with(['employee', 'indicator.department', 'scores'])
                ->where('business_id', $business_id);

            // Apply month and year filter if provided
            if ($request->has('month') && $request->has('year')) {
                $appraisalMonth = $request->year . '-' . str_pad($request->month, 2, '0', STR_PAD_LEFT);
                $query->where('appraisal_month', $appraisalMonth);
            }

            try {
                $appraisals = $query->get();
            } catch (\Exception $e) {
                return response()->json(['error' => 'An error occurred while fetching data'], 500);
            }

            return DataTables::of($appraisals)
                ->addIndexColumn()
              ->addColumn('employee', function ($row) {
                    // Check if the employee relationship exists and concatenate first and last name
                    return isset($row->employee)
                        ? $row->employee->last_name . ' ' .  $row->employee->first_name
                        : 'N/A';
                })
                ->addColumn('department', function ($row) {
                    return $row->indicator->department->name ?? 'N/A';
                })
                ->addColumn('appraisal_month', function ($row) {
                    return \Carbon\Carbon::parse($row->appraisal_month)->format('F Y');
                })
                ->addColumn('expect_score', function ($row) {
                    return $row->scores->sum(function ($score) {
                        return $score->expect_score ? floatval($score->expect_score) : 0;
                    });
                })
                ->addColumn('actual_score', function ($row) {
                    return $row->scores->sum(function ($score) {
                        return $score->actual_score ? floatval($score->actual_score) : 0;
                    });
                })
                ->addColumn('action', function ($row) {
                    $viewUrl = route('customercardb1.visa.appraisal.view', ['id' => $row->id]);

                    $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">' .
                        __('messages.actions') .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">
                        <li><a href="#" data-href="' . $viewUrl . '" class="btn-modal" data-container=".kpi_report_modal">
                            <i class="fas fa-eye"></i> ' . __('messages.view') . '</a></li>
                    </ul>
                </div>';

                    return $html;
                })
                ->make(true);
        }

        return view('customercardb1::Indicator.report');
    }
    public function delete($id)
    {
        $business_id = request()->session()->get('user.business_id');

        // Fetch the indicator
        $indicator = VisaIndicator::where('business_id', $business_id)->findOrFail($id);

        // Attempt to delete the indicator and its competencies
        try {
            DB::beginTransaction();

            // Delete related competencies first
            $indicator->competencies()->delete();

            // Delete the indicator
            $indicator->delete();

            DB::commit();
            return redirect()->route('customercardb1.visa.indicator.index')->with('success', 'Indicator successfully deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('customercardb1.visa.indicator.index')->with('error', 'Error deleting indicator: ' . $e->getMessage());
        }
    }


}
