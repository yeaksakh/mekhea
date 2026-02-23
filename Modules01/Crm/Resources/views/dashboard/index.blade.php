@extends('crm::layouts.app')
@section('title', __('business.dashboard'))
@section('content')

<div class="dashboard-container">
    <div class="background-wrapper" id="dashboard-background">
        <div class="clock-overlay" id="live-clock"></div>
     <br>
           <!-- Title -->
  <div @click="open = !open" class="tw-cursor-pointer tw-flex tw-justify-center tw-items-center tw-text-center tw-w-full">
    <h2 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black tw-text-center">
      ការកម្មង់ទំនិញ
    </h2>
  </div>

  <br>




<!-- Main content -->

		<div class="min-h-screen w-full px-4 md:px-8 lg:px-16 py-8">

	<div class="row row-custom tw-text-center">
		@if( $contact->type == 'supplier' || $contact->type == 'both')
	    	<div class="col-md-3 col-sm-6 col-xs-12 ">
		      <div class="info-box info-box-new-style">
		        <span class="info-box-icon bg-aqua"><i class="ion ion-cash"></i></span>
		        <div class="info-box-content">
		          <span class="text-white text-xl ">@lang('report.total_purchase')</span>
		          <span class="info-box-number text-white display_currency" data-currency_symbol="true">
		          	{{ $contact->total_purchase }}
		          </span>
		        </div>
		      </div>
		    </div>

		    <div class="col-md-3 col-sm-6 col-xs-12 ">
		      <div class="info-box info-box-new-style">
		        <span class="info-box-icon bg-green">
		        	<i class="fas fa-money-check-alt"></i>
		        </span>
		        <div class="info-box-content">
		          <span class="text-white text-xl ">@lang('contact.total_purchase_paid')</span>
		          <span class="info-box-number text-white display_currency" data-currency_symbol="true">
		          	{{ $contact->purchase_paid }}
		          </span>
		        </div>
		      </div>
		    </div>

		    <div class="col-md-3 col-sm-6 col-xs-12 ">
		      <div class="info-box info-box-new-style">
		        <span class="info-box-icon bg-yellow">
		        	<i class="fas fa-money-check-alt"></i>
					<i class="fa fa-exclamation"></i>
		        </span>
		        <div class="info-box-content">
		          <span class="text-white text-xl ">@lang('contact.total_purchase_due')</span>
		          <span class="info-box-number text-white display_currency" data-currency_symbol="true">
		          	{{ $contact->total_purchase - $contact->purchase_paid }}
		          </span>
		        </div>
		      </div>
		    </div>
	    @endif

	    @if( $contact->type == 'customer' || $contact->type == 'both')
		    <div class="col-md-3 col-sm-6 col-xs-12 ">
		      <div class="info-box info-box-new-style">
		        <span class="info-box-icon bg-aqua">
		        	<i class="ion ion-ios-cart-outline"></i>
		        </span>
		        <div class="info-box-content">
		          <span class="text-white text-xl ">@lang('report.total_sell')</span>
		          <span class="info-box-number text-white display_currency" data-currency_symbol="true">
		          	{{ $contact->total_invoice }}
		          </span>
		        </div>
		      </div>
		    </div>

	        <div class="col-md-3 col-sm-6 col-xs-12 ">
	          <div class="info-box info-box-new-style">
	            <span class="info-box-icon bg-green">
	              <i class="fas fa-money-check-alt"></i>
	            </span>
	            <div class="info-box-content">
	              <span class="text-white text-xl ">
	                @lang('contact.total_sale_paid')
	              </span>
	              <span class="info-box-number text-white display_currency" data-currency_symbol="true">
	              	{{ $contact->invoice_received }}
	              </span>
	            </div>
	          </div>
	        </div>

	        <div class="col-md-3 col-sm-6 col-xs-12 ">
	          <div class="info-box info-box-new-style">
	            <span class="info-box-icon bg-yellow">
	              	<i class="fas fa-money-check-alt"></i>
					<i class="fa fa-exclamation"></i>
	            </span>
	            <div class="info-box-content">
	              <span class="text-white text-xl ">
	                @lang('contact.total_sale_due')
	              </span>
	              <span class="info-box-number text-white display_currency" data-currency_symbol="true">
	              	{{ $contact->total_invoice - $contact->invoice_received }}
	              </span>
	            </div>
	          </div>
	        </div>
        @endif

        @if(!empty($contact->opening_balance) && $contact->opening_balance != '0.00')
	        <div class="col-md-3 col-sm-6 col-xs-12 ">
	          <div class="info-box info-box-new-style">
	            <span class="info-box-icon bg-aqua">
	              <i class="fas fa-donate"></i>
	            </span>
	            <div class="info-box-content">
	              <span class="text-white text-xl ">
	                @lang('lang_v1.opening_balance')
	              </span>
	              <span class="info-box-number text-white display_currency" data-currency_symbol="true">
		            {{ $contact->opening_balance }}
		           </span>
	            </div>
	          </div>
	        </div>

	        <div class="col-md-3 col-sm-6 col-xs-12 ">
	          <div class="info-box info-box-new-style">
	            <span class="info-box-icon bg-yellow">
	              <i class="fas fa-donate"></i>
	              <i class="fa fa-exclamation"></i>
	            </span>
	            <div class="info-box-content">
	              <span class="text-white text-xl ">
	                @lang('lang_v1.opening_balance_due')
	              </span>
	              <span class="info-box-number text-white display_currency" data-currency_symbol="true">
		            {{ $contact->opening_balance - $contact->opening_balance_paid }}
		           </span>
	            </div>
	          </div>
	        </div>
	    @endif
    </div>

	 <!-- Change Background Tile -->
           <div class="home-grid-tile" data-key="change_background" style="position: fixed; bottom: 80px; right: 20px;">
                <a href="#" onclick="document.getElementById('imageInput').click(); return false;">
                    <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/setting/change_background.svg') }}" class="home-icon" alt="">
                    <span class="home-label">Background</span>
                </a>
            </div>

  <input type="file" id="imageInput" accept="image/*" onchange="changeBackground(event)" style="display: none;">
            
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
  
	.info-box-new-style {
	background-color: rgba(18, 100, 1, 0.6) !important; /* semi-transparent white */
	border: 1px solid #ddd;
	backdrop-filter: blur(4px); /* optional: gives glass effect */
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
	border-radius: 8px;
	}

	.info-box-icon {
	background-color: rgba(0, 2, 5, 0.3) !important; /* make icon background transparent */
	border-radius: 50%;
	}

    .clock-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 10vw;
        font-weight: bold;
        color: rgba(255, 254, 254, 0.2);
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
        z-index: 0;
        font-family: 'Arial', sans-serif;
        pointer-events: none;
        user-select: none;
    }

    @media (max-width: 768px) {
        .clock-overlay {
            font-size: 20vw;
        }
    }

    .dashboard-container {
        height: calc(100vh);
        width: 100vw;
        position: fixed;
        overflow: hidden;
    }

    
    .background-wrapper {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-image: url('/public/images/background/default.jpg');
        transition: background-image 0.5s ease;
    }

        



 
</style>
@endsection

@section('javascript')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Clock functionality
    function updateClock() {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        document.getElementById('live-clock').textContent = `${hours}:${minutes}`;
    }
    
    updateClock();
    setInterval(updateClock, 60000);

    // Clock Toggle Functionality
    const clockElement = document.getElementById('live-clock');
    const toggleButton = document.getElementById('toggle-clock');
    let clockVisible = true;

    // Load clock visibility state from localStorage
    const savedVisibility = localStorage.getItem('clockVisibility');
    if (savedVisibility === 'hidden') {
        clockElement.classList.add('hidden');
        toggleButton.textContent = 'Show Clock';
        clockVisible = false;
    }

   

    // Load background image if cached
    const cachedImage = localStorage.getItem('backgroundImage');
    if (cachedImage) {
        document.getElementById('dashboard-background').style.backgroundImage = `url(${cachedImage})`;
    }

    // Initialize features
    setupBackgroundUpload();
});


function setupBackgroundUpload() {
    const uploadInput = document.getElementById('imageInput');
    if (uploadInput) {
        uploadInput.addEventListener('change', changeBackground);
    }
}

function changeBackground(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        const imageUrl = e.target.result;

        // Set background
        document.getElementById('dashboard-background').style.backgroundImage = `url(${imageUrl})`;

        // Save to localStorage
        localStorage.setItem('backgroundImage', imageUrl);
    };
    reader.readAsDataURL(file);
}


</script>
@endsection











  