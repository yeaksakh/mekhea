@inject('request', 'Illuminate\Http\Request')
<!-- Main Header -->
<header class="main-header no-print">
    <a href="{{ action([\Modules\Crm\Http\Controllers\DashboardController::class, 'index']) }}" class="logo">
        <span class="logo-lg">{{ Session::get('business.name') }}</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button with blue window-style icon -->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
           <img src="{{ !empty($business->logo) ? asset('/Uploads/business_logos/' . $business->logo) : asset('public/icons/' . (session('business.icon_pack') ?? 'v1') . '/header/plus.svg') }}" 
     alt="{{ $business->name ?? 'Business' }}" 
     style="height: 24px; width: 24px;">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                @include('layouts.partials.header-notifications')
                
                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                       <img title="Profile" src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/header/profile.svg') }}" width="24" height="24" alt="">
                    </a>
                    <ul class="dropdown-menu" style="padding: 20px; min-width: 200px;">
                        <li style="margin-bottom: 10px;">
                            <a href="{{ action([\Modules\Crm\Http\Controllers\ManageProfileController::class, 'getProfile']) }}" class="dropdown-link">
                                <img src="{{ !empty($business->logo) ? asset('/Uploads/business_logos/' . $business->logo) : asset('public/icons/' . (session('business.icon_pack') ?? 'v1') . '/header/profile.svg') }}" 
                                    alt="" class="icon">
                                <span class="dropdown-text">@lang('lang_v1.profile')</span>
                            </a>
                        </li>

                        <!-- General Section -->
                        <li class="dropdown-header">General</li>
                        <li>
                            <a href="#" class="dropdown-link">
                                <span class="dropdown-text">Help center</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="dropdown-link">
                                <span class="dropdown-text">Arimako for business</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="dropdown-link">
                                <span class="dropdown-text">Terms & policies</span>
                            </a>
                        </li>
                        <!-- End General Section -->

                        <li class="divider"></li>

                        <li>
                            <a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'logout']) }}" class="dropdown-link">
                                <img src="{{ !empty($business->logo) ? asset('/Uploads/business_logos/' . $business->logo) : asset('public/icons/' . (session('business.icon_pack') ?? 'v1') . '/header/profile.svg') }}" 
                                    alt="" class="icon">
                                <span class="dropdown-text">@lang('lang_v1.sign_out')</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize sidebar toggle
    const sidebarToggle = document.querySelector('[data-toggle="push-menu"]');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            const body = document.querySelector('body');
            if (body) {
                body.classList.toggle('sidebar-collapse');
                body.classList.toggle('sidebar-open');
            }
        });
    }
});
</script>

<style>
.dropdown-link {
    display: flex;
    align-items: center; /* Ensures icon and text are vertically centered in one row */
    text-decoration: none; /* Remove underline from link */
    padding: 12px; /* Increased padding for larger appearance */
    border-radius: 5px; /* Rounded corners for button-like appearance */
    white-space: nowrap; /* Prevents text from wrapping to ensure single row */
}

.dropdown-link .icon {
    margin-right: 15px; /* Maintain increased space between icon and text */
    display: inline-block; /* Ensures icon stays inline */
}

.dropdown-link .dropdown-text {
    font-size: 14px; /* Text size */
    display: inline-block; /* Ensures text stays inline */
}



.dropdown-menu li {
    margin-bottom: 30px; /* Spacing between dropdown items */
}

.dropdown-menu li:last-child {
    margin-bottom: 30; /* Remove margin from last item */
}
</style>
