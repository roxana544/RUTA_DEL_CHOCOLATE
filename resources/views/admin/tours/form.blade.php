@extends('layouts.admin')
@section('title', $tour->exists ? 'Editar tour' : 'Nuevo tour')

@section('content')
    @php($selected = old('factories', $tour->exists ? $tour->factories->pluck('id')->all() : []))
    @php($days = old('weekdays', $tour->weekdays ?? []))
    <x-admin.form :model="$tour" :action="$tour->exists ? route('admin.tours.update', $tour) : route('admin.tours.store')">
        <div class="grid gap-4 md:grid-cols-2">
            <x-admin.input name="name_es" label="Nombre (ES)" :value="$tour->name_es" required />
            <x-admin.input name="name_en" label="Nombre (EN)" :value="$tour->name_en" required />
            <x-admin.input name="slug" label="Slug (URL)" :value="$tour->slug" required />
            <x-admin.input name="price" label="Precio por persona (Bs)" type="number" step="0.01" :value="$tour->price" required />
            <x-admin.input name="duration_minutes" label="Duración (minutos)" type="number" :value="$tour->duration_minutes" required />
            <x-admin.input name="capacity" label="Cupos por salida" type="number" :value="$tour->capacity" required />
            <x-admin.input name="departure_times" label="Horarios de salida (HH:MM, separados por comas)" :value="implode(', ', $tour->departure_times ?? [])" required class="md:col-span-2" />
            <x-admin.input name="meeting_point_es" label="Punto de encuentro (ES)" :value="$tour->meeting_point_es" placeholder="Plaza 25 de Mayo, frente a la Catedral" />
            <x-admin.input name="meeting_point_en" label="Punto de encuentro (EN)" :value="$tour->meeting_point_en" />
            <x-admin.input name="meeting_lat" label="Latitud del encuentro" type="number" step="0.0000001" :value="$tour->meeting_lat" />
            <x-admin.input name="meeting_lng" label="Longitud del encuentro" type="number" step="0.0000001" :value="$tour->meeting_lng" />
            <x-admin.input name="description_es" label="Descripción (ES)" type="textarea" :value="$tour->description_es" />
            <x-admin.input name="description_en" label="Descripción (EN)" type="textarea" :value="$tour->description_en" />
        </div>

        <div>
            <span class="label">Días de salida</span>
            <div class="flex flex-wrap gap-4 text-sm">
                @foreach ([1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 7 => 'Dom'] as $n => $d)
                    <label class="flex items-center gap-1.5"><input type="checkbox" name="weekdays[]" value="{{ $n }}" class="accent-[#d9a441]" @checked(in_array($n, array_map('intval', $days)))> {{ $d }}</label>
                @endforeach
            </div>
            @error('weekdays')<p class="error">{{ $message }}</p>@enderror
        </div>

        <div>
            <span class="label">Fábricas que visita (en orden de la ruta)</span>
            <div class="flex flex-wrap gap-4 text-sm">
                @foreach ($factories as $f)
                    <label class="flex items-center gap-1.5"><input type="checkbox" name="factories[]" value="{{ $f->id }}" class="accent-[#d9a441]" @checked(in_array($f->id, array_map('intval', $selected)))> {{ $f->name }}</label>
                @endforeach
            </div>
            @error('factories')<p class="error">{{ $message }}</p>@enderror
        </div>

        <x-admin.image :model="$tour" />
        <x-admin.checkbox name="is_active" label="Visible y reservable" :checked="$tour->is_active" />
    </x-admin.form>
@endsection
