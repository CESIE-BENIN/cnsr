<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <!-- Bootstrap & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/formulaire.css') }}">
    <link rel="shortcut icon" href="{{ asset('image/cnsr.jpg') }}" type="image/x-icon">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* RESET TOTAL */
        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        /* LAYOUT */
        .layout {
            display: flex;
            width: 100%;
            height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            background-color: #1c1c1c;
            color: #fff;
            display: flex;
            flex-direction: column;
        }

        .sidebar a {
            color: #fff;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #343a40;
        }

        /* MAIN */
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        /* HEADER */
        .header {
            height: 56px;
            background: linear-gradient(90deg, #ff8c00, #0d223a);
            color: #fff;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* CONTENT */
        .content {
            flex: 1;
            overflow-y: auto;
            padding: 0; /* ZÉRO marge */
        }
    </style>
</head>

<body>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
    <h4 class="px-3 py-3 mb-0">CNSR</h4>

    <ul class="nav flex-column px-2">

        {{-- ADMIN --}}
        @if(auth()->user()->isAdmin())
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link">
                    <i class="fas fa-chart-line me-2"></i> Dashboard
                </a>
            </li>
        @endif

        {{-- UTILISATEUR SIMPLE --}}
        @if(!auth()->user()->isAdmin())
            <li class="nav-item">
                <a href="{{ route('formulaire') }}" class="nav-link">
                    <i class="fas fa-car-crash me-2"></i> Accidents
                </a>
            </li>
        @endif

    </ul>
</aside>

    <!-- MAIN -->
    <main class="main">

        <!-- HEADER -->
        <nav class="header">
            <span><h3 class="mb-4">Accidents</h3></span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-light btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </button>
            </form>
        </nav>

        <!-- CONTENT -->
        <section class="content">
            @yield('content')
        </section>

    </main>

</div>

</body>
</html>
