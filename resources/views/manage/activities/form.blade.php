@extends('layouts.admin')
@section('title', $activity->exists ? 'Editar actividad' : 'Nueva actividad')

@section('content')
    @php($cfg = $activity->config ?? [])
    <x-admin.form :model="$activity" :action="$activity->exists ? route('manage.activities.update', $activity) : route('manage.activities.store')">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="md:col-span-2"><label class="label" for="tour_stop_id">Parada</label>
                <select class="field" id="tour_stop_id" name="tour_stop_id" required>@foreach ($stops as $s)<option value="{{ $s->id }}" @selected((int) old('tour_stop_id', $activity->tour_stop_id) === $s->id)>{{ $s->tour->name_es }} → {{ $s->factory->name }}</option>@endforeach</select></div>
            <x-admin.input name="name_es" label="Nombre (ES)" :value="$activity->name_es" required />
            <x-admin.input name="name_en" label="Nombre (EN)" :value="$activity->name_en" required />
            <x-admin.input name="description_es" label="Descripción (ES)" type="textarea" :value="$activity->description_es" />
            <x-admin.input name="description_en" label="Descripción (EN)" type="textarea" :value="$activity->description_en" />
            <div><label class="label" for="type">Tipo</label><select class="field" id="type" name="type">@foreach (\App\Models\Activity::TYPES as $k => $l)<option value="{{ $k }}" @selected(old('type', $activity->type) === $k)>{{ $l }}</option>@endforeach</select></div>
            <x-admin.input name="max_points" label="Puntos máximos" type="number" :value="$activity->max_points" required />
        </div>
        <fieldset class="space-y-4 rounded-xl border border-cream-100/10 p-4">
            <legend class="px-2 text-sm text-cream-100/60">Solo para tipo "Pregunta (quiz)"</legend>
            <x-admin.input name="question_es" label="Pregunta (ES)" :value="$cfg['question_es'] ?? null" />
            <x-admin.input name="question_en" label="Pregunta (EN)" :value="$cfg['question_en'] ?? null" />
            <div class="grid gap-4 md:grid-cols-2">
                <x-admin.input name="options_es" label="Opciones (ES, una por línea)" type="textarea" :value="implode(\"\n\", $cfg['options_es'] ?? [])" />
                <x-admin.input name="options_en" label="Opciones (EN, una por línea)" type="textarea" :value="implode(\"\n\", $cfg['options_en'] ?? [])" />
            </div>
            <x-admin.input name="answer" label="Número de la opción correcta (1, 2, 3…)" type="number" :value="isset($cfg['answer']) ? $cfg['answer'] + 1 : 1" class="w-48" />
        </fieldset>
        <x-admin.checkbox name="is_active" label="Activa" :checked="$activity->is_active" />
    </x-admin.form>
@endsection
