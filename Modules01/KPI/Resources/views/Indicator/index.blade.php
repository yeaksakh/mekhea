@extends('layouts.app')
@section('title', __('kpi::lang.kpi'))
@section('content')
@include('kpi::layouts.nav')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>            
        @lang('kpi::lang.appraisal_list')
        <!-- Add Button -->
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row mb-3">
        <div class="col-md-12 text-right mb-5" style="padding-bottom: 1rem;">
            <a href="{{ route('indicator.create') }}" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> @lang('kpi::lang.add')
            </a>
        </div>
    </div>
    
    <div class="row">
        @php
            // Group indicators by department
            $indicatorsByDepartment = $indicators->groupBy('department.name');
        @endphp

        @foreach ($indicatorsByDepartment as $departmentName => $departmentIndicators)
        <!-- Table for Each Department -->
        <div class="col-md-6">
            <div class="card shadow-lg p-4 mb-5 bg-white rounded" style="border: 1px solid #e3e3e3; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);">
                <h3 class="card-header bg-light" style="border-bottom: 2px solid #007bff;">{{ $departmentName ?? 'N/A' }}</h3> <!-- Dynamic department name -->
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="bg-light">
                                <th>#</th>
                                <th>@lang('kpi::lang.title')</th>
                                <th>@lang('kpi::lang.action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($departmentIndicators as $index => $indicator)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $indicator->title }}</td> <!-- Dynamic indicator title -->
                                <td>
                                    <!-- Actions Dropdown -->
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            @lang('kpi::lang.action')
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton" style="min-width: 160px;">
                                            <a class="dropdown-item d-flex align-items-center" href="{{ route('indicator.view', $indicator->id) }}">
                                                <i class="fas fa-eye mr-2"></i> @lang('kpi::lang.view')
                                            </a>
                                            <!-- Edit -->
                                            <a class="dropdown-item d-flex align-items-center" href="{{ route('indicator.edit', $indicator->id) }}">
                                                <i class="fas fa-pencil-alt mr-2"></i> @lang('kpi::lang.edit')
                                            </a>
                                                                                        
                                            <!-- Divider -->
                                            <div class="dropdown-divider"></div>

                                            <!-- Delete -->
                                            <form action="{{ route('indicator.delete', $indicator->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                                                    <i class="fas fa-trash mr-2"></i> @lang('kpi::lang.delete')
                                                </button>
                                            </form>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@stop