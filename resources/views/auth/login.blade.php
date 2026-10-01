@extends('layouts.auth2')
@section('title', __('lang_v1.login'))
@inject('request', 'Illuminate\Http\Request')
@section('content')
    @php
        $username = old('username');
        $password = null;
        if (config('app.env') == 'demo') {
            $username = 'admin';
            $password = '123456';

            $demo_types = [
                'all_in_one' => 'admin',
                'super_market' => 'admin',
                'pharmacy' => 'admin-pharmacy',
                'electronics' => 'admin-electronics',
                'services' => 'admin-services',
                'restaurant' => 'admin-restaurant',
                'superadmin' => 'superadmin',
                'woocommerce' => 'woocommerce_user',
                'essentials' => 'admin-essentials',
                'manufacturing' => 'manufacturer-demo',
            ];

            if (!empty($_GET['demo_type']) && array_key_exists($_GET['demo_type'], $demo_types)) {
                $username = $demo_types[$_GET['demo_type']];
            }
        }
    @endphp
    <div class="modern-login-wrapper">
        @if (config('app.env') == 'demo')
        <div style="width: 100%; max-width: 450px;">
            @component('components.widget', [
                'class' => 'box-primary',
                'header' =>
                    '<h4 class="text-center" style="color:#fff;">Demo Shops <small style="color:#aaa;"><i> <br/>Demos are for example purpose only, this application <u>can be used in many other similar businesses.</u></i> <br/><b>Click button to login that business</b></small></h4>',
            ])
                <!-- Demo login buttons remain unchanged -->
                <a href="?demo_type=all_in_one" class="btn btn-app bg-olive demo-login" data-toggle="tooltip"
                    title="Showcases all feature available in the application."
                    data-admin="{{ $demo_types['all_in_one'] }}"> <i class="fas fa-star"></i> All In One</a>
                <!-- the rest of the demo shops buttons -->
                <a href="?demo_type=pharmacy" class="btn bg-maroon btn-app demo-login" data-toggle="tooltip"
                    title="Shops with products having expiry dates." data-admin="{{ $demo_types['pharmacy'] }}"><i
                        class="fas fa-medkit"></i>Pharmacy</a>

                <a href="?demo_type=services" class="btn bg-orange btn-app demo-login" data-toggle="tooltip"
                    title="For all service providers like Web Development, Restaurants, Repairing, Plumber, Salons, Beauty Parlors etc."
                    data-admin="{{ $demo_types['services'] }}"><i class="fas fa-wrench"></i>Multi-Service Center</a>

                <a href="?demo_type=electronics" class="btn bg-purple btn-app demo-login" data-toggle="tooltip"
                    title="Products having IMEI or Serial number code." data-admin="{{ $demo_types['electronics'] }}"><i
                        class="fas fa-laptop"></i>Electronics & Mobile Shop</a>

                <a href="?demo_type=super_market" class="btn bg-navy btn-app demo-login" data-toggle="tooltip"
                    title="Super market & Similar kind of shops." data-admin="{{ $demo_types['super_market'] }}"><i
                        class="fas fa-shopping-cart"></i> Super Market</a>

                <a href="?demo_type=restaurant" class="btn bg-red btn-app demo-login" data-toggle="tooltip"
                    title="Restaurants, Salons and other similar kind of shops."
                    data-admin="{{ $demo_types['restaurant'] }}"><i class="fas fa-utensils"></i> Restaurant</a>
                <hr style="border-top:1px solid #333">

                <i class="icon fas fa-plug" style="color:#fff"></i> <span style="color:#fff">Premium optional modules:</span><br><br>

                <a href="?demo_type=superadmin" class="btn bg-red-active btn-app demo-login" data-toggle="tooltip"
                    title="SaaS & Superadmin extension Demo" data-admin="{{ $demo_types['superadmin'] }}"><i
                        class="fas fa-university"></i> SaaS / Superadmin</a>

                <a href="?demo_type=woocommerce" class="btn bg-woocommerce btn-app demo-login" data-toggle="tooltip"
                    title="WooCommerce demo user - Open web shop in minutes!!" style="color:white !important"
                    data-admin="{{ $demo_types['woocommerce'] }}"> <i class="fab fa-wordpress"></i> WooCommerce</a>

                <a href="?demo_type=essentials" class="btn bg-navy btn-app demo-login" data-toggle="tooltip"
                    title="Essentials & HRM (human resource management) Module Demo" style="color:white !important"
                    data-admin="{{ $demo_types['essentials'] }}">
                    <i class="fas fa-check-circle"></i>
                    Essentials & HRM</a>

                <a href="?demo_type=manufacturing" class="btn bg-orange btn-app demo-login" data-toggle="tooltip"
                    title="Manufacturing module demo" style="color:white !important"
                    data-admin="{{ $demo_types['manufacturing'] }}">
                    <i class="fas fa-industry"></i>
                    Manufacturing Module</a>

            @endcomponent
        </div>
        @endif

        <div class="modern-login-card">
            <div class="logo-container">
                <img src="{{ asset('img/dhc-logo.svg') }}" alt="DHC Shoes" style="width: 220px; height: auto; margin-bottom: 12px;">
                <p class="modern-brand-tagline">
                    <span class="modern-brand-name">DHC SHOES</span>
                </p>
                <h1 class="modern-title">@lang('lang_v1.welcome_back')</h1>
                <h2 class="modern-subtitle">
                    @lang('lang_v1.login_to_your') <span>DHC Shoes</span>
                </h2>
            </div>

            <form method="POST" action="{{ route('login') }}" id="login-form">
                {{ csrf_field() }}
                
                <div class="modern-form-group">
                    <label for="username">@lang('lang_v1.username')</label>
                    <div class="modern-input-wrapper">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="7" r="4" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                        <input
                            class="modern-input"
                            name="username" required autofocus placeholder="@lang('lang_v1.username')"
                            id="username" type="text"
                            value="{{ $username }}" />
                    </div>
                    @if ($errors->has('username'))
                        <span class="error-message">
                            <strong>{{ $errors->first('username') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="modern-form-group">
                    <div class="flex-between">
                        <label for="password" style="margin-bottom:0;">@lang('lang_v1.password')</label>
                        @if (config('app.env') != 'demo')
                            <a href="{{ route('password.request') }}" class="forgot-pass" tabindex="-1">@lang('lang_v1.forgot_your_password')</a>
                        @endif
                    </div>
                    <div class="modern-input-wrapper" style="margin-top:8px;">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="5" y="11" width="14" height="10" rx="2" /><circle cx="12" cy="16" r="1" /><path d="M8 11v-4a4 4 0 0 1 8 0v4" /></svg>
                        <input
                            class="modern-input"
                            id="password" type="password" name="password" value="{{ $password }}" required
                            placeholder="••••••••" style="padding-right: 42px;" />
                        <button type="button" id="show_hide_icon" class="btn-show-pass">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                            </svg>
                        </button>
                    </div>
                    @if ($errors->has('password'))
                        <span class="error-message">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>

                <label class="modern-remember">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} class="modern-checkbox">
                    @lang('lang_v1.remember_me')
                </label>

                @if(config('constants.enable_recaptcha'))
                <div style="margin-top: 15px;">
                    <div class="g-recaptcha" data-sitekey="{{ config('constants.google_recaptcha_key') }}"></div>
                        @if ($errors->has('g-recaptcha-response'))
                            <span class="error-message">{{ $errors->first('g-recaptcha-response') }}</span>
                        @endif
                </div>
                @endif

                <button type="submit" class="modern-btn">
                    @lang('lang_v1.login')
                </button>
            </form>

            <div class="modern-footer">
                @if (!($request->segment(1) == 'business' && $request->segment(2) == 'register'))
                    @if (config('constants.allow_registration'))
                        {{ __('business.not_yet_registered') }}
                        <a href="{{ route('business.getRegister') }}@if (!empty(request()->lang)) {{ '?lang=' . request()->lang }} @endif">
                            {{ __('business.register_now') }}
                        </a>
                    @endif
                @endif
            </div>
        </div>
    </div>

@stop
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#show_hide_icon').off('click');
            $('.change_lang').click(function() {
                window.location = "{{ route('login') }}?lang=" + $(this).attr('value');
            });
            $('a.demo-login').click(function(e) {
                e.preventDefault();
                $('#username').val($(this).data('admin'));
                $('#password').val("{{ $password }}");
                $('form#login-form').submit();
            });

            $('#show_hide_icon').on('click', function(e) {
            e.preventDefault();
            const passwordInput = $('#password');

            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                $('#show_hide_icon').html('<svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.585 10.587a2 2 0 0 0 2.829 2.828"/><path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87"/><path d="M3 3l18 18"/></svg>');
            }
            else if (passwordInput.attr('type') === 'text') {
                passwordInput.attr('type', 'password');
                $('#show_hide_icon').html('<svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/></svg>');
            }
        });
        })
    </script>
@endsection
