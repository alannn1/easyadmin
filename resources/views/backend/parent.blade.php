<!DOCTYPE html>
<html lang="en">

<head>
  <title>@stack("mtitle")</title>
  <meta charset="utf-8" />
  <meta name="base_url" content="{{url('')}}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="description" content="{{ config('idev.app_description', 'iDev easyadmin is a laravel package to make your life more easy') }}" />
  <meta name="keywords" content="{{ config('idev.app_keywords', 'Laravel, idev semarang, Berry, Dashboard UI Kit, Bootstrap 5, Admin Template, Admin Dashboard, CRM, CMS, Bootstrap Admin Template') }}" />
  <meta name="author" content="CodedThemes" />
	<link rel="icon" href=" {{ asset('assets/logo_harumo.png') }}">

  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" id="main-font-link" />

  <link rel="stylesheet" href="{{ asset('easyadmin/theme/default/fonts/tabler-icons.min.css')}}" />

  <link rel="stylesheet" href="{{ asset('easyadmin/theme/default/fonts/material.css')}}" />

  <link rel="stylesheet" href="{{ asset('easyadmin/theme/'.config('idev.theme','default').'/css/style.css')}}" id="main-style-link" />
  <link rel="stylesheet" href="{{ asset('easyadmin/theme/'.config('idev.theme','default').'/css/style-preset.css')}}" id="preset-style-link" />
  <link href="{{ asset('easyadmin/idev/styles.css')}}" rel="stylesheet" />
  @foreach(config('idev.import_styles', []) as $item)
    <link href="{{ $item }}" rel="stylesheet" />
  @endforeach
  @vite('resources/css/app.css')
</head>


<body>
  <div class="push-style-ajax">@stack("styles")</div>

  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>


  <header class="pc-header">
    <div class="m-header d-none d-lg-flex">
      {!! env('PROJECT_NAME', config('idev.app_name','iDev Admin') ) !!}
      <div class="pc-h-item">
        <a href="#" class="pc-head-link head-link-secondary m-0 p-1 d-none d-lg-block" id="sidebar-hide">
          <i class="ti ti-menu-2 m-1"></i>
        </a>
      </div>
    </div>
    <div class="header-wrapper">
      <div class="me-auto pc-mob-drp title-header">
        @stack("mtitle")
      </div>

      <div class="ms-auto">
        <ul class="list-unstyled">
          <li class="pc-h-item header-mobile-collapse">
            <a href="#" class="pc-head-link head-link-secondary m-0 p-1 d-lg-none d-md-block" id="mobile-collapse">
              <i class="ti ti-menu-2 m-1"></i>
            </a>
          </li>
          <!--li class="dropdown pc-h-item">
            <a class="pc-head-link head-link-secondary dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
              <i class="ti ti-bell"></i>
            </a>
          </li-->
          <li class="dropdown pc-h-item">
            <a class="pc-head-link head-link-secondary dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
              <i class="ti ti-user"></i>
            </a>
            <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
              <div class="dropdown-header">
                <h4>Hi, {{ Auth::user()->name }}</h4>
                <p class="text-muted">Harumo Parfum</p>
                <hr />
                <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 280px)">
                  <a href="{{url('my-account')}}" class="dropdown-item">
                    <i class="ti ti-settings"></i>
                    <span>Account Settings</span>
                  </a>
                  <a href="{{route('logout')}}" class="dropdown-item">
                    <i class="ti ti-logout"></i>
                    <span>Logout</span>
                  </a>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </header>

  <nav class="pc-sidebar">
    <div class="navbar-wrapper">
      <div class="m-header">
        {!! env('PROJECT_NAME', config('idev.app_name','iDev Admin') ) !!}
      </div>
      @include('backend.sidebar')
    </div>
  </nav>

  @yield("content")

  <footer class="pc-footer">
    <div class="footer-wrapper container-fluid">
      <div class="row">
        <div class="col my-1">
          <p class="m-0"> {!!config('idev.copyright','Copyright &copy; iDev Semarang') !!} </p>
        </div>
      </div>
    </div>
  </footer>

  <script src="{{ asset('easyadmin/theme/default/js/plugins/jquery-3.6.3.min.js')}}"></script>
  <script src="{{ asset('easyadmin/theme/default/js/plugins/popper.min.js')}}"></script>
  <script src="{{ asset('easyadmin/theme/default/js/plugins/simplebar.min.js')}}"></script>
  <script src="{{ asset('easyadmin/theme/default/js/plugins/bootstrap.min.js')}}"></script>
  <script src="{{ asset('easyadmin/theme/default/js/plugins/sweet-alert.js')}}"></script>
  <script src="{{ asset('easyadmin/theme/default/js/config.js')}}"></script>
  <script src="{{ asset('easyadmin/theme/default/js/pcoded.js')}}"></script>
  <script src="{{ asset('easyadmin/idev/scripts.js')}}"></script>
  @foreach(config('idev.import_scripts', []) as $item)
  <script src="{{ $item }}"></script>
  @endforeach
  <div class="push-script-ajax">@stack("scripts")</div>

</body>

</html>
