@extends('layouts.admin')
@section('title', $product->exists ? 'Editar producto' : 'Nuevo producto')

@section('content')
    <x-admin.form :model="$product" :action="$product->exists ? route('admin.products.update', $product) : route('admin.products.store')">
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="label" for="factory_id">Fábrica</label>
                <select class="field" id="factory_id" name="factory_id" required>
                    @foreach ($factories as $f)
                        <option value="{{ $f->id }}" @selected(old('factory_id', $product->factory_id) == $f->id)>{{ $f->name }}</option>
                    @endforeach
                </select>
            </div>
            <x-admin.input name="slug" label="Slug (URL)" :value="$product->slug" required />
            <x-admin.input name="name_es" label="Nombre (ES)" :value="$product->name_es" required />
            <x-admin.input name="name_en" label="Nombre (EN)" :value="$product->name_en" required />
            <x-admin.input name="description_es" label="Descripción (ES)" type="textarea" :value="$product->description_es" />
            <x-admin.input name="description_en" label="Descripción (EN)" type="textarea" :value="$product->description_en" />
            <x-admin.input name="price" label="Precio (Bs)" type="number" step="0.01" :value="$product->price" required />
            <x-admin.input name="stock" label="Stock" type="number" :value="$product->stock" required />
            <x-admin.input name="weight" label="Peso / contenido" :value="$product->weight" placeholder="100 g" />
            <x-admin.input name="cacao" label="% cacao" :value="$product->cacao" placeholder="70%" />
            <x-admin.input name="variety" label="Variedad" :value="$product->variety" placeholder="Bombones, tabletas, trufas…" />
        </div>
        <x-admin.image :model="$product" />
        <x-admin.checkbox name="is_active" label="Visible en la tienda" :checked="$product->is_active" />
    </x-admin.form>
@endsection
