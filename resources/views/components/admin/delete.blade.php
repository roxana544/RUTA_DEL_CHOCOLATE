@props(['action'])
<form method="POST" action="{{ $action }}" onsubmit="return confirm('¿Eliminar definitivamente?')" class="inline">
    @csrf @method('DELETE')
    <button class="text-red-300 hover:underline">Eliminar</button>
</form>
