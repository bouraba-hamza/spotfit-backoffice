<?php

namespace App\Http\Controllers;

use App\CustomerSubscription;
use App\Repositories\CustomerRepository;
use App\Services\AuthService;
use App\Services\CustomerSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
class PaymentController extends Controller
{
    protected $customer;
    protected $customerSubscriptionService;
    private $authService;

    public function __construct(
        AuthService $authService,
        CustomerRepository $customerRepository,
        CustomerSubscriptionService $customerSubscriptionService)
    {
        $this->customer = $customerRepository;
        $this->authService = $authService;
        $this->customerSubscriptionService = $customerSubscriptionService;
    }


    public function getRemainingSession($subscriptionId)
    {
        return \App\Subscription::where("id", $subscriptionId)->value('duration');

    }

    public function getPrice($gymSubscriptionId)
    {
        return \App\GymSubscriptionType::where('id',$gymSubscriptionId)->value('price');

    }

    public function  paymentBycartcmi(Request $request)
    {

        $client = new Client(); //GuzzleHttp\Client
        $result = $client->post('your-request-uri', [
            'form_params' => [
                'sample-form-data' => 'value'
            ]
        ]);

        return response()->json([
        'customer_charged' => $result
    ]);

    }

   public function payFormBinga() {

       $storeId = ""; // store id
       $privateKey = ""; // private key

       try {
           if (isset($_POST['code'], $_POST['orderCheckSum'])) {
               $code = $_POST['code'];
               $order_check_sum = $_POST['orderCheckSum'];
               if (md5("PAY" . $_POST['amount'] . $storeId . $_POST['externalId'] . $_POST['buyerEmail'] . $privateKey) == $order_check_sum) {
                   // Le client a effectivement payé chez Wafa cash
                   // Mettre la commande à jour sur base du code Binga précédemment inséré dans "book.php"
                   // Ne pas insérer des variables $_POST directement à la base de données, pensez à échapper les caractères spéciaux
                   echo "100;" . date('Y-m-d\TH:i:se');
               } else {
                   echo "000;" . date('Y-m-d\TH:i:se');
               }
           }

       } catch (Exception $e) {
           dd("000;" . date('Y-m-d\TH:i:se'));
       }
   }

    public function bookFromBinga(){

        $storeId = ""; // store id
        $privateKey = ""; // private key

        try {
            if (isset($_POST['code'], $_POST['orderCheckSum'])) {
                $code = $_POST['code'];
                $order_check_sum = $_POST['orderCheckSum'];
                if (md5("PRE-PAY" . $_POST['amount'] . $storeId, $_POST['externalId'] . $_POST['buyerEmail'] . $privateKey) == $order_check_sum) {
                    // insérer le code binga dans votre base de données avec un status pending
                    // Ne pas insérer des variables $_POST directement à la base de données, pensez à échapper les caractères spéciaux.
                } else {
                    throw new Exception("Checksums do not match!");
                }
            }
        } catch (Exception $e) {
            dd('Caught exception: ',  $e->getMessage(), "\n");
        }
    }

    public function payCashFromBinga(Request $request)
    {
        $customer = $this->authService->connected(true);
         Log::info($customer);
        $data = $request->all();
//        $paymentID = $request->get('payment');
        $total = 0;
        try {
            //Todo Send a call to QrCode generator
            $client = new Client([
                'base_uri' => 'http://preprod.binga.ma'
            ]);

            foreach ($data as $value) {
                Log::info($value);
                $total += $this->getPrice($value['id']);
            }


            // remplacer par le StoreId et PrivateKey fournis
            $storeId = "";
            $privateKey = "";
            $bingaUrl = "";

// variable à modifier afin de refléter la command du marchand
            $amount_raw = 455.878; // montant à payer
            $externalId = 123456; // id de la transaction du marchand
            $expirationDate = date('Y-m-d\TH:i:se', strtotime("+30 days")); // date d'expiration now + 30 days
            $amount = bcadd(round($amount_raw, 2), '0', 2); // arrondi  et converti en unités à virgule flottante en double précision
            $firstName = "Ahmad";
            $lastName = "bin Rochd";
            $email = "example@example.com"; // email du client
            $address = "Córdoba, Spain";
            $phone = "+1 123 456 789";
            $checksum = md5("PRE-PAY" . $amount . $storeId . $externalId . $email . $privateKey);

//            http://preprod.binga.ma/bingaApi/api/orders/pay
            $response = $client->request('POST',
                '/bingaApi/api/orders/pay',
                [
                    'auth' => ['Binga.ma', 'Binga'],
                    'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                    'body' => [
                        'apiVersion' => '1.1',
                        'externalId' => '',
                        'expirationDate' => '',
                        'amount' => $total,
                        'storeId' => '',
                        'successUrl' => '',
                        'failureUrl' => '',
                        'bookUrl' => '',
                        'payUrl' => '',
                        'buyerFirstName' => '',
                        'buyerLastName' => '',
                        'buyerEmail' => '',
                        'buyerAddress' => '',
                        'buyerPhone' => '',
                        'orderCheckSum' => ''
                    ]

                ]
            );

            if ($response.getStatusCode() == 200) {
                foreach ($data as $value) {
                    Log::info($value);

                    //get session from duration
                    $customerSubscription = CustomerSubscription::create([
                        'customer_id' => $customer->id,
                        'gym_subscription_type' => $value['id'],
                        "qrcode" => (string)Str::uuid(),
                        "payment_method_id" => 4,
                        // todo price calculate here not in the frontend
                        "price" => $this->getPrice($value['id']),
                        "consumed_at" => $value['consumed_at'],
                        "remaining_sessions" => $this->getRemainingSession($value['subscription_id']),
                    ]);
                    $customerSubscription->statuses()->attach(1, ['datetime' => now()]);
                }
            } else {
                dd($response.getStatusCode());
            }


        } catch (Exception $e) {
            Log::info($e->getMessage());
            return false;
        }

        return response()->json([
            'customer_charged' => $customerSubscription
        ]);

    }
}
