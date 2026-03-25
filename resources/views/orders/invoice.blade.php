<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Order #{{ $order->id }}</title>
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
        .total { font-size: 18px; font-weight: 700; color: #111827; }
        @media print { .no-print { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1 class="title">INVOICE #{{ $order->id }}</h1>
            <p class="muted">HIKARI Logistik Warehouse Management System</p>
        </div>
        <button class="no-print" onclick="window.print()">Cetak</button>
    </div>

    <div class="box">
        <div><strong>Penerima:</strong> {{ $viewerRole === 'supplier' ? 'Gudang' : ($order->user->name ?? '-') }}</div>
        <div><strong>Tanggal:</strong> {{ $order->created_at->format('d M Y H:i') }}</div>
        <div><strong>Status:</strong> {{ ucfirst($order->status) }}</div>
        <div><strong>Catatan:</strong> {{ $viewerRole === 'supplier' ? ($order->supplier_note ?: '-') : ($order->note ?: '-') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama barang</th>
                <th class="right">Qty</th>
                <th>Satuan</th>
                <th class="right">Harga</th>
                <th class="right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotal = 0;
                $supplierBaseTotal = 0;
            @endphp
            @foreach($invoiceItems as $item)
                @php
                    $supplierUnitPrice = $item->product->supplier_price ?? 0;
                    $unitPrice = $useSupplierPrice
                        ? $supplierUnitPrice
                        : $item->price;
                    $subtotal = $item->quantity * $unitPrice;
                    $supplierSubtotal = $item->quantity * $supplierUnitPrice;
                    $grandTotal += $subtotal;
                    $supplierBaseTotal += $supplierSubtotal;
                @endphp
                <tr>
                    <td>{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                    <td class="right">{{ $item->quantity }}</td>
                    <td>{{ $item->product->unit ?? 'pcs' }}</td>
                    <td class="right">Rp {{ number_format($unitPrice, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(in_array($viewerRole, ['admin', 'superadmin']))
        @php
            $operasionalTotal = (float) (
                ($order->operational_bensin ?? 0)
                + ($order->operational_kuli ?? 0)
                + ($order->operational_makan_minum ?? 0)
                + ($order->operational_listrik ?? 0)
                + ($order->operational_wifi ?? 0)
            );
            $hargaBeliDapur = (float) $order->total_price;
            $labaKotor = ($supplierBaseTotal + $operasionalTotal) - $hargaBeliDapur;
        @endphp

        <div class="box" style="margin-top: 12px;">
            <div><strong>Harga beli supplier (H.B.S):</strong> Rp {{ number_format($supplierBaseTotal, 0, ',', '.') }}</div>
            <div><strong>Operasional - Bensin:</strong> Rp {{ number_format($order->operational_bensin ?? 0, 0, ',', '.') }}</div>
            <div><strong>Operasional - Kuli:</strong> Rp {{ number_format($order->operational_kuli ?? 0, 0, ',', '.') }}</div>
            <div><strong>Operasional - Makan minum:</strong> Rp {{ number_format($order->operational_makan_minum ?? 0, 0, ',', '.') }}</div>
            <div><strong>Operasional - Listrik:</strong> Rp {{ number_format($order->operational_listrik ?? 0, 0, ',', '.') }}</div>
            <div><strong>Operasional - Wifi:</strong> Rp {{ number_format($order->operational_wifi ?? 0, 0, ',', '.') }}</div>
            <div><strong>Total Operasional:</strong> Rp {{ number_format($operasionalTotal, 0, ',', '.') }}</div>
            <div><strong>Harga beli dapur:</strong> Rp {{ number_format($hargaBeliDapur, 0, ',', '.') }}</div>
            <div><strong>Laba kotor (H.B.S + Operasional - Harga beli dapur):</strong> Rp {{ number_format($labaKotor, 0, ',', '.') }}</div>
        </div>
    @endif

    <div class="right" style="margin-top: 12px;">
        <span class="total">Total: Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
    </div>
</body>
</html>
