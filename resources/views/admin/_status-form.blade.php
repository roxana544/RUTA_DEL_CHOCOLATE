<form method="POST" action="{{ $action }}">
    @csrf @method('PATCH')
    <select name="status" class="rounded-lg border border-cream-100/15 bg-cacao-900 px-2 py-1 text-xs" onchange="this.form.submit()" aria-label="Cambiar estado">
        @foreach ($statuses as $s)
            <option value="{{ $s }}" @selected($current === $s)>{{ __('status.'.$s, [], 'es') }}</option>
        @endforeach
    </select>
</form>
