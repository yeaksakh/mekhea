@extends('layouts.app')
@section('title', __('customercardb1::visa.visa'))

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
@include('customercardb1::layouts.nav_visa')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        @lang('customercardb1::visa.appraisal')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <!-- GET form for "Go" button to load data -->
    {!! Form::open(['id' => 'appraisalForm', 'method' => 'GET', 'url' => route('customercardb1.visa.appraisal')]) !!}
    @csrf

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);">
                <h3 class="card-header bg-light" style="border-bottom: 2px solid #007bff;">@lang('customercardb1::visa.give_appraisal')</h3>
                <div class="card-body">
                    <!-- Title Select -->
                    <div class="form-group row justify-content-center">
                        <label for="title" class="col-sm-5 col-form-label text-right">@lang('customercardb1::visa.title') : </label>
                        <div class="col-sm-3">
                            {{ Form::select('indicator', $indicators, $selectedIndicator, ['class' => 'form-control select2', 'placeholder' => 'Select Indicator', 'id' => 'indicatorSelect']) }}
                        </div>
                    </div>

                    <div class="form-group row justify-content-center">
                        <label for="contact" class="col-sm-5 col-form-label text-right">@lang('customercardb1::contact.customer'):</label>
                        <div class="col-sm-3">
                            {{ Form::select('contact', $contact, old('contact', request()->get('contact')), ['class' => 'form-control select2', 'placeholder' => 'Select Customer', 'id' => 'contactSelect']) }}
                        </div>
                    </div>

                    <div class="form-group row justify-content-center">
                        <label for="appraisal_month" class="col-sm-5 col-form-label text-right">@lang('customercardb1::lang.date') <span class="text-danger">*</span>:</label>
                        <div class="col-sm-3">
                            <div class="input-icon">
                                <input type="date" id="appraisal_month" name="appraisal_month" class="form-control"
                                    value="{{ old('appraisal_month', 
        request()->get('appraisal_month') ?
        date('Y-m-d', strtotime(request()->get('appraisal_month'))) : 
        (isset($model->appraisal_month) ? date('Y-m-d', strtotime($model->appraisal_month)) : '')) }}" required>
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

                    <!-- Go Button -->
                    <div class="form-group row justify-content-center">
                        <div class="col-sm-5"></div>
                        <div class="col-sm-3">
                            <button type="button" class="btn btn-primary" id="goButton">@lang('customercardb1::visa.go')</button>
                        </div>
                    </div>

                    {!! Form::close() !!}
                    @if(isset($indicator))
                    <!-- Competencies Table Section -->
                    {!! Form::open(['id' => 'submitForm', 'method' => 'POST', 'url' => route('customercardb1.visa.appraisal.store')]) !!}
                    @csrf
                    <input type="hidden" name="indicator_id" value="{{ $indicator->id }}">
                    <input type="hidden" name="appraisal_id" value="{{ request()->input('appraisal_id') }}">
                    <input type="hidden" name="contact" value="{{ request()->get('contact') }}">
                    <input type="hidden" name="appraisal_month" value="{{ request()->get('appraisal_month') }}">
                    <input type="hidden" name="employee" value="{{ request()->get('employee') }}">

                    <div class="row">
                        <!-- Technical Competencies Section -->
                        <div class="col-md-12">
                            <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);">
                                <h3 class="card-header bg-light" style="border-bottom: 2px solid #007bff;">@lang('customercardb1::visa.financial')</h3>
                                <div class="card-body">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr class="bg-light">
                                                <th>#</th>
                                                <th>@lang('customercardb1::visa.indicator')</th>
                                                <th>@lang('customercardb1::visa.actual_value')</th>
                                                <th class="score-width">@lang('customercardb1::visa.actual_score')</th>
                                                <th>@lang('customercardb1::visa.note')</th>
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
                                                <td>{{ $competency->value }}</td>
                                                <td>
                                                    <input type="number" name="technical[{{ $competency->id }}][actual_value]" class="form-control actual-value" placeholder="@lang('customercardb1::visa.actual_value_money')" data-expected-value="{{ $competency->value }}" data-competency-score="{{ $competency->score }}" data-score-input="technical-score-{{ $competency->id }}" data-pers-input="technical-pers-{{ $competency->id }}" value="{{ $scoreData ? $scoreData->actual_value : '' }}" required>
                                                    <input type="hidden" name="technical[{{ $competency->id }}][expect_value]" value="{{ $competency->value }}">
                                                    <input type="hidden" name="technical[{{ $competency->id }}][expect_score]" value="{{ $competency->score }}">
                                                </td>
                                                <td>
                                                    <input type="text" name="technical[{{ $competency->id }}][actual_score]" class="form-control actual-score" id="technical-score-{{ $competency->id }}" placeholder="@lang('customercardb1::visa.actual_score')" readonly required value="{{ $scoreData ? $scoreData->actual_score : '' }}">
                                                </td>
                                                <td>
                                                    <textarea name="technical[{{ $competency->id }}][note]" class="form-control" placeholder="@lang('customercardb1::visa.note')">{{ $scoreData ? $scoreData->note : '' }}</textarea>
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
                            <button type="submit" class="btn btn-success">@lang('customercardb1::visa.submit')</button>
                        </div>
                    </div>

                    {!! Form::close() !!}
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript for form submission and score calculation -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get references to DOM elements (with null checks)
        const actualValueInputs = document.querySelectorAll('.actual-value');
        const indicatorSelect = document.getElementById('indicatorSelect');
        const contactSelect = document.getElementById('contactSelect');
        const appraisalMonthInput = document.getElementById('appraisal_month');
        const goButton = document.getElementById('goButton');
        const appraisalForm = document.getElementById('appraisalForm');
        const submitForm = document.getElementById('submitForm');

        // Function to calculate and update the score and percentage
        function calculateScoreAndPercentage(input) {
            const rawValue = input.value;
            const expectedValue = parseFloat(input.getAttribute('data-expected-value')) || 0;
            const competencyScore = parseFloat(input.getAttribute('data-competency-score')) || 0;
            const scoreInputId = input.getAttribute('data-score-input');
            const persInputId = input.getAttribute('data-pers-input');

            const scoreInput = document.getElementById(scoreInputId);
            const persInput = document.getElementById(persInputId);

            // If no actual value provided or expected is not positive, clear outputs
            if (rawValue === '' || isNaN(parseFloat(rawValue)) || expectedValue <= 0) {
                if (scoreInput) scoreInput.value = '';
                if (persInput) persInput.value = '';
                return;
            }

            const actualValue = parseFloat(rawValue);

            let score = (actualValue * competencyScore) / expectedValue;
            let percentage = (actualValue / expectedValue) * 100;

            const formattedScore = Number.isInteger(score) ? score : score.toFixed(1);
            const formattedPercentage = Number.isInteger(percentage) ? percentage : percentage.toFixed(1);

            if (scoreInput) scoreInput.value = formattedScore;
            if (persInput) persInput.value = `${formattedPercentage}%`;
        }

        // Apply calculations to existing inputs
        actualValueInputs.forEach(function(input) {
            calculateScoreAndPercentage(input);

            input.addEventListener('input', function() {
                calculateScoreAndPercentage(input);
            });
        });

        // Handle indicator change - only reset date field
        if (indicatorSelect) {
            indicatorSelect.addEventListener('change', function() {
                if (appraisalMonthInput) {
                    appraisalMonthInput.value = '';
                }
            });
        }

        // Handle Go button click
        if (goButton && appraisalForm) {
            goButton.addEventListener('click', function() {
                if (contactSelect) {
                    const contactHiddenInput = appraisalForm.querySelector('input[name="contact"]');
                    if (!contactHiddenInput) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'contact';
                        hiddenInput.value = contactSelect.value;
                        appraisalForm.appendChild(hiddenInput);
                    } else {
                        contactHiddenInput.value = contactSelect.value;
                    }
                    console.log("Contact ID being submitted:", contactSelect.value);
                }

                appraisalForm.submit();
            });
        }

        // Handle submit form to ensure contact ID is included and enforce required actual_score fields
        if (submitForm) {
            submitForm.addEventListener('submit', function(e) {
                const contactIdInput = document.querySelector('input[name="contact"]');
                if (contactSelect && contactIdInput) {
                    contactIdInput.value = contactSelect.value;
                }

                // Ensure that when any actual_value is empty, the related actual_score remains empty
                document.querySelectorAll('.actual-value').forEach(function(input) {
                    const scoreInputId = input.getAttribute('data-score-input');
                    const scoreInput = document.getElementById(scoreInputId);
                    if (scoreInput && (input.value === '' || isNaN(parseFloat(input.value)))) {
                        scoreInput.value = '';
                    }
                });

                // Explicitly validate all required actual_score fields since some browsers ignore required on readonly
                const scoreInputs = Array.from(document.querySelectorAll('.actual-score[required]'));
                const firstEmpty = scoreInputs.find(function(el) { return (el.value || '').trim() === ''; });
                if (firstEmpty) {
                    e.preventDefault();
                    // Try to show a validation message and focus the paired actual_value
                    firstEmpty.setCustomValidity('This actual score is required');
                    // Find paired actual value input by reversing the id mapping
                    const relatedActualValue = Array.from(document.querySelectorAll('.actual-value')).find(function(iv) {
                        return iv.getAttribute('data-score-input') === firstEmpty.id;
                    });
                    if (relatedActualValue) {
                        relatedActualValue.focus();
                    } else {
                        firstEmpty.reportValidity();
                        firstEmpty.focus();
                    }
                    // Clear custom validity for future attempts
                    setTimeout(function(){ firstEmpty.setCustomValidity(''); }, 0);
                }
            });
        }
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endpush