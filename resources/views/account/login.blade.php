@extends('layouts.app')
@section('title', __('Ingresar'))

@section('content')
    <section class="px-4 pb-28 pt-36 sm:px-6">
        <form method="POST" action="{{ route('login') }}" class="card mx-auto max-w-md space-y-4 p-8">
            @csrf
            <h1 class="font-display text-3xl font-semibold">{{ __('Entrar') }}</h1>
            <div><label class="label" for="email">{{ __('Correo electrónico') }}</label><input class="field" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>@error('email')<p class="error">{{ $message }}</p>@enderror</div>
            <div><label class="label" for="password">{{ __('Contraseña') }}</label><input class="field" type="password" id="password" name="password" required></div>
            <label class="flex items-center gap-2 text-sm text-cream-100/70"><input type="checkbox" name="remember" class="accent-[#d9a441]"> {{ __('Recordarme') }}</label>
            <button class="btn btn-primary w-full">{{ __('Entrar') }}</button>
            <p class="text-center text-sm text-cream-100/60">{{ __('¿Aún no tienes cuenta?') }} <a class="text-gold hover:underline" href="{{ route('register') }}">{{ __('Regístrate') }}</a></p>
        </form>
    </section>
@endsection
