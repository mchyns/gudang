<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Order #{{ $order->id }}</title>
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
        .section-title { font-size: 13px; font-weight: 700; margin-top: 14px; margin-bottom: 6px; }
        .supplier-card { border: 1px solid #dbeafe; border-radius: 10px; margin-bottom: 14px; overflow: hidden; }
        .supplier-head { background: #eff6ff; padding: 10px 12px; font-size: 13px; font-weight: 700; color: #1e3a8a; }
        .small { font-size: 12px; color: #64748b; }
        .subtotal-row td { background: #f8fafc; font-weight: 700; }
        @media print { .no-print { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    @php
        $isSupplierPurchase = $order->order_type === 'supplier_purchase';
        $isDapurViewer = $viewerRole === 'dapur';
        $isAdminViewer = in_array($viewerRole, ['admin', 'superadmin']);
    @endphp

    <div class="header">
        <div>
            <h1 class="title">{{ $isSupplierPurchase ? ($viewerRole === 'supplier' ? 'NOTA PENJUALAN SUPPLIER' : 'NOTA PEMBELIAN GUDANG DARI SUPPLIER') : 'NOTA PEMBELIAN DAPUR' }} #{{ $order->id }}</h1>
            <p class="muted">HIKARI Logistik Warehouse Management System</p>
        </div>
        <button class="no-print" onclick="window.print()">Cetak</button>
    </div>

    <div class="box">
        <div><strong>Penerima:</strong> {{ $isSupplierPurchase ? 'Gudang' : ($order->user->name ?? '-') }}</div>
        <div><strong>Tanggal:</strong> {{ $order->created_at->format('d M Y H:i') }}</div>
        <div><strong>Status:</strong> {{ ucfirst($order->status) }}</div>
        @if($isSupplierPurchase)
            <div><strong>Status Pengiriman:</strong> {{ ucfirst($order->shipping_status ?? 'pending') }}</div>
        @endif
        @if(!$isSupplierPurchase)
            <div><strong>Status Pengiriman:</strong> {{ ucfirst($order->shipping_status ?? 'pending') }}</div>
        @endif
        <div><strong>Catatan:</strong> {{ $isAdminViewer ? ($order->admin_note ?: $order->note ?: '-') : ($order->note ?: '-') }}</div>
    </div>

    @php
        $grandTotal = 0;
        $supplierBaseTotal = 0;
    @endphp

    @if($isSupplierPurchase)
        @foreach(($groupedItems ?? collect())->filter(fn($items) => $items->isNotEmpty()) as $supplierName => $items)
            @php $supplierTotal = 0; @endphp
            <div class="supplier-card">
                <div class="supplier-head">Supplier: {{ $supplierName }}</div>
                <table>
                    <thead>
                        <tr>
                            <th>Nama barang</th>
                            <th>Spesifikasi</th>
                            <th class="right">Qty</th>
                            <th>Satuan</th>
                            <th class="right">Harga Satuan</th>
                            <th class="right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            @php
                                $unitPrice = (float) ($item->price ?? $item->product->supplier_price ?? 0);
                                $subtotal = $item->quantity * $unitPrice;
                                $supplierTotal += $subtotal;
                                $grandTotal += $subtotal;
                            @endphp
                            <tr>
                                <td>
                                    <div>{{ $item->product->name ?? 'Produk Dihapus' }}</div>
                                    <div class="small">Order Item #{{ $item->id }}</div>
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($item->product->description ?? '-', 70) }}</td>
                                <td class="right">{{ $item->quantity }}</td>
                                <td>{{ strtoupper($item->product->unit ?? 'pcs') }}</td>
                                <td class="right">Rp {{ number_format($unitPrice, 0, ',', '.') }}</td>
                                <td class="right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="subtotal-row">
                            <td colspan="5" class="right">Subtotal {{ $supplierName }}</td>
                            <td class="right">Rp {{ number_format($supplierTotal, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
    @else
        <table>
            <thead>
                <tr>
                    <th>Nama barang</th>
                    <th class="right">Qty</th>
                    <th>Satuan</th>
                    @if(!$isDapurViewer)
                        <th class="right">Harga</th>
                        <th class="right">Jumlah</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($invoiceItems as $item)
                    @php
                        $supplierUnitPrice = $item->product->supplier_price ?? 0;
                        $unitPrice = $useSupplierPrice ? $supplierUnitPrice : $item->price;
                        $subtotal = $item->quantity * $unitPrice;
                        $grandTotal += $subtotal;
                        $supplierBaseTotal += ($item->quantity * $supplierUnitPrice);
                    @endphp
                    <tr>
                        <td>{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                        <td class="right">{{ $item->quantity }}</td>
                        <td>{{ $item->product->unit ?? 'pcs' }}</td>
                        @if(!$isDapurViewer)
                            <td class="right">Rp {{ number_format($unitPrice, 0, ',', '.') }}</td>
                            <td class="right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($isAdminViewer && !$isSupplierPurchase)
        @php
            $hargaBeliDapur = (float) $order->total_price;
            $labaKotor = $hargaBeliDapur - $supplierBaseTotal;
        @endphp

        <div class="box" style="margin-top: 12px;">
            <div><strong>Harga beli supplier (H.B.S):</strong> Rp {{ number_format($supplierBaseTotal, 0, ',', '.') }}</div>
            <div><strong>Harga beli dapur:</strong> Rp {{ number_format($hargaBeliDapur, 0, ',', '.') }}</div>
            <div><strong>Laba kotor (Harga beli dapur - H.B.S):</strong> Rp {{ number_format($labaKotor, 0, ',', '.') }}</div>
        </div>
    @endif

    <div class="right" style="margin-top: 12px;">
        <span class="total">Total: Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
    </div>
</body>
</html>
