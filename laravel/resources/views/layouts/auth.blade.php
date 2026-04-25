<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vidya') — Vidya</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center" style="background-color: #08080a; font-family: 'Inter', sans-serif;">

    @yield('content')

    <p class="fixed bottom-6 left-1/2 -translate-x-1/2 text-[11px] tracking-[0.55px] whitespace-nowrap" style="color: #6a665f;">
        Vidya &nbsp;·&nbsp; v1.0 &nbsp;·&nbsp; by Monoloop Productions
    </p>

</body>
</html>
