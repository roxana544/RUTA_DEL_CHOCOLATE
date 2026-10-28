<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingresar · Admin Ruta del Chocolate</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grid min-h-screen place-items-center bg-cacao-950 px-4">
    <form method="POST" action="{{ route('admin.login') }}" class="card w-full max-w-sm space-y-4 p-8">
        @csrf
        <h1 class="font-display text-2xl font-semibold">Panel de administración</h1>
        <div>
            <label class="label" for="email">Correo</label>
            <input class="field" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label" for="password">Contraseña</label>
            <input class="field" type="password" id="password" name="password" required>
        </div>
        <label class="flex items-center gap-2 text-sm text-cream-100/70"><input type="checkbox" name="remember" class="accent-[#d9a441]"> Recordarme</label>
        <button class="btn btn-primary w-full">Ingresar</button>
    </form>
</body>
</html>
