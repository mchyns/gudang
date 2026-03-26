<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $headerTitle }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #1f2937; margin: 16px; }
        .sheet { max-width: 1180px; margin: 0 auto; }
        .header-note { margin-bottom: 8px; }
        .header-note .date-main { font-size: 30px; font-weight: 700; margin: 0; }
        .header-note .sub { color: #2c7da0; font-size: 22px; font-weight: 700; margin: 2px 0 0; }
        .header-note .deadline { font-size: 22px; font-weight: 700; margin: 2px 0 0; }
        .toolbar { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; margin-bottom: 8px; }
        .toolbar select, .toolbar input, .toolbar button, .toolbar a {
            font-size: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 6px 8px;
            background: #fff;
            color: #0f172a;
            text-decoration: none;
        }
        .toolbar button { cursor: pointer; }
        .toolbar .print-btn { margin-left: auto; }
        .tbl-wrap { border: 2px solid #111827; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #111827; padding: 3px 6px; font-size: 12px; vertical-align: top; }
        th { background: #c4e3ef; font-weight: 700; text-align: center; }
        .center { text-align: center; }
        .right { text-align: right; }
        .spec { font-size: 10px; line-height: 1.15; }
        .currency { width: 34px; text-align: center; }
        .w-supplier { width: 110px; }
        .w-item { width: 270px; }
        .w-spec { width: 340px; }
        .w-qty { width: 70px; }
        .w-unit { width: 60px; }
        .w-price { width: 110px; }
        .w-total { width: 130px; }
        .w-date { width: 180px; }
        .supplier-sep td { background: #d7ebf3; padding: 0; height: 8px; border-top: 2px solid #111827; border-bottom: 2px solid #111827; }
        .summary { margin-top: 8px; text-align: right; font-size: 14px; font-weight: 700; }
        .muted { color: #6b7280; font-size: 12px; }
        @media print {
            .toolbar { display: none; }
            body { margin: 0; }
            .sheet { max-width: none; }
        }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="toolbar">
            @if($showPeriodFilter)
                <form method="GET" action="{{ route('admin.orders.dapur-purchase-note') }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                    <select name="period">
                        <option value="daily" @selected($selectedPeriod === 'daily')>Harian</option>
                        <option value="weekly" @selected($selectedPeriod === 'weekly')>Mingguan</option>
                        <option value="monthly" @selected($selectedPeriod === 'monthly')>Bulanan</option>
                    </select>
                    <input type="date" name="date" value="{{ $selectedDate }}">
                    <button type="submit">Terapkan</button>
                    <a href="{{ route('admin.orders.dapur-purchase-note') }}">Reset</a>
                </form>
            @endif
            <button class="print-btn" type="button" onclick="window.print()">Cetak</button>
        </div>

        <div class="header-note">
            <p class="date-main">{{ $dateTitle }}</p>
            @if(!empty($dropNote))
                <p class="sub">({{ $dropNote }})</p>
            @endif
            <p class="deadline">MAKSIMAL PUKUL 10.00</p>
        </div>

        <div class="tbl-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="w-supplier" rowspan="2">Supplier</th>
                        <th class="w-item" rowspan="2">Uraian Jenis Bahan Makanan</th>
                        <th class="w-spec" rowspan="2">Spesifikasi</th>
                        <th class="w-qty" rowspan="2">Keb(Kg)</th>
                        <th class="w-unit" rowspan="2">Satuan</th>
                        <th colspan="2">Harga Satuan</th>
                        <th colspan="2">Total Harga</th>
                        <th class="w-date" rowspan="2">{{ $dateTitle }}</th>
                    </tr>
                    <tr>
                        <th class="currency">Rp</th>
                        <th class="w-price"></th>
                        <th class="currency">Rp</th>
                        <th class="w-total"></th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $lastSupplier = null;
                        $grandTotal = 0;
                    @endphp

                    @forelse($rows as $row)
                        @if($lastSupplier !== null && $lastSupplier !== $row['supplier'])
                            <tr class="supplier-sep">
                                <td colspan="10"></td>
                            </tr>
                        @endif

                        @php
                            $grandTotal += (float) $row['total'];
                            $lastSupplier = $row['supplier'];
                        @endphp

                        <tr>
                            <td>{{ $row['supplier'] }}</td>
                            <td>{{ $row['item_name'] }}</td>
                            <td class="spec">{{ $row['specification'] }}</td>
                            <td class="center">{{ $row['quantity'] }}</td>
                            <td class="center">{{ $row['unit'] }}</td>
                            <td class="currency">Rp</td>
                            <td class="right">{{ number_format((float) $row['unit_price'], 0, ',', '.') }}</td>
                            <td class="currency">Rp</td>
                            <td class="right">{{ number_format((float) $row['total'], 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="center muted">Belum ada data pembelian dapur pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="summary">Total Keseluruhan: Rp {{ number_format($grandTotal ?? 0, 0, ',', '.') }}</div>
    </div>
</body>
</html>
