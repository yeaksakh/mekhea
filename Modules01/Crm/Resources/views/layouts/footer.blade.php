<style>
    .jester_ecommerce_news_tabs-container {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        gap: 1rem;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #ddd;
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
    .jester_ecommerce_news_tabs-container::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Opera */
    }
    .jester_ecommerce_news_tab {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        font-weight: 500;
        color: #333;
        background-color: #f1f5f9;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
        border: 1px solid transparent;
    }
    .jester_ecommerce_news_tab:hover {
        background-color: #e2e8f0;
    }
    .jester_ecommerce_news_tab.active {
        background-color: #ff6200 !important;
        color: white !important;
        font-weight: bold !important;
    }
    .jester_ecommerce_news_grid-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    .jester_ecommerce_news_card {
        border: 1px solid #ddd;
        border-radius: 5px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: box-shadow 0.3s ease-in-out;
        background-color: #fff;
        cursor: pointer;
    }
    .jester_ecommerce_news_card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .jester_ecommerce_news_card-image {
        padding: 10px; /* Padding around the image */
    }
    .jester_ecommerce_news_card-image img {
        width: 100%;
        aspect-ratio: 1 / 1;
        object-fit: cover;
        border-radius: 5px; /* Slightly rounded corners for the image */
    }
    .jester_ecommerce_news_card-body {
        padding: 15px;
        flex-grow: 1;
    }
    .jester_ecommerce_news_card-body h4 {
        margin-top: 0;
        font-size: 1.2em;
        font-weight: 600;
    }
    .jester_ecommerce_news_card-body p {
        display: none; /* Hide description in card */
    }
    .footer-menu {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgb(230, 230, 230);
        padding: 0.5rem 0;
        box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        border-top: 1px solid #3399ff; /* បន្ទាត់ខាងលើពណ៌ខៀវ */
        box-shadow: 
            0 -2px 5px rgba(0, 0, 0, 0.1), /* ស្រមោលដើម */
            0 -3px 8px rgba(51, 153, 255, 0.2); /* ស្រមោលខៀវស្រាលៗ */
        
        border-top-left-radius: 20px; /* លើកោង */
        border-top-right-radius: 20px;
        border-bottom-left-radius: 0; /* ខាងក្រោមត្រង់ */
        border-bottom-right-radius: 0;
    }
    .footer-menu a {
        color:rgb(31, 31, 31);
        transition: color 0.3s ease;
    }
    .footer-menu a:hover {
        color: #ffffff;
    }
    .footer-menu .active {
        color: #3b82f6;
    }
    
    /* Full screen modal styles */
    .jester_ecommerce_full-screen-modal-container {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 60px; /* Adjust this value to match the height of your footer */
        background-color: #f4f4f4;
        z-index: 1001;
        transform: translateY(-100%);
        transition: transform 0.3s ease-in-out;
        overflow-y: auto;
        border-bottom-left-radius: 20px;
        border-bottom-right-radius: 20px;
    }
    .jester_ecommerce_full-screen-modal-container.active {
        transform: translateY(0);
    }
    
    /* Desktop styles for full screen modal */
    @media (min-width: 1024px) {
        .jester_ecommerce_full-screen-modal-container {
            top: 0;
            right: 0;
            left: auto;
            bottom: 0;
            width: 400px;
            transform: translateX(100%);
            border-radius: 0;
            box-shadow: -5px 0 15px rgba(0,0,0,0.1);
        }
        .jester_ecommerce_full-screen-modal-container.active {
            transform: translateX(0);
        }
    }
    
    /* Profile Details Modal Styles */
    .jester_ecommerce_profile-details-modal-container {
        position: fixed;
        bottom: 60px; /* Space for the footer */
        left: 0;
        right: 0;
        top: 0;
        background-color: white;
        z-index: 1002; /* Higher than the first modal */
        
        /* Initial state - hidden */
        transform: translateY(100%);
        opacity: 0;
        transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
        pointer-events: none; /* Prevent interaction when hidden */
        
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        border-bottom-left-radius: 20px;
        border-bottom-right-radius: 20px;
        display: flex;
        flex-direction: column;
    }
    
    .jester_ecommerce_profile-details-modal-container.active {
        /* Active state - visible */
        transform: translateY(0);
        opacity: 1;
        pointer-events: auto; /* Enable interaction when active */
    }
    
    .jester_ecommerce_profile-details-modal-container.closing {
        /* Closing state - animate back to hidden */
        transform: translateY(100%);
        opacity: 0;
        pointer-events: none;
    }
    
    /* Desktop styles for Profile Details Modal */
    @media (min-width: 1024px) {
        .jester_ecommerce_profile-details-modal-container {
            top: 0;
            right: 0;
            left: auto;
            bottom: 0;
            width: 400px; /* Same as the full screen modal */
            transform: translateX(100%);
            border-radius: 0;
            box-shadow: -5px 0 15px rgba(0,0,0,0.1);
        }
        .jester_ecommerce_profile-details-modal-container.active {
            transform: translateX(0);
        }
        .jester_ecommerce_profile-details-modal-container.closing {
            transform: translateX(100%);
        }
    }
    
    /* Modal content styles */
    .jester_ecommerce_modal-content-inner {
        padding: 1rem;
    }
    .jester_ecommerce_account-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
    }
    .jester_ecommerce_account-title {
        font-size: 1.5rem;
        font-weight: bold;
    }
    .jester_ecommerce_close-modal-btn {
        font-size: 1.5rem;
        color: #333;
        text-decoration: none;
    }
    .jester_ecommerce_profile-section {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        padding: 0.65rem; /* Increased padding */
        background-color: white;
        border-radius: 12px; /* Rounded corners */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Subtle box shadow */
        transition: transform 0.2s ease, box-shadow 0.2s ease; /* Smooth transition */
    }
    .jester_ecommerce_profile-section:hover {
        transform: translateY(-2px); /* Slight lift on hover */
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15); /* Enhanced shadow on hover */
    }
    .jester_ecommerce_profile-info {
        margin-left: 1rem;
    }
    .jester_ecommerce_profile-name {
        font-size: 1.8rem;
        font-weight: bold;
        margin: 0;
    }
    .jester_ecommerce_profile-view-link {
        text-decoration: none;
    }
    .jester_ecommerce_profile-view {
        color: #3b82f6; /* Changed to blue color */
        font-weight: 500; /* Slightly bolder */
        margin: 0;
        padding: 0.3rem 0; /* Added some padding */
        display: inline-block; /* To allow padding */
        border-bottom: 1px solid transparent; /* For underline effect */
        transition: border-color 0.3s ease;
    }
    .jester_ecommerce_profile-view:hover {
        border-bottom-color: #3b82f6; /* Underline on hover */
    }
    .jester_ecommerce_promo-banner {
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                    url('https://media.istockphoto.com/id/485371557/photo/twilight-at-spirit-island.jpg?s=612x612&w=0&k=20&c=FSGliJ4EKFP70Yjpzso0HfRR4WwflC6GKfl4F3Hj7fk=');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        position: relative;
    }
    .jester_ecommerce_promo-content h3 {
        font-size: 1.2rem;
        font-weight: bold;
        margin: 0 0 0.5rem 0;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
    }
    .jester_ecommerce_learn-more {
        color: white;
        text-decoration: none;
        font-weight: 500;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
    }
    .jester_ecommerce_grid-menu {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .jester_ecommerce_grid-item {
        background-color: white;
        padding: 1.5rem;
        border-radius: 0.5rem;
        text-align: center;
        text-decoration: none;
        color: #333;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .jester_ecommerce_grid-item i {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    .jester_ecommerce_perks-section {
        background-color: white;
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }
    .jester_ecommerce_perks-title {
        font-size: 1.2rem;
        font-weight: bold;
        margin: 0 0 1rem 0;
        color: #212529; /* Darker color for better contrast */
    }
    .jester_ecommerce_perks-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .jester_ecommerce_perks-list li a {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        text-decoration: none;
        color: #212529; /* Darker color for better readability */
        border-bottom: 1px solid #f4f4f4;
    }
    .jester_ecommerce_perks-list li:last-child a {
        border-bottom: none;
    }
    .jester_ecommerce_perks-list li a i:first-child {
        color: #6a11cb; /* Consistent purple color for icons */
    }
    .jester_ecommerce_perks-list li a i:last-child {
        color: #6c757d; /* Gray color for chevron icons */
    }
    .jester_ecommerce_perks-list li a:hover {
        color: #3b82f6; /* Blue color on hover */
    }
    .jester_ecommerce_perks-list li a:hover i:first-child {
        color: #3b82f6; /* Blue color for icons on hover */
    }
    .jester_ecommerce_perk-text {
        margin-left: 15px;
    }
    .jester_ecommerce_logout-section {
        margin-top: 1.5rem;
        text-align: center;
    }
    .jester_ecommerce_logout-button {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        width: 100%;
        background-color: red;
        color: white;
        border-radius: 0.5rem;
        text-decoration: none;
        font-weight: bold;
    }
    .jester_ecommerce_profile-details-modal-content {
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
        padding: 1rem;
    }
    .jester_ecommerce_profile-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eee;
        margin-bottom: 1rem;
        flex-shrink: 0;
    }
    .jester_ecommerce_profile-modal-title {
        font-size: 1.2rem;
        font-weight: bold;
    }
    .jester_ecommerce_profile-modal-body {
        overflow-y: auto;
        flex-grow: 1;
        padding: 1rem;
    }
</style>
<footer class="footer-menu">
    <div class="mx-auto flex justify-between items-center px-6 max-w-md">
        <a href="{{action([\Modules\Crm\Http\Controllers\DashboardController::class, 'index'])}}"  class="flex flex-col items-center flex-1 py-2">
            <i class="fas fa-home text-2xl"></i>
            <span class="text-xs">Home</span>
        </a>
        <a href="{{action([\Modules\Crm\Http\Controllers\OrderRequestController::class, 'index'])}}" class="flex flex-col items-center flex-1 py-2">
            <i class="fas fa-shopping-cart text-2xl"></i>
            <span class="text-xs">ការកម្មង់</span>
        </a>
        <a href="{{action([\Modules\Crm\Http\Controllers\ContactBookingController::class, 'index'])}}" class="flex flex-col items-center flex-1 py-2">
            <i class="fas fa-book text-2xl"></i>
            <span class="text-xs">ណាត់ជួប</span>
        </a>
        <a href="{{action([\Modules\Crm\Http\Controllers\SellController::class, 'getSellList'])}}" class="flex flex-col items-center flex-1 py-2">
            <i class="fas fa-file-invoice text-2xl"></i>
            <span class="text-xs">វិក័យបត្រ</span>
        </a>
        <a href="#" class="dropdown-toggle flex flex-col items-center flex-1 py-2" data-toggle="modal">
            <i class="fas fa-user text-2xl"></i>
            <span class="text-xs">គណនី</span>
        </a>
    </div>
</footer>
<!-- Full screen modal -->
<div id="full-screen-modal" class="jester_ecommerce_full-screen-modal-container">
    <div class="jester_ecommerce_modal-content-inner">
        <div class="jester_ecommerce_account-header">
            <span class="jester_ecommerce_account-title">Account</span>
            <a href="#" class="jester_ecommerce_close-modal-btn"><i class="fas fa-cog"></i></a>
        </div>
        
        <div class="jester_ecommerce_profile-section" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="jester_ecommerce_profile-info">
                <h2 class="jester_ecommerce_profile-name">{{ Auth::user()->last_name }} {{ Auth::user()->first_name ?? 'User' }}</h2>
                <a href="#" class="jester_ecommerce_profile-view-link" id="open-profile-modal">
                    <p class="jester_ecommerce_profile-view">View Profile</p>
                </a>
            </div>
            <div class="jester_ecommerce_profile-avatar">
                @if(!empty(auth()->user()->media))
                    <div style="padding: 5px; border: 2px solid #3b82f6; border-radius: 50%; display: inline-block; background-image: linear-gradient(to bottom, #f0f0f0, #d0d0d0);">
                        {!! auth()->user()->media->thumbnail([100, 100], 'img-circle') !!}
                    </div>
                @else
                    <div style="width: 100px; height: 100px; border-radius: 50%; background-color: #f0f0f0; display: flex; justify-content: center; align-items: center;">
                        <i class="fa fa-user" style="font-size: 50px; color: #888;"></i>
                    </div>
                @endif
            </div>
        </div>
        <div class="jester_ecommerce_promo-banner">
            <div class="jester_ecommerce_promo-content">
                <h3 class="tw-text-white">Save on your future orders with Jester</h3>
                <a href="#" class="jester_ecommerce_learn-more">Learn more <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="jester_ecommerce_grid-menu">
            <a href="{{action([\Modules\Crm\Http\Controllers\OrderRequestController::class, 'index'])}}" class="jester_ecommerce_grid-item">
                <i class="fas fa-shopping-cart"></i>
                <span>ការកម្មង់</span>
            </a>
            <a href="{{action([\Modules\Crm\Http\Controllers\ContactBookingController::class, 'index'])}}" class="jester_ecommerce_grid-item">
                <i class="fas fa-book"></i>
                <span>ណាត់ជួប</span>
            </a>
            <a href="{{action([\Modules\Crm\Http\Controllers\SellController::class, 'getSellList'])}}" class="jester_ecommerce_grid-item">
                <i class="fas fa-file-invoice"></i>
                <span>វិក័យបត្រ</span>
            </a>
            <a href="#" class="jester_ecommerce_grid-item" data-target="#notification-modal">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
            </a>
            <a href="#" class="jester_ecommerce_grid-item" style="grid-column: span 2;">
                <i class="fas fa-download"></i>
                <span>Install</span>
            </a>
        </div>
        <div class="jester_ecommerce_perks-section">
            <h3 class="jester_ecommerce_perks-title">Perks for you</h3>
            <ul class="jester_ecommerce_perks-list jester_ecommerce_perks-for-you-list">
                <li><a href="#" data-target="#become-a-pro-modal"><span><i class="fas fa-crown"></i><span class="jester_ecommerce_perk-text">Become a pro</span></span><i class="fas fa-chevron-right"></i></a></li>
                <li><a href="#" data-target="#vouchers-modal"><span><i class="fas fa-tags"></i><span class="jester_ecommerce_perk-text">Vouchers</span></span><i class="fas fa-chevron-right"></i></a></li>
                <li><a href="#" data-target="#arimako-rewards-modal"><span><i class="fas fa-award"></i><span class="jester_ecommerce_perk-text">Arimako rewards</span></span><i class="fas fa-chevron-right"></i></a></li>
                <li><a href="#" data-target="#invite-friends-modal"><span><i class="fas fa-gift"></i><span class="jester_ecommerce_perk-text">Invite friends</span></span><i class="fas fa-chevron-right"></i></a></li>
            </ul>
            <h3 class="jester_ecommerce_perks-title" style="margin-top: 1.5rem;">General</h3>
            <ul class="jester_ecommerce_perks-list jester_ecommerce_general-list">
                <li><a href="#" data-target="#help-center-modal"><span><i class="fas fa-question-circle"></i><span class="jester_ecommerce_perk-text">Help center</span></span><i class="fas fa-chevron-right"></i></a></li>
                <li><a href="#" data-target="#arimako-for-business-modal"><span><i class="fas fa-briefcase"></i><span class="jester_ecommerce_perk-text">Arimako for business</span></span><i class="fas fa-chevron-right"></i></a></li>
                <li><a href="#" data-target="#terms-and-policies-modal"><span><i class="fas fa-file-contract"></i><span class="jester_ecommerce_perk-text">Terms & policies</span></span><i class="fas fa-chevron-right"></i></a></li>
            </ul>
        </div>
        <div class="jester_ecommerce_logout-section">
            <a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'logout']) }}" class="jester_ecommerce_logout-button">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</div>
<!-- Profile Details Modal -->
<div id="profile-details-modal" class="jester_ecommerce_profile-details-modal-container">
    <div class="jester_ecommerce_profile-details-modal-content">
        @include('crm::layouts.edit_profile_modal')
    </div>
</div>

<!-- Perks for you Modals -->
<div id="become-a-pro-modal" class="jester_ecommerce_profile-details-modal-container">
    <div class="jester_ecommerce_profile-details-modal-content">
        <div class="jester_ecommerce_profile-modal-header">
            <h3 class="jester_ecommerce_profile-modal-title">Become a pro</h3>
            <a href="#" class="close-perk-modal-btn"><i class="fas fa-times"></i></a>
        </div>
        <div class="jester_ecommerce_profile-modal-body">
            @include('crm::order_request.partials.become_a_pro')
        </div>
    </div>
</div>

<div id="vouchers-modal" class="jester_ecommerce_profile-details-modal-container">
    <div class="jester_ecommerce_profile-details-modal-content">
        <div class="jester_ecommerce_profile-modal-header">
            <h3 class="jester_ecommerce_profile-modal-title">Vouchers</h3>
            <a href="#" class="close-perk-modal-btn"><i class="fas fa-times"></i></a>
        </div>
        <div class="jester_ecommerce_profile-modal-body">
            @include('crm::order_request.partials.vouchers')
        </div>
    </div>
</div>

<div id="arimako-rewards-modal" class="jester_ecommerce_profile-details-modal-container">
    <div class="jester_ecommerce_profile-details-modal-content">
        <div class="jester_ecommerce_profile-modal-header">
            <h3 class="jester_ecommerce_profile-modal-title">Arimako Rewards</h3>
            <a href="#" class="close-perk-modal-btn"><i class="fas fa-times"></i></a>
        </div>
        <div class="jester_ecommerce_profile-modal-body">
            @include('crm::order_request.partials.arimako_rewards')
        </div>
    </div>
</div>

<div id="invite-friends-modal" class="jester_ecommerce_profile-details-modal-container">
    <div class="jester_ecommerce_profile-details-modal-content">
        <div class="jester_ecommerce_profile-modal-header">
            <h3 class="jester_ecommerce_profile-modal-title">Invite friends</h3>
            <a href="#" class="close-perk-modal-btn"><i class="fas fa-times"></i></a>
        </div>
        <div class="jester_ecommerce_profile-modal-body">
            @include('crm::order_request.partials.invite_friends')
        </div>
    </div>
</div>

<!-- General Modals -->
<div id="help-center-modal" class="jester_ecommerce_profile-details-modal-container">
    <div class="jester_ecommerce_profile-details-modal-content">
        <div class="jester_ecommerce_profile-modal-header">
            <h3 class="jester_ecommerce_profile-modal-title">Help center</h3>
            <a href="#" class="close-perk-modal-btn"><i class="fas fa-times"></i></a>
        </div>
        <div class="jester_ecommerce_profile-modal-body">
            @include('crm::order_request.partials.help_center')
        </div>
    </div>
</div>

<div id="arimako-for-business-modal" class="jester_ecommerce_profile-details-modal-container">
    <div class="jester_ecommerce_profile-details-modal-content">
        <div class="jester_ecommerce_profile-modal-header">
            <h3 class="jester_ecommerce_profile-modal-title">Arimako for business</h3>
            <a href="#" class="close-perk-modal-btn"><i class="fas fa-times"></i></a>
        </div>
        <div class="jester_ecommerce_profile-modal-body">
            @include('crm::order_request.partials.arimako_for_business')
        </div>
    </div>
</div>

<div id="terms-and-policies-modal" class="jester_ecommerce_profile-details-modal-container">
    <div class="jester_ecommerce_profile-details-modal-content">
        <div class="jester_ecommerce_profile-modal-header">
            <h3 class="jester_ecommerce_profile-modal-title">Terms & policies</h3>
            <a href="#" class="close-perk-modal-btn"><i class="fas fa-times"></i></a>
        </div>
        <div class="jester_ecommerce_profile-modal-body">
            @include('crm::order_request.partials.terms_and_policies')
        </div>
    </div>
</div>

<div id="notification-modal" class="jester_ecommerce_profile-details-modal-container">
    <div class="jester_ecommerce_profile-details-modal-content">
        <div class="jester_ecommerce_profile-modal-header">
            <h3 class="jester_ecommerce_profile-modal-title">Notifications</h3>
            <a href="#" class="close-perk-modal-btn"><i class="fas fa-times"></i></a>
        </div>
        <div class="jester_ecommerce_profile-modal-body">
            @include('crm::order_request.partials.ecommerce_news')
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Modal Handling (for footer button) ---
    const modalContainer = document.getElementById('full-screen-modal');
    const modalToggleBtn = document.querySelector('.footer-menu a[data-toggle="modal"]'); // Specific to footer
    const closeModalBtn = document.querySelector('.jester_ecommerce_close-modal-btn');
    
    if (modalToggleBtn && modalContainer) {
        modalToggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            modalContainer.classList.toggle('active');
        });
    }
    
    if (closeModalBtn && modalContainer) {
        closeModalBtn.addEventListener('click', function(e) {
            e.preventDefault();
            modalContainer.classList.remove('active');
        });
    }
    
    // --- Profile Details Modal ---
    const profileViewLink = document.getElementById('open-profile-modal');
    const profileDetailsModal = document.getElementById('profile-details-modal');
    const closeProfileDetailsModalBtns = document.querySelectorAll('.close-profile-details-modal-btn');
    
    if (profileViewLink && profileDetailsModal) {
        profileViewLink.addEventListener('click', function(e) {
            e.preventDefault();
            profileDetailsModal.classList.add('active');
        });
    }
    
    if (closeProfileDetailsModalBtns.length > 0 && profileDetailsModal) {
        closeProfileDetailsModalBtns.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                // Add closing class to trigger animation
                profileDetailsModal.classList.add('closing');
                profileDetailsModal.classList.remove('active');
                
                // Remove closing class after animation completes
                setTimeout(function() {
                    profileDetailsModal.classList.remove('closing');
                }, 300); // Match the transition duration
            });
        });
    }

    // --- Perks Modals ---
    const perkLinks = document.querySelectorAll('.jester_ecommerce_perks-list a[data-target], .jester_ecommerce_grid-item[data-target]');
    const closePerkModalBtns = document.querySelectorAll('.close-perk-modal-btn');

    perkLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.getAttribute('data-target');
            const modal = document.querySelector(modalId);
            if (modal) {
                modal.classList.add('active');
            }
        });
    });

    closePerkModalBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const modal = this.closest('.jester_ecommerce_profile-details-modal-container');
            if (modal) {
                modal.classList.add('closing');
                modal.classList.remove('active');
                setTimeout(function() {
                    modal.classList.remove('closing');
                }, 300);
            }
        });
    });
    
    // --- Dropdown Handling (for header) ---
    const headerDropdownToggle = document.querySelector('.main-header .dropdown-toggle[data-toggle="dropdown"]');
    if (headerDropdownToggle) {
        headerDropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = this.parentElement; // Should be li.dropdown.user.user-menu
            if (parent) {
                parent.classList.toggle('open');
            }
        });
    }
    
    // --- Generic Click Outside to Close (for header dropdown) ---
    document.addEventListener('click', function(e) {
        const openDropdown = document.querySelector('.main-header .dropdown.open');
        if (openDropdown && !openDropdown.contains(e.target)) {
            openDropdown.classList.remove('open');
        }
    });
});
</script>