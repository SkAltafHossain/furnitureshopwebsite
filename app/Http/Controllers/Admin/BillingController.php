<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Bill;
use App\Models\BillItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;

class BillingController extends Controller
{
    public function getData()
    {
        $bills = Bill::with(['items', 'createdBy'])
            ->select('bills.*')
            ->latest();
            
        return DataTables::of($bills)
            ->addColumn('items_count', function($bill) {
                return $bill->items->count();
            })
            ->addColumn('created_by', function($bill) {
                return $bill->createdBy ? $bill->createdBy->name : 'System';
            })
            ->make(true);
    }

    public function searchProducts(Request $request)
    {
        $query = $request->get('query');
        
        if (empty($query)) {
            return response()->json([]);
        }

        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('slug', 'like', "%{$query}%")
            ->where('status', 'active')
            ->where('stock', '>', 0)
            ->select('id', 'name', 'slug', 'price', 'discount_price', 'stock', 'image')
            ->limit(10)
            ->get();

        return response()->json($products);
    }

    public function getProduct($id)
    {
        $product = Product::with('category')->findOrFail($id);
        
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'discount_price' => $product->discount_price,
            'stock' => $product->stock,
            'image' => $product->image,
            'category' => $product->category ? $product->category->name : 'N/A'
        ]);
    }

    public function createBill(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:cash,card,upi',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        
        try {
            $subtotal = 0;
            $billItems = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }

                $mrp = $item['price'];
                $sellingPrice = $product->discount_price ? $product->discount_price : $mrp;
                $discount = $mrp - $sellingPrice;
                $itemTotal = $sellingPrice * $item['quantity'];
                $subtotal += $itemTotal;

                $billItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $mrp,
                    'discount' => $discount,
                    'total' => $itemTotal,
                ];

                // Update stock
                $product->decrement('stock', $item['quantity']);
            }

            $totalAmount = $subtotal;

            $bill = Bill::create([
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($billItems as $item) {
                $bill->items()->create($item);
            }

            // Generate PDF
            $pdf = $this->generateBillPDF($bill);
            
            // Save PDF
            $pdfPath = 'bills/' . $bill->bill_number . '.pdf';
            Storage::disk('public')->put($pdfPath, $pdf->output());
            $bill->pdf_path = $pdfPath;
            $bill->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'bill_id' => $bill->id,
                'bill_number' => $bill->bill_number,
                'message' => 'Bill created successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function generateBillPDF($bill)
    {
        $bill->load('items.product');
        
        $data = [
            'bill' => $bill,
            'shop_name' => 'Local Shop',
            'shop_address' => '123 Main Street, City',
            'shop_phone' => '+91 9876543210',
            'shop_email' => 'info@localshop.com',
        ];

        $pdf = Pdf::loadView('admin.billing.pdf', $data);
        return $pdf;
    }

    public function downloadBill($id)
    {
        $bill = Bill::findOrFail($id);
        
        if (!$bill->pdf_path || !Storage::disk('public')->exists($bill->pdf_path)) {
            // Generate PDF if not exists
            $pdf = $this->generateBillPDF($bill);
            $pdfPath = 'bills/' . $bill->bill_number . '.pdf';
            Storage::disk('public')->put($pdfPath, $pdf->output());
            $bill->pdf_path = $pdfPath;
            $bill->save();
        }

        return Storage::disk('public')->download($bill->pdf_path, $bill->bill_number . '.pdf');
    }

    public function sendBill(Request $request, $id)
    {
        $request->validate([
            'send_method' => 'required|in:email,whatsapp,sms',
        ]);

        $bill = Bill::with('items.product')->findOrFail($id);

        if (!$bill->pdf_path) {
            return response()->json([
                'success' => false,
                'message' => 'PDF not generated for this bill'
            ], 400);
        }

        $pdfUrl = Storage::disk('public')->url($bill->pdf_path);
        $message = "Dear {$bill->customer_name},\n\nYour bill #{$bill->bill_number} has been generated.\nTotal Amount: {$bill->formatted_total}\n\nYou can download your bill from: {$pdfUrl}\n\nThank you for shopping with us!";

        try {
            switch ($request->send_method) {
                case 'email':
                    if (!$bill->customer_email) {
                        throw new \Exception('Customer email not provided');
                    }
                    // Here you would integrate with email service
                    // For now, we'll just mark as sent
                    break;
                    
                case 'whatsapp':
                    // Here you would integrate with WhatsApp API
                    // For now, we'll just mark as sent
                    break;
                    
                case 'sms':
                    // Here you would integrate with SMS service
                    // For now, we'll just mark as sent
                    break;
            }

            $bill->is_sent = true;
            $bill->sent_at = now();
            $bill->save();

            return response()->json([
                'success' => true,
                'message' => "Bill sent via {$request->send_method} successfully",
                'pdf_url' => $pdfUrl
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function history()
    {
        return view('admin.billing.history');
    }

    public function showBill($id)
    {
        $bill = Bill::with(['items.product.category', 'createdBy'])->findOrFail($id);
        return view('admin.billing.show', compact('bill'));
    }
}
