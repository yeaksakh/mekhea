<?php

namespace Modules\ModuleCreateModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;

class QRViewForModuleController extends Controller
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function createShow($moduleName, $title, $type)
    {
        $viewPath = base_path("Modules/{$moduleName}/Resources/views/{$moduleName}/qr_view.blade.php");
        $moduleNameLower = strtolower($moduleName);

        $fields = [];

        if ((!is_null($title) && is_array($title)) && (!is_null($type) && is_array($type))) {
            foreach ($title as $index => $fieldTitle) {
                $fieldType = $type[$index];
                $cleanFieldTitle = str_replace('_', '', $fieldTitle);

                $fields[] = $this->generateFieldHTML($fieldType, $fieldTitle, $cleanFieldTitle, $moduleNameLower, $moduleName);
            }
        }

        $fieldsString = implode("\n", $fields);

        if (!$this->files->exists($viewPath)) {
            $content = $this->generateViewTemplate($moduleNameLower, $fieldsString, $moduleName);
            $this->files->put($viewPath, $content);
        }
    }

    private function generateFieldHTML($fieldType, $fieldTitle, $cleanFieldTitle, $moduleNameLower, $moduleName)
    {
        $html = '<div class="profile-section" id="' . $fieldTitle . 'content">' . "\n";
        $html .= '    <h4>@lang(\'' . $moduleNameLower . '::lang.' . $fieldTitle . '\')</h4>' . "\n";

        switch ($fieldType) {
            case 'file':
                $html .= '    <div class="profile-image">' . "\n";
                $html .= '        <img src="{{ asset(\'uploads/' . $moduleName . '/\' . $' . $moduleNameLower . '->' . $fieldTitle . ') }}" alt="Document Image">' . "\n";
                $html .= '    </div>' . "\n";
                break;
            case 'boolean':
                $html .= '    <p>{!! $' . $moduleNameLower . '->' . $fieldTitle . ' == 1 ? __(\'' . $moduleNameLower . '::lang.yes\') : __(\'' . $moduleNameLower . '::lang.no\') !!}</p>' . "\n";
                break;
            case 'users':
                $html .= '    <p>{{ $' . $moduleNameLower . '->' . $cleanFieldTitle . '->first_name . \' \' . $' . $moduleNameLower . '->' . $cleanFieldTitle . '->last_name }}</p>' . "\n";
                break;
            case 'supplier':
                $html .= '    <p>{{ $' . $moduleNameLower . '->' . $cleanFieldTitle . '->supplier_business_name }}</p>' . "\n";
                break;
            case 'departments':
            case 'designations':
            case 'customer':
            case 'lead':
            case 'product':
                $html .= '    <p>{{ $' . $moduleNameLower . '->' . $cleanFieldTitle . '->name }}</p>' . "\n";
                break;
            default:
                $html .= '    <p>{{ $' . $moduleNameLower . '->' . $fieldTitle . ' }}</p>' . "\n";
                break;
        }

        $html .= '</div>';
        return $html;
    }

    private function generateViewTemplate($moduleNameLower, $fieldsString, $moduleName)
    {
        return <<<EOT
<div class="container">
    <!-- Header Section -->
    <div class="header-section">
        <div class="header-content">
            <h2 class="header-title">@lang('{$moduleNameLower}::lang.details')</h2>
            <div class="filter-section">
                <button class="filter-button tw-dw-btn tw-dw-btn-primary tw-text-white no-print" onclick="toggleHideShow()">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <div class="hide-show no-print" style="display: none;">
                    <div class="checkbox-grid">
                        @foreach (\$checkboxes as \$checkbox)
                        <div class="checkbox-item">
                            <div class="form-group">
                                <input
                                    type="checkbox"
                                    id="hide-show-checkbox-{{ \$loop->index }}"
                                    onclick="toggleCheckboxContent('{{ \$checkbox['id'] }}', this.checked)"
                                    checked>
                                <label for="hide-show-checkbox-{{ \$loop->index }}" class="form-check-label">
                                    @lang(\$checkbox['label'])
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Card -->
    <div class="profile-card" id="print-content">
        <!-- Category Section -->
        <div class="profile-section" id="categorycontent">
            @if(\${$moduleNameLower}->category)
            @php
            \$imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
            \$fileExtension = strtolower(pathinfo(\${$moduleNameLower}->category->image, PATHINFO_EXTENSION));
            @endphp

            @if(in_array(\$fileExtension, \$imageExtensions))
            <div class="profile-image">
                <img src="{{ asset('uploads/{$moduleName}Category/' . basename(\${$moduleNameLower}->category->image)) }}" alt="Document Image">
            </div>
            @elseif(\$fileExtension === 'pdf')
            <div class="profile-image">
                <a href="{{ asset('uploads/{$moduleName}Category/' . basename(\${$moduleNameLower}->category->image)) }}" target="_blank">
                    <i class="fas fa-file-pdf"></i>
                </a>
            </div>
            @endif
            <div class="profile-info">
                <h3>{{ \${$moduleNameLower}->category->name }}</h3>
                <p>{!! \${$moduleNameLower}->category->description !!}</p>
            </div>
            @endif
        </div>
        $fieldsString
        <!-- Additional Sections -->
        <div class="profile-section" id="createdbycontent">
            <h4>@lang('silentb11::lang.created_by')</h4>
            <p>{{ \$name }}</p>
        </div>

        <div class="profile-section" id="createdatcontent">
            <h4>@lang('silentb11::lang.created_at')</h4>
            <p>{{ \${$moduleNameLower}->created_at }}</p>
        </div>

        <!-- QR Code Section -->
        <div class="profile-section" id="qrcontent">
            <h4>@lang('{$moduleNameLower}::lang.qrcode')</h4>
            <div class="qrcode">{!! \$qrcode !!}</div>
        </div>
    </div>

    <!-- Footer Section -->
    <div class="footer-section no-print">
        <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white" aria-label="Print" onclick="handlePrint()">
            <i class="fa fa-print"></i> @lang('messages.print')
        </button>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/printThis/1.15.0/printThis.min.js"></script>

<script>
function handlePrint() {
    $('#print-content').printThis({
        importCSS: true,
        importStyle: true,
        removeScripts: true,
        header: null,
        footer: null,
        beforePrint: function() {
            document.querySelectorAll('.no-print').forEach(elem => {
                elem.style.display = 'none';
            });
        },
        afterPrint: function() {
            document.querySelectorAll('.no-print').forEach(elem => {
                elem.style.display = '';
            });
        }
    });
}

function toggleCheckboxContent(contentId, isChecked) {
    const content = document.getElementById(contentId);
    if (!content) {
        console.warn(`Element with ID "\${contentId}" not found.`);
        return;
    }
    content.style.display = isChecked ? 'block' : 'none';
}

function toggleHideShow() {
    const hideShowSection = document.querySelector('.hide-show');
    if (hideShowSection) {
        if (hideShowSection.style.display === 'none' || !hideShowSection.style.display) {
            hideShowSection.style.display = 'block';
            setTimeout(() => {
                hideShowSection.style.opacity = '1';
                hideShowSection.style.transform = 'translateY(0)';
            }, 10);
        } else {
            hideShowSection.style.opacity = '0';
            hideShowSection.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                hideShowSection.style.display = 'none';
            }, 300);
        }
    }
}
</script>

<style>
    .container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .header-section {
        background-color: #ffffff;
        padding: 20px;
        border-bottom: 1px solid #e0e0e0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
    }

    .header-title {
        margin: 0;
        font-size: 24px;
        font-weight: 600;
        color: #333;
    }

    .filter-section {
        position: relative;
    }

    .filter-button {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        transition: background-color 0.3s ease;
    }

    .filter-button:hover {
        background-color: #0056b3;
    }

    .checkbox-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        position: absolute;
        top: 50px;
        right: 0;
        z-index: 1000;
        width: 300px;
    }

    .profile-card {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .profile-section {
        margin-bottom: 20px;
    }

    .profile-section h3,
    .profile-section h4 {
        margin: 0 0 10px;
        color: #333;
    }

    .profile-section p {
        margin: 0;
        color: #666;
    }

    .profile-image img {
        max-width: 100px;
        height: auto;
        border-radius: 50%;
    }

    .profile-image .fa-file-pdf {
        font-size: 50px;
        color: #dc3545;
    }

    .qrcode img {
        max-width: 150px;
        height: auto;
    }

    .footer-section {
        margin-top: 20px;
        text-align: right;
    }

    @media print {
        .no-print {
            display: none !important;
        }
        
        .container {
            margin: 0;
            padding: 0;
            box-shadow: none;
        }
        
        .profile-card {
            box-shadow: none;
        }
        
        .profile-section {
            page-break-inside: avoid;
        }
    }
</style>
EOT;
    }
}