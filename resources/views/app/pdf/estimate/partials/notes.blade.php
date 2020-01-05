@if ($estimate->notes != '' && $estimate->notes != null)
    <div class="notes">
        <div class="notes-label">
            Notes
        </div>
        {!! nl2br(htmlspecialchars($estimate->notes)) !!}
    </div>
@endif
