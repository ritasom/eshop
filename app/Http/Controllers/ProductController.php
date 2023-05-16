<?php

namespace App\Http\Controllers;

//use App\Models\Order;
use App\Models\Product;
use http\Env\Response;
use Illuminate\Http\Request;
use Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Webhook;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::all();
        return view('home', compact('products'));
    }  
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function show(Request $request, $id)
    {
        $product = Product::find($request->id);
        $intent = auth()->user()->createSetupIntent();
        return view("checkout", compact("product", "intent"));
    }
        
    // public function processPayment(Request $request, String $product, $price)
    // {
    //     $user = Auth::user();
    //     //$product = Product::find($request->id);
    //     $paymentMethod = $request->input('payment_method');
    //     $user->createOrGetStripeCustomer();
    //     if(! $user->hasPaymentMethod())
    //     $user->addPaymentMethod($paymentMethod);
    //     try
    //     {
    //         $user->charge($price*100, $paymentMethod);
    //     }
    //     catch (\Exception $e)
    //     {
    //         return back()->withErrors(['message' => 'Error creating subscription. ' . $e->getMessage()]);
    //     }
    //     return redirect('home');
    // }

    public function checkout(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $product = Product::find($request->plan);
        
        $session = Session::create([
            'line_items' => [
                [
                  'price_data' => [
                    'currency' => 'inr',
                    'product_data' => ['name' => $product->name],
                    'unit_amount' => $product->price*100,
                  ],
                  'quantity' => 1,
                ],
              ],
            'mode' => 'payment',
            'success_url' => route('checkout.success', [], true) . "?session_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => route('checkout.cancel', [], true),
        ]);

        // $order = new Order();
        // $order->status = 'unpaid';
        // $order->total_price = $totalPrice;
        // $order->session_id = $session->id;
        // $order->save();

        return redirect($session->url);
    }

    public function success()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $sessionId = $_GET['session_id'];

        try {
            $session = Session::retrieve($sessionId);
            if (!$session) {
                throw new NotFoundHttpException;
            }
            $customer = Customer::retrieve($session->customer);

            return view('checkout-success', compact('customer'));
        } catch (\Exception $e) {
            throw new NotFoundHttpException();
        }

    }

    public function cancel()
    {
        return view('cancel');
    }

//     public function webhook()
//     {
//         // This is your Stripe CLI webhook secret for testing your endpoint locally.
//         $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

//         $payload = @file_get_contents('php://input');
//         $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
//         $event = null;

//         try {
//             $event = Webhook::constructEvent(
//                 $payload, $sig_header, $endpoint_secret
//             );
//         } catch (\UnexpectedValueException $e) {
//             // Invalid payload
//             return response('', 400);
//         } catch (\Stripe\Exception\SignatureVerificationException $e) {
//             // Invalid signature
//             return response('', 400);
//         }

// // Handle the event
//         switch ($event->type) {
//             case 'checkout.session.completed':
//                 $session = $event->data->object;

//                 $order = Order::where('session_id', $session->id)->first();
//                 if ($order && $order->status === 'unpaid') {
//                     $order->status = 'paid';
//                     $order->save();
//                     // Send email to customer
//                 }

//             // ... handle other event types
//             default:
//                 echo 'Received unknown event type ' . $event->type;
//         }

//         return response('');
//     }
}


?>