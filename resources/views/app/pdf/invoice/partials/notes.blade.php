@if ($invoice->notes != '' && $invoice->notes != null)
    <div class="notes">
        <div class="notes-label">
            Notes
        </div>
        {!! $invoice->notes !!}
    </div>
@endif
