<?php

namespace App\Http\Controllers;

use App\Models\StudentPayment;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Nafezly\Payments\Classes\PaymobPayment;
use stdClass;

class PaymentController extends Controller
{
//    public $token = '';
    //Todo: Add Request Data to parameters
    public function loginToPaymentGateway(Request $request)
    {
        $user = User::find($request->id);
        // Paymob API URL
        $url = 'https://accept.paymob.com/api/auth/tokens';

        // Your merchant credentials and other required data
        $payload = [
            'api_key' => env('PAYMOB_API_KEY'),
        ];

        // Make the POST request
        $client = new Client();
        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);

        // Get the response body
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        //Auth Token
        $authToken = $data['token'];
        session(['token' => $data['token']]);

         $this->orderRegestrationAPI($authToken,$user);

         return response()->json(['success'=>'Student bill making has been done']);


    }

    public function orderRegestrationAPI($authToken,$user)
    {

        $url = 'https://accept.paymob.com/api/ecommerce/orders';

        // Your merchant credentials and other required data
        $items = [];
            $amount = 100 * 100; //TODO add real amount
        $data = [
            "auth_token" => $authToken,
            "delivery_needed" => "false",
            "amount_cents" => $amount,
            "currency" => env('PAYMOB_CURRENCY'),
//            "items" => $items,

        ];
        $response = Http::post($url, $data);
        $body = $response->getBody()->getContents();


        // Do something with the response (e.g., decode JSON)
        $data = json_decode($body, true);
        $this->token = $data['token'];

        $billingData = new stdClass();
        $billingData->orderId = $data['id'];
        $billingData->paymentToken = $data['token'];
        $billingData->amountCents = $amount;
        $billingData->billingData = $this->formatUserData($user) ;


      $paymentToken = $this->acceptancePaymentKey($authToken,$billingData);

      $finalBillInfo =   $this->kioskOrder($paymentToken);
      $this->saveTransactionData($user->id,$finalBillInfo);

    }

    private function saveTransactionData($userId,$bill){
        $payment = StudentPayment::create([
           'student_id' => $userId,
           'payment_id' => $bill['id'],
           'payment_code' => $bill['data']['bill_reference'],
            'amount'=>$bill['amount_cents']/100,
            'success'=> $bill['success']
        ]);
        $payment->save();
    }

    public function acceptancePaymentKey($authToken,$billingData){

        $url = 'https://accept.paymob.com/api/acceptance/payment_keys';

        $data = [
            "auth_token" => $authToken,
            "currency" => env('PAYMOB_CURRENCY'),
            "integration_id" => env('PAYMOB_INTEGRATION_ID'),
            "amount_cents" => $billingData->amountCents,
            "expiration" => 3600,
            "order_id" => $billingData->orderId,
            "billing_data" => $billingData->billingData

        ];

        $response = Http::post($url, $data);
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        return $data['token'];
    }

    public function kioskOrder($payment_token)
    {

        $url = 'https://accept.paymob.com/api/acceptance/payments/pay';

        $payload = [
            'source' => [
                'identifier' => 'AGGREGATOR',
                'subtype' => 'AGGREGATOR'
            ],
            'payment_token' => $payment_token
        ];

        $client = new Client();
        $response = $client->post($url, [
            'headers' => [
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ],
            'json' => $payload,
        ]);

        $body =  $response->getBody()->getContents();
        return json_decode($body, true);



    }

    public function loginToPaymentGateway2(Request $request) //Test the library
    {
        $payment = new PaymobPayment();
         $user = User::find($request->id);
        $userName = explode(" ",$user->name);

        $returned_date = $payment->setUserId($user->id)
            ->setUserFirstName($userName[0])
            ->setUserLastName($userName[0])
            ->setUserEmail($user->email)
            ->setUserPhone($user->phone)
            ->setAmount($request->amount)
            ->pay();

//        return $returned_date;

        //pay function response
        //        [
        //            'payment_id'=>"", // refrence code that should stored in your orders table
        //            'redirect_url'=>"", // redirect url available for some payment gateways
        //            'html'=>"" // rendered html available for some payment gateways
        //        ]

//        $studentPayment = StudentPayment::create();
//        $studentPayment->student_id = $request->id;
//        $studentPayment->payment_id = $returned_date['payment_id'];

//verify function
        $newRequest = new Request();

      $verifiedPayment =   $payment->verify($request);
      return $verifiedPayment;


    }

    private function formatUserData($user)
    {
        $userName = explode(" ",$user->name);

        return [
          'first_name' => $userName[0] ,
          'last_name' => $userName[1],
          'email' => $user->email,
          'phone_number' => $user->phone,
            'street'=> $user->address,
            'floor' => 1,
            'building' => 1,
            'apartment'=>1,
            'country' => 'Egypt',
            'city' => 'cairo'
        ];


    }
}
