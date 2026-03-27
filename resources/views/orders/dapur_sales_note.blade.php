<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Penjualan Gudang #{{ $order->id }}</title>
    <style>
        @page { size: A4; margin: 12mm; }
        body { font-family: "Times New Roman", serif; color: #111; margin: 12px; }
        .sheet { max-width: 620px; margin: 0 auto; }
        .no-print { margin-bottom: 8px; font-size: 12px; padding: 2px 10px; }
        .head-wrap { display: grid; grid-template-columns: 1.45fr 1fr; align-items: end; }
        .head-left { padding: 5px 7px; border: 0; display: flex; gap: 8px; align-items: center; }
        .brand-logo { width: 100px; height: 100px; object-fit: contain; }
        .head-left h1 { margin: 0; font-size: 13px; font-style: italic; letter-spacing: 0.1px; }
        .head-left p { margin: 0; font-size: 10px; line-height: 1.15; }
        .head-right { padding: 0 6px 4px 8px; font-size: 12px; display: flex; align-items: flex-end; }
        .meta { width: 100%; border-collapse: collapse; }
        .meta td { padding: 0; border: 0; line-height: 1.2; }
        .meta .label { width: 74px; font-weight: 700; }
        .meta .value-red { color: #c00000; font-weight: 700; }
        .nota-title {
            margin-top: 6px;
            border: 1px solid #222;
            background: #fff200;
            text-align: center;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.1px;
            line-height: 1.15;
            padding: 1px 0;
        }
        table.items { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.items th, table.items td { border: 1px solid #222; padding: 2px 4px; font-size: 10px; line-height: 1.15; }
        table.items th { background: #111; color: #fff; text-align: center; }
        table.items td { height: 14px; }
        .c { text-align: center; }
        .r { text-align: right; }
        .foot-wrap { margin-top: 8px; display: grid; grid-template-columns: 1fr 170px; gap: 10px; }
        .catatan { font-size: 11px; line-height: 1.25; }
        .catatan .title { font-weight: 700; margin-bottom: 2px; }
        .sign { text-align: center; font-size: 11px; }
        .sign .gap { height: 34px; }

        .color-exact {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            forced-color-adjust: none !important;
        }

        @media print {
            .no-print { display: none; }
            body { margin: 0; }
            .sheet { max-width: 620px; }
        }
    </style>
</head>
<body>
    @php
        $invoiceDate = optional($order->dapur_sales_note_locked_at)->copy() ?? $order->created_at->copy();
        $dueDate = optional($order->drop_date)->copy() ?? $invoiceDate->copy()->addDays(7);
        $dueDateId = $dueDate->locale('id');
        $items = collect($order->orderItems);
        $filledRows = $items->count();
        $minRows = 18;
        $blankRows = max($minRows - $filledRows, 0);
        $grandTotal = 0;
    @endphp

    <div class="sheet">
    <button class="no-print" onclick="window.print()">Cetak</button>

    <div class="head-wrap">
        <div class="head-left">
            <img src="{{ asset('images/' . rawurlencode('logo fix UD.Trenggalek Jaya.png')) }}" alt="Logo UD Treggalek Jaya" class="brand-logo">
            <div>
                <h1>UD. TREGGALEK JAYA</h1>
                <p>Melayani segala kebutuhan sembako dll</p>
                <p>Jl. Raya Pogalan-Bedorejo</p>
            </div>
        </div>
        <div class="head-right">
            <table class="meta">
                <tr>
                    <td class="label">Kepada</td>
                    <td>: {{ $order->user->name ?? 'Mitra Dapur' }}</td>
                </tr>
                <tr>
                    <td class="label">Nomor</td>
                    <td>: {{ str_pad((string) $order->id, 4, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <td class="label">Jatuh Tempo</td>
                    <td class="value-red">: {{ $dueDateId->translatedFormat('l, j F Y') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="nota-title color-exact" style="background:#fff200;">NOTA PENJUALAN</div>

    <table class="items">
        <thead>
            <tr>
                <th class="color-exact" bgcolor="#111111" style="width:30px;color:#ffffff;">No</th>
                <th class="color-exact" bgcolor="#111111" style="color:#ffffff;">Nama Barang</th>
                <th class="color-exact" bgcolor="#111111" style="width:72px;color:#ffffff;">Banyaknya</th>
                <th class="color-exact" bgcolor="#111111" style="width:66px;color:#ffffff;">Satuan</th>
                <th class="color-exact" bgcolor="#111111" style="width:86px;color:#ffffff;">Harga</th>
                <th class="color-exact" bgcolor="#111111" style="width:96px;color:#ffffff;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $idx => $item)
                @php
                    $price = (float) ($item->price ?? 0);
                    $finalQuantity = (int) ($item->dapur_final_quantity ?? $item->quantity);
                    $subtotal = $price * $finalQuantity;
                    $grandTotal += $subtotal;
                @endphp
                <tr>
                    <td class="c">{{ $idx + 1 }}</td>
                    <td>{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                    <td class="r">{{ $finalQuantity }}</td>
                    <td class="c">{{ strtoupper($item->product->unit ?? 'PCS') }}</td>
                    <td class="r">{{ number_format($price, 0, ',', '.') }}</td>
                    <td class="r">{{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            @for($i = 0; $i < $blankRows; $i++)
                <tr>
                    <td class="c">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="r">&nbsp;</td>
                    <td class="c">&nbsp;</td>
                    <td class="r">&nbsp;</td>
                    <td class="r">&nbsp;</td>
                </tr>
            @endfor

            <tr>
                <td colspan="5" class="r" style="font-weight:700;">Total</td>
                <td class="r" style="font-weight:700;">{{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="foot-wrap">
        <div class="catatan">
            <div class="title">Catatan:</div>
            <div>{{ $order->admin_note ?: '-' }}</div>
            @if($order->dapur_adjustment_note)
                <div style="margin-top:4px;"><strong>Review Dapur:</strong> {{ $order->dapur_adjustment_note }}</div>
            @endif
            <div style="margin-top:4px;"><strong>Tanggal Nota:</strong> {{ $invoiceDate->format('d/m/Y') }}</div>
        </div>
        <div class="sign">
            <div>Hormat kami,</div>
            <div class="gap"></div>
            <div><strong>({{ $sellerName }})</strong></div>
        </div>
    </div>
    </div>
</body>
</html>
