@extends('layouts.app')
@section('title', __('kpi::lang.kpi'))
@section('content')

@include('kpi::layouts.nav')

<!-- Content Header -->
<section class="content-header">
    <h1>@lang('kpi::lang.indicator')</h1>
</section>

<!-- Main Content -->
<section class="content">
    {!! Form::open(['route' => 'indicator.store', 'method' => 'post']) !!}
    {{ csrf_field() }}
    <!-- Performance Appraisal Section -->
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.15);">
                <h3 class="card-header bg-light" style="border-bottom: 2px solid #007bff;">@lang('kpi::lang.create_indicator')</h3>
                <div class="card-body">
                    <div class="form-group row justify-content-center">
                        <label for="title" class="col-sm-4 col-form-label text-right">@lang('kpi::lang.title')<span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <input type="text" id="title" name="title" class="form-control" placeholder="@lang('kpi::lang.enter_title')" required>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center">
                        <label for="department" class="col-sm-4 col-form-label text-right">@lang('kpi::lang.department')<span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            {{ Form::select('department', $department, 
                                null, 
                                ['class' => 'form-control', 'placeholder' => __('kpi::lang.select'), 'required' => true]) 
                            }}
                        </div>
                    </div>
                    <div class="form-group row justify-content-center">
                        <label for="designation" class="col-sm-4 col-form-label text-right">@lang('kpi::lang.designation')</label>
                        <div class="col-sm-4">
                            {{ Form::select('designation', $designation, 
                                null, 
                                ['class' => 'form-control', 'placeholder' => __('kpi::lang.select')]) 
                            }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Technical Competencies Section -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.15);">
                <h3 class="card-header bg-light" style="border-bottom: 2px solid #007bff;">@lang('kpi::lang.finance')</h3>
                <div class="card-body">
                    <table class="table table-hover" id="technical-competencies-table" style="width: 100%;">
                        <thead>
                            <tr class="bg-light">
                                <th style="width: 5%;">#</th>
                                <th style="width: 40%;">@lang('kpi::lang.indicator')</th>
                                <th style="width: 25%;">@lang('kpi::lang.value')</th>
                                <th style="width: 15%;">@lang('kpi::lang.score')</th>
                                <th style="width: 10%;">@lang('kpi::lang.action')</th>
                            </tr>
                        </thead>
                        <tbody id="technical-competencies-body">
                            @foreach ([
                        ['name' => __('kpi::lang.sale_target'), 'index' => 0],
                        ['name' => __('kpi::lang.sale_yearly'), 'index' => 1],
                        ['name' => __('kpi::lang.sale_monthly'), 'index' => 2],
                        ['name' => __('kpi::lang.sale_daily'), 'index' => 3],
			['name' => __('kpi::lang.sale_team'), 'index' => 4],
			['name' => __('kpi::lang.sale_person'), 'index' => 5],
			//Expense
			['name' => __('kpi::lang.expense_target'), 'index' => 6],
                        ['name' => __('kpi::lang.expense_yearly'), 'index' => 7],
                        ['name' => __('kpi::lang.expense_monthly'), 'index' => 8],
                        ['name' => __('kpi::lang.expense_daily'), 'index' => 9],
			['name' => __('kpi::lang.expense_team'), 'index' => 10],
			['name' => __('kpi::lang.expense_person'), 'index' => 11],
			//profit
			 ['name' => __('kpi::lang.profit_target'), 'index' => 12],
                        ['name' => __('kpi::lang.profit_yearly'), 'index' => 13],
                        ['name' => __('kpi::lang.profit_monthly'), 'index' => 14],
                        ['name' => __('kpi::lang.profit_daily'), 'index' => 15],
			['name' => __('kpi::lang.profit_team'), 'index' => 16],
			['name' => __('kpi::lang.profit_person'), 'index' => 17]
						

                            ] as $key => $competency)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><input type="text" name="technical_indicators[{{$competency['index']}}][name]" class="form-control" value="{{ $competency['name'] }}"></td>
                                    <td><input type="text" name="technical_indicators[{{$competency['index']}}][value]" class="form-control" placeholder="@lang('kpi::lang.enter_value')"></td> <!-- Placeholder added -->
                                    <td><input type="number" name="technical_indicators[{{$competency['index']}}][score]" class="form-control form-control-sm" placeholder="@lang('kpi::lang.enter_score')"></td> <!-- Placeholder added -->
                                    <td><button type="button" class="btn btn-danger btn-sm remove-row">@lang('kpi::lang.remove')</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-primary btn-sm" id="add-technical-row">@lang('kpi::lang.add_finance')</button>
                </div>
            </div>
        </div>

        <!-- Behavioural / Organizational Competencies Section -->
        <div class="col-md-6">
            <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.15);">
                <h3 class="card-header bg-light" style="border-bottom: 2px solid #007bff;">@lang('kpi::lang.non_finance')</h3>
                <div class="card-body">
                    <table class="table table-hover" id="behavioural-competencies-table" style="width: 100%;">
                        <thead>
                            <tr class="bg-light">
                                <th style="width: 5%;">#</th>
                                <th style="width: 40%;">@lang('kpi::lang.indicator')</th>
                                <th style="width: 25%;">@lang('kpi::lang.value')</th>
                                <th style="width: 15%;">@lang('kpi::lang.score')</th>
                                <th style="width: 10%;">@lang('kpi::lang.action')</th>
                            </tr>
                        </thead>
                        <tbody id="behavioural-competencies-body">
                            @foreach ([
			['name' => __('kpi::lang.customer_experience_management'), 'index' => 0],
			['name' => __('kpi::lang.marketing'), 'index' => 1],
			['name' => __('kpi::lang.management'), 'index' => 2],
			['name' => __('kpi::lang.administration'), 'index' => 3],
			['name' => __('kpi::lang.presentation_skill'), 'index' => 4],
			['name' => __('kpi::lang.quality_of_work'), 'index' => 5],
			['name' => __('kpi::lang.capacity_builder'), 'index' => 6],                      
                        ['name' => __('kpi::lang.Integrity'), 'index' => 7],
                        ['name' =>__('kpi::lang.professionalism'), 'index' => 8],
                        ['name' => __('kpi::lang.team_work'), 'index' => 9],
                        ['name' => __('kpi::lang.critical_thinking'), 'index' => 10],
                        ['name' => __('kpi::lang.conflict_management'), 'index' => 11],
			['name' => __('kpi::lang.attendance'), 'index' => 12],
			['name' => __('kpi::lang.ability_to_meet_deadline'), 'index' => 13],
			//new
			['name' => __('kpi::lang.marketing'), 'index' => 15],
			['name' => __('kpi::lang.management'), 'index' => 16],
			['name' => __('kpi::lang.administration'), 'index' => 17],
			['name' => __('kpi::lang.presentation_skill'), 'index' => 18],
			['name' => __('kpi::lang.quality_of_work'), 'index' => 19],
			['name' => __('kpi::lang.capacity_builder'), 'index' => 20],                      
                        ['name' => __('kpi::lang.Integrity'), 'index' => 21],
                        ['name' =>__('kpi::lang.professionalism'), 'index' => 22],
                        ['name' => __('kpi::lang.team_work'), 'index' => 23],
                        ['name' => __('kpi::lang.critical_thinking'), 'index' => 24],
                        ['name' => __('kpi::lang.conflict_management'), 'index' => 25],
			['name' => __('kpi::lang.attendance'), 'index' => 26]

                            ] as $key => $competency)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><input type="text" name="behavioral_indicators[{{$competency['index']}}][name]" class="form-control" value="{{ $competency['name'] }}"></td>
                                    <td><input type="text" name="behavioral_indicators[{{$competency['index']}}][value]" class="form-control" placeholder="@lang('kpi::lang.enter_value')"></td> <!-- Placeholder added -->
                                    <td><input type="number" name="behavioral_indicators[{{$competency['index']}}][score]" class="form-control form-control-sm" placeholder="@lang('kpi::lang.enter_score')"></td> <!-- Placeholder added -->
                                    <td><button type="button" class="btn btn-danger btn-sm remove-row">@lang('kpi::lang.remove')</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-primary btn-sm" id="add-behavioural-row">@lang('kpi::lang.add_non_finance')</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="row">
        <div class="col-xs-12 text-right">
            <div class="form-group">
                {{ Form::submit(__('messages.submit'), ['class' => 'btn btn-success']) }}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</section>

<!-- Javascript for adding and removing rows -->
<script>
    document.getElementById('add-technical-row').addEventListener('click', function() {
        addCompetencyRow('technical-competencies-body', 'technical_indicators');
    });

    document.getElementById('add-behavioural-row').addEventListener('click', function() {
        addCompetencyRow('behavioural-competencies-body', 'behavioral_indicators');
    });

    function addCompetencyRow(tableBodyId, namePrefix) {
        let tableBody = document.getElementById(tableBodyId);
        let rowCount = tableBody.getElementsByTagName('tr').length;
        let newRow = `
            <tr>
                <td>${rowCount + 1}</td>
                <td><input type="text" name="${namePrefix}[${rowCount}][name]" class="form-control" placeholder="New Competency"></td>
                <td><input type="text" name="${namePrefix}[${rowCount}][value]" class="form-control" placeholder="Enter Value"></td> <!-- Placeholder added -->
                <td><input type="number" name="${namePrefix}[${rowCount}][score]" class="form-control form-control-sm" placeholder="Score"></td> <!-- Placeholder added -->
                <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
            </tr>`;
        tableBody.insertAdjacentHTML('beforeend', newRow);
        addRemoveRowFunctionality();
    }

    function addRemoveRowFunctionality() {
        document.querySelectorAll('.remove-row').forEach(function(button) {
            button.addEventListener('click', function() {
                this.closest('tr').remove();
                updateRowNumbers();
            });
        });
    }

    function updateRowNumbers() {
        // Update row numbers for the technical competencies table
        document.querySelectorAll('#technical-competencies-table tbody tr').forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
        });

        // Update row numbers for the behavioural competencies table
        document.querySelectorAll('#behavioural-competencies-table tbody tr').forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
        });
    }

    addRemoveRowFunctionality();
</script>
@stop