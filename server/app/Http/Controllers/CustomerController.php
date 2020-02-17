<?php

namespace App\Http\Controllers;

use App\Account;
use App\customerSubscription;
use App\Http\Requests\CustomerRequest;
use App\Repositories\CustomerRepository;
use App\Services\AuthService;
use App\Services\CustomerSubscriptionService;
use App\Services\IdentityCardUploaderService;
use App\Services\ProfileAvatarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Stripe\Transfer;
use Validator;

class CustomerController extends Controller
{
    protected $customer;
    protected $profileAvatarService;
    protected $identityCardUploaderService;
    protected $customerSubscriptionService;

    private $authService;

    public function __construct(
        AuthService $authService,
        CustomerRepository $customerRepository,
        ProfileAvatarService $profileAvatarService,
        IdentityCardUploaderService $identityCardUploaderService,
        CustomerSubscriptionService $customerSubscriptionService
    )
    {
        $this->customer = $customerRepository;
        $this->profileAvatarService = $profileAvatarService;
        $this->identityCardUploaderService = $identityCardUploaderService;
        $this->authService = $authService;
        $this->customerSubscriptionService = $customerSubscriptionService;
    }

    public function index()
    {
        return $this->customer->all();
    }

    public function getSetupIntent(Request $request)
    {

//        get current Account
        $account = \JWTAuth::parseToken()->authenticate();
        if (!$account)
            abort(404);

        $customer = $account->accountable()->first();
//
//
//        Log::info($customer->id);
//
//        $customer_id = $request->get('customer_id');

//        $customer_id = 1;
//
//        $customer = $this->customer->find($customer_id);

        return $customer->createSetupIntent();
    }

    public function postPaymentMethods(Request $request)
    {

//        $account = \JWTAuth::parseToken()->authenticate();
//
//        $customer = $account->accountable()->first();

//        $customer_id = 1;
//
//        $customer = $this->customer->find($customer_id);

        $account = \JWTAuth::parseToken()->authenticate();
        if (!$account)
            abort(404);

        $customer = $account->accountable()->first();

        $paymentMethodID = $request->get('payment_method');

        Log::info($paymentMethodID);

        if ($customer->stripe_id == null) {

            $customer->createAsStripeCustomer();

        }

//        payment_method: {
//            card: this.card,
//            billing_details: {
//            name: this.name
//            }
//        }

        $customer->addPaymentMethod($paymentMethodID);
        $customer->updateDefaultPaymentMethod($paymentMethodID);

        return response()->json(null, 204);
    }

    public function getPaymentMethods(Request $request)
    {

//        $customer = $request->customer();

        $account = \JWTAuth::parseToken()->authenticate();
        if (!$account)
            abort(404);

        $customer = $account->accountable()->first();

        $methods = array();

        foreach ($customer->paymentMethods() as $method) {
//                Log::info($method);

            array_push($methods, [
                'id' => $method->id,
                'brand' => $method->card->brand,
                'last_four' => $method->card->last4,
                'exp_month' => $method->card->exp_month,
                'exp_year' => $method->card->exp_year,
                'fingerprint' => $method->card->fingerprint,
            ]);
        }


        return response()->json($methods);
    }

    public function removePaymentMethod(Request $request)
    {
        $account = \JWTAuth::parseToken()->authenticate();
        if (!$account)
            abort(404);

        $customer = $account->accountable()->first();
//        $customer = $request->customer();
        $paymentMethodID = $request->get('id');
        // get
        $paymentMethods = $customer->paymentMethods();

        foreach ($paymentMethods as $method) {
            if ($method->id == $paymentMethodID) {
                $method->delete();
                break;
            }
        }

        return response()->json('deleted', 204);
    }

    public function getSubscriptionByCustomerId()
    {
        $account = \JWTAuth::parseToken()->authenticate();

        $customer = $account->accountable()->first();
        $customerSubscription = customerSubscription::where('customer_id', $customer->id)
            ->get();

        return $customerSubscription;
    }


    public function updateSubscription(Request $request)
    {
        $account = \JWTAuth::parseToken()->authenticate();
        if (!$account)
            abort(404);

        $customer = $account->accountable()->first();

        $passId = $request->get('pass');
//        $request->get('status_id');
        $statusId = 1;  //confirmed

//        $dateConsumption = $request->get('date_consumption');

        $customerId = $customer->id;

        $data = $request->all();
        $paymentID = $request->get('payment');

        try {
//            20000 equivalent a 200 dh because stripe take all amount as a cent so 2000 cent /100 => ? dh
            $stripeCharge = $customer->charge(39999, $paymentID);

//        group_subscription_id
//        customer_id
//        price
//        qrcode
//        payment_method_id
//        consumed_at
//        remaining_sessions
            //Todo Send a call to QrCode generator
            $customerSubscription = customerSubscription::create($data);


            Log::info($customerSubscription);
            // Todo attach status to custome subscription By confirmed when payed
            //status by default inactive

//            $tabCustomer = DB::table('customer_subscription_statuses')->insert(
//                [
//                    'customer_subscription_id' => $customerSubscription->id,
//                    'status_id' => $statusId,
//                    'datetime' => now(),
//                ]
//            );
//            $customerSubscription->statuses()->attach([19 , $statusId]);


//            $payment = $user->charge(100, $paymentMethod);
        } catch (Exception $e) {

            Log::info($e->getMessage());
            abort(500, $e->getMessage());
            return false;
        }

//        \Stripe\Stripe::setApiKey('sk_test_A2o5D96F7v1MDIQ8VUXghqAZ00U6SEtx8t');
//
//        // `source` is obtained with Stripe.js; see https://stripe.com/docs/payments/accept-a-payment-charges#web-create-token
//        \Stripe\Charge::create([
//            'amount' => 2000,
//            'currency' => 'mad',
//            'source' => 'tok_mastercard',
//            'description' => 'Charge for jenny.rosen@example.com',
//        ]);

        return response()->json([
            'customer_charged' => $stripeCharge
        ]);
    }

    public function transfert()
    {
        // Create a Transfer to a connected account (later):

        $account = \Stripe\Account::create([
            'country' => 'US',
            'type' => 'custom',
            'requested_capabilities' => ['card_payments', 'transfers'],
        ]);

        $transfer = Transfer::create([
            'amount' => 7000,
            'currency' => 'usd',
            'destination' => $account->id,
            'transfer_group' => '{ORDER10}',
        ]);
    }


    public function show($customer_id)
    {
        return $this->customer->find($customer_id);
    }

    public function store(Request $request)
    {
        // filter unwanted inputs from request
        $customer = $request->all();


        $validator = Validator::make($customer, [
            'email' => "required|email",
            'password' => 'min:6',
        ], CustomerRequest::VALIDATION_MESSAGES);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        // save the file in storage
        if ($request->hasFile("avatar")) {
            $customer["avatar"] = $this->profileAvatarService->store($request->file('avatar'))["fakeName"];
        }
        // create customer account
        $customer_id = $this->customer->insert($customer)->id;

        // return the id of the resource just created
        return ['customer_id' => $customer_id];
    }

    public function update(Request $request, $customer_id)
    {
        // check if the the requested resource exist in database
        $customer = $this->customer->find($customer_id);
        $data = $request->all();

        $validator = Validator::make($data, [
            'gender' => 'in:m,f',
            'birthDay' => 'date_format:Y-m-d',
            'avatar' => 'image',
            'account.email' => "required|email|unique:accounts,email,{$customer->account->id}",
            'account.username' => "required|unique:accounts,username,{$customer->account->id}",
            'account.password' => 'min:6',
        ], CustomerRequest::VALIDATION_MESSAGES);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        if ($request->hasFile("avatar")) {
            $data["avatar"] = $this->profileAvatarService->update($customer->avatar, $request->file("avatar"))["fakeName"];
        }

        $this->customer->update($customer_id, $data);

        return ['customer_id' => $customer_id];
    }


    public function editProfile(Request $request)
    {
        $account = JWTAuth::parseToken()->authenticate();
        if (!$account)
            abort(404);
        $customer = $account->accountable()->first();

        $data = $request->all();

        // Updating email and password should not be here
        /*unset($data['account']['email']);
        unset($data['account']['password']);*/

        $validator = Validator::make($data, [
            'gender' => 'in:m,f',
            'birthDay' => 'date_format:Y-m-d',
            'avatar' => 'image',
            'account.email' => "email|unique:accounts,email,{$customer->account->id}",
            'account.password' => 'min:6',
        ], CustomerRequest::VALIDATION_MESSAGES);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        if ($request->hasFile("avatar")) {
            $data["avatar"] = $this->profileAvatarService->update($customer->avatar, $request->file("avatar"))["fakeName"];
        }

        $this->customer->update($customer->id, $data);

        return ['customer_id' => $customer->id];
    }

    public function becomeAmbassador(Request $request, $customer_id, $promote)
    {
        $customer = $this->customer->find($customer_id);
        if (!$customer) abort(404);

        $customer->update(["ambassador" => $promote]);

        return $customer;
    }

    public function storeclient(CustomerRequest $request)
    {
        // filter unwanted inputs from request
        $customer = $request->all();

        // create customer account
        $customer = $this->customer->insert($customer);
        // return the resource just created
        return $this->customer->findBy("id", $customer->id);
    }

    public function storeClientFromSignInMethod(Request $request)
    {
        $customerinfo = $request->all();

        // create customer account
        $customer = $this->customer->insert($customerinfo);

        // get token from client authenticated
        $account = Account::where("email", "=", trim($customerinfo["email"] ?? NULL))->first();

        $token = JWTAuth::fromUser($account);

        // mark this pass
        $account->update(["lastLogin" => now()]);

        // reformat the response
        $account["jwtToken"] = $this->formatToken($token);
        return $customer;

    }

    private function formatToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }


    public function uploadIdentityCard(Request $request)
    {
        // \Log::info($request->all());

        $data = $request->all();

        // apply validation rules
        $validator = Validator::make($data, [
            'IDF' => 'image|max:2048',
            'IDB' => 'image|max:2048',
            'SELFIE' => 'image|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        // retrieve the customer
        $account = JWTAuth::parseToken()->authenticate();
        if (!$account)
            abort(404);
        $customer = $account->accountable()->first();

        // upload the files to SPOTFIT storage

        if ($request->hasFile("SELFIE")) {
            $data["avatar"] = $this->profileAvatarService->update($customer->avatar, $request->file("SELFIE"))["fakeName"];
        }

        if ($request->hasFile("IDF")) {
            $data["IDF"] = $this->identityCardUploaderService->update($customer->IDF, $request->file("IDF"))["fakeName"];
        }

        if ($request->hasFile("IDB")) {
            $data["IDB"] = $this->identityCardUploaderService->update($customer->IDB, $request->file("IDB"))["fakeName"];
        }

        unset($data['SELFIE']);

        $this->customer->update($customer->id, $data);

        return ['customer_id' => $customer->id];
    }

    public function getSubscriptions()
    {
        $customer = $this->authService->connected(true);

        $subscriptions = $this->customerSubscriptionService->getCustomerSubscriptions($customer->id);
        return response()->json($subscriptions);
    }

    public function getNotifications()
    {
        $customer = $this->authService->connected(true);
        if (!$customer)
            return;

        return response()->json($customer->notifications()->orderBy("data->datetime", "desc")->get(["id", "data"])->map(function ($notification) {
            $n = json_decode($notification->data);
            $n->id = $notification->id;
            return $n;
        }));
    }


}
