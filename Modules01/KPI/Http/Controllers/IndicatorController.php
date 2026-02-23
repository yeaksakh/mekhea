<?php

namespace Modules\KPI\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use App\User;
use DB;
use Modules\KPI\Entities\KpiAppraisal;
use Modules\KPI\Entities\KpiAppraisalScore;
use Modules\KPI\Entities\KpiCompetency;
use Modules\KPI\Entities\KpiIndicator;
use Yajra\DataTables\DataTables;

class IndicatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        // Fetch indicators with the associated department
        $indicators = KpiIndicator::with('department')->where('business_id', $business_id)->get();

        // Pass the indicators to the view
        return view('kpi::Indicator.index', compact('indicators'));
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

        return view('kpi::Indicator.create', compact('department', 'designation'));
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
            'department' => 'required|exists:categories,id',
            'designation' => 'nullable|exists:categories,id',
            'technical_indicators.*.name' => 'required|string',
            'technical_indicators.*.value' => 'nullable|string', // Allow value to be nullable
            'technical_indicators.*.score' => 'nullable|string', // Allow score to be nullable
            'behavioral_indicators.*.name' => 'required|string',
            'behavioral_indicators.*.value' => 'nullable|string', // Allow value to be nullable
            'behavioral_indicators.*.score' => 'nullable|string', // Allow score to be nullable
        ]);

        // Create the indicator
        $indicator = KpiIndicator::create([
            'title' => $request->input('title'),
            'department_id' => $request->input('department'),
            'designation_id' => $request->input('designation'),
            'business_id' => $business_id,
        ]);

        // Store technical competencies
        if ($request->has('technical_indicators')) {
            foreach ($request->input('technical_indicators') as $technical) {
                KpiCompetency::create([
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
                KpiCompetency::create([
                    'indicator_id' => $indicator->id,
                    'type' => 'behavioral',
                    'name' => $behavioral['name'],
                    'value' => $behavioral['value'] ?? null, // Allow null values
                    'score' => $behavioral['score'] ?? null, // Allow null scores
                    'business_id' => $business_id,
                ]);
            }
        }

        return redirect()->route('indicator.index')->with('success', 'Indicator created successfully');
    }

    public function view($id)
    {
        $business_id = request()->session()->get('user.business_id');

        // Fetch the indicator along with its related competencies
        $indicator = KpiIndicator::with('competencies')->find($id);
        if (!$indicator) {
            return redirect()->route('indicator.index')->with('error', 'Indicator not found');
        }

        // Fetch department and designation
        $department = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_department')
            ->pluck('name', 'id');

        $designation = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_designation')
            ->pluck('name', 'id');

        return view('kpi::Indicator.view_indicator', compact('indicator', 'department', 'designation'));
    }

    /**
     * Show the form for editing the specified indicator.
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');

        // Fetch the indicator along with its related competencies
        $indicator = KpiIndicator::with('competencies')->find($id);
        if (!$indicator) {
            return redirect()->route('indicator.index')->with('error', 'Indicator not found');
        }

        // Fetch department and designation
        $department = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_department')
            ->pluck('name', 'id');

        $designation = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_designation')
            ->pluck('name', 'id');

        return view('kpi::Indicator.edit', compact('indicator', 'department', 'designation'));
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
            'department' => 'required|exists:categories,id',
            'designation' => 'nullable|exists:categories,id',
            'technical_indicators.*.name' => 'required|string',
            'technical_indicators.*.value' => 'nullable|string',
            'technical_indicators.*.score' => 'nullable|string',
            'behavioral_indicators.*.name' => 'required|string',
            'behavioral_indicators.*.value' => 'nullable|string',
            'behavioral_indicators.*.score' => 'nullable|string',
        ]);
    
        // Find the existing indicator
        $indicator = KpiIndicator::find($id);
        if (!$indicator) {
            return redirect()->route('indicator.index')->with('error', 'Indicator not found');
        }
    
        // Update the indicator's basic details
        $indicator->update([
            'title' => $request->input('title'),
            'department_id' => $request->input('department'),
            'designation_id' => $request->input('designation'),
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
                    KpiCompetency::create([
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
    
        return redirect()->route('indicator.index')->with('success', 'Indicator updated successfully');
    }
    


    /**
     * Show the form for giving an appraisal for a specific indicator.
     */
    public function give_appraisal($id, Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        // Load the indicator and its competencies
        $indicator = KpiIndicator::with('competencies')->find($id);
        if (!$indicator) {
            return redirect()->route('indicator.index')->with('error', 'Indicator not found');
        }

        $department = Category::where('business_id', $business_id)->where('category_type', 'hrm_department')->pluck('name', 'id');
        $designation = Category::where('business_id', $business_id)->where('category_type', 'hrm_designation')->pluck('name', 'id');
        $employee = User::forDropdown($business_id, false);

        // Retrieve the employee and appraisal month from the request
        $employeeId = $request->get('employee');
        $appraisalMonth = $request->get('appraisal_month');

        $appraisalss = null;
        $scores = [];

        // Case when both employee and appraisal month are provided
        if ($employeeId && $appraisalMonth) {
            $appraisalss = KpiAppraisal::where('employee_id', $employeeId)
                ->where('business_id', $business_id)
                ->where('appraisal_month', $appraisalMonth)
                ->where('indicator_id', $id)
                ->first();

            if ($appraisalss) {
                $appraisalScores = KpiAppraisalScore::where('appraisal_id', $appraisalss->id)->get();

                foreach ($appraisalScores as $score) {
                    $scores[$score->competency_id] = $score->score;
                }
            }
        }
        // Case when only the appraisal month is provided (no employee)
        elseif ($appraisalMonth) {
            $appraisalss = KpiAppraisal::where('appraisal_month', $appraisalMonth)
                ->where('business_id', $business_id)
                ->where('indicator_id', $id)
                ->get();

            foreach ($appraisalss as $appraisal) {
                $appraisalScores = KpiAppraisalScore::where('appraisal_id', $appraisal->id)->get();
                foreach ($appraisalScores as $score) {
                    $scores[$score->competency_id] = $scores[$score->competency_id] ?? $score->score;
                }
            }
        }

        return view('kpi::Indicator.give_appraisal', compact('indicator', 'department', 'designation', 'employee', 'appraisalss', 'scores'));
    }

    /**
     * Store the appraisal and the competency scores for a given indicator.
     */
    public function store_appraisal(Request $request, $id)
    {
        $business_id = $request->session()->get('user.business_id');

        // Load the indicator with its competencies
        $indicator = KpiIndicator::with('competencies')->find($id);
        if (!$indicator) {
            return redirect()->route('indicator.index')->with('error', 'Indicator not found');
        }

        // Validate the request input
        $request->validate([
            'appraisal_month' => 'required|string|min:7|max:7', // YYYY-MM format
            'employee' => 'nullable|exists:users,id', // Employee is optional
            'technical.*.value' => 'required|string|in:None,Beginner,Intermediate,Advanced',
            'behavioral.*.value' => 'required|string|in:None,Beginner,Intermediate,Advanced',
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
        $appraisal = KpiAppraisal::updateOrCreate(
            $conditions,
            ['business_id' => $business_id]
        );

        // Store technical competency scores
        foreach ($request->input('technical') as $technicalInput) {
            $competency = $indicator->competencies->where('id', $technicalInput['id'])->where('type', 'technical')->first();
            if ($competency) {
                KpiAppraisalScore::updateOrCreate(
                    [
                        'appraisal_id' => $appraisal->id,
                        'competency_id' => $competency->id,
                        'business_id' => $business_id,
                    ],
                    [
                        'score' => $technicalInput['value'],
                    ]
                );
            }
        }

        // Store behavioral competency scores
        foreach ($request->input('behavioral') as $behavioralInput) {
            $competency = $indicator->competencies->where('id', $behavioralInput['id'])->where('type', 'behavioral')->first();
            if ($competency) {
                KpiAppraisalScore::updateOrCreate(
                    [
                        'appraisal_id' => $appraisal->id,
                        'competency_id' => $competency->id,
                        'business_id' => $business_id,
                    ],
                    [
                        'score' => $behavioralInput['value'],
                    ]
                );
            }
        }

        // Redirect with success message
        return redirect()->route('indicator.index')->with('success', 'Appraisal saved successfully');
    }

    public function appraisal(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        // Fetch indicators with the associated department
        $indicators = KpiIndicator::where('business_id', $business_id)
            ->pluck('title', 'id');

        // Get the selected indicator from the request (if any)
        $selectedIndicator = $request->get('indicator');

        // Load the indicator and its competencies based on selected indicator
        $indicator = null;
        if ($selectedIndicator) {
            $indicator = KpiIndicator::with('competencies')->find($selectedIndicator);
        }

        $department = Category::where('business_id', $business_id)->where('category_type', 'hrm_department')->pluck('name', 'id');
        $designation = Category::where('business_id', $business_id)->where('category_type', 'hrm_designation')->pluck('name', 'id');
        $employee = User::forDropdown($business_id, false);

        // Retrieve the employee and appraisal month from the request
        $employeeId = $request->get('employee'); // Can be null
        $appraisalMonth = $request->get('appraisal_month');

        $appraisalss = null;
        $scores = [];

        // Query with or without employeeId
        $query = KpiAppraisal::where('appraisal_month', $appraisalMonth)
            ->where('business_id', $business_id)
            ->where('indicator_id', $selectedIndicator);

        if ($employeeId) {
            $query->where('employee_id', $employeeId); // Include employee if provided
        }

        // Fetch the appraisal(s)
        $appraisalss = $query->get();

        // If appraisals exist, load the scores
        if ($appraisalss->isNotEmpty()) {
            foreach ($appraisalss as $appraisal) {
                $appraisalScores = KpiAppraisalScore::where('appraisal_id', $appraisal->id)->get();
                foreach ($appraisalScores as $score) {
                    $scores[$score->competency_id] = $score; // Store score data by competency_id
                }
            }
        }

        return view('kpi::Indicator.appraisal', compact('indicators', 'indicator', 'department', 'designation', 'employee', 'appraisalss', 'scores', 'selectedIndicator'));
    }



    public function appraisal_store(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $userId = $request->session()->get('user.id');

        // Validate the input data
        $request->validate([
            'appraisal_month' => 'required|string|min:7|max:7',
            'employee' => 'nullable|exists:users,id',
            'technical.*.actual_value' => 'nullable|numeric',
            'behavioral.*.actual_value' => 'nullable|numeric',
            'technical.*.note' => 'nullable|string',
            'behavioral.*.note' => 'nullable|string',
        ]);

        $indicator = KpiIndicator::with('competencies')->find($request->input('indicator_id'));
        if (!$indicator) {
            return redirect()->route('indicator.index')->with('error', 'Indicator not found');
        }

        $employeeId = $request->input('employee');
        $appraisalMonth = $request->input('appraisal_month');

        // Create or update the appraisal record
        $appraisal = KpiAppraisal::updateOrCreate(
            [
                'indicator_id' => $indicator->id,
                'appraisal_month' => $appraisalMonth,
                'employee_id' => $employeeId,
            ],
            [
                'business_id' => $business_id,
                'created_by' => $userId,
            ]
        );

        DB::beginTransaction();
        try {
            // Store technical competencies
            foreach ($request->input('technical', []) as $competencyId => $technicalInput) {
                \Log::info("Storing technical competency: Competency ID: {$competencyId}", $technicalInput);

                KpiAppraisalScore::updateOrCreate(
                    [
                        'appraisal_id' => $appraisal->id,
                        'competency_id' => $competencyId,
                    ],
                    [
                        'business_id' => $business_id,
                        'expect_value' => $technicalInput['expect_value'] ?? null,
                        'expect_score' => $technicalInput['expect_score'] ?? null,
                        'actual_value' => $technicalInput['actual_value'] ?? null,
                        'actual_score' => $technicalInput['actual_score'] ?? null,
                        'note' => $technicalInput['note'] ?? null,
                    ]
                );
            }

            // Store behavioral competencies
            foreach ($request->input('behavioral', []) as $competencyId => $behavioralInput) {
                \Log::info("Storing behavioral competency: Competency ID: {$competencyId}", $behavioralInput);

                KpiAppraisalScore::updateOrCreate(
                    [
                        'appraisal_id' => $appraisal->id,
                        'competency_id' => $competencyId,
                    ],
                    [
                        'business_id' => $business_id,
                        'expect_value' => $behavioralInput['expect_value'] ?? null,
                        'expect_score' => $behavioralInput['expect_score'] ?? null,
                        'actual_value' => $behavioralInput['actual_value'] ?? null,
                        'actual_score' => $behavioralInput['actual_score'] ?? null,
                        'note' => $behavioralInput['note'] ?? null,
                    ]
                );
            }

            DB::commit();
            return redirect()->route('appraisal.list')->with('success', 'Appraisal saved successfully.');
        } catch (\Exception $e) {
            
            \Log::error('Error saving appraisal score: ' . $e->getMessage());
            return redirect()->route('appraisal.list')->with('error', 'Error saving appraisal score.');
        }
    }





    public function appraisal_list(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $query = KpiAppraisal::with(['employee', 'indicator.department', 'scores'])->where('business_id', $business_id);

            // Apply year and month filters if provided
            if ($request->has('month') && $request->has('year')) {
                // Create a date string like '2024-10' based on the selected year and month
                $appraisalMonth = $request->year . '-' . str_pad($request->month, 2, '0', STR_PAD_LEFT);
                $query->where('appraisal_month', $appraisalMonth);
            }

            $appraisals = $query->get();

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
                ->addColumn('expect_value', function ($row) {
                    return $row->scores->sum(function ($score) {
                        return $score->expect_value ? floatval($score->expect_value) : 0;
                    });
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
                ->addColumn('actual_value', function ($row) {
                    return $row->scores->sum(function ($score) {
                        return $score->actual_value ? floatval($score->actual_value) : 0;
                    });
                })
                
                ->addColumn('%', function ($row) {
                    // Calculate the total percentage by summing up the percentage of each score
                    $totalPercentage = $row->scores->sum(function ($score) {
                        // Prevent division by zero and handle null values
                        if ($score->expect_value == 0 || $score->expect_value == null) {
                            return 0;
                        }
                
                        // Calculate the percentage for each score
                        return ($score->actual_value * 100) / $score->expect_value;
                    });
                
                    // Get the count of scores
                    $scoreCount = $row->scores->count();
                
                    // If there are scores, calculate the average percentage
                    if ($scoreCount > 0) {
                        $averagePercentage = $totalPercentage / $scoreCount;
                        if (floor($averagePercentage) == $averagePercentage) {
                            return number_format($averagePercentage, 0) . '%';
                        }
                        
                        return number_format($averagePercentage, 2) . '%';
                    }
                    return '0%';
                })
                
                ->addColumn('created_by', function ($row) {
                    return isset($row->createdBy)
                        ? $row->createdBy->first_name . ' ' . $row->createdBy->last_name
                        : 'N/A';
                })

                ->addColumn('action', function ($row) {
                    $viewUrl = route('appraisal.view', ['id' => $row->id]);
                    $editUrl = route('appraisal.store') . '?indicator=' . $row->indicator_id . '&appraisal_month=' . $row->appraisal_month;
                    if ($row->employee) {
                        $editUrl .= '&employee=' . $row->employee->id;
                    }
                    $deleteUrl = route('appraisal.delete', ['appraisal_id' => $row->id]);
        
                    $html = '<div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">' .
                                __('messages.actions') .
                                '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                    <li><a href="javascript:void(0);" data-href="' . $viewUrl . '" class="btn-modal" data-container=".kpi_modal">
                                        <i class="fas fa-eye"></i> ' . __('messages.view') . '</a></li>
                                    <li><a href="' . $editUrl . '">
                                         <i class="fas fa-edit"></i> ' . __('messages.edit') . '</a></li>
                                    <li><a href="javascript:void(0);" data-href="' . $deleteUrl . '" class="delete-kpi">
                                        <i class="fas fa-trash"></i> ' . __('messages.delete') . '</a></li>
                                </ul>
                            </div>';
        
                    return $html;
                })
                ->make(true);
        }

        return view('kpi::Indicator.list_appraisal');
    }


    public function appraisal_view(Request $request, $id)
    {
        $business_id = $request->session()->get('user.business_id');
        
        try {
            $appraisalData = DB::table('kpi_appraisal_scores AS ascore')
                ->join('kpi_appraisals AS a', 'ascore.appraisal_id', '=', 'a.id')
                ->join('kpi_competencies AS c', 'ascore.competency_id', '=', 'c.id')
                ->join('kpi_indicators AS i', 'c.indicator_id', '=', 'i.id')
                ->leftJoin('categories AS dept', 'i.department_id', '=', 'dept.id')
                ->leftJoin('categories AS desig', 'i.designation_id', '=', 'desig.id')
                ->leftJoin('users AS u', 'a.employee_id', '=', 'u.id')
                ->where('a.business_id', $business_id)
                ->where('a.id', $id)
                ->select([
                    'a.id AS appraisal_id',
                    'a.employee_id',
                    DB::raw("CONCAT(u.last_name, ' ', u.first_name) AS employee_username"),
                    'a.appraisal_month',
                    'c.type AS competency_type',
                    'c.name AS competency_name',
                    'ascore.expect_value',
                    'ascore.expect_score',
                    'ascore.actual_value',
                    'ascore.actual_score',
                    'ascore.note',
                    'i.title AS indicator_title',
                    'dept.name AS department_name',
                    'desig.name AS designation_name'
                ])
                ->get();

            if ($appraisalData->isEmpty()) {
                \Log::warning("Appraisal not found for ID: {$id} in Business ID: {$business_id}");
                return response()->json(['error' => 'Appraisal not found'], 404);
            }

            return view('kpi::Indicator.view_appraisal', compact('appraisalData'));
        } catch (\Exception $e) {
            \Log::error("Failed to retrieve appraisal data for ID: {$id} with error: " . $e->getMessage());
            return response()->json(['error' => 'Error retrieving appraisal data'], 500);
        }
    }





    public function appraisal_delete($id)
    {
        try {
            DB::beginTransaction();
            KpiAppraisalScore::where('appraisal_id', $id)->delete();
            KpiAppraisal::where('id', $id)->delete();
            DB::commit();
            return response()->json(['success' => 'Appraisal deleted successfully']);
        } catch (\Exception $e) {
            
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
            $query = KpiAppraisal::with(['employee', 'indicator.department', 'scores'])
                ->where('business_id', $business_id);

            // Apply month and year filter if provided
            if ($request->has('month') && $request->has('year')) {
                $appraisalMonth = $request->year . '-' . str_pad($request->month, 2, '0', STR_PAD_LEFT);
                $query->where('appraisal_month', $appraisalMonth);
            }

            try {
                $appraisals = $query->get();
            } catch (\Exception $e) {
                \Log::error('Appraisal report error: ' . $e->getMessage());
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
                    $viewUrl = route('appraisal.view', ['id' => $row->id]);

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

        return view('kpi::Indicator.report');
    }
    public function delete($id)
    {
        $business_id = request()->session()->get('user.business_id');

        // Fetch the indicator
        $indicator = KpiIndicator::where('business_id', $business_id)->findOrFail($id);

        // Attempt to delete the indicator and its competencies
        try {
            DB::beginTransaction();

            // Delete related competencies first
            $indicator->competencies()->delete();

            // Delete the indicator
            $indicator->delete();

            DB::commit();
            return redirect()->route('indicator.index')->with('success', 'Indicator successfully deleted');
        } catch (\Exception $e) {
            
            return redirect()->route('indicator.index')->with('error', 'Error deleting indicator: ' . $e->getMessage());
        }
    }


}
