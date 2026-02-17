@php
    $isCompleted = $log && $log->is_completed;
@endphp
<div class="border-bottom p-3 sleep-hygiene-item" data-item-id="{{ $item->id }}">
    <form class="sleep-hygiene-form ajax-form" method="POST" action="{{ route('client.sleep-hygiene.store') }}" data-target=".sleep-hygiene-item[data-item-id={{ $item->id }}]">
        @csrf
        <input type="hidden" name="sleep_hygiene_item_id" value="{{ $item->id }}">
        <input type="hidden" name="log_date" value="{{ $logDate }}">
        <div class="d-flex align-items-start gap-3">
            <div class="form-check flex-shrink-0 mt-1">
                <input class="form-check-input sleep-checkbox" type="checkbox" id="item_{{ $item->id }}" {{ $isCompleted ? 'checked' : '' }}
                    onchange="var f=this.closest('form'); f.querySelector('[name=is_completed]').value=this.checked?'1':'0'; f.submit();">
                <input type="hidden" name="is_completed" value="{{ $isCompleted ? '1' : '0' }}">
            </div>
            <div class="flex-grow-1">
                <label class="form-check-label {{ $isCompleted ? 'text-decoration-line-through text-success' : '' }}" for="item_{{ $item->id }}">
                    {{ $item->label }}
                </label>
                <div class="mt-2">
                    <input type="text" class="form-control form-control-sm" name="notes" placeholder="Add a note (optional)" value="{{ e($log?->notes ?? '') }}" data-original="{{ e($log?->notes ?? '') }}"
                        onblur="if(this.value!==this.dataset.original){var f=this.closest('form');var cb=document.getElementById('item_{{ $item->id }}');if(cb)f.querySelector('[name=is_completed]').value=cb.checked?'1':'0';this.dataset.original=this.value;f.submit();}">
                </div>
            </div>
        </div>
    </form>
</div>
