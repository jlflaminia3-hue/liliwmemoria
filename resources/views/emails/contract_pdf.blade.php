<p>Hello{{ $contract->client?->full_name ? ' '.$contract->client->full_name : '' }},</p>

<p>
    Attached is your contract PDF
    {{ $contract->contract_number ? '(Contract '.$contract->contract_number.')' : '' }}.
</p>

<p class="text-muted">This PDF is system-generated for standardized formatting and record-keeping.</p>

