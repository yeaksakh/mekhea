   
    <div class="home-grid-tile" data-key="hrm">
        <a href="{{ action([\Modules\Backup\Http\Controllers\BackupController::class, 'index']) }}"  title="{{__('backup::lang.backup')}}">
            <img src="{{ asset('public/uploads/Backup/1752221604_data-backup-icon.svg') }}" class="home-icon" alt="">
            <span class="home-label">{{__('backup::lang.backup')}}</span>
        </a>
    </div>