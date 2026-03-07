@props(['type' => 'text', 'label' => null, 'error' => null, 'required' => false])

<div class="mb-3">
    @if($label)
        <label for="{{ $attributes->get('id', $attributes->get('name')) }}" class="form-label text-stone-700 fw-semibold">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <input 
        type="{{ $type }}"
        id="{{ $attributes->get('id', $attributes->get('name')) }}"
        {{ $attributes->except('id')->merge([
            'class' => 'form-control ' . ($error ? 'is-invalid' : '')
        ]) }}
    >
    
    @if($error)
        <div class="invalid-feedback d-block">
            {{ $error }}
        </div>
    @endif
</div>
