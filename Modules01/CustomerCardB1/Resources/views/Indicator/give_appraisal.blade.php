@extends('layouts.app')
@section('title', __('customercardb1::visa.visa'))
@section('content')
    @include('customercardb1::layouts.nav_visa')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            @lang('customercardb1::visa.give_appraisal')
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        {!! Form::open(['route' => ['customercardb1.visa.indicator.store_appraisal', $indicator->id], 'method' => 'post']) !!}

        <!-- Performance Appraisal Section -->
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-lg p-4 mb-5 bg-white rounded"
                    style="border: 1px solid #e3e3e3; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);">
                    <h3 class="card-header bg-light" style="border-bottom: 2px solid #007bff;">Give Performance Appraisal</h3>
                    <div class="card-body">
                        <!-- Title Display -->
                        <div class="form-group row justify-content-center">
                            <label for="title" class="col-sm-5 col-form-label text-right">Title : </label>
                            <div class="col-sm-2">
                                <p class="form-control-static">{{ $indicator->title }}</p>
                            </div>
                        </div>

                        <div class="form-group row justify-content-center">
                            <label for="contact" class="col-sm-5 col-form-label text-right">Contact:</label>
                            <div class="col-sm-3">
                                {{ Form::select('contact', $contact, old('contact', request()->get('contact')), ['class' => 'form-control', 'placeholder' => 'Select Employee', 'id' => 'contactSelect']) }}
                            </div>
                        </div>

                        <!-- Appraisal Month Input (from Appraisal) -->
                        <div class="form-group row justify-content-center">
                            <label for="appraisal_month" class="col-sm-5 col-form-label text-right">Select Month <span
                                    class="text-danger">*</span>:</label>
                            <div class="col-sm-3">
                                <input type="date" id="appraisal_month" name="appraisal_month" class="form-control"
                                    value="{{ old('appraisal_month', request()->get('appraisal_month')) }}" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Technical Competencies Section -->
            <div class="col-md-6">
                <div class="card shadow-lg p-4 mb-5 bg-white rounded"
                    style="border: 1px solid #e3e3e3; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);">
                    <h3 class="card-header bg-light" style="border-bottom: 2px solid #007bff;">Technical Competencies</h3>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr class="bg-light">
                                    <th>#</th> <!-- New column for numbering -->
                                    <th>Indicator</th>
                                    <th>Expected Value</th>
                                    <th>Set Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $techCounter = 1; @endphp <!-- Counter for technical competencies -->
                                @foreach ($indicator->competencies->where('type', 'technical') as $competency)
                                    <tr>
                                        <td>{{ $techCounter++ }}</td> <!-- Display and increment counter -->
                                        <td>{{ $competency->name }}</td>
                                        <td>{{ $competency->value }}</td>
                                        <td>
                                            {{ Form::hidden('technical[' . $competency->id . '][id]', $competency->id) }}
                                            {{ Form::select(
                                                'technical[' . $competency->id . '][value]',
                                                ['None' => '0', 'Beginner' => '1', 'Intermediate' => '2', 'Advanced' => '3', 'Expert' => '4', 'Leader' => '5'],
                                                old(
                                                    'technical[' . $competency->id . '][value]', // Check for old input first (in case of validation error)
                                                    $scores[$competency->id] ?? 'None', // Otherwise, get the score from the $scores array
                                                ),
                                                ['class' => 'form-control'],
                                            ) }}
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
        <div class="row">
            <div class="col-xs-12">
                <div class="form-group pull-right">
                    {{ Form::submit(__('messages.submit'), ['class' => 'btn btn-success']) }}
                </div>
            </div>
        </div>

        {!! Form::close() !!}
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const employeeSelect = document.getElementById('employeeSelect');
            const appraisalMonthInput = document.getElementById('appraisal_month');
            const designationField = document.getElementById('designation'); // Checking designation field presence
    
            appraisalMonthInput.addEventListener('change', function() {
                redirectToAppraisal();
            });
    
            if (designationField && designationField.innerHTML.trim() !== '') {
                if (employeeSelect) {
                    employeeSelect.addEventListener('change', function() {
                        redirectToAppraisal();
                    });
                }
            }
    
            function redirectToAppraisal() {
                const employeeId = employeeSelect ? employeeSelect.value : null;
                const appraisalMonth = appraisalMonthInput.value;
    
                if (designationField && designationField.innerHTML.trim() !== '') {
                    if (employeeId && appraisalMonth) {
                        redirectToUrl(employeeId, appraisalMonth);
                    }
                } else {
                    if (appraisalMonth) {
                        redirectToUrl(null, appraisalMonth);
                    }
                }
            }
    
            function redirectToUrl(employeeId, appraisalMonth) {
                const url = new URL(window.location.href);
    
                if (employeeId) {
                    url.searchParams.set('employee', employeeId);
                } else {
                    url.searchParams.delete('employee'); // Remove employee if not needed
                }
    
                url.searchParams.set('appraisal_month', appraisalMonth);
                window.location.href = url.toString();
            }
        });
    </script>
@stop