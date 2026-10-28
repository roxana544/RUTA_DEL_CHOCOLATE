@extends('layouts.admin')
@section('title', $factory->exists ? 'Editar fábrica' : 'Nueva fábrica')

@section('content')
    <x-admin.form :model="$factory" :action="$factory->exists ? route('admin.factories.update', $factory) : route('admin.factories.store')">
        <div class="grid gap-4 md:grid-cols-2">
            <x-admin.input name="name" label="Nombre" :value="$factory->name" required />
            <x-admin.input name="slug" label="Slug (URL)" :value="$factory->slug" required />
            <x-admin.input name="tagline_es" label="Lema (ES)" :value="$factory->tagline_es" />
            <x-admin.input name="tagline_en" label="Lema (EN)" :value="$factory->tagline_en" />
            <x-admin.input name="description_es" label="Descripción (ES)" type="textarea" :value="$factory->description_es" />
            <x-admin.input name="description_en" label="Descripción (EN)" type="textarea" :value="$factory->description_en" />
            <x-admin.input name="address" label="Dirección" :value="$factory->address" required class="md:col-span-2" />
            <x-admin.input name="visit_hours_es" label="Horario de visita (ES)" :value="$factory->visit_hours_es" placeholder="Lun–Vie 09:00–17:00" />
            <x-admin.input name="visit_hours_en" label="Horario de visita (EN)" :value="$factory->visit_hours_en" placeholder="Mon–Fri 09:00–17:00" />
            <x-admin.input name="phone" label="Teléfono" :value="$factory->phone" />
            <x-admin.input name="website" label="Sitio web" type="url" :value="$factory->website" placeholder="https://" />
            <x-admin.input name="social_url" label="Red social (URL)" type="url" :value="$factory->social_url" placeholder="https://facebook.com/..." />
            <x-admin.input name="lat" label="Latitud" type="number" step="0.0000001" :value="$factory->lat" required />
            <x-admin.input name="lng" label="Longitud" type="number" step="0.0000001" :value="$factory->lng" required />
            <x-admin.input name="founded_year" label="Año de fundación" type="number" :value="$factory->founded_year" />
            <x-admin.input name="route_order" label="Orden en la ruta" type="number" :value="$factory->route_order ?? 0" required />
            <x-admin.input name="color" label="Color del marcador" type="color" :value="$factory->color" class="w-40" />
        </div>
        <p class="text-xs text-cream-100/50">Tip: en Google Maps haz clic derecho sobre la fábrica para copiar latitud y longitud.</p>
        <x-admin.image :model="$factory" />
        <x-admin.checkbox name="has_museum" label="Tiene museo abierto al público" :checked="$factory->has_museum" />
        <x-admin.checkbox name="is_active" label="Visible en la web" :checked="$factory->is_active" />
    </x-admin.form>
@endsection
