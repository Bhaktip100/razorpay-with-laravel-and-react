<?php

namespace App\Http\Controllers;

use Razorpay\Api\Api;
use Razorpay\Api\Errors;
use Illuminate\Http\Request;

class RazorpayController extends Controller
{
    public function createOrder(Request $request)
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        try {
            $order = $api->order->create(array(
                'amount' => 1000, // Amount in paise (1 INR = 100 paise)
                'currency' => 'INR',
                'receipt' => uniqid(),
                'notes' => [
                    'user_id' => 100, // Pass the user's ID => who pay for it
                    'pandit_id' => 200, // Pass the pandit's ID
                ],
            ));
            return response()->json(['order_id' => $order->id]);
        } catch (Error $e) {
            return response()->json(['error' => 'Error creating Razorpay order'], 500);
        }
    }

    public function fetchPaymentDetails(Request $request)
    {
        dd(11);
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        try {
            $razorpayOrderId = $request->post('razorpay_order_id');
            $razorpayPaymentId = $request->post('razorpay_payment_id');
            $razorpaySignature = $request->post('razorpay_signature');

            dd($api->utility->verifyPaymentSignature(array('razorpay_order_id' => $razorpayOrderId, 'razorpay_payment_id' => $razorpayPaymentId, 'razorpay_signature' => $razorpaySignature)));


            $paymentDetails = $api->payment->fetch($request->post('razorpay_payment_id'));
            return response()->json(['paymentDetails' => $paymentDetails]);
        } catch (Error $e) {
            return response()->json(['error' => 'Error creating Razorpay order'], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        \Log::info("Handle webhook");
        $data = $request->all();
        \Log::info($data);

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $razorpayOrderId = $data['payload']['payment']['entity']['order_id'];
        $razorpayPaymentId = $data['payload']['payment']['entity']['id'];
        $razorpaySignature = request()->header('X-Razorpay-Signature');

         // Calculate the expected signature
         \Log::info("expectedSignature");
         $expectedSignature = hash_hmac('sha256', file_get_contents('php://input'), env('RAZORPAY_SECRET'));
         \Log::info($expectedSignature);

        if (request()->header('X-Razorpay-Signature') == $expectedSignature) {
            // Payment::create([
            //         'payment_id' => $data['payload']['payment']['entity']['id'],
            //         'amount' => $data['payload']['payment']['entity']['amount'],
            //         'status' => $data['payload']['payment']['entity']['status'],
            //         // Add other relevant payment data
            //     ]);
        }

        // \Log::info($api->utility->verifyPaymentSignature(array('razorpay_order_id' => $razorpayOrderId, 'razorpay_payment_id' => $razorpayPaymentId, 'razorpay_signature' => $razorpaySignature)));

        // Verify the webhook signature to ensure it's from Razorpay (recommended).
        // You can use Razorpay's SDK to verify the signature.

        // Your processing logic here
        // Extract payment information from $data and store it in your database

        // Example: Store payment data in your database
        // Payment::create([
        //     'payment_id' => $data['payload']['payment']['entity']['id'],
        //     'amount' => $data['payload']['payment']['entity']['amount'],
        //     'status' => $data['payload']['payment']['entity']['status'],
        //     // Add other relevant payment data
        // ]);

        return response('Webhook Received', 200);
    }


}
