@extends('layouts.guest')

@section('content')
    <!-- Main content -->
    <section class="content">
     
          
                
                <!-- User active or not -->
                <div class="user-profile-container" 
                    style="width: 100%; background-color: white; border-radius: 5px; text-align: center;">
                       <!-- User Details Container -->
                    <div style="display: flex; flex-direction: column; justify-content: center;">
                                 @if ($user->status == 'active')
                    <span class="label label-success no-print" style="font-size: clamp(50px, 3vw, 50px); padding: 5vh 9vw;">
                        @lang('user.working')
                    </span>
                @else
                    <span class="label label-danger no-print" style="font-size: clamp50px, 3vw, 50px); padding: 5vh 9vw;">
                        @lang('user.stop_working')
                    </span>
                @endif
                <br>
                    <!-- User Profile Image  -->
                    <div class="profile-img-container" 
                        style="width: 120px; height: 120px; border-radius: 50%; overflow: hidden; border: 3px solid #fff; margin: 0 auto 2vh;">
                        @php
                            $img_src = $user->media->display_url ?? 'https://ui-avatars.com/api/?name=' . $user->first_name;
                        @endphp
                        <img class="profile-user-img img-responsive img-circle" src="{{ $img_src }}" 
                            alt="Employee profile picture" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>

                        <span class="user-name" style="color: #000; font-size: clamp(20px, 6vw, 28px); font-weight: bold;">{{ $user->user_full_name }}</span>
                        <p>
                            <span class="user-role" style="color: #000; font-size: clamp(16px, 4vw, 20px); font-weight: bold;">{{ $user_designstion->name ?? '' }}</span>  
                            {{ $user_department->name ?? '' }}<br>
                            {{ $user->contact_number ?? '' }}<br>
                            {{ $user->alt_number ?? '' }}
                     
                        </p>
                    </div>
                    
                    <!-- Social Media Icons -->
                    <div class="social-media-container"
                        style="display: flex; justify-content: center; gap: 15px; margin-top: 2vh;">
                        @if (!empty($user->fb_link))
                            <a href="{{ $user->fb_link }}" target="_blank" style="color: blue; font-size: clamp(16px, 4vw, 20px); text-decoration: none;" title="Facebook">
                                <i class="fab fa-facebook"></i>
                            </a>
                        @endif
                        @if (!empty($user->twitter_link))
                            <a href="{{ $user->twitter_link }}" target="_blank" style="color: blue; font-size: clamp(16px, 4vw, 20px); text-decoration: none;" title="Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                        @endif
                        @if (!empty($user->social_media_1))
                            <a href="{{ $user->social_media_1 }}" target="_blank" style="color: blue; font-size: clamp(16px, 4vw, 20px); text-decoration: none;" title="YouTube">
                                <i class="fab fa-youtube"></i>
                            </a>
                        @endif
                        @if (!empty($user->social_media_2))
                            <a href="{{ $user->social_media_2 }}" target="_blank" style="color: blue; font-size: clamp(16px, 4vw, 20px); text-decoration: none;" title="Telegram">
                                <i class="fab fa-telegram"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <hr>
             <!-- Business Logo Container -->
			<div class="business-logo-container" 
			     style="display: flex; align-items: center; margin: 0 auto 2vh; width: 100%;">
			    <div style="width: 50px; height: 50px; border-radius: 50%; overflow: hidden; border: 3px solid #0000FF; padding: 5px; flex-shrink: 0;">
			        <img class="img-responsive" src="{{ url('uploads/business_logos/' . $business->logo) }}" style="width: 100%; height: 100%; object-fit: cover;">
			    </div>
			    <div style="margin-left: 15px; flex-grow: 1; text-align: left; font-size: 20px;">
			    {{ Session::get('business.name') }}
			</div>
			    <a href="https://yeaksa.com" target="_blank" style="margin-right: 15px; color: blue; font-size: clamp(16px, 4vw, 20px); text-decoration: none;" title="Facebook">
			          <i class="fas fa-globe"></i>
			   </a>
			</div>
                    <!-- CSS -->
<style>
.icon-container {
    display: inline-block;
}

.fas.fa-globe {
    font-size: 24px; /* Adjust size as needed */
    animation: shake 0.5s infinite;
}

@keyframes shake {
    0% { transform: translate(0, 0) rotate(0deg); }
    25% { transform: translate(2px, 2px) rotate(2deg); }
    50% { transform: translate(-2px, -2px) rotate(-2deg); }
    75% { transform: translate(2px, -2px) rotate(2deg); }
    100% { transform: translate(0, 0) rotate(0deg); }
}
</style>

<!-- JavaScript -->
<script>
function getRandomColor() {
    const colors = ['red', 'green', 'blue', 'white'];
    return colors[Math.floor(Math.random() * colors.length)];
}

function changeColor() {
    const icon = document.getElementById('shaking-icon');
    icon.style.color = getRandomColor();
}

// Change color every 500ms (0.5 seconds)
setInterval(changeColor, 500);

// Initial color
changeColor();
</script>




<!-- Business Address at Bottom -->
<div class="business-address"
    style="width: 100%; margin-top: auto; background-color: #007bff; padding: 5%; border-radius: 5px; text-align: center; color: white; font-size: clamp(12px, 3vw, 16px); -webkit-print-color-adjust: exact; print-color-adjust: exact;">
   
    @if ($business_location)
        {{ $business_location->city }}, {{ $business_location->state }},
        {{ $business_location->country }}, Landmark: {{ $business_location->landmark }}, Zip:
        {{ $business_location->zip_code }}
    @else
        No business location found.
    @endif
</div>