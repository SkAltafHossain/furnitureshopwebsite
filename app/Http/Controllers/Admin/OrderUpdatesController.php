<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderUpdatesController extends Controller
{
    public function stream()
    {
        $response = new \Symfony\Component\HttpFoundation\StreamedResponse();
        
        $response->setCallback(function () {
            $lastChecked = time();
            
            while (true) {
                // Check for updated orders in the last 5 seconds
                $updatedOrders = Order::where('updated_at', '>', date('Y-m-d H:i:s', $lastChecked))
                    ->get(['id', 'order_id', 'order_status', 'updated_at']);
                
                if ($updatedOrders->isNotEmpty()) {
                    echo "data: " . json_encode([
                        'type' => 'order_update',
                        'orders' => $updatedOrders
                    ]) . "\n\n";
                    ob_flush();
                    flush();
                }
                
                $lastChecked = time();
                
                // Sleep for 30 seconds before checking again
                sleep(50000000000);
            }
        });
        
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no'); // For Nginx
        
        return $response;
    }
}
