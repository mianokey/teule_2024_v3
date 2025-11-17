<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Town;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MerchandiseController extends Controller
{
    // Show all products
    public function index()
    {
        $products = Product::with('variants')->get();
        return view('merch.index', compact('products'));
    }

    // Show single product page
    public function show(Product $product)
    {
        $product->load('variants');
        return view('merch.show', compact('product'));
    }

    // Show checkout page for selected items
    public function checkout(Request $request)
    {
        $items = $request->input('items', []);
        $towns = Town::all();

        // Calculate subtotal
        $subtotal = 0;
        foreach ($items as $item) {
            $variant = ProductVariant::find($item['variant_id']);
            if ($variant) {
                $subtotal += ($variant->price ?? $variant->product->base_price) * $item['quantity'];
            }
        }

        return view('merch.checkout', compact('items','towns','subtotal'));
    }

    // Place order and update stock
    public function placeOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'town_id' => 'required|exists:towns,id',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.design_file' => 'nullable|file|mimes:jpg,png,pdf',
        ]);

        $user = $request->user();
        $town = Town::findOrFail($request->town_id);

        DB::transaction(function() use ($request, $user, $town, &$order) {
            $order = Order::create([
                'user_id' => $user->id,
                'town_id' => $town->id,
                'total_amount' => 0,
                'status' => 'pending'
            ]);

            $total = 0;

            foreach ($request->items as $item) {
                $variant = ProductVariant::findOrFail($item['variant_id']);

                // Check stock if preorder not allowed
                if (!$variant->product->allow_preorder && $variant->stock < $item['quantity']) {
                    throw new \Exception("Not enough stock for {$variant->product->name} - {$variant->color}/{$variant->size}");
                }

                // Handle design file
                $designPath = null;
                if (isset($item['design_file'])) {
                    $designPath = $item['design_file']->store('designs','public');
                }

                $orderItem = $order->items()->create([
                    'product_variant_id' => $variant->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $variant->price ?? $variant->product->base_price,
                    'design_file' => $designPath
                ]);

                $total += $orderItem->unit_price * $orderItem->quantity;

                // Reduce stock if preorder not allowed
                if (!$variant->product->allow_preorder) {
                    $variant->decrement('stock', $item['quantity']);
                }
            }

            // Add delivery cost
            $total += $town->delivery_cost;
            $order->update(['total_amount' => $total]);
        });

        // Optionally trigger M-PESA payment here
        // return redirect()->route('merch.payment', $order->id);
        return redirect()->route('merch.thankyou', $order->id);
    }

    // Show thank you page after order
    public function thankYou(Order $order)
    {
        return view('merch.thankyou', compact('order'));
    }

    // Example M-PESA STK push (simplified)
    public function mpesaPay(Order $order)
    {
        // Use Daraja API to trigger STK push
        // You'll need your credentials and access token
        // After payment success, update $order->status = 'paid';

        return response()->json([
            'message' => 'STK Push triggered (simulation)',
            'order_id' => $order->id
        ]);
    }

    // M-PESA callback endpoint
    public function mpesaCallback(Request $request)
    {
        $data = $request->all();
        $checkoutRequestID = $data['CheckoutRequestID'] ?? null;

        if ($checkoutRequestID) {
            $order = Order::where('id', $checkoutRequestID)->first();
            if ($order) {
                $order->update(['status' => 'paid']);
            }
        }

        return response()->json(['status' => 'success']);
    }


        // Show create form
    public function create()
    {
        return view('admin.merch.create');
    }
}
