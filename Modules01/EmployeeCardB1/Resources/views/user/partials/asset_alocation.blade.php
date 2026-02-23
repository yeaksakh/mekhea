<!-- Using company asset -->
<div class="row no-print" id="asset-print" style="margin-bottom: 20px; page-break-inside: avoid;">
    <h2 style="border-bottom: 2px solid #333; padding-bottom: 5px; color: #333; font-size: 18px;">
        @lang('user.using_company_asset')
        <button onclick="printAssetSection()" class="btn btn-secondary no-print">
            <i class="fas fa-print"></i> @lang('user.print')
        </button>
    </h2>

    <div class="cv-header" style="text-align: center; margin-bottom: 30px;">
        <h1 style="margin-bottom: 5px; color: #333; font-size: 28px;">{{ $user->first_name }} {{ $user->last_name }}
        </h1>
        <p><strong>@lang('lang_v1.name_in_khmer'):</strong> <span
                style="<?php echo isIncomplete($user->name_in_khmer) ? 'color: red;' : ''; ?>">{{ $user->name_in_khmer ?? __('user.no_data') }}</span>
        </p>
    </div>

    @if($user->assets && $user->assets->count() > 0)
        <div style="margin-top: 15px; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead>
                    <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 10px; text-align: left; min-width: 120px;">@lang('user.asset_category')
                        </th>
                        <th style="padding: 10px; text-align: left; min-width: 120px;">@lang('user.asset_name')</th>
                        <th style="padding: 10px; text-align: center; width: 80px;">@lang('user.quantity')</th>
                        <th style="padding: 10px; text-align: left; min-width: 100px;">@lang('user.series_model')
                        </th>
                        <th style="padding: 10px; text-align: left; width: 100px;">@lang('user.purchase_date')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($user->assets as $asset)
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td
                                        style="padding: 10px; {{ !isset($asset->category_name) && !isset($asset->category) ? 'color: red;' : '' }}">
                                        @if(isset($asset->category_name))
                                            {{ Str::limit($asset->category_name, 20) }}
                                        @elseif(isset($asset->category))
                                            {{ Str::limit($asset->category, 20) }}
                                        @else
                                            {{ __('user.no_data') }}
                                        @endif
                                    </td>
                                    <td style="padding: 10px; {{ is_null($asset->name) ? 'color: red;' : '' }}">
                                        @php
                                            $isUrl = false;
                                            $url = $asset->name;

                                            // Check if it's a valid URL
                                            if (filter_var($url, FILTER_VALIDATE_URL)) {
                                                $isUrl = true;
                                            } elseif (Str::startsWith($url, ['http://', 'https://', 'www.'])) {
                                                $url = Str::startsWith($url, 'www.') ? 'https://' . $url : $url;
                                                $isUrl = true;
                                            } elseif (Str::contains($url, 'facebook.com')) {
                                                $url = 'https://' . $url;
                                                $isUrl = true;
                                            }
                                        @endphp

                                        @if($isUrl)
                                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                                                style="color: #0066cc; text-decoration: none; word-break: break-all;"
                                                title="{{ $asset->name }}">
                                                {{ Str::limit(preg_replace('/^https?:\/\/(www\.)?/', '', $url), 30) }}
                                            </a>
                                        @else
                                            {{ Str::limit($asset->name, 30) }}
                                        @endif
                                    </td>
                                    <td style="padding: 10px; text-align: center; {{ is_null($asset->quantity) ? 'color: red;' : '' }}">
                                        {{ number_format($asset->quantity, 0) ?? __('user.no_data') }}
                                    </td>
                                    <td style="padding: 10px; {{ is_null($asset->model) ? 'color: red;' : '' }}">
                                        @if(!is_null($asset->model) && $asset->model !== 'user.no_data')
                                                        @php
                                                            $model = $asset->model;
                                                            $isModelUrl = false;
                                                            $modelUrl = $model;

                                                            // Check if it's a URL
                                                            if (filter_var($model, FILTER_VALIDATE_URL)) {
                                                                $isModelUrl = true;
                                                            } elseif (Str::startsWith($model, ['http://', 'https://', 'www.'])) {
                                                                $modelUrl = Str::startsWith($model, 'www.') ? 'https://' . $model : $model;
                                                                $isModelUrl = true;
                                                            } elseif (Str::contains($model, ['.com', '.net', '.org', 'facebook.com'])) {
                                                                $modelUrl = 'https://' . $model;
                                                                $isModelUrl = true;
                                                            }
                                                        @endphp

                                                        @if($isModelUrl)
                                                            <a href="{{ $modelUrl }}" target="_blank" rel="noopener noreferrer"
                                                                style="color: #0066cc; text-decoration: none; word-break: break-all;" title="{{ $model }}">
                                                                {{ Str::limit(preg_replace('/^https?:\/\/(www\.)?/', '', $modelUrl), 20) }}
                                                            </a>
                                                        @else
                                                            {{ Str::limit($model, 20) }}
                                                        @endif
                                        @else
                                            {{ __('user.no_data') }}
                                        @endif
                                    </td>
                                    <td style="padding: 10px; {{ is_null($asset->purchase_date) ? 'color: red;' : '' }}">
                                        @if($asset->purchase_date && $asset->purchase_date !== 'user.no_data')
                                            {{ \Carbon\Carbon::parse($asset->purchase_date)->format('d-m-Y') }}
                                        @else
                                            {{ __('user.no_data') }}
                                        @endif
                                    </td>
                                </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p style="margin-top: 10px; color: #666;">@lang('user.no_assets_assigned')</p>
    @endif
</div>


<script>
    function printAssetSection() {
        var printContents = document.getElementById('asset-print').innerHTML;
        var originalContents = document.body.innerHTML;
        
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload();
    }
    
    function printJobDescription() {
        var printContents = document.getElementById('job-description-print').innerHTML;
        var originalContents = document.body.innerHTML;
        
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload();
    }
    </script>


<style>
    /* Hide print-only elements by default */
    .print-only {
        display: none;
    }
    
    /* Show print-only elements when printing */
    @media print {
        .print-only {
            display: block !important;
        }
        .no-print {
            display: none !important;
        }
    }
</style>