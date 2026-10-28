@props(['action', 'model'])
<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="card max-w-4xl space-y-5 p-6 md:p-8">
    @csrf
    @if ($model->exists) @method('PUT') @endif
    {{ $slot }}
    <div class="flex gap-3 border-t border-cream-100/10 pt-5">
        <button class="btn btn-primary">Guardar</button>
        <a href="{{ url()->previous() }}" class="btn btn-ghost">Cancelar</a>
    </div>
</form>
