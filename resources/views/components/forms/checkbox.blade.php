<div class="form-check">
    <input 
        type="checkbox" 
        name="{{ $name }}" 
        id="{{ $id ?? $name }}"
        value="{{ $value }}"
        @if(old($name, $checked)) checked @endif
        {{ $attributes->merge(['class' => 'form-check-input']) }}
    >
    
    @if($label)
        <label for="{{ $id ?? $name }}" class="form-check-label">{{ $label }}</label>
    @endif
</div>