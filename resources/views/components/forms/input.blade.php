{{-- resources/views/components/forms/input.blade.php --}}

@props([
    'name',
    'label' => null,
    'type' => 'text',
    'id' => null,
    'value' => ''
])

<div class="form-input mb-4">
    <label for="{{ $id ?? $name }}">{{ $label ?? ucfirst($name) }}</label>
    <input 
        type="{{ $type }}" 
        name="{{ $name }}" 
        id="{{ $id ?? $name }}"
        @if($type === 'checkbox' && $value)
            checked="{{ old($name, $value) }}"
        @endif
        value="{{ old($name, $value) }}"
        
        @error($name)
            {{ $attributes->merge(['class' => 'error']) }}
        @enderror
    >
    @error($name)
        <p class="error">{{ $message }}</p>
    @enderror
</div>