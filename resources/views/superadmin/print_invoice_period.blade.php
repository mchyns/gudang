<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota {{ $label }} - Superadmin</title>
    <style>
        body { font-family: Arial, sans-serif; color: #1f2937; margin: 24px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
        .title { font-size: 24px; font-weight: 700; margin: 0; }
        .muted { color: #6b7280; font-size: 13px; }
        .box { border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 8px; text-align: left; font-size: 13px; }
        th { background: #f9fafb; font-size: 11px; text-transform: uppercase; color: #6b7280; }
        .right { text-align: right; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 999px; font-size: 11px; background: #eef2ff; color: #3730a3; }
        .no-print { border: 1px solid #d1d5db; background: white; padding: 8px 10px; border-radius: 6px; cursor: pointer; }
        @media print { .no-print { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1 class="title">NOTA {{ strtoupper($label) }}</h1>
            <p class="muted">Periode {{ $start->format('d M Y H:i') }} - {{ $end->format('d M Y H:i') }}</p>
            <p class="muted">HIKARI Logistik Warehouse Management System</p>
        </div>
        <button class="no-print" onclick="window.print()">Cetak</button>
    </div>

    <div class="box">
        <div><strong>Total Harga Beli Supplier (H.B.S):</strong> Rp {{ number_format($hargaBeliSupplier, 0, ',', '.') }}</div>
        <div><strong>Total Operasional:</strong> Rp {{ number_format($operasionalTotal, 0, ',', '.') }}</div>
        <div><strong>Total Harga Beli Dapur:</strong> Rp {{ number_format($hargaBeliDapur, 0, ',', '.') }}</div>
        <div><strong>Laba Kotor (H.B.S + Operasional - Harga Beli Dapur):</strong> Rp {{ number_format($labaKotor, 0, ',', '.') }}</div>
        <div><strong>Total Order:</strong> {{ $orders->count() }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Order</th>
                <th>Tanggal</th>
                <th>Mitra Dapur</th>
                <th>Status</th>
                <th class="right">Harga Supplier</th>
                <th class="right">Operasional</th>
                <th class="right">Harga Dapur</th>
                <th class="right">Laba Kotor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                @php
                    $supplierTotalPerOrder = $order->orderItems->sum(function ($item) {
                        return ($item->quantity ?? 0) * ($item->product->supplier_price ?? 0);
                    });
                    $opsPerOrder = (float) (
                        ($order->operational_bensin ?? 0)
                        + ($order->operational_kuli ?? 0)
                        + ($order->operational_makan_minum ?? 0)
                        + ($order->operational_listrik ?? 0)
                        + ($order->operational_wifi ?? 0)
                    );
                    $grossPerOrder = ($supplierTotalPerOrder + $opsPerOrder) - (float) $order->total_price;
                @endphp
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $order->user->name ?? '-' }}</td>
                    <td><span class="badge">{{ ucfirst($order->status) }}</span></td>
                    <td class="right">Rp {{ number_format($supplierTotalPerOrder, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($opsPerOrder, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($grossPerOrder, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="right">Tidak ada order untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
