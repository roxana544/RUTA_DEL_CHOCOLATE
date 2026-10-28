@props(['model'])
<div>
    <label class="label" for="image">Imagen</label>
    @if ($model->imageUrl())
        <img src="{{ $model->imageUrl() }}" alt="" class="mb-3 h-32 rounded-xl object-cover">
    @endif
    <input type="file" id="image" name="image" accept="image/*" class="text-sm text-cream-100/70 file:mr-4 file:rounded-full file:border-0 file:bg-cream-100/10 file:px-4 file:py-2 file:text-cream-100">
    @error('image')<p class="error">{{ $message }}</p>@enderror
</div>
