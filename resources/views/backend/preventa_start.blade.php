<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('backend/css/main.css') }}" rel="stylesheet" type="text/css" />
    {{-- Bootstrap 5 si no lo tienes cargado ya --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #5867dd 0%, #34bfa3 100%);
            color: #fff;
        }
        .welcome-card {
            background-color: rgba(255,255,255,0.1);
            backdrop-filter: blur(8px);
            border-radius: 1.5rem;
            padding: 3rem 2rem;
            box-shadow: 0 0 25px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        .welcome-card h1 {
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        .btn-start {
            font-size: 1.25rem;
            padding: .75rem 2.5rem;
            border-radius: 50rem;
        }
    </style>
</head>
<body>
    <div class="welcome-card">
        <h1>Bienvenido</h1>
        <p class="mb-4">Gestiona tus ventas, cobranzas y liquidaciones de forma sencilla.</p>
        <a href="{{ route('dashboard.preventa.index') }}" class="btn btn-light btn-start">
            🚀 Iniciar
        </a>
    </div>

    {{-- Bootstrap JS si no está ya cargado --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
