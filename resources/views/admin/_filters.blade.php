<form class="mb-6 flex flex-wrap gap-3">
    <input class="field max-w-xs" name="q" value="{{ request('q') }}" placeholder="Buscar código, nombre o correo">
    <select class="field max-w-[12rem]" name="status" onchange="this.form.submit()">
        <option value="">Todos los estados</option>
        @foreach ($statuses as $s)
            <option value="{{ $s }}" @selected($status === $s)>{{ __('status.'.$s, [], 'es') }}</option>
        @endforeach
    </select>
    <button class="btn btn-ghost">Filtrar</button>
</form>
