@extends('layouts.admin')
@section('title', 'Usuarios')

@section('content')
    <form class="mb-6 flex flex-wrap gap-3">
        <input class="field max-w-xs" name="q" value="{{ request('q') }}" placeholder="Buscar nombre o correo">
        <select class="field max-w-[12rem]" name="rol" onchange="this.form.submit()"><option value="">Todos los roles</option>@foreach (\App\Models\User::ROLES as $k => $l)<option value="{{ $k }}" @selected(request('rol') === $k)>{{ $l }}</option>@endforeach</select>
        <button class="btn btn-ghost">Filtrar</button>
    </form>
    <x-admin.table :head="['Nombre', 'Correo', 'Teléfono', 'Inscripciones', 'Rol']">
        @foreach ($users as $u)
            <tr>
                <td class="px-4 py-3 font-semibold">{{ $u->name }}</td><td class="px-4 py-3">{{ $u->email }}</td><td class="px-4 py-3">{{ $u->phone }}</td><td class="px-4 py-3">{{ $u->enrollments_count }}</td>
                <td class="px-4 py-3"><form method="POST" action="{{ route('admin.users.update', $u) }}" class="flex gap-2">@csrf @method('PATCH')
                    <select class="field w-44" name="role" onchange="this.form.submit()">@foreach (\App\Models\User::ROLES as $k => $l)<option value="{{ $k }}" @selected($u->role === $k)>{{ $l }}</option>@endforeach</select></form></td>
            </tr>
        @endforeach
    </x-admin.table>
    <div class="mt-4">{{ $users->links() }}</div>
@endsection
