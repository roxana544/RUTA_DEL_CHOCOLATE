@props(['name', 'label', 'value' => null, 'type' => 'text'])
<div {{ $attributes->only('class') }}>
    <label class="label" for="{{ $name }}">{{ $label }}</label>
    @if ($type === 'textarea')
        <textarea class="field" id="{{ $name }}" name="{{ $name }}" rows="4" {{ $attributes->except('class') }}>{{ old($name, $value) }}</textarea>
    @else
        <input class="field" type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}" {{ $attributes->except('class') }}>
    @endif
    @error($name)<p class="error">{{ $message }}</p>@enderror
</div>
