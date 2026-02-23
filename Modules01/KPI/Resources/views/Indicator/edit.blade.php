@extends('layouts.app')
@section('title', __('kpi::lang.edit_indicator'))
@section('content')

@include('kpi::layouts.nav')

<!-- Content Header -->
<section class="content-header">
    <h1>@lang('kpi::lang.edit_indicator')</h1>
</section>

<!-- Main Content -->
<section class="content">
    {!! Form::model($indicator, ['route' => ['indicator.update', $indicator->id], 'method' => 'put']) !!}
    {{ csrf_field() }}

    <!-- Performance Appraisal Section -->
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);">
                <h3 class="card-header bg-light" style="border-bottom: 2px solid #007bff;">Edit Indicator</h3>
                <div class="card-body">
                    <div class="form-group row justify-content-center">
                        <label for="title" class="col-sm-4 col-form-label text-right">Title <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <input type="text" id="title" name="title" class="form-control" value="{{ $indicator->title }}" required>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center">
                        <label for="department" class="col-sm-4 col-form-label text-right">Department <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            {{ Form::select('department', $department, $indicator->department_id, ['class' => 'form-control', 'placeholder' => 'Select Department', 'required' => true]) }}
                        </div>
                    </div>
                    <div class="form-group row justify-content-center">
                        <label for="designation" class="col-sm-4 col-form-label text-right">Designation</label>
                        <div class="col-sm-4">
                            {{ Form::select('designation', $designation, $indicator->designation_id, ['class' => 'form-control', 'placeholder' => 'Select Designation']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Technical Competencies Section -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);">
                <h3 class="card-header bg-light" style="border-bottom: 2px solid #007bff;">Technical Competencies</h3>
                <div class="card-body">
                    <table class="table table-hover" id="technical-competencies-table">
                        <thead>
                            <tr class="bg-light">
                                <th style="width: 5%;">#</th>
                                <th style="width: 40%;">Indicator</th>
                                <th style="width: 25%;">Value</th>
                                <th style="width: 15%;">Score</th>
                                <th style="width: 10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="technical-competencies-body">
                            @php $maxTechId = 0; @endphp
                            @foreach ($indicator->competencies->where('type', 'technical') as $competency)
                                @php $maxTechId = max($maxTechId, $competency->id); @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><input type="text" name="technical_indicators[{{ $competency->id }}][name]" class="form-control" value="{{ $competency->name }}"></td>
                                    <td><input type="text" name="technical_indicators[{{ $competency->id }}][value]" class="form-control" value="{{ $competency->value }}"></td>
                                    <td><input type="number" name="technical_indicators[{{ $competency->id }}][score]" class="form-control form-control-sm" value="{{ $competency->score }}" placeholder="Score"></td>
                                    <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-primary btn-sm" id="add-technical-row">Add Technical Competency</button>
                </div>
            </div>
        </div>

        <!-- Behavioural Competencies Section -->
        <div class="col-md-6">
            <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);">
                <h3 class="card-header bg-light" style="border-bottom: 2px solid #007bff;">Behavioural Competencies</h3>
                <div class="card-body">
                    <table class="table table-hover" id="behavioural-competencies-table">
                        <thead>
                            <tr class="bg-light">
                                <th style="width: 5%;">#</th>
                                <th style="width: 40%;">Indicator</th>
                                <th style="width: 25%;">Value</th>
                                <th style="width: 15%;">Score</th>
                                <th style="width: 10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="behavioural-competencies-body">
                            @php $maxBehavId = 0; @endphp
                            @foreach ($indicator->competencies->where('type', 'behavioral') as $competency)
                                @php $maxBehavId = max($maxBehavId, $competency->id); @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><input type="text" name="behavioral_indicators[{{ $competency->id }}][name]" class="form-control" value="{{ $competency->name }}"></td>
                                    <td><input type="text" name="behavioral_indicators[{{ $competency->id }}][value]" class="form-control" value="{{ $competency->value }}"></td>
                                    <td><input type="number" name="behavioral_indicators[{{ $competency->id }}][score]" class="form-control form-control-sm" value="{{ $competency->score }}" placeholder="Score"></td>
                                    <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-primary btn-sm" id="add-behavioural-row">Add Behavioural Competency</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="row">
        <div class="col-xs-12 text-right">
            <div class="form-group">
                {{ Form::submit(__('messages.update'), ['class' => 'btn btn-success']) }}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialize counters for display purposes based on the last row's display number in each table
    let techDisplayCounter = document.querySelectorAll('#technical-competencies-body tr').length;
    let behavDisplayCounter = document.querySelectorAll('#behavioural-competencies-body tr').length;

    let maxTechId = {{ $maxTechId ?? 0 }};
    let maxBehavId = {{ $maxBehavId ?? 0 }};

    document.getElementById('add-technical-row').addEventListener('click', function() {
        addCompetencyRow('technical-competencies-body', 'technical_indicators', ++maxTechId, ++techDisplayCounter);
    });

    document.getElementById('add-behavioural-row').addEventListener('click', function() {
        addCompetencyRow('behavioural-competencies-body', 'behavioral_indicators', ++maxBehavId, ++behavDisplayCounter);
    });

    function addCompetencyRow(tableBodyId, namePrefix, id, displayNumber) {
        let tableBody = document.getElementById(tableBodyId);
        let newRow = `
            <tr>
                <td>${displayNumber}</td>
                <td><input type="text" name="${namePrefix}[${id}][name]" class="form-control" placeholder="New Competency"></td>
                <td><input type="text" name="${namePrefix}[${id}][value]" class="form-control" placeholder="Enter Value"></td>
                <td><input type="number" name="${namePrefix}[${id}][score]" class="form-control form-control-sm" placeholder="Score"></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row" onclick="removeRow(this, '${tableBodyId}')">Remove</button></td>
            </tr>`;
        tableBody.insertAdjacentHTML('beforeend', newRow);
        addRemoveRowFunctionality();
    }

    function removeRow(button, tableBodyId) {
        let tableBody = button.closest('tbody');
        button.closest('tr').remove();
        updateRowNumbers(tableBody, tableBodyId);
    }

    function updateRowNumbers(tableBody, tableBodyId) {
        let rows = tableBody.querySelectorAll('tr');
        let index = 1; // start index from 1 for display
        rows.forEach((row) => {
            row.querySelector('td:first-child').textContent = index;
            index++;
        });
    }

    function addRemoveRowFunctionality() {
        document.querySelectorAll('.remove-row').forEach(button => {
            button.onclick = function() {
                let tableBodyId = this.closest('table').id === 'technical-competencies-table' ? 'technical-competencies-body' : 'behavioural-competencies-body';
                removeRow(this, tableBodyId);
            };
        });
    }

    addRemoveRowFunctionality();
});

</script>


@stop
