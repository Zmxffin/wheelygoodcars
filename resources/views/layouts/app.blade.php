<!doctype html>
<html lang="nl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>WheelyGoodCars</title>
        @vite(['resources/css/app.scss', 'resources/js/app.js'])
        @stack('head')
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-dark d-print-none bg-black">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('home') }}"><strong class="text-primary">Wheely</strong> good cars<strong class="text-primary">!</strong></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link text-light" href="{{ route('public.index') }}">Alle auto's</a></li>
                        @auth
                            <li class="nav-item"><a class="nav-link text-light" href="{{ route('cars.index') }}">Mijn aanbod</a></li>
                            <li class="nav-item"><a class="nav-link text-light" href="{{ route('cars.create') }}">Aanbod plaatsen</a></li>
                            <li class="nav-item dropdown">
                                <a class="nav-link text-light dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Beheer</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.tags') }}">Tag-statistieken</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.notable') }}">Opvallende aanbieders</a></li>
                                </ul>
                            </li>
                        @endauth
                    </ul>
                    <ul class="navbar-nav">
                        @guest
                            <li class="nav-item"><a class="nav-link text-secondary" href="{{ route('register') }}">Registreren</a></li>
                            <li class="nav-item"><a class="nav-link text-secondary" href="{{ route('login') }}">Inloggen</a></li>
                        @endguest
                        @auth
                            <li class="nav-item"><span class="nav-link text-light disabled">{{ Auth::user()->name }}</span></li>
                            <li class="nav-item"><a class="nav-link text-secondary" href="{{ route('logout') }}">Uitloggen</a></li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        @if (session('status'))
            <div class="container mt-3 d-print-none">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        <div class="container py-4">
            @yield('content')
        </div>

        @stack('scripts')
    </body>
</html>
