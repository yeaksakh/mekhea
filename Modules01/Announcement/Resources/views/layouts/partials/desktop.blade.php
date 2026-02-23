@if (auth()->user()->can('announcement.view_announcement'))
    <div class="home-grid-tile" data-key="announcement-dashboard">
        <a href="{{ action([\Modules\Announcement\Http\Controllers\AnnouncementController::class, 'index']) }}"
            title="{{ __('announcement::lang.announcement') }}">
            <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/announcement.svg') }}" 
                 class="home-icon" 
                 alt="">
            <span class="home-label">{{ __('announcement::lang.announcement') }}</span>
        </a>
    </div>
@endif
