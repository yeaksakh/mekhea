@extends('layouts.app')
@section('title', __('visa::lang.visa'))
@section('content')

@include('visa::layouts.nav')

<!-- Content Header -->
<section class="content-header">
    <h1>@lang('visa::lang.indicator')</h1>
</section>

<!-- Main Content -->
<section class="content">
    {!! Form::open(['route' => 'visa.indicator.store', 'method' => 'post']) !!}
    {{ csrf_field() }}
    <!-- Performance Appraisal Section -->
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.15);">
                <h3 class="card-header bg-light" style="border-bottom: 2px solid #007bff;"></h3>
                <div class="card-body">
                    <div class="form-group row justify-content-center">
                        <label for="title" class="col-sm-4 col-form-label text-right">@lang('visa::lang.visa_title')<span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <input type="text" id="title" name="title" class="form-control" placeholder="@lang('visa::lang.enter_title')" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Technical Competencies Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.15);">
               
                <div class="card-body">
                    <table class="table table-hover" id="technical-competencies-table" style="width: 100%;">
                        <thead>
                            <tr class="bg-light">
                                <th style="width: 5%;">#</th>
                                <th style="width: 40%;">@lang('visa::lang.indicator')</th>
                                <th style="width: 25%;">@lang('visa::lang.value')</th>
                                <th style="width: 15%;">@lang('visa::lang.score')</th>
                                <th style="width: 10%;">@lang('visa::lang.action')</th>
                            </tr>
                        </thead>
                        <tbody id="technical-competencies-body">
                            @foreach ([
                            ['name' => __('visa::lang.current_status_of_the_store'), 'index' => 0],
                            ['name' => __('visa::lang.customer_service'), 'index' => 1],
                            ['name' => __('visa::lang.use_of_company_products'), 'index' => 2],
                            ['name' => __('visa::lang.product_display'), 'index' => 3],
                            ['name' => __('visa::lang.regular_communication_with_the_company'), 'index' => 4],
                            ] as $key => $competency)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td><input type="text" name="technical_indicators[{{$competency['index']}}][name]" class="form-control" value="{{ $competency['name'] }}"></td>
                                <td><input type="text" name="technical_indicators[{{$competency['index']}}][value]" value="5" readonly class="form-control" placeholder="@lang('visa::lang.enter_value')"></td> <!-- Placeholder added -->
                                <td><input type="number" name="technical_indicators[{{$competency['index']}}][score]" class="form-control form-control-sm" placeholder="@lang('visa::lang.enter_score')"></td> <!-- Placeholder added -->
                                <td><button type="button" class="btn btn-danger btn-sm remove-row">@lang('visa::lang.remove')</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- <button type="button" class="btn btn-primary btn-sm" id="add-technical-row">@lang('visa::lang.add_finance')</button> -->
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