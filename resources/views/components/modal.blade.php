@props(['name' => 'modal', 'title' => null])

<div 
    x-data="modal(false)"
    x-show="open"
    x-cloak
    class="modal fade"
    style="display: none;"
    @keydown.escape.window="close()"
    tabindex="-1"
>
    <!-- Backdrop -->
    <div 
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="modal-backdrop fade"
        @click="close()"
    ></div>

    <!-- Modal -->
    <div class="modal-dialog modal-dialog-centered">
        <div 
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            class="modal-content rounded-xl shadow-xl"
        >
            @if($title)
                <div class="modal-header border-bottom border-stone-200 px-4 py-3">
                    <h5 class="modal-title fw-semibold text-stone-900">{{ $title }}</h5>
                    <button type="button" class="btn-close" @click="close()" aria-label="Close"></button>
                </div>
            @endif
            
            <div class="modal-body px-4 py-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    .modal.show {
        display: block !important;
    }
</style>
