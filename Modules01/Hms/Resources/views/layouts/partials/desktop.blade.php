
                @can('user.view')
                    <li class="home-grid-tile" data-key="hms">
                        <a class="navbar-brand d-flex flex-column align-items-center py-2" 
                           href="{{ action([\Modules\Hms\Http\Controllers\HmsController::class, 'index']) }}" 
                           title="{{ __('hms::lang.hms') }}">
                            <img src="{{ asset('public/icons/' . (session('business.icon_pack') ?: 'v1') . '/modules/hotel.svg') }}" 
                                 class="home-icon mb-1" 
                                 alt="{{ __('hms::lang.hms') }}">
                            <span class="home-label">{{ __('hms::lang.hms') }}</span>
                        </a>
                    </li>
                @endcan
                