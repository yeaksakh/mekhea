@if (auth()->user()->can('announcement.view_announcement'))
    <div class="recommended-item" data-id="announcement">
        <a href="{{ action([\Modules\Announcement\Http\Controllers\AnnouncementController::class, 'index']) }}"
        class="recommended-link {{ request()->segment(2) == 'Memos' ? 'active' : '' }}">
            <img src="{{ asset('public/uploads/Announcement/icons/announcement/announcement.svg') }}"
                class="recommended-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">@lang('announcement::lang.announcement')</p>
                <p class="recommended-text text-sm text-gray-600">Announcement</p>
            </div>
        </a>
    </div>
@endif