<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('images/icon.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body class="{{ auth()->user()->dark_mode ? 'dark-mode' : '' }}">
    @php $configuration = Configuration::where('sector_id', auth()->user()->sector->id ?? null)->first(); @endphp

    <v-app id="app" class="auth-layout">
        <nav class="navbar navbar-expand-md shadow-sm p-0">
            <div class="container-fluid mx-4">
                <a class="navbar-brand" href="{{ url('/') }}">
                    @if ($configuration && $configuration->logo_url)
                        <img src="{{ $configuration->logo_url }}" alt="{{ config('app.name', 'Laravel') }}" width="200px">
                    @else
                        {{ config('app.name', 'Laravel') }}
                    @endif
                </a>

                <div class="d-flex d-md-none mobile-navigation">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#profileMenu" aria-controls="profileMenu" aria-expanded="false" aria-label="{{ __('Toggle profile navigation') }}">
                        <span class="avatar" badge="2">
                            <img src="{{ auth()->user()->avatar_url ?? asset('images/default-avatar.jpg') }}" alt="Default Avatar" width="50" height="50">
                        </span>
                        <i class="fas fa-caret-down ml-2"></i>
                    </button>

                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainMenu" aria-controls="mainMenu" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="fas fa-bars"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse d-md-none" id="profileMenu">
                    <ul class="navbar-nav align-items-end">
                        <li class="nav-item">
                            <a href="{{ route('profile') }}" class="nav-link {{ Route::is('profile') ? 'active' : '' }}">Meu perfil</a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('configuration') }}" class="nav-link {{ Route::is('configuration') ? 'active' : '' }}">{{ __('Configurations') }}</a>
                        </li>
                        
                        <dark-mode-toggle :auth-user="{{ auth()->user() }}" :dropdown-mode="false"></dark-mode-toggle>
                        
                        <li class="nav-item">
                            <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">{{ __('Logout') }}</a>

                            <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>

                <div class="collapse navbar-collapse" id="mainMenu">
                    <ul class="navbar-nav ml-auto align-items-md-center align-items-end">
                        <li class="nav-item">
                            <a href="{{ route('programmation') }}" class="nav-link {{ Route::is('programmation') ? 'active' : '' }}">{{ __('Programmations') }}</a>
                        </li>

                        @canany(['administrator', 'scheduler', 'responsible'])
                            <li class="nav-item">
                                <a href="{{ route('schedule') }}" class="nav-link {{ Route::is('schedule') ? 'active' : '' }}">{{ __('Schedules') }}</a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('space-category') }}" class="nav-link {{ Route::is('space-category') ? 'active' : '' }}">{{ __('Spaces and Categories') }}</a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('axis-occupation') }}" class="nav-link {{ Route::is('axis-occupation') ? 'active' : '' }}">{{ __('Axes and Occupations') }}</a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('custom-holiday') }}" class="nav-link {{ Route::is('custom-holiday') ? 'active' : '' }}">{{ __('Custom Holiday') }}</a>
                            </li>
                        @endcanany
                            
                        @canany(['administrator', 'responsible'])
                            <li class="nav-item">
                                <a href="{{ route('user') }}" class="nav-link {{ Route::is('user') ? 'active' : '' }}">{{ __('Users') }}</a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('log') }}" class="nav-link {{ Route::is('log') ? 'active' : '' }}">{{ __('Logs') }}</a>
                            </li>
                        @endcanany

                        @can('administrator')
                            <li class="nav-item">
                                <a href="{{ route('sector') }}" class="nav-link {{ Route::is('sector') ? 'active' : '' }}">{{ __('Sectors') }}</a>
                            </li>
                        @endcan

                        @if ($configuration && $configuration->contact)
                            <li class="nav-item">
                                <a href="mailto:{{ $configuration->contact }}" class="nav-link">
                                    <i class="fas fa-envelope"></i>
                                </a>
                            </li>
                        @endif

                        <li class="nav-item dropdown d-sm-inline-block d-none py-2">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" aria-haspopup="true" aria-expanded="false" v-pre>
                                <span class="avatar" badge="2">
                                    <img src="{{ auth()->user()->avatar_url ?? asset('images/default-avatar.jpg') }}" alt="Default Avatar" width="50" height="50">
                                </span>
                                <i class="fas fa-caret-down ml-2"></i>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a href="{{ route('profile') }}" class="dropdown-item {{ Route::is('profile') ? 'active' : '' }}">
                                    <i class="fas fa-user"></i>
                                    Meu perfil
                                </a>

                                @canany(['administrator', 'responsible'])
                                    <a class="dropdown-item {{ Route::is('configuration') ? 'active' : '' }}" href="{{ route('configuration') }}">
                                        <i class="fas fa-cog"></i>
                                        {{ __('Configurations') }}
                                    </a>
                                @endcanany

                                <dark-mode-toggle :auth-user="{{ auth()->user() }}" :dropdown-mode="true"></dark-mode-toggle>

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

        <footer class="d-flex">
            <div class="container-fluid p-md-4 p-0 mx-4">
                <div class="d-flex align-items-center justify-content-end">
                    <img src="{{ asset('images/icon-mirante.png') }}" class="me-2" width="60">
                    <span>Instituto Mirante de Cultura e Arte</span>
                    <span class="mx-2 d-none d-md-inline">|</span>
                    <span class="d-none d-md-inline">Museu da Imagem e do Som Chico Albuquerque - {{ date('Y') }}</span>
                    @if ($configuration && ($configuration->copyright))
                        <span class="mx-2 d-none d-md-inline">|</span> 
                        <span class="d-none d-md-inline">{{ $configuration->copyright }}</span>
                    @endif
                </div>
            </div>
        </footer>
    </v-app>

    <script src="https://kit.fontawesome.com/beb227f18d.js" crossorigin="anonymous"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/vue.js') }}"></script>
</body>
</html>
