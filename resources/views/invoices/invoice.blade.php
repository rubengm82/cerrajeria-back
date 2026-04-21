<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Factura - {{ $invoice->number }}</title>

<style>
@page {
    size: A4;
    margin: 15mm;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;
    color: #333;
    margin: 0;
    padding: 0;
}

/* CONTENEDOR */
.container {
    width: 100%;
}

/* HEADER */
.header-table {
    width: 100%;
    border-bottom: 2px solid #333;
    margin-bottom: 20px;
}

.company-name {
    font-size: 18px;
    font-weight: bold;
    color: #2c3e50;
}

.company-details {
    font-size: 10px;
    line-height: 1.3;
}

.invoice-title {
    text-align: right;
    font-size: 22px;
    font-weight: bold;
    color: #2c3e50;
}

/* INFO */
.info-table {
    width: 100%;
    margin-bottom: 20px;
}

.section-title {
    font-weight: bold;
    margin-bottom: 5px;
    color: #2c3e50;
}

.customer-info {
    font-size: 11px;
}

/* META */
.meta-table {
    width: 100%;
    font-size: 11px;
}

.meta-table td {
    padding: 2px 0;
}

.meta-label {
    font-weight: bold;
}

/* ITEMS */
.items-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.items-table th {
    background: #34495e;
    color: white;
    padding: 8px;
    font-size: 10px;
    text-align: right;
}

.items-table th:first-child {
    text-align: left;
}

.items-table td {
    padding: 6px;
    border-bottom: 1px solid #ddd;
    font-size: 10px;
}

.text-right {
    text-align: right;
}

/* TOTALES */
.totals {
    width: 250px;
    margin-left: auto;
    margin-top: 20px;
    border: 1px solid #ddd;
    padding: 10px;
    background: #f9f9f9;
    border-collapse: collapse;
}

.totals td {
    padding: 5px;
    font-size: 11px;
}

.totals .label {
    text-align: left;
}

.totals .value {
    text-align: right;
}

.totals .total {
    font-weight: bold;
    font-size: 14px;
    border-top: 2px solid #333;
}

/* FOOTER */
.footer {
    margin-top: 30px;
    text-align: center;
    font-size: 9px;
    color: #666;
    border-top: 1px solid #ddd;
    padding-top: 10px;
}
</style>
</head>

<body>
<div class="container">

    <!-- HEADER -->
    <table class="header-table">
        <tr>
            <td width="60%">
                <div class="company-name">Serralleria Solidària</div>
                <div class="company-details">
                    Carrer d'Atenes, 27<br>
                    Barcelona, Sarrià-Sant Gervasi, 08006<br>
                    España<br>
                    Tel: +34 644 11 18 27<br>
                    empresa@serralleriasolidaria.cat
                </div>
            </td>
            <td width="40%" class="invoice-title">
                FACTURA
            </td>
        </tr>
    </table>

    <!-- INFO -->
    <table class="info-table">
        <tr>
            <td width="50%">
                <div class="section-title">Facturar a:</div>
                <div class="customer-info">
                    {{ $invoice->customer->name }} {{ $invoice->customer->last_name_one }} {{ $invoice->customer->last_name_second }}<br>
                    {{ $invoice->customer->address }}<br>
                    {{ $invoice->customer->zip_code }} {{ $invoice->customer->province }}<br>
                    {{ $invoice->customer->country }}<br>
                    DNI: {{ $invoice->customer->dni }}<br>
                    E-Mail: {{ $invoice->customer->email }}
                </div>
            </td>
            <td width="50%">
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Factura Nº:</td>
                        <td>{{ $invoice->number }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Data:</td>
                        <td>{{ $invoice->date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Venciment:</td>
                        <td>{{ $invoice->due_date->format('d/m/Y') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- ITEMS -->
    <table class="items-table">
        <thead>
            <tr>
                <th>Descripció</th>
                <th>Quantitat</th>
                <th>Preu Unit.</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2, ',', '.') }} €</td>
                <td class="text-right">{{ number_format($item->total, 2, ',', '.') }} €</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TOTALES -->
    <table class="totals">
        <tr>
            <td class="label">Subtotal:</td>
            <td class="value">{{ number_format($invoice->subtotal, 2, ',', '.') }} €</td>
        </tr>

        @if($invoice->tax_rate > 0)
        <tr>
            <td class="label">IVA ({{ $invoice->tax_rate }}%):</td>
            <td class="value">{{ number_format($invoice->tax_amount, 2, ',', '.') }} €</td>
        </tr>
        @endif

        <tr class="total">
            <td class="label"><strong>Total:</strong></td>
            <td class="value"><strong>{{ number_format($invoice->total, 2, ',', '.') }} €</strong></td>
        </tr>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        <strong>Gràcies per la vostra compra!</strong><br>
        Aquesta factura ha estat generada automàticament.
    </div>

</div>
</body>
</html>