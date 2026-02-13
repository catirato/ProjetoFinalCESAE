<!--Para a WELCOME PAGE, HELLO PAGE, ADICIONAR USER, TODOS OS USERS, TODAS AS TAREFAS, ADICIONAR TAREFA-->


<!DOCTYPE html>
<html lang="en" data-theme="lofi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) ? $title . ' - Estacionamento' : 'Estacionamento' }}</title>
    {{-- <title>{{ isset($title) ? $title . ' - Chirper' : 'Chirper' }}</title> --}}
    {{-- <title>Estacionamento CESAE - Home</title> --}}
    <link rel="preconnect" href="<https://fonts.bunny.net>">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex flex-col bg-base-200 font-sans">
    <nav class="navbar bg-base-100">
        <div class="navbar-start">
            <a href="/" class="btn btn-ghost text-xl">🐦CESAE Estacionamento </a>
        </div>
        <div class="navbar-end gap-2">
            <!--ir para a route do login-->
            <a href="/login" class="btn btn-primary btn-sm">Login</a>
            <!--ir para a route do register-->
            {{-- <a href="/register" class="btn btn-primary btn-sm">Registar</a> --}}
        </div>
    </nav>

    <main class="flex-1 container mx-auto px-4 py-8">
      {{-- {{$slot}} --}}
      @yield('content')
    </main>

    <footer class="footer footer-center p-5 bg-base-300 text-base-content text-xs">
        <div>
            <p>© 2026 CESAE Digital - Built with Laravel and ❤️</p>
        </div>
    </footer>
</body>

</html>