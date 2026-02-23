    <div class="recommended-item" data-id="backup">
        <a href="{{ action([\Modules\Backup\Http\Controllers\BackupController::class, 'index']) }}"  title="{{__('backup::lang.backup')}}"
        class="recommended-link {{ request()->segment(2) == 'backup' ? 'active' : '' }}">
            <img src="{{ asset('public/uploads/Backup/1752221604_data-backup-icon.svg') }}"
                class="recommended-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">{{__('backup::lang.backup')}}</p>
                <p class="recommended-text text-sm text-gray-600">Manage {{__('backup::lang.backup')}}</p>
            </div>
        </a>
    </div>