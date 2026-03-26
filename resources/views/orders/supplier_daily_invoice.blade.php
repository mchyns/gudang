<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Gabungan Supplier {{ $date->format('Y-m-d') }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #1f2937; margin: 24px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
        .title { font-size: 24px; font-weight: 700; margin: 0; }
        .muted { color: #6b7280; font-size: 13px; }
        .supplier-block { margin-top: 16px; }
        .supplier-title { font-size: 14px; font-weight: 700; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 8px; text-align: left; font-size: 13px; }
        th { background: #f9fafb; font-size: 11px; text-transform: uppercase; color: #6b7280; }
        .right { text-align: right; }
        .total { margin-top: 16px; text-align: right; font-size: 18px; font-weight: 700; }
        .no-print { border: 1px solid #d1d5db; background: white; padding: 8px 10px; border-radius: 6px; cursor: pointer; }
        @media print { .no-print { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1 class="title">NOTA GABUNGAN PEMBELIAN SUPPLIER</h1>
            <p class="muted">Tanggal {{ $date->format('d M Y') }}</p>
            <p class="muted">HIKARI Logistik Warehouse Management System</p>
            <form method="GET" action="{{ route('admin.orders.supplier-purchase.invoice-daily') }}" class="no-print" style="margin-top: 8px; display: flex; align-items: center; gap: 8px;">
                <label for="date" class="muted" style="font-size: 12px;">Filter tanggal:</label>
                <input id="date" name="date" type="date" value="{{ $date->toDateString() }}" style="border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 8px; font-size: 12px;">
                <button type="submit" style="border: 1px solid #d1d5db; background: #ffffff; border-radius: 6px; padding: 6px 10px; font-size: 12px; cursor: pointer;">Terapkan</button>
                <a href="{{ route('admin.orders.supplier-purchase.invoice-daily') }}" style="font-size: 12px; color: #374151; text-decoration: none;">Hari Ini</a>
            </form>
        </div>
        <button class="no-print" onclick="window.print()">Cetak</button>
    </div>

    @forelse($groupedBySupplier as $supplierName => $items)
        <div class="supplier-block">
            <div class="supplier-title">Supplier: {{ $supplierName }}</div>
            <table>
                <thead>
                    <tr>
                        <th>Nama barang</th>
                        <th class="right">Qty</th>
                        <th>Satuan</th>
                        <th class="right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item->product->name ?? '-' }}</td>
                            <td class="right">{{ $item->quantity }}</td>
                            <td>{{ $item->product->unit ?? 'pcs' }}</td>
                            <td class="right">Rp {{ number_format((float) $item->price * (int) $item->quantity, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <p class="muted">Belum ada item supplier yang di-ACC untuk tanggal ini.</p>
    @endforelse

    <div class="total">Total: Rp {{ number_format($total, 0, ',', '.') }}</div>
</body>
</html>
