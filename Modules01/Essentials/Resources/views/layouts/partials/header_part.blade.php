@if ($__is_essentials_enabled && $is_employee_allowed)

    <!-- Green Clock In Button -->
    <button type="button" data-type="clock_in" data-toggle="tooltip" data-placement="bottom" title="@lang('essentials::lang.clock_in')"
        class="@if (!empty($clock_in)) hide @endif clock_in_btn tw-inline-flex tw-flex-col tw-items-center tw-justify-center tw-text-sm tw-font-medium tw-text-white tw-transition-all tw-duration-200 tw-bg-green-800 hover:tw-bg-green-700 tw-p-2 tw-rounded-lg tw-ring-1 hover:tw-text-white tw-ring-white/10">
        <div style="height: auto; width: auto; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <i class="fas fa-fingerprint shake-icon" style="font-size: 16px; color: white;"></i>
            
        </div>
    </button>

    <style>
        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-4px); }
            50% { transform: translateX(4px); }
            75% { transform: translateX(-4px); }
            100% { transform: translateX(4px); }
        }

        .shake-icon {
            animation: shake 0.5s ease-in-out infinite;
        }
    </style>

    <!-- Red Clock Out Button -->
    <button type="button" class="btn bg-red pull-left m-33 btn-sm mt-18 clock_out_btn
        @if(empty($clock_in))
            hide
        @endif
    " 
    data-type="clock_out"
    data-toggle="tooltip"
    data-placement="bottom"
    data-html="true"
    title="@lang('essentials::lang.clock_out') @if(!empty($clock_in))
                <br>
                <small>
                    <b>@lang('essentials::lang.clocked_in_at'):</b> {{@format_datetime($clock_in->clock_in_time)}}
                </small>
                <br>
                <small><b>@lang('essentials::lang.shift'):</b> {{ucfirst($clock_in->shift_name)}}</small>
                @if(!empty($clock_in->start_time) && !empty($clock_in->end_time))
                    <br>
                    <small>
                        <b>@lang('restaurant.start_time'):</b> {{@format_time($clock_in->start_time)}}<br>
                        <b>@lang('restaurant.end_time'):</b> {{@format_time($clock_in->end_time)}}
                    </small>
                @endif
            @endif" 
    style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
        <div style="height: auto; width: auto; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <i class="fas fa-fingerprint shake-icon" style="color: white; font-size: 16px;"></i>
           
        </div>
    </button>

@endif
