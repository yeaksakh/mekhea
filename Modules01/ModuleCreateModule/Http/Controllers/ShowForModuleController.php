<?php

namespace Modules\ModuleCreateModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;

class ShowForModuleController extends Controller
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function createShow($moduleName, $title, $type)
    {
        // Your function implementation here
        // Example content creation code
        $viewPath = base_path("Modules/{$moduleName}/Resources/views/{$moduleName}/show.blade.php");
        $moduleNameLower = strtolower($moduleName);

        $fields = [];
        // Process title and type
        if ((!is_null($title) && is_array($title)) && (!is_null($type) && is_array($type))) {
            foreach ($title as $index => $fieldTitle) {
                $fieldType = $type[$index]; // Ensure that type is defined
                $cleanFieldTitle = str_replace('_', '', $fieldTitle);

                switch ($fieldType) {
                    case 'string':
                        $fields[] = <<<HTML
                            <div class="form-group" style="padding-top: 2rem; line-height: 1.6;">
                                <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                <p id="{$fieldTitle}" class="form-control-static" >{{ \${$moduleNameLower}->{'$fieldTitle'} }}</p>
                            </div>
                        HTML;
                        break;
                    case 'float':
                        $fields[] = <<<HTML
                            <div class="form-group" style="padding-top: 2rem; line-height: 1.6;">
                                <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                <p id="{$fieldTitle}" class="form-control-static" >{{ \${$moduleNameLower}->{'$fieldTitle'} }}</p>
                            </div>
                        HTML;
                        break;
                    case 'date':
                        $fields[] = <<<HTML
                            <div class="form-group" style="padding-top: 2rem; line-height: 1.6;">
                                <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                <p id="{$fieldTitle}" class="form-control-static" >{{ \${$moduleNameLower}->{'$fieldTitle'} }}</p>
                            </div>
                        HTML;
                        break;
                    // case 'qrcode':
                    //     $fields[] = <<<HTML
                    //         @if(\${$moduleNameLower}->{'$fieldTitle'})
                    //             <div class="form-group" style="padding-top: 2rem; line-height: 1.6;">
                    //                 <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                    //                 <div id="{$fieldTitle}" class="{$fieldTitle}">{!! \$qrcode !!}</div>
                    //                 <div id="{$fieldTitle}" class="{$fieldTitle}">
                    //                     <a href="{{ \$link }}" target="_blank"><p>Link</p></a>
                    //                 </div>
                    //             </div>
                    //         @endif
                    //     HTML;
                    //     break;
                    case 'boolean':
                        $fields[] = <<<HTML
                            <div class="col-md-6"> 
                                <div class="form-group">
                                    <div class="form-check">
                                    <input class="form-check-input" 
                                            style="width: 20px; height: 20px;" 
                                            type="checkbox" 
                                            value="1" 
                                            id="{{ \${$moduleNameLower}->{$fieldTitle} }}" 
                                            name="{$fieldTitle}"
                                            {{ \${$moduleNameLower}->{'$fieldTitle'} == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{$fieldTitle}" style="line-height: 20px;"> 
                                            @lang('{$moduleNameLower}::lang.{$fieldTitle}')
                                        </label>
                                    </div>
                                </div>
                            </div>
                        HTML;
                        break;
                    case 'text':
                        $fields[] = <<<HTML
                            <div class="form-group" style="padding-top: 2rem; line-height: 1.6;">
                                <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                <p id="{$fieldTitle}" class="form-control-static" >{!! \${$moduleNameLower}->{'$fieldTitle'} !!}</p>
                            </div>
                        HTML;
                        break;
                    case 'file':
                        $fields[] = <<<HTML
                            @if(\${$moduleNameLower}->{'$fieldTitle'})
                                @php
                                    \$imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                                    \$fileExtension = strtolower(pathinfo(\${$moduleNameLower}->{'$fieldTitle'}, PATHINFO_EXTENSION));
                                    \$filePath = 'uploads/{$moduleName}/' . basename(\${$moduleNameLower}->{'$fieldTitle'});
                                @endphp

                                <div class="mt-3">
                                    @if(in_array(\$fileExtension, \$imageExtensions))
                                        <img src="{{ asset(\$filePath) }}" 
                                            alt="Document Image" 
                                            class="mt-2"
                                            style="max-width: 100px;">
                                    @elseif(\$fileExtension === 'pdf')
                                        <a href="{{ asset(\$filePath) }}" 
                                        target="_blank">
                                            <iframe 
                                                src="{{ asset(\$filePath) }}#toolbar=0&navpanes=0&scrollbar=0"
                                                width="100%"
                                                height="600px"
                                                frameborder="0"
                                                class="pdf-viewer">
                                            </iframe>
                                        </a>
                                    @endif
                                </div>
                            @endif

                        HTML;
                        break;
                    case 'users':
                        $fields[] = <<<HTML
                            <div class="form-group" style="padding-top: 2rem; line-height: 1.6;">
                                <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                <p id="{$fieldTitle}" class="form-control-static" >{{ \${$moduleNameLower}->{$cleanFieldTitle}->first_name . ' ' . \${$moduleNameLower}->{$cleanFieldTitle}->last_name }}</p>
                            </div>
                        HTML;
                        break;
                    case 'supplier':
                        $fields[] = <<<HTML
                            <div class="form-group" style="padding-top: 2rem; line-height: 1.6;">
                                <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                <p id="{$fieldTitle}" class="form-control-static" >{{ \${$moduleNameLower}->{$cleanFieldTitle}->supplier_business_name }}</p>
                            </div>
                        HTML;
                        break;
                    case 'customer':
                        $fields[] = <<<HTML
                            <div class="form-group" style="padding-top: 2rem; line-height: 1.6;">
                                <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                <p id="{$fieldTitle}" class="form-control-static" >{{ \${$moduleNameLower}->{$cleanFieldTitle}->name }}</p>
                            </div>
                        HTML;
                        break;
                    case 'lead':
                        $fields[] = <<<HTML
                            <div class="form-group" style="padding-top: 2rem; line-height: 1.6;">
                                <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                <p id="{$fieldTitle}" class="form-control-static" >{{ \${$moduleNameLower}->{$cleanFieldTitle}->name }}</p>
                            </div>
                        HTML;
                        break;
                    case 'departments':
                        $fields[] = <<<HTML
                            <div class="form-group" style="padding-top: 2rem; line-height: 1.6;">
                                <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                <p id="{$fieldTitle}" class="form-control-static" >{{ \${$moduleNameLower}->{$cleanFieldTitle}->name }}</p>
                            </div>
                        HTML;
                        break;
                    case 'designations':
                        $fields[] = <<<HTML
                            <div class="form-group" style="padding-top: 2rem; line-height: 1.6;">
                                <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                <p id="{$fieldTitle}" class="form-control-static" >{{ \${$moduleNameLower}->{$cleanFieldTitle}->name }}</p>
                            </div>
                        HTML;
                        break;
                    case 'product':
                        $fields[] = <<<HTML
                            <div class="form-group" style="padding-top: 2rem; line-height: 1.6;">
                                <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                <p id="{$fieldTitle}" class="form-control-static" >{{ \${$moduleNameLower}->{$cleanFieldTitle}->name }}</p>
                            </div>
                        HTML;
                        break;
                    case 'status_true_false':
                        $fields[] = <<<HTML
                            <div class="mb-4">
                                <label for="{$fieldTitle}" class="block text-sm font-medium text-gray-700">
                                    @lang('{$moduleNameLower}::lang.{$fieldTitle}'):
                                </label>
                                <p id="{$fieldTitle}" class="mt-1 text-sm text-gray-900">
                                    {!! \${$moduleNameLower}->{$fieldTitle} == 1 ? __('{$moduleNameLower}::lang.yes') : __('{$moduleNameLower}::lang.no') !!}
                                </p>
                            </div>
                        HTML;
                        break;
                    case 'status_authorize':
                        $fields[] = <<<HTML
                            <div class="form-group" style="padding-top: 2rem; line-height: 1.6;">
                                <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                <p id="{$fieldTitle}" class="form-control-static" >{!! \${$moduleNameLower}->{'$fieldTitle'} !!}</p>
                            </div>
                        HTML;
                        break;
                    case 'status_priority':
                        $fields[] = <<<HTML
                            <div class="form-group" style="padding-top: 2rem; line-height: 1.6;">
                                <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                <p id="{$fieldTitle}" class="form-control-static" >{!! \${$moduleNameLower}->{'$fieldTitle'} !!}</p>
                            </div>
                        HTML;
                        break;
                    case 'status_payment':
                        $fields[] = <<<HTML
                            <div class="form-group" style="padding-top: 2rem; line-height: 1.6;">
                                <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                <p id="{$fieldTitle}" class="form-control-static" >{!! \${$moduleNameLower}->{'$fieldTitle'} !!}</p>
                            </div>
                        HTML;
                        break;
                    
                    case 'status_delivery':
                        $fields[] = <<<HTML
                            <div class="form-group" style="padding-top: 2rem; line-height: 1.6;">
                                <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                <p id="{$fieldTitle}" class="form-control-static" >{!! \${$moduleNameLower}->{'$fieldTitle'} !!}</p>
                            </div>
                        HTML;
                        break;
                }
            }
        }
        $fieldsString = implode("\n", $fields);
        if (!$this->files->exists($viewPath)) {
            $content = <<<EOT
            
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content" style="font-family: sans-serif;">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">@lang('{$moduleNameLower}::lang.details')</h4>
                    </div>
                        
                    <div class="modal-body" id="print-content" style="background-color: #f7f5f3; padding: 2rem;">
                        <div style="background: white; max-width: 1000px; margin: auto; padding: 2rem; color: #333;">
                            <div class="{$moduleNameLower}_header">
                                <div class="{$moduleNameLower}_header-left">
                                    @if (\$businessInfo['logo_exists'])
                                    <img src="{{ \$businessInfo['logo_url'] }}" class="{$moduleNameLower}_business-logo"
                                        alt="{{ \$businessInfo['name'] ?? 'Business' }} Logo">
                                    @endif
                                    <div>
                                        <div class="{$moduleNameLower}_business-name">{{ \$businessInfo['name'] ?? 'Business Name' }}</div>
                                        <div class="{$moduleNameLower}_business-location">{{ \$businessInfo['location'] }}</div>
                                        <div class="{$moduleNameLower}_page-number" id="{$moduleNameLower}_page-display">Page 1</div>
                                    </div>
                                </div>

                                <div class="{$moduleNameLower}_header-right">
                                    <div class="{$moduleNameLower}_name">@lang('{$moduleNameLower}::lang.{$moduleNameLower}')</div>
                                    @if(!empty(\$date_range))
                                    <div class="{$moduleNameLower}_date-range"><span class="{$moduleNameLower}_bold-name">{{ \$date_range }}</span></div>
                                    @endif
                                    @if(\$print_by)
                                    <div class="{$moduleNameLower}_date-range">{{ __('Printed by') }}: <span class="{$moduleNameLower}_bold-name">{{ \$print_by }}</span></div>
                                    @endif
                                    <div class="{$moduleNameLower}_date-range">{{ __('Printed on') }}: {{ now()->setTimezone(config('app.timezone'))->format('F j, Y g:i A') }}</div>
                                </div>
                            </div>
                            <div style="padding-top: 2rem; padding-bottom: 2rem; border-top: 1px solid #eee; border-bottom: 1px solid #eee;">
                                <table style="border-collapse: collapse; width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td style="padding: 0.25rem 0; font-weight: bold; width: 80px;">From:</td>
                                            <td style="padding: 0.25rem 0;">{{ \$name }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 0.25rem 0; font-weight: bold; width: 80px;">Date:</td>
                                            <td style="padding: 0.25rem 0;">{{ \Carbon\Carbon::parse(\${$moduleNameLower}->created_at)->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 0.25rem 0; font-weight: bold; width: 80px;">Subject:</td>
                                            <td style="padding: 0.25rem 0;">{{ \$first_field }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        
                        
                        
                            <label class="form-check-label" id="categorycontent">
                            @if(\${$moduleNameLower}->category)
                                @php
                                    \$imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                                    \$fileExtension = strtolower(pathinfo(\${$moduleNameLower}->category->image, PATHINFO_EXTENSION));
                                @endphp
                                
                                @if(in_array(\$fileExtension, \$imageExtensions))
                                    <label class="form-check-label pt-2 mb-0" for="categoryCheck">
                                        <img src="{{ asset('uploads/{$moduleName}Category/' . basename(\${$moduleNameLower}->category->image)) }}" 
                                            alt="Document Image" 
                                            style="max-width: 25px; max-height: 25px; vertical-align: middle;">
                                        </label>
                                @elseif(\$fileExtension === 'pdf')
                                    <span class="me-2">
                                        <a href="{{ asset('uploads/{$moduleName}Category/' . basename(\${$moduleNameLower}->category->image)) }}" 
                                        target="_blank" 
                                        style="text-decoration: none;">
                                            <i class="fas fa-file-pdf" style="font-size: 25px; color: #dc3545;"></i>
                                        </a>
                                    </span>
                                @endif
                                <span>{{ \${$moduleNameLower}->category->name }}</span>
                                <div class="form-group" id="category-detail">
                                    <label for="categorydetail">@lang('employeecontractb1::lang.description'):</label>
                                    <p id="categorydetail" class="form-control-static">{!! \${$moduleNameLower}->category->description !!}</p>
                                </div>
                                @endif
                            </label>

                            <!-- Modal Content Goes Here -->
                            {$fieldsString}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print" aria-label="Print"
                            onclick="$('#print-content').printThis({ importCSS: true, importStyle: true });">
                            <i class="fa fa-print"></i> @lang( 'messages.print' )
                        </button>
                        <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print" data-dismiss="modal">@lang('messages.close')</button>
                    </div>
                </div>
            </div>
            <style>
            .{$moduleNameLower}_header {
                margin-bottom: 20px;
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                align-items: center;
                background-color: #f8f9fa;
                padding: 15px;
                border-radius: 4px;
                position: relative;
            }

            .{$moduleNameLower}_header-left {
                display: flex;
                align-items: center;
                flex: 1;
                z-index: 1;
            }

            .{$moduleNameLower}_header-right {
                flex: 1;
                text-align: right;
                z-index: 1;
            }

            .{$moduleNameLower}_business-logo {
                max-height: 50px;
                max-width: 50px;
                margin-right: 15px;
            }

            .{$moduleNameLower}_business-name {
                font-size: 20px;
                font-weight: 600;
            }

            .{$moduleNameLower}_business-location {
                font-size: 14px;
                color: #666;
                margin-top: 2px;
            }

            .{$moduleNameLower}_page-number {
                font-size: 12px;
                color: #666;
                margin-top: 2px;
            }

            .{$moduleNameLower}_name {
                font-size: 22px;
                font-weight: 600;
                margin-bottom: 5px;
            }

            .{$moduleNameLower}_date-range {
                font-size: 14px;
                margin-top: 5px;
            }

            .{$moduleNameLower}_bold-name {
                font-weight: bold;
            }

            .no-screen {
                display: none;
            }


            @media print {
                #{$moduleNameLower}_print-content {
                    padding: 0rem !important;
                }

                #{$moduleNameLower}_print-content>div {
                    padding: 0.25rem !important;
                }

                @page {
                    margin: 10mm;

                    @bottom-center {
                        content: "Page " counter(page) " of " counter(pages);
                        font-size: 10px;
                        color: #000;
                    }
                }

                body {
                    counter-reset: page;
                }

                a {
                    text-decoration: none;
                    color: #000;
                }

                .no-print {
                    display: none;
                }

                .{$moduleNameLower}_header {
                    margin-bottom: 16px !important;
                    display: flex !important;
                    justify-content: space-between !important;
                    align-items: center !important;
                    background-color: #f8f9fa !important;
                    padding: 12px !important;
                    position: relative !important;
                    border-radius: 0 !important;
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                    page-break-after: avoid !important;
                    width: 100% !important;
                }

                .{$moduleNameLower}_header-left {
                    display: flex !important;
                    align-items: center !important;
                    justify-content: flex-start !important;
                    text-align: left !important;
                    z-index: 1 !important;
                    flex: 1 !important;
                    overflow: hidden !important;
                    min-height: 40px !important;
                    padding: 2px 0 !important;
                }

                .{$moduleNameLower}_header-left>div {
                    text-align: left !important;
                }

                .{$moduleNameLower}_header-right {
                    flex: 1 !important;
                    text-align: right !important;
                    z-index: 1 !important;
                }

                .{$moduleNameLower}_business-logo {
                    max-height: 40px;
                    max-width: 40px;
                    margin-right: 12px;
                    width: 35px !important;
                    height: 35px !important;
                    margin-top: 2px !important;
                    margin-bottom: 2px !important;
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                    flex-shrink: 0 !important;
                    object-fit: contain !important;
                    object-position: center !important;
                    display: block !important;
                    page-break-inside: avoid !important;
                }

                .{$moduleNameLower}_business-name {
                    font-size: 12.8px;
                    font-weight: bold;
                }

                .{$moduleNameLower}_business-location {
                    font-size: 8.8px !important;
                    color: #000 !important;
                    text-align: left !important;
                    margin-top: 2px !important;
                }

                .{$moduleNameLower}_page-number {
                    font-size: 10px !important;
                    color: #000 !important;
                    text-align: left !important;
                    margin-top: 2px !important;
                    display: block !important;
                }

                .{$moduleNameLower}_name {
                    font-size: 12.8px !important;
                    font-weight: bold !important;
                    margin-bottom: 5px !important;
                    color: #000 !important;
                    text-align: right !important;
                }

                .{$moduleNameLower}_date-range {
                    font-size: 8.8px !important;
                    margin-top: 4px !important;
                    color: #000 !important;
                    text-align: right !important;
                }

                .{$moduleNameLower}_bold-name {
                    font-weight: bold !important;
                    color: #000 !important;
                }

                .no-screen {
                    display: block !important;
                }

                .page-footer {
                    position: fixed;
                    bottom: 10mm;
                    left: 0;
                    right: 0;
                    text-align: center;
                    font-size: 10px;
                    color: #000;
                    padding: 5px;
                    z-index: 999;
                }

                .page-footer:after {
                    content: "Page " counter(page);
                }
            }
        </style>
        <script>
            function updatePageCounter() {
                const pageHeight = 1056;
                const headerHeight = document.querySelector('.{$moduleNameLower}_header') ? document.querySelector('.{$moduleNameLower}_header').offsetHeight : 0;
                const contentHeight = document.body.scrollHeight;
                const estimatedPages = Math.ceil(contentHeight / pageHeight);

                const pageDisplay = document.getElementById('{$moduleNameLower}_page-display');
                if (pageDisplay) {
                    pageDisplay.textContent = estimatedPages > 1 ? 'Page 1 of ' + estimatedPages : 'Page 1';
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Update page counter
                setTimeout(function() {
                    updatePageCounter();
                }, 100);


            });

            // Update page counter on window resize
            window.addEventListener('resize', function() {
                updatePageCounter();
            });

        </script>
        EOT;
            $this->files->put($viewPath, $content);
        }
    }
}
