<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bill {{ $bill->bill_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .bill-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .bill-info div {
            flex: 1;
        }
        
        .bill-info h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        .bill-info p {
            margin: 5px 0;
        }
        
        .bill-info strong {
            color: #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table th {
            background: #f5f5f5;
            color: #333;
            font-weight: bold;
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        
        table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        
        table td.text-right {
            text-align: right;
        }
        
        table th.text-right {
            text-align: right;
        }
        
        .summary {
            margin-left: auto;
            width: 300px;
        }
        
        .summary table {
            margin-bottom: 0;
        }
        
        .summary td {
            padding: 8px 10px;
        }
        
        .summary .total td {
            background: #f5f5f5;
            font-weight: bold;
            font-size: 14px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
        }
        
        .footer p {
            margin: 5px 0;
        }
        
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature div {
            text-align: center;
            width: 200px;
        }
        
        .signature .line {
            border-top: 1px solid #333;
            margin-top: 60px;
        }
        
        @media print {
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $shop_name }}</h1>
            <p>{{ $shop_address }}</p>
            <p>Phone: {{ $shop_phone }} | Email: {{ $shop_email }}</p>
        </div>
        
        <!-- Bill Information -->
        <div class="bill-info">
            <div>
                <h3>Bill Details</h3>
                <p><strong>Bill Number:</strong> {{ $bill->bill_number }}</p>
                <p><strong>Date:</strong> {{ $bill->created_at->format('d M Y, h:i A') }}</p>
                <p><strong>Payment Method:</strong> {{ ucfirst($bill->payment_method) }}</p>
            </div>
            <div>
                <h3>Customer Details</h3>
                <p><strong>Name:</strong> {{ $bill->customer_name }}</p>
                <p><strong>Phone:</strong> {{ $bill->customer_phone }}</p>
                @if($bill->customer_email)
                <p><strong>Email:</strong> {{ $bill->customer_email }}</p>
                @endif
            </div>
        </div>
        
        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Item Description</th>
                    <th class="text-right" style="width: 80px;">MRP</th>
                    <th class="text-right" style="width: 80px;">Price</th>
                    <th class="text-right" style="width: 60px;">Qty</th>
                    <th class="text-right" style="width: 80px;">Discount</th>
                    <th class="text-right" style="width: 100px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bill->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td class="text-right">
                        <span style="text-decoration: line-through; color: #999;">{{ number_format($item->unit_price, 2) }}</span>
                    </td>
                    <td class="text-right">
                        <strong style="color: #28a745;">{{ number_format($item->unit_price - $item->discount, 2) }}</strong>
                    </td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">
                        @if($item->discount > 0)
                            <span style="color: #28a745;">{{ number_format($item->discount, 2) }}</span>
                            <br>
                            <small style="color: #666;">{{ number_format(($item->discount / $item->unit_price) * 100, 1) }}%</small>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right"><strong>{{ number_format($item->total, 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Summary -->
        @php
            $mrpTotal = $bill->items->sum(function($item) {
                return $item->unit_price * $item->quantity;
            });
            $totalDiscount = $mrpTotal - $bill->subtotal;
        @endphp
        <div class="summary">
            <table>
                <tr>
                    <td><strong>MRP Total:</strong></td>
                    <td class="text-right"><span style="text-decoration: line-through; color: #999;">{{ number_format($mrpTotal, 2) }}</span></td>
                </tr>
                <tr>
                    <td><strong>Total Discount:</strong></td>
                    <td class="text-right" style="color: #28a745;">{{ number_format($totalDiscount, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td class="text-right">{{ number_format($bill->subtotal, 2) }}</td>
                </tr>
                <tr class="total">
                    <td><strong>Grand Total:</strong></td>
                    <td class="text-right">{{ number_format($bill->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>
        
        @if($bill->notes)
        <div style="margin-top: 20px; padding: 10px; background: #f9f9f9; border-left: 3px solid #ddd;">
            <strong>Notes:</strong> {{ $bill->notes }}
        </div>
        @endif
        
        <!-- Signature -->
        <div class="signature">
            <div>
                <p><strong>Customer Signature</strong></p>
                <div class="line"></div>
            </div>
            <div>
                <p><strong>For {{ $shop_name }}</strong></p>
                <div class="line"></div>
                <p style="margin-top: 10px; font-size: 10px;">Authorized Signatory</p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>Terms & Conditions:</strong></p>
            <p>1. Goods once sold will not be taken back.</p>
            <p>2. Please check the items before leaving the shop.</p>
            <p>3. This is a computer-generated invoice and does not require a physical signature.</p>
            <p style="margin-top: 15px;">Thank you for shopping with {{ $shop_name }}!</p>
            <p>Generated on: {{ $bill->created_at->format('d M Y, h:i A') }}</p>
        </div>
    </div>
</body>
</html>