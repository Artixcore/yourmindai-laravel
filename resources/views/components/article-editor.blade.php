@props([
    'name' => 'content',
    'value' => '',
    'required' => false
])

<div {{ $attributes->merge(['class' => 'article-editor-wrapper']) }}>
    <textarea 
        name="{{ $name }}" 
        id="{{ $name }}" 
        class="tinymce-editor"
        {{ $required ? 'required' : '' }}
    >{{ $value }}</textarea>
</div>

@push('scripts')
<script>
    // TinyMCE will be initialized here
    // See tinymce-config.js for full configuration
</script>
@endpush
