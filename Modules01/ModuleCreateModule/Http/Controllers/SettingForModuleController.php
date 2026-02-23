<?php

namespace Modules\ModuleCreateModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;

class SettingForModuleController extends Controller
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function createSetting($moduleName)
    {
        $viewPath = base_path("Modules/{$moduleName}/Resources/views/{$moduleName}/setting.blade.php");
        $moduleNameLower = strtolower($moduleName);

        if (!$this->files->exists($viewPath)) {
            $content = <<<EOT
            @extends('layouts.app')

            @section('title', __('{$moduleNameLower}::lang.{$moduleName}'))

            @section('content')
            @includeIf('{$moduleNameLower}::layouts.nav')

            <section class="content-header no-print">
                <h1>@lang('{$moduleNameLower}::lang.{$moduleName}')</h1>
            </section>
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#permission_settings" data-toggle="tab" aria-expanded="true">
                                        <i class="fa fa-users"></i>
                                        @lang('{$moduleNameLower}::lang.permission_settings')
                                    </a>
                                </li>
                                
                                <li>
                                    <a href="#language_keys" data-toggle="tab" aria-expanded="false">
                                        <i class="fas fa-language"></i>
                                        @lang('{$moduleNameLower}::lang.update_language')
                                    </a>
                                </li>
                                <li>
                                    <a href="#telegram_setting" data-toggle="tab" aria-expanded="false">
                                        <i class="fab fa-telegram"></i>
                                        @lang('{$moduleNameLower}::lang.telegram')
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <!-- Permission Settings Tab -->
                                <div class="tab-pane active" id="permission_settings">
                                    {!! Form::open(['route' => '{$moduleName}.permission', 'method' => 'post']) !!}
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h3> @lang('{$moduleNameLower}::lang.permission_settings')</h3>
                                                <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-primary " id="select-all">
                                                    @lang('lang_v1.select_all')
                                                </button>
                                                <hr style="margin: 10px 0 10px 0;">
                                                @foreach (\$roles as \$role)
                                                    <div class="form-check mb-2">
                                                        <label>
                                                            {!! Form::checkbox('roles[]', \$role->id, in_array(\$role->id, \$rolePermissions), [
                                                                'class' => 'role-checkbox',
                                                                'id' => 'role_' . \$role->id,
                                                            ]) !!}
                                                            {{ \$role->name }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <hr style="margin: 10px 0 10px 0;">
                                        <div class="form-group mt-3 text-center">
                                            <button type="submit" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full ">@lang('messages.update')</button>
                                        </div>
                                    {!! Form::close() !!}
                                </div>

                                <!-- Telegram Keys Tab -->
                                <div class="tab-pane" id="telegram_setting">
                                    <hr>
                                    <h1>Telegram Alert Settings</h1>
                                    <hr>
                                    @if (session('success'))
                                    <p class="text-success">{{ session('success') }}</p>
                                    @endif

                                    @if (isset(\$error))
                                    <p class="text-danger">{{ \$error }}</p>
                                    @else
                                    <form method="POST" action="{{ route('{$moduleName}.update-social') }}">
                                        @csrf
                                        <div class="form-group row align-items-center mb-3">

                                            <div class="col-sm-6">
                                                <label for="social_token" class="col-sm-12 col-form-label font-weight-bold">Telegram Bot Token:</label>
                                                <input type="text" class="form-control" id="social_token"
                                                    placeholder="7978956020:AAG4JAzdlE1MxnuMGhBtHuT8jLZ3a38UEQY" value="{{ \${$moduleName}->social_token ?? '' }}"
                                                    name="social_token">
                                                <small class="form-text text-muted">Token used for Telegram API.</small>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="social_id" class="col-sm-12 col-form-label font-weight-bold">Telegram Group ID:</label>
                                                <input type="text" class="form-control" id="social_id"
                                                    placeholder="-1002332935570" value="{{ \${$moduleName}->social_id ?? '' }}"
                                                    name="social_id">
                                                <small class="form-text text-muted">Your Telegram numeric ID.</small>
                                            </div>
                                        </div>
                                        <input type="hidden" name="social_type" value="telegram">
                                        <div class="form-group row align-items-center">

                                            <div class="col-sm-6">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="social_status" name="social_status"
                                                        value="1" {{ optional(\${$moduleName})->social_status ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="social_status">
                                                        Enable Notifications
                                                    </label>
                                                    <small class="form-text text-muted">Check to enable Telegram Alert</small>
                                                </div>
                                            </div>
                                            <div class="form-group row align-items-center">
                                                <div class="col-sm-6">
                                                    <div class="form-check">
                                                        <!-- Icon Trigger for Modal -->
                                                        <span data-toggle="modal" data-target="#botCreationModal" style="cursor: pointer;">
                                                            <!-- Use a square-shaped icon with larger size -->
                                                            <i class="text-red fas fa-info-circle"></i> Info
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <!-- Modal Structure -->
                                        <div class="modal fade" id="botCreationModal" tabindex="-1" role="dialog" aria-labelledby="botCreationModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="botCreationModalLabel">Creating a Bot using BotFather</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <!DOCTYPE html>

                                                        <p style="color: blue; font-size: 24px; font-weight: bold; text-align: center;">
                                                            ការណែនាំបង្កើត Telegram Bot និងរក User ID
                                                        </p>
                                                        <hr>
                                                        <p style="background-color: blue; color: white; padding: 10px; border-radius: 5px;">
                                                            1. ដំណាក់កាលក្នុងការបង្កើត Telegram Bot:
                                                        </p>
                                                        <br>
                                                        <p style="margin-left: 20px;">1. ដំណាក់កាលទី 1៖ បើក BotFather</p>
                                                        <p style="margin-left: 40px;">ទៅ Telegram និងស្វែងរក BotFather</p>
                                                        <p style="margin-left: 40px;">ចាប់ផ្តើមជជែកជាមួយ BotFather ដើម្បីចាប់ផ្តើមបង្កើត Bot ថ្មី</p>

                                                        <p style="margin-left: 20px;">2. ដំណាក់កាលទី 2៖ ចាប់ផ្តើមបង្កើត Bot</p>
                                                        <p style="margin-left: 40px;">ប្រើពាក្យគន្លឹះ /newbot ដើម្បីចាប់ផ្តើមការបង្កើត Bot ថ្មី</p>

                                                        <p style="margin-left: 20px;">3. ដំណាក់កាលទី 3៖ កំណត់ឈ្មោះ Bot របស់អ្នក</p>
                                                        <p style="margin-left: 40px;">BotFather នឹងស្នើឱ្យអ្នកបញ្ចូលឈ្មោះសម្រាប់ Bot របស់អ្នក (ឧទាហរណ៍៖ "MyLaundryBot")។</p>

                                                        <p style="margin-left: 20px;">4. ដំណាក់កាលទី 4៖ កំណត់ Username សម្រាប់ Bot</p>
                                                        <p style="margin-left: 40px;">Username ត្រូវបញ្ចប់ដោយពាក្យ "bot" (ឧទាហរណ៍៖ "MyLaundryBot" ឬ "My_Bot123")។</p>

                                                        <p style="margin-left: 20px;">5. ដំណាក់កាលទី 5៖ រក្សា API Token</p>
                                                        <p style="margin-left: 40px;">BotFather នឹងផ្ដល់ API Token ជាសារ Telegram</p>
                                                        <p style="margin-left: 40px;">រក្សា Token នេះដោយសុវត្ថិភាព ដោយវាជាចំណុចសំខាន់ក្នុងការភ្ជាប់ Bot នឹងកម្មវិធីផ្សេងៗ</p>

                                                        <br>
                                                        <p style="background-color: blue; color: white; padding: 10px; border-radius: 5px;">
                                                            2. ដំណាក់កាលក្នុងការរក Telegram User ID:
                                                        </p>
                                                        <br>

                                                        <p style="margin-left: 20px;">6. ដំណាក់កាលទី 1៖ ស្វែងរក ID Bot</p>
                                                        <p style="margin-left: 40px;">ស្វែងរក Bot ដូចជា @userinfobot ឬ @getidsbot នៅក្នុង Telegram</p>
                                                        <p style="margin-left: 40px;">ចាប់ផ្តើមជជែកជាមួយ Bot នោះ</p>

                                                        <p style="margin-left: 20px;">7. ដំណាក់កាលទី 2៖ រក User ID របស់អ្នក</p>
                                                        <p style="margin-left: 40px;">បញ្ជូនពាក្យគន្លឹះ /start ដើម្បីទទួលបាន User ID របស់អ្នក</p>
                                                        <p style="margin-left: 40px;">Bot នឹងបង្ហាញលេខ ID ជាលេខតែមួយគត់ដែលជាការចម្បងសម្រាប់ Telegram Account របស់អ្នក</p>

                                                        <p style="margin-left: 20px;">
                                                            សូមមើលនៅទីនេះ
                                                            <a href="https://yeaksa.com/yeaksa/telegramsetting" target="_blank" style="color: blue; text-decoration: underline;">
                                                                https://yeaksa.com/yeaksa/telegramsetting
                                                            </a>
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-4 text-center">
                                            <button type="submit" class="btn btn-primary btn-lg">@lang('messages.update')</button>
                                        </div>
                                    </form>

                                    @endif
                                </div>
                                <!-- Language Keys Tab -->
                                <div class="tab-pane" id="language_keys">

                                    <h1>@lang('{$moduleNameLower}::lang.language_settings')</h1>
                                    @if (session('success'))
                                        <p class="text-success">{{ session('success') }}</p>
                                    @endif
                                    @if (isset(\$error))
                                        <p class="text-danger">{{ \$error }}</p>
                                    @else
                                        <form method="POST" action="{{ route('{$moduleName}.update-language') }}">
                                            @csrf
                                            <div class="form-group">
                                                <label for="your_language">@lang('{$moduleNameLower}::lang.select_language'):</label>
                                                <select name="your_language" id="your_language" class="form-control">
                                                    @foreach (\$languages as \$code => \$language)
                                                        <option value="{{ \$code }}" {{ \$user->your_language == \$code ? 'selected' : '' }}>
                                                            {{ \$language }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group mt-3 text-center">
                                                <button type="submit" class="btn btn-primary">@lang('{$moduleNameLower}::lang.update_language')</button>
                                            </div>
                                        </form>
                                    @endif

                                    <h1>@lang('{$moduleNameLower}::lang.language_keys')</h1>
                                    @if (session('success'))
                                        <p class="text-success">{{ session('success') }}</p>
                                    @endif
                                    @if (isset(\$error))
                                        <p class="text-danger">{{ \$error }}</p>
                                    @else
                                        <form method="POST" action="{{ route('{$moduleName}.lang') }}">
                                            @csrf
                                            <table class="table border-0">
                                                <thead>
                                                    <tr class="border-0">
                                                        <th class="border-0">@lang('{$moduleNameLower}::lang.key')</th>
                                                        <th class="border-0">@lang('{$moduleNameLower}::lang.translation')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach (\$translations as \$key => \$translation)
                                                        <tr class="border-0">
                                                            <td class="border-0">{{ \$key }}</td>
                                                            <td class="border-0">
                                                                <input type="text" name="{{ \$key }}" value="{{ \$translation }}" class="form-control" style="width: 300px;">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                           
                                            <div class="form-group mt-3 text-center">
                                                <button type="submit" the class="btn btn-primary">@lang('{$moduleNameLower}::lang.save_translations')</button>
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
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selectAllBtn = document.getElementById('select-all');
                const roleCheckboxes = document.getElementsByClassName('role-checkbox');
                let isAllSelected = false;

                selectAllBtn.addEventListener('click', function() {
                    isAllSelected = !isAllSelected; // Toggle the state
                    
                    for (let checkbox of roleCheckboxes) {
                        checkbox.checked = isAllSelected;
                    }
                });
            });
            </script>

            EOT;
            $this->files->put($viewPath, $content);
        }
    }
}
