<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;
use Srmklive\PayPal\Services\ExpressCheckout;
use Srmklive\PayPal\Services\AdaptivePayments;



class PayPalController extends Controller
{
    /**
     * Responds with a welcome message with instructions
     *
     * @return \Illuminate\Http\Response
     */
    public function payment()
    {
        $data = [];
        $data['items'] = [
            [
                'name' => 'ItSolutionStuff.com',
                'price' => 100,
                'desc'  => 'Description for ItSolutionStuff.com',
                'qty' => 1
            ]
        ];

        $data['invoice_id'] = 1;
        $data['invoice_description'] = "Order #{$data['invoice_id']} Invoice";
        $data['return_url'] = route('payment.success');
        $data['cancel_url'] = route('payment.cancel');
        $data['total'] = 100;


        $provider = new ExpressCheckout;      // To use express checkout.

        // Through facade. No need to import namespaces
        $provider = \PayPal::setProvider('express_checkout');      // To use express checkout(used by default).

        $response = $provider->setCurrency('MXN')->setExpressCheckout($data);

        // Use the following line when creating recurring payment profiles (subscriptions)

         // This will redirect user to PayPal
        return redirect($response['paypal_link']);

    }


    /**
     * Responds with a welcome message with instructions
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel()
    {
        dd('Your payment is canceled. You can create cancel page here.');
    }

    /**
     * esponds with a welcome message with instructions
     *
     * @return \Illuminate\Http\Response
     */

    public function success(Request $request)

    {
        $provider = new ExpressCheckout;      // To use express checkout.

        // Through facade. No need to import namespaces
        $provider = \PayPal::setProvider('express_checkout');
        $response = $provider->getExpressCheckoutDetails($request->token);

        dd($response);
        
    }

}
