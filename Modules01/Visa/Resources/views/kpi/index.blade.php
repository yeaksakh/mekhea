@extends('layouts.app')
@section('title', __('visa::lang.visa'))
@section('content')
    @includeIf('visa::layouts.nav')
    <section class="content-header no-print">
        <h1>@lang('visa::lang.visa')</h1>
    </section>
    <section class="content no-print">
       
        @component('components.widget', ['class' => 'box-primary', 'title' => __('ddb11::lang.all_DdB11')])
            
            <table class="table table-bordered table-striped" id="DdB11_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('ddb11::lang.title')</th>
                        <th>@lang('ddb11::lang.category')</th>
                        <th>@lang('ddb11::lang.create_by')</th>
                        
                        
                        
                        
                        
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        @endcomponent

    </section>
    <div class="modal fade DdB11_modal" tabindex="-1" role="dialog" aria-labelledby="createDdB11ModalLabel" aria-hidden="true"></div>
@stop