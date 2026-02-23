<section class="no-print" style="border: 1px solid #000; margin: 16px; padding: 10px;">
    <nav style=" background-color: #ffffff; padding: 10px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
        <!-- Left side - Brand and Menu button -->
        <div style="display: flex; align-items: center; gap: 15px;">
            <button type="button" id="menuButton" style="border: 1px solid #000; background-color: #ffffff; padding: 5px 10px; cursor: pointer;" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false" onclick="handleMenuClick(this)">
                <span>Menu</span>
                <span style="display: block; width: 20px; height: 2px; background: #000; margin: 3px 0;"></span>
                <span style="display: block; width: 20px; height: 2px; background: #000; margin: 3px 0;"></span>
                <span style="display: block; width: 20px; height: 2px; background: #000; margin: 3px 0;"></span>
            </button>
            <a class="navbar-brand" href="{{action([\Modules\EmployeeTracker\Http\Controllers\EmployeeTrackerController::class, 'dashboard'])}}" style="border: 1px solid #000; padding: 10px; text-decoration: none; color: #000; font-weight: bold;">
                @lang("employeetracker::lang.dashboard")
            </a>
        </div>
        
        <!-- Right side - Navigation links -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1" style="border: 1px solid #000; padding: 5px;">
            <ul style="list-style: none; padding: 0; margin: 0; display: flex; gap: 5px; flex-wrap: wrap;">
                <li @if(request()->segment(2) == 'EmployeeTracker') class="active" @endif style="border: 1px solid #000;">
                    <a href="{{action([\Modules\EmployeeTracker\Http\Controllers\EmployeeTrackerController::class, 'index'])}}" style="display: block; padding: 8px 12px; text-decoration: none; color: #000;">
                        @lang("employeetracker::lang.employeetracker")
                    </a>
                </li>
                <li @if(request()->segment(2) == 'EmployeeTracker-categories') class="active" @endif style="border: 1px solid #000;">
                    <a href="{{action([\Modules\EmployeeTracker\Http\Controllers\EmployeeTrackerController::class, 'getCategories'])}}" style="display: block; padding: 8px 12px; text-decoration: none; color: #000;">
                        @lang("employeetracker::lang.EmployeeTracker_category")
                    </a>
                </li>
                <li @if(request()->segment(2) == 'EmployeeTracker-permission') class="active" @endif style="border: 1px solid #000;">
                    <a href="{{action([\Modules\EmployeeTracker\Http\Controllers\SettingController::class, 'showEmployeeTrackerPermissionForm'])}}" style="display: block; padding: 8px 12px; text-decoration: none; color: #000;">
                        @lang("employeetracker::lang.setting")
                    </a>
                </li>
                <li @if(request()->segment(2) == 'EmployeeTracker-permission') class="active" @endif style="border: 1px solid #000;">
                    <a href="{{action([\Modules\EmployeeTracker\Http\Controllers\ActivityFormController::class, 'indexActivityForm'])}}" style="display: block; padding: 8px 12px; text-decoration: none; color: #000;">
                    @lang("employeetracker::lang.form_employeetracker")
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</section>

<style>
    @keyframes buttonClick {
        0% {
            background-color: #ffffff;
            transform: scale(1);
        }
        50% {
            background-color: #007bff;
            transform: scale(0.9);
            box-shadow: 0 0 20px #007bff;
        }
        100% {
            background-color: #ffffff;
            transform: scale(1);
        }
    }
    
    .animate-click {
        animation: buttonClick 0.3s ease-in-out !important;
    }
</style>

<script>
    function handleMenuClick(button) {
        // Remove any existing animation class
        button.classList.remove('animate-click');
        
        // Trigger reflow to restart animation
        void button.offsetWidth;
        
        // Add animation class
        button.classList.add('animate-click');
        
        console.log('Button clicked - animation should play'); // For debugging
    }
</script>