<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order {{ $order->order_id }}</title>
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
        
        .order-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .order-info div {
            flex: 1;
        }
        
        .order-info h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        .order-info p {
            margin: 5px 0;
        }
        
        .order-info strong {
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
        
        <!-- Order Information -->
        <div class="order-info">
            <div>
                <h3>Order Details</h3>
                <p><strong>Order ID:</strong> {{ $order->order_id }}</p>
                <p><strong>Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</p>
                <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>
                <p><strong>Order Status:</strong> {{ ucfirst($order->order_status) }}</p>
                <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</p>
            </div>
            <div>
                <h3>Customer Details</h3>
                <p><strong>Name:</strong> {{ $order->customer_name }}</p>
                <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                @if($order->customer_email)
                <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                @endif
                <p><strong>Delivery Address:</strong></p>
                <p>{{ $order->customer_address }}</p>
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
                    <th class="text-right" style="width: 100px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td class="text-right">
                        @if($item->product->discount_price)
                            <span style="text-decoration: line-through; color: #999;">{{ number_format($item->product->price, 2) }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        <strong style="color: #28a745;">{{ number_format($item->price, 2) }}</strong>
                    </td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right"><strong>{{ number_format($item->total, 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Summary -->
        @php
            $mrpTotal = $order->items->sum(function($item) {
                return $item->product->price * $item->quantity;
            });
            $totalDiscount = $mrpTotal - $order->subtotal;
        @endphp
        <div class="summary">
            <table>
                @if($totalDiscount > 0)
                <tr>
                    <td><strong>MRP Total:</strong></td>
                    <td class="text-right"><span style="text-decoration: line-through; color: #999;">{{ number_format($mrpTotal, 2) }}</span></td>
                </tr>
                <tr>
                    <td><strong>Total Discount:</strong></td>
                    <td class="text-right" style="color: #28a745;">{{ number_format($totalDiscount, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td class="text-right">{{ number_format($order->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Shipping:</strong></td>
                    <td class="text-right">{{ $order->shipping == 0 ? 'FREE' : number_format($order->shipping, 2) }}</td>
                </tr>
                <tr class="total">
                    <td><strong>Grand Total:</strong></td>
                    <td class="text-right">{{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>
        
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
            <p>2. Please check the items before delivery.</p>
            <p>3. This is a computer-generated invoice and does not require a physical signature.</p>
            <p style="margin-top: 15px;">Thank you for shopping with {{ $shop_name }}!</p>
            <p>Generated on: {{ $order->created_at->format('d M Y, h:i A') }}</p>
        </div>
    </div>
</body>
</html>