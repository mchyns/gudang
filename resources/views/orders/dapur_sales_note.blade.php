<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Penjualan Gudang #{{ $order->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #1f2937; margin: 24px; }
        .header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px; }
        .title { font-size: 24px; font-weight: 700; margin: 0; }
        .muted { color: #6b7280; font-size: 13px; }
        .box { border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 8px; text-align: left; font-size: 13px; }
        th { background: #f9fafb; font-size: 11px; text-transform: uppercase; color: #6b7280; }
        .right { text-align: right; }
        .total { font-size: 20px; font-weight: 700; color: #111827; }
        @media print { .no-print { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1 class="title">NOTA PENJUALAN GUDANG #{{ $order->id }}</h1>
            <p class="muted">{{ config('app.name', 'HIKARISOU') }} Warehouse Management System</p>
        </div>
        <button class="no-print" onclick="window.print()">Cetak</button>
    </div>

    <div class="box">
        <div><strong>Penjual:</strong> {{ $sellerName }}</div>
        <div><strong>Pembeli:</strong> {{ $order->user->name ?? 'Mitra Dapur' }}</div>
        <div><strong>Tanggal:</strong> {{ $order->created_at->format('d M Y H:i') }}</div>
        <div><strong>Tanggal Fix:</strong> {{ optional($order->dapur_sales_note_locked_at)->format('d M Y H:i') ?? '-' }}</div>
        <div><strong>Status:</strong> {{ ucfirst($order->status) }}</div>
        <div><strong>Catatan:</strong> {{ $order->note ?: '-' }}</div>
        <div><strong>Catatan Penyesuaian Dapur:</strong> {{ $order->dapur_adjustment_note ?: '-' }}</div>
    </div>

    @php $grandTotal = 0; @endphp
    <table>
        <thead>
            <tr>
                <th>Nama barang</th>
                <th>Spesifikasi</th>
                <th class="right">Qty Order</th>
                <th class="right">Qty Final</th>
                <th>Satuan</th>
                <th class="right">Harga Satuan</th>
                <th class="right">Jumlah</th>
                <th>Catatan Dapur</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $item)
                @php
                    $price = (float) ($item->price ?? 0);
                    $finalQuantity = (int) ($item->dapur_final_quantity ?? $item->quantity);
                    $subtotal = $price * $finalQuantity;
                    $grandTotal += $subtotal;
                @endphp
                <tr>
                    <td>{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($item->product->description ?? '-', 70) }}</td>
                    <td class="right">{{ $item->quantity }}</td>
                    <td class="right">{{ $finalQuantity }}</td>
                    <td>{{ strtoupper($item->product->unit ?? 'pcs') }}</td>
                    <td class="right">Rp {{ number_format($price, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    <td>{{ $item->dapur_item_note ?: '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="right" style="margin-top: 12px;">
        <span class="total">Total: Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
    </div>
</body>
</html>
