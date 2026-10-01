<!-- Left side column. contains the logo and sidebar -->
<aside class="side-bar admin-pro-sidebar tw-relative tw-hidden tw-h-full tw-bg-white tw-w-64 xl:tw-w-64 lg:tw-flex lg:tw-flex-col tw-shrink-0 tw-border-r tw-border-gray-200 tw-shadow-sm">

    <!-- sidebar: style can be found in sidebar.less -->

    {{-- <a href="{{route('home')}}" class="logo">
		<span class="logo-lg">{{ Session::get('business.name') }}</span>
	</a> --}}

    <a href="{{route('home')}}"
        class="admin-pro-sidebar-brand tw-flex tw-items-center tw-gap-2 tw-justify-center tw-w-full tw-border-r tw-h-15 tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-800 tw-shrink-0 tw-border-primary-500/30 tw-px-3">
        <img src="{{ asset('img/dhc-logo.svg') }}" alt="DHC Shoes" class="tw-h-8 tw-w-auto tw-bg-white tw-rounded-md tw-px-1">
        <p class="tw-text-lg tw-font-medium tw-text-white side-bar-heading tw-text-center tw-truncate">
            DHC Shoes <span class="tw-inline-block tw-w-3 tw-h-3 tw-bg-emerald-400 tw-rounded-full tw-align-middle" title="Online"></span>
        </p>
    </a>

    <!-- Sidebar Menu -->
    {!! Menu::render('admin-sidebar-menu', 'adminltecustom') !!}

    <!-- /.sidebar-menu -->
    <!-- /.sidebar -->
</aside>
