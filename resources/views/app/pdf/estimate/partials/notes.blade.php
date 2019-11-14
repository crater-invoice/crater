@if ($estimate->notes != '' && $estimate->notes != null)
    <div class="notes">
        <div class="notes-label">
            Notes
        </div>
        {{$estimate->notes}}
    </div>
@endif
