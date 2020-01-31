<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Repositories\CustomerRepository;
use App\Services\IdentityCardUploaderService;
use App\Services\ProfileAvatarService;
use Illuminate\Http\Request;
use Validator;
use JWTAuth;

class CustomerController extends Controller
{
    protected $customer;
    protected $profileAvatarService;
    protected $identityCardUploaderService;

    public function __construct(
        CustomerRepository $customerRepository,
        ProfileAvatarService $profileAvatarService,
        IdentityCardUploaderService $identityCardUploaderService
    ) {
        $this->customer = $customerRepository;
        $this->profileAvatarService = $profileAvatarService;
        $this->identityCardUploaderService = $identityCardUploaderService;
    }

    public function index()
    {
        return $this->customer->all();
    }

    public function show($customer_id)
    {
        return $this->customer->find($customer_id);
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
        if(!$account)
            abort(404);
        $customer =  $account->accountable()->first();

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

    public function becomeAmbassador(Request $request, $customer_id, $promote) {
        $customer = $this->customer->find($customer_id);
        if(!$customer) abort(404);

        $customer->update(["ambassador" => $promote]);

        return $customer;
    }

    public function store(CustomerRequest $request)
    {
        // filter unwanted inputs from request
        $customer = $request->all();

        // create customer account
        $customer = $this->customer->insert($customer);

        // return the resource just created
        return $this->customer->findBy("id", $customer->id);
    }


    public function uploadIdentityCard(Request $request) {
        // \Log::info($request->all());

        $data = $request->all();

        // apply validation rules
        $validator = Validator::make($data, [
            'IDF'=> 'image|max:2048',
            'IDB'=> 'image|max:2048',
            'SELFIE'=> 'image|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        // retrieve the customer
        $account = JWTAuth::parseToken()->authenticate();
        if(!$account)
            abort(404);
        $customer =  $account->accountable()->first();

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
}
