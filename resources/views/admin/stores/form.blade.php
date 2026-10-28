@extends('layouts.admin')
@section('title', $store->exists ? 'Editar punto de recojo' : 'Nuevo punto de recojo')

@section('content')
    <x-admin.form :model="$store" :action="$store->exists ? route('admin.stores.update', $store) : route('admin.stores.store')">
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="label" for="factory_id">Fábrica</label>
                <select class="field" id="factory_id" name="factory_id" required>
                    @foreach ($factories as $f)
                        <option value="{{ $f->id }}" @selected(old('factory_id', $store->factory_id) == $f->id)>{{ $f->name }}</option>
                    @endforeach
                </select>
            </div>
            <x-admin.input name="name" label="Nombre" :value="$store->name" required />
            <x-admin.input name="address" label="Dirección" :value="$store->address" required class="md:col-span-2" />
            <x-admin.input name="lat" label="Latitud" type="number" step="0.0000001" :value="$store->lat" required />
            <x-admin.input name="lng" label="Longitud" type="number" step="0.0000001" :value="$store->lng" required />
            <x-admin.input name="hours" label="Horario" :value="$store->hours" placeholder="Lun–Sáb 09:00–18:00" />
            <x-admin.input name="phone" label="Teléfono" :value="$store->phone" />
        </div>
        <x-admin.checkbox name="is_factory_outlet" label="Es la tienda de la propia fábrica" :checked="$store->is_factory_outlet" />
        <x-admin.checkbox name="is_active" label="Disponible para recojo" :checked="$store->is_active" />
    </x-admin.form>
@endsection
