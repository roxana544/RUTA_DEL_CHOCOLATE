@extends('layouts.app')
@section('title', __('Crear cuenta'))

@section('content')
    <section class="px-4 pb-28 pt-36 sm:px-6">
        <form method="POST" action="{{ route('register') }}" class="card mx-auto max-w-md space-y-4 p-8">
            @csrf
            <h1 class="font-display text-3xl font-semibold">{{ __('Crea tu cuenta') }}</h1>
            <p class="text-sm text-cream-100/60">{{ __('Registra tus checkpoints, junta puntos y desbloquea insignias recorriendo la ruta.') }}</p>
            <div><label class="label" for="name">{{ __('Nombre completo') }}</label><input class="field" id="name" name="name" value="{{ old('name') }}" required autofocus>@error('name')<p class="error">{{ $message }}</p>@enderror</div>
            <div><label class="label" for="email">{{ __('Correo electrónico') }}</label><input class="field" type="email" id="email" name="email" value="{{ old('email') }}" required>@error('email')<p class="error">{{ $message }}</p>@enderror</div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="label" for="phone">{{ __('Teléfono') }}</label><input class="field" id="phone" name="phone" value="{{ old('phone') }}"></div>
                <div><label class="label" for="origin_city">{{ __('Ciudad de origen') }}</label><input class="field" id="origin_city" name="origin_city" value="{{ old('origin_city') }}"></div>
            </div>
            <div><label class="label" for="password">{{ __('Contraseña') }}</label><input class="field" type="password" id="password" name="password" required minlength="8">@error('password')<p class="error">{{ $message }}</p>@enderror</div>
            <div><label class="label" for="password_confirmation">{{ __('Repite la contraseña') }}</label><input class="field" type="password" id="password_confirmation" name="password_confirmation" required></div>
            <label class="flex items-start gap-2 text-xs text-cream-100/60"><input type="checkbox" name="marketing_consent" value="1" class="mt-0.5 accent-[#d9a441]"> {{ __('Quiero recibir novedades y recordatorios de la ruta.') }}</label>
            <button class="btn btn-primary w-full">{{ __('Crear cuenta') }}</button>
            <p class="text-center text-sm text-cream-100/60">{{ __('¿Ya tienes cuenta?') }} <a class="text-gold hover:underline" href="{{ route('login') }}">{{ __('Ingresar') }}</a></p>
        </form>
    </section>
@endsection
