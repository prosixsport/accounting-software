<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>House Expense Report</title>
    <style>
        body{font:14px system-ui,sans-serif;color:#16202c;margin:24px}h1{margin:8px 0}
        table{width:100%;border-collapse:collapse;margin:16px 0}th,td{border:1px solid #aab3bc;padding:7px;text-align:left}
        th,.subtotal{background:#eff4f6}.num{text-align:right;white-space:nowrap}.total{font-size:19px}
        @page{size:A4;margin:12mm}
        @media print{body{margin:0}.toolbar{display:none}thead{display:table-header-group}tr{break-inside:avoid}}
    </style>
</head>
<body>
@php
    $label = static fn ($day) => \Illuminate\Support\Carbon::parse($day)->format('d M Y');
    $money = static fn ($paise) => 'Rs '.number_format(intdiv((int) $paise, 100))
        .'.'.str_pad((string) ((int) $paise % 100), 2, '0', STR_PAD_LEFT);
@endphp
<button class="toolbar" type="button" onclick="window.print()">Print / Save PDF</button>
<h1>House Expense Report</h1>
<p>{{ $label($from) }} to {{ $label($to) }}</p>
<p class="total"><strong>Total spent: {{ $money($totalPaise) }}</strong></p>
<table>
    <thead><tr><th>Date</th><th>Category</th><th>Item / details</th><th>Shop / place</th><th class="num">Amount</th></tr></thead>
    <tbody>
        @forelse($days as $date => $entries)
            @foreach($entries as $entry)
                <tr><td>{{ $label($date) }}</td><td>{{ $entry->category }}</td><td>{{ $entry->description }}</td><td>{{ $entry->location ?: '—' }}</td><td class="num">{{ $money($entry->amount_paise) }}</td></tr>
            @endforeach
            <tr class="subtotal"><th colspan="4" scope="row">{{ $label($date) }} total</th><td class="num"><strong>{{ $money($entries->sum('amount_paise')) }}</strong></td></tr>
        @empty
            <tr><td colspan="5">No expenses recorded in this date range.</td></tr>
        @endforelse
    </tbody>
</table>
</body>
</html>
