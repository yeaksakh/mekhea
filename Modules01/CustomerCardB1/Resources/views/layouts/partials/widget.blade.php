<div class="tw-my-2 lg:tw-col-span-1 tw-bg-white tw-rounded-xl tw-ring-1 tw-ring-gray-200">
    <div class="box-header with-border" style="cursor: pointer;">
        <h3 class="box-title tw-pt-2 tw-pb-2 tw-pl-2">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapseFilter" style="color: black; display: flex; align-items: center;">
                @if (!empty($icon))
                    {!! $icon !!}
                @else
                    <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                        <path d="M5 12L5 4" stroke="#222222" stroke-linecap="round"></path>
                        <path d="M19 20L19 17" stroke="#222222" stroke-linecap="round"></path>
                        <path d="M5 20L5 16" stroke="#222222" stroke-linecap="round"></path>
                        <path d="M19 13L19 4" stroke="#222222" stroke-linecap="round"></path>
                        <path d="M12 7L12 4" stroke="#222222" stroke-linecap="round"></path>
                        <path d="M12 20L12 11" stroke="#222222" stroke-linecap="round"></path>
                        <circle cx="5" cy="14" r="2" stroke="#222222" stroke-linecap="round"></circle>
                        <circle cx="12" cy="9" r="2" stroke="#222222" stroke-linecap="round"></circle>
                        <circle cx="19" cy="15" r="2" stroke="#222222" stroke-linecap="round"></circle>
                    </svg>
                @endif
                <span class="tw-ml-2">{{ $title ?? '' }}</span>
            </a>
        </h3>
    </div>
    @php
        if (isMobile()) {
            $closed = true;
        }
        $closed = true;
    @endphp
    <div id="collapseFilter" class="panel-collapse active collapse @if (empty($closed)) in @endif tw-pt-4 tw-pb-4" aria-expanded="true">
        <div class="box-body">
            {{ $slot }}
        </div>
    </div>
</div>