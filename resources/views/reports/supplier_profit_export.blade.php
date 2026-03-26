<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
</head>
<body>
    <table border="1" cellspacing="0" cellpadding="4" style="border-collapse:collapse;width:100%;font-size:12px;">
        <tr>
            <td colspan="8" style="background:#2f5597;color:#ffffff;font-weight:700;text-align:center;font-size:14px;">
                {{ strtoupper($title) }} - {{ $periodLabel }} ({{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }})
            </td>
        </tr>
        <tr>
            <th style="background:#d9d9d9;text-align:center;">NAMA BARANG</th>
            <th style="background:#d9d9d9;text-align:center;">QTY</th>
            <th style="background:#d9d9d9;text-align:center;">SATUAN</th>
            <th style="background:#d9d9d9;text-align:center;">HARGA SUPPLIER</th>
            <th style="background:#d9d9d9;text-align:center;">TOTAL SUPPLIER</th>
            <th style="background:#d9d9d9;text-align:center;">HARGA BELI DAPUR</th>
            <th style="background:#d9d9d9;text-align:center;">TOTAL HARGA DAPUR</th>
            <th style="background:#d9d9d9;text-align:center;">LABA</th>
        </tr>

        @php
            $grandSupplier = 0;
            $grandDapur = 0;
            $grandLaba = 0;
        @endphp

        @forelse($supplierSections as $section)
            <tr>
                <td colspan="8" style="background:#ffff00;font-weight:700;text-align:center;">{{ strtoupper($section['supplier_name']) }}/{{ $end->format('d/m/y') }}</td>
            </tr>

            @php
                $subSupplier = 0;
                $subDapur = 0;
                $subLaba = 0;
            @endphp

            @foreach($section['rows'] as $row)
                @php
                    $subSupplier += $row['total_supplier'];
                    $subDapur += $row['total_dapur'];
                    $subLaba += $row['laba'];
                @endphp
                <tr>
                    <td style="text-align:center;">{{ $row['product_name'] }}</td>
                    <td style="text-align:center;">{{ rtrim(rtrim(number_format($row['qty'], 2, '.', ''), '0'), '.') }}</td>
                    <td style="text-align:center;">{{ $row['unit'] }}</td>
                    <td style="text-align:center;">Rp {{ number_format($row['harga_supplier'], 0, ',', '.') }}</td>
                    <td style="text-align:center;">Rp {{ number_format($row['total_supplier'], 0, ',', '.') }}</td>
                    <td style="text-align:center;">Rp {{ number_format($row['harga_dapur'], 0, ',', '.') }}</td>
                    <td style="text-align:center;">Rp {{ number_format($row['total_dapur'], 0, ',', '.') }}</td>
                    <td style="text-align:center;">Rp {{ number_format($row['laba'], 0, ',', '.') }}</td>
                </tr>
            @endforeach

            @php
                $grandSupplier += $subSupplier;
                $grandDapur += $subDapur;
                $grandLaba += $subLaba;
            @endphp

            <tr>
                <td colspan="3" style="background:#00b0f0;font-weight:700;text-align:center;">TOTAL</td>
                <td style="background:#00b0f0;"></td>
                <td style="background:#00b0f0;font-weight:700;text-align:center;">Rp {{ number_format($subSupplier, 0, ',', '.') }}</td>
                <td style="background:#00b0f0;"></td>
                <td style="background:#00b0f0;font-weight:700;text-align:center;">Rp {{ number_format($subDapur, 0, ',', '.') }}</td>
                <td style="background:#00b0f0;font-weight:700;text-align:center;">Rp {{ number_format($subLaba, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align:center;">Tidak ada data untuk periode ini.</td>
            </tr>
        @endforelse

        @if(!empty($supplierSections))
            <tr>
                <td colspan="3" style="background:#00b0f0;font-weight:700;text-align:center;">GRAND TOTAL</td>
                <td style="background:#00b0f0;"></td>
                <td style="background:#00b0f0;font-weight:700;text-align:center;">Rp {{ number_format($grandSupplier, 0, ',', '.') }}</td>
                <td style="background:#00b0f0;"></td>
                <td style="background:#00b0f0;font-weight:700;text-align:center;">Rp {{ number_format($grandDapur, 0, ',', '.') }}</td>
                <td style="background:#00b0f0;font-weight:700;text-align:center;">Rp {{ number_format($grandLaba, 0, ',', '.') }}</td>
            </tr>
        @endif
    </table>
</body>
</html>
