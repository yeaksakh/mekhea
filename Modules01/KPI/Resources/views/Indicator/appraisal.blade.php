@extends('layouts.app')
@section('title', __('kpi::lang.kpi'))
@section('content')
@include('kpi::layouts.nav')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        @lang('kpi::lang.appraisal')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <!-- GET form for "Go" button to load data -->
    {!! Form::open(['id' => 'appraisalForm', 'method' => 'GET', 'url' => route('appraisal')]) !!}
    @csrf

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);">
                <h3 class="card-header bg-light" style="border-bottom: 2px solid #007bff;">@lang('kpi::lang.give_appraisal')</h3>
                <div class="card-body">
                    <!-- Title Select -->
                    <div class="form-group row justify-content-center">
                        <label for="title" class="col-sm-5 col-form-label text-right">@lang('kpi::lang.title') : </label>
                        <div class="col-sm-3">
                            {{ Form::select('indicator', $indicators, $selectedIndicator, ['class' => 'form-control', 'placeholder' => 'Select Indicator', 'id' => 'indicatorSelect']) }}
                        </div>
                    </div>
                    
                    <div class="form-group row justify-content-center">
    <label for="appraisal_month" class="col-sm-5 col-form-label text-right">@lang('kpi::lang.month') <span class="text-danger">*</span>:</label>
    <div class="col-sm-3">
        <div class="input-icon">
            <input type="month" id="appraisal_month" name="appraisal_month" class="form-control"
                value="{{ old('appraisal_month', request()->get('appraisal_month')) }}" required>
            <i class="fas fa-calendar-alt"></i>
        </div>
    </div>
</div>

<style>
    .input-icon {
        position: relative;
    }
    .input-icon i {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }
</style>



                    <!-- Department Display -->
                    @if(isset($indicator))
                    <div class="form-group row justify-content-center">
                        <label for="department" class="col-sm-5 col-form-label text-right">@lang('kpi::lang.department') : </label>
                        <div class="col-sm-2">
                            <p class="form-control-static">{{ $department[$indicator->department_id] }}</p>
                        </div>
                    </div>

                    <!-- Designation Display -->
                    @if (!empty($designation[$indicator->designation_id]))
                    <div class="form-group row justify-content-center">
                        <label for="designation" class="col-sm-5 col-form-label text-right">@lang('kpi::lang.designation'):</label>
                        <div class="col-sm-2">
                            <p class="form-control-static">{{ $designation[$indicator->designation_id] }}</p>
                        </div>
                    </div>

                    <!-- Employee Input -->
                    <div class="form-group row justify-content-center">
                        <label for="employee" class="col-sm-5 col-form-label text-right">@lang('kpi::lang.employee'):</label>
                        <div class="col-sm-3">
                            {{ Form::select('employee', $employee, old('employee', request()->get('employee')), ['class' => 'form-control', 'placeholder' => 'Select Employee', 'id' => 'employeeSelect']) }}
                        </div>
                    </div>
                    @endif

                    

                    @endif

                    <!-- Go Button -->
                    <div class="form-group row justify-content-center">
                        <div class="col-sm-5"></div>
                        <div class="col-sm-3">
                            <button type="button" class="btn btn-primary" id="goButton">@lang('kpi::lang.go')</button> <!-- Triggers form submission with GET -->
                        </div>
                    </div>

                    {!! Form::close() !!} <!-- Close GET form -->

                    @if(isset($indicator))
                    <!-- Competencies Table Section -->
                    {!! Form::open(['id' => 'submitForm', 'method' => 'POST', 'url' => route('appraisal.store')]) !!} <!-- POST form for Submit -->
                    @csrf
                    <input type="hidden" name="indicator_id" value="{{ $indicator->id }}">
                    <input type="hidden" name="appraisal_month" value="{{ request()->get('appraisal_month') }}">
                    <input type="hidden" name="employee" value="{{ request()->get('employee') }}">

                    <div class="row">
                        <!-- Technical Competencies Section -->
                        <div class="col-md-12">
                            <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);">
                                <h3 class="card-header bg-light" style="border-bottom: 2px solid #007bff;">@lang('kpi::lang.financial')</h3>
                                <div class="card-body">
                                    <table class="table table-hover">
                                        <thead>
                                             <tr class="bg-light">
                                                <th>#</th>
                                                <th>@lang('kpi::lang.indicator')</th>
                                                <th>@lang('kpi::lang.expect_value_money')</th> <!-- Added Expected Value Column -->
                                                <th>@lang('kpi::lang.expect_score')</th>
                                                <th>@lang('kpi::lang.actual_value_money')</th>
                                                <th class="score-width">@lang('kpi::lang.actual_score')</th>
                                                <th class="percentage-width">%</th>
                                                <th>@lang('kpi::lang.note')</th> <!-- Added Note Column -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $techCounter = 1; @endphp
                                            @foreach ($indicator->competencies->where('type', 'technical') as $competency)
                                            @php
                                            $scoreData = $scores[$competency->id] ?? null;
                                            @endphp
                                            <tr>
                                                <td>{{ $techCounter++ }}</td>
                                                <td>{{ $competency->name }}</td>
                                                <td>{{ $competency->value }}</td> <!-- Display Expected Value -->
                                                <td>{{ $competency->score }}</td> <!-- Display the Expected Score -->
                                                <td>
                                                    <input type="number" name="technical[{{ $competency->id }}][actual_value]" class="form-control actual-value" placeholder="@lang('kpi::lang.actual_value_money')" data-expected-value="{{ $competency->value }}" data-competency-score="{{ $competency->score }}" data-score-input="technical-score-{{ $competency->id }}" data-pers-input="technical-pers-{{ $competency->id }}" value="{{ $scoreData ? $scoreData->actual_value : '' }}">
                                                    <input type="hidden" name="technical[{{ $competency->id }}][expect_value]" value="{{ $competency->value }}"> <!-- Hidden Expected Value -->
                                                    <input type="hidden" name="technical[{{ $competency->id }}][expect_score]" value="{{ $competency->score }}"> <!-- Hidden Expected Score -->
                                                </td>
                                                <td>
                                                    <input type="text" name="technical[{{ $competency->id }}][actual_score]" class="form-control" id="technical-score-{{ $competency->id }}" placeholder="Actual Score" readonly value="{{ $scoreData ? $scoreData->actual_score : '' }}">
                                                </td>
                                                <td>
                                                    <input type="text" name="technical[{{ $competency->id }}][pers]" class="form-control" id="technical-pers-{{ $competency->id }}" placeholder="%" readonly value="{{ $scoreData ? $scoreData->percentage : '' }}">
                                                </td>
                                                <td>
                                                    <!-- Input for Notes -->
                                                    <textarea name="technical[{{ $competency->id }}][note]" class="form-control" placeholder="@lang('kpi::lang.note')">{{ $scoreData ? $scoreData->note : '' }}</textarea>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Behavioural Competencies Section -->
                        <div class="col-md-12">
                            <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);">
                                <h3 class="card-header bg-light" style="border-bottom: 2px solid #007bff;">@lang('kpi::lang.non_financial')</h3>
                                <div class="card-body">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr class="bg-light">
                                                <th>#</th>
                                                <th>@lang('kpi::lang.indicator')</th>
                                                <th>@lang('kpi::lang.expect_value')</th> <!-- Added Expected Value Column -->
                                                <th>@lang('kpi::lang.expect_score')</th>
                                                <th>@lang('kpi::lang.actual_value')</th>
                                                <th class="score-width">@lang('kpi::lang.actual_score')</th>
                                                <th class="percentage-width">%</th>
                                                <th>@lang('kpi::lang.note')</th> <!-- Added Note Column -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $behCounter = 1; @endphp
                                            @foreach ($indicator->competencies->where('type', 'behavioral') as $competency)
                                            @php
                                            $scoreData = $scores[$competency->id] ?? null;
                                            @endphp
                                            <tr>
                                                <td>{{ $behCounter++ }}</td>
                                                <td>{{ $competency->name }}</td>
                                                <td>{{ $competency->value }}</td> <!-- Display Expected Value -->
                                                <td>{{ $competency->score }}</td> <!-- Display the Expected Score -->
                                                <td>
                                                    <input type="number" name="behavioral[{{ $competency->id }}][actual_value]" class="form-control actual-value" placeholder="@lang('kpi::lang.actual_value')" data-expected-value="{{ $competency->value }}" data-competency-score="{{ $competency->score }}" data-score-input="behavioral-score-{{ $competency->id }}" data-pers-input="behavioral-pers-{{ $competency->id }}" value="{{ $scoreData ? $scoreData->actual_value : '' }}">
                                                    <input type="hidden" name="behavioral[{{ $competency->id }}][expect_value]" value="{{ $competency->value }}"> <!-- Hidden Expected Value -->
                                                    <input type="hidden" name="behavioral[{{ $competency->id }}][expect_score]" value="{{ $competency->score }}"> <!-- Hidden Expected Score -->
                                                </td>
                                                <td>
                                                    <input type="text" name="behavioral[{{ $competency->id }}][actual_score]" class="form-control" id="behavioral-score-{{ $competency->id }}" placeholder="Actual Score" readonly value="{{ $scoreData ? $scoreData->actual_score : '' }}">
                                                </td>
                                                <td>
                                                    <input type="text" name="behavioral[{{ $competency->id }}][pers]" class="form-control" id="behavioral-pers-{{ $competency->id }}" placeholder="%" readonly value="{{ $scoreData ? $scoreData->percentage : '' }}">
                                                </td>
                                                <td>
                                                    <!-- Input for Notes -->
                                                    <textarea name="behavioral[{{ $competency->id }}][note]" class="form-control" placeholder="@lang('kpi::lang.note')">{{ $scoreData ? $scoreData->note : '' }}</textarea>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row justify-content-center">
                        <div class="col-sm-5"></div>
                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-success">@lang('kpi::lang.submit')</button>
                        </div>
                    </div>

                    {!! Form::close() !!} <!-- Close POST form -->
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript for form submission and score calculation -->
<script>
    document.getElementById('goButton').addEventListener('click', function() {
        document.getElementById('appraisalForm').submit(); // Trigger GET request to load the data
    });

    document.addEventListener('DOMContentLoaded', function() {
        const actualValueInputs = document.querySelectorAll('.actual-value');

        // Function to calculate and update the score and percentage
        function calculateScoreAndPercentage(input) {
            const actualValue = parseFloat(input.value) || 0;
            const expectedValue = parseFloat(input.getAttribute('data-expected-value')) || 0;
            const competencyScore = parseFloat(input.getAttribute('data-competency-score')) || 0;
            const scoreInputId = input.getAttribute('data-score-input');
            const persInputId = input.getAttribute('data-pers-input');

            let score = 0;
            let percentage = 0;

            if (expectedValue > 0) {
                score = (actualValue * competencyScore) / expectedValue; // Calculate actual score
                percentage = (actualValue / expectedValue) * 100; // Calculate percentage
            }

            // Format score and percentage
            const formattedScore = Number.isInteger(score) ? score : score.toFixed(1);
            const formattedPercentage = Number.isInteger(percentage) ? percentage : percentage.toFixed(1);

            // Update the actual score input field
            document.getElementById(scoreInputId).value = formattedScore;

            // Update the percentage input field
            document.getElementById(persInputId).value = `${formattedPercentage}%`;
        }

        // Attach the input event listener and calculate for existing values
        actualValueInputs.forEach(function(input) {
            // Calculate when the page loads (for pre-filled values)
            calculateScoreAndPercentage(input);

            // Calculate whenever the user inputs new data
            input.addEventListener('input', function() {
                calculateScoreAndPercentage(input);
            });
        });

        // Clear employee and month fields when the indicator changes
        const indicatorSelect = document.getElementById('indicatorSelect');
        const employeeSelect = document.getElementById('employeeSelect');
        const appraisalMonthInput = document.getElementById('appraisal_month');

        indicatorSelect.addEventListener('change', function() {
            // Clear employee and appraisal month fields
            if (employeeSelect) {
                employeeSelect.value = ''; // Reset employee dropdown
            }
            if (appraisalMonthInput) {
                appraisalMonthInput.value = ''; // Reset month input
            }
        });
    });
</script>

<!-- Custom styles -->
<style>
    .score-width {
        width: 10% !important;
    }

    .percentage-width {
        width: 10% !important;
    }
</style>
@endsection