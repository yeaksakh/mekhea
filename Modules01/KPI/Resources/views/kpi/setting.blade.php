@extends('layouts.app')

@section('title', __('ddb11::lang.DdB11'))

@section('content')
    @includeIf('ddb11::layouts.nav')

    <section class="content-header no-print">
        <h1>@lang('ddb11::lang.DdB11')</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#permission_settings" data-toggle="tab" aria-expanded="true">
                                <i class="fa fas fa-check-circle"></i>
                                @lang('ddb11::lang.permission_settings')
                            </a>
                        </li>
                        <li>
                            <a href="#language_keys" data-toggle="tab" aria-expanded="false">
                                <i class="fas fa fa-desktop"></i>
                                @lang('ddb11::lang.update_language')
                            </a>
                        </li>
                        <li>
                            <a href="#language_settings" data-toggle="tab" aria-expanded="false">
                                <i class="fas fa-language"></i>
                                @lang('ddb11::lang.language_settings')
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Permission Settings Tab -->
                        <div class="tab-pane active" id="permission_settings">
                            {!! Form::open(['route' => 'DdB11.permission', 'method' => 'post']) !!}
                                <div class="row">
                                    <div class="col-md-12">
                                        @foreach ($roles as $role)
                                            <div class="form-check mb-2">
                                                <label>
                                                    {!! Form::checkbox('roles[]', $role->id, in_array($role->id, $rolePermissions), [
                                                        'class' => 'input-icheck',
                                                        'id' => 'role_' . $role->id,
                                                    ]) !!}
                                                    {{ $role->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="form-group mt-3 text-center">
                                    <button type="submit" class="btn btn-primary">@lang('messages.update')</button>
                                </div>
                            {!! Form::close() !!}
                        </div>

                        <!-- Language Keys Tab -->
                        <div class="tab-pane" id="language_keys">
                            <h1>@lang('ddb11::lang.language_keys')</h1>

                            @if (session('success'))
                                <p class="text-success">{{ session('success') }}</p>
                            @endif

                            @if (isset($error))
                                <p class="text-danger">{{ $error }}</p>
                            @else
                                <form method="POST" action="{{ route('DdB11.lang') }}">
                                    @csrf
                                    <table class="table border-0">
                                        <thead>
                                            <tr class="border-0">
                                                <th class="border-0">@lang('ddb11::lang.key')</th>
                                                <th class="border-0">@lang('ddb11::lang.translation')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($translations as $key => $translation)
                                                <tr class="border-0">
                                                    <td class="border-0">{{ $key }}</td>
                                                    <td class="border-0">
                                                        <input type="text" name="{{ $key }}" value="{{ $translation }}" class="form-control" style="width: 300px;">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="form-group mt-3 text-center">
                                        <button type="submit" class="btn btn-primary">@lang('ddb11::lang.save_translations')</button>
                                    </div>
                                </form>
                            @endif
                        </div>

                        <!-- Language Settings Tab -->
                        <div class="tab-pane" id="language_settings">
                            <h1>@lang('ddb11::lang.language_settings')</h1>

                            @if (session('success'))
                                <p class="text-success">{{ session('success') }}</p>
                            @endif

                            @if (isset($error))
                                <p class="text-danger">{{ $error }}</p>
                            @else
                                <form method="POST" action="{{ route('DdB11.update-language') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="your_language">@lang('ddb11::lang.select_language'):</label>
                                        <select name="your_language" id="your_language" class="form-control">
                                            @foreach ($languages as $code => $language)
                                                <option value="{{ $code }}" {{ $user->your_language == $code ? 'selected' : '' }}>
                                                    {{ $language }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mt-3 text-center">
                                        <button type="submit" class="btn btn-primary">@lang('ddb11::lang.update_language')</button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
