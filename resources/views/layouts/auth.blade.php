<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('images/icon.png') }}" type="image/png">
</head>
<body>
    @php $configuration = Configuration::first(); @endphp

    <v-app id="app" class="auth-layout">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm p-0">
            <div class="container-fluid mx-4">
                <a class="navbar-brand" href="{{ url('/') }}">
                    @if ($configuration && $configuration->logo_url)
                        <img src="{{ $configuration->logo_url }}" alt="{{ config('app.name', 'Laravel') }}" width="250px">
                    @else
                        {{ config('app.name', 'Laravel') }}
                    @endif
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto align-items-center">
                       
                    </ul>

                    <ul class="navbar-nav ml-auto align-items-center">
                        <li class="nav-item">
                            <a href="{{ route('programmation') }}" class="nav-link {{ Route::is('programmation') ? 'active' : '' }}">{{ __('Programmations') }}</a>
                        </li>

                        @canany(['administrator', 'scheduler'])
                            <li class="nav-item">
                                <a href="{{ route('schedule') }}" class="nav-link {{ Route::is('schedule') ? 'active' : '' }}">{{ __('Schedules') }}</a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('space-category') }}" class="nav-link {{ Route::is('space-category') ? 'active' : '' }}">{{ __('Spaces and Categories') }}</a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('custom-holiday') }}" class="nav-link {{ Route::is('custom-holiday') ? 'active' : '' }}">{{ __('Custom Holiday') }}</a>
                            </li>
                        @endcanany
                            
                        @can('administrator')
                            <li class="nav-item">
                                <a href="{{ route('user') }}" class="nav-link {{ Route::is('user') ? 'active' : '' }}">{{ __('Users') }}</a>
                            </li>

                            {{-- <li class="nav-item">
                                <a href="{{ route('sector') }}" class="nav-link {{ Route::is('sector') ? 'active' : '' }}">{{ __('Sectors') }}</a>
                            </li> --}}

                            <li class="nav-item">
                                <a href="{{ route('log') }}" class="nav-link {{ Route::is('log') ? 'active' : '' }}">{{ __('Logs') }}</a>
                            </li>
                        @endcan

                        @if ($configuration && $configuration->contact)
                            <li class="nav-item">
                                <a href="mailto:{{ $configuration->contact }}" class="nav-link">
                                    <i class="fas fa-envelope"></i>
                                </a>
                            </li>
                        @endif

                        <li class="nav-item dropdown py-2">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" aria-haspopup="true" aria-expanded="false" v-pre>
                                <span class="avatar" badge="2">
                                    <img src="{{ auth()->user()->avatar_url ?? asset('images/default-avatar.jpg') }}" alt="Default Avatar" width="50" height="50">
                                </span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a href="{{ route('profile') }}" class="dropdown-item {{ Route::is('profile') ? 'active' : '' }}">
                                    <i class="fas fa-user"></i>
                                    Meu perfil
                                </a>

                                {{-- <a href="#" class="dropdown-item d-flex align-items-center">
                                    <i class="fas fa-bell"></i>
                                    Notificações
                                    <span class="badge badge-danger ml-auto">2</span>
                                </a> --}}
                                
                                @can('administrator')
                                    <a class="dropdown-item {{ Route::is('configuration') ? 'active' : '' }}" href="{{ route('configuration') }}">
                                        <i class="fas fa-cog"></i>
                                        {{ __('Configurations') }}
                                    </a>
                                @endcan

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt"></i>
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>

        @if ($configuration && ($configuration->copyright))
            <footer class="bg-white text-center py-3">
                {{ $configuration->copyright }}
            </footer>
        @endif
    </v-app>

    <script src="https://kit.fontawesome.com/beb227f18d.js" crossorigin="anonymous"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/vue.js') }}"></script>
</body>
</html>
