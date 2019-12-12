<?php

namespace App\Http\Controllers;

use App\Account;
use App\Customer;
use App\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;

class CustomerController extends Controller
{
    const VALIDATION_MESSAGES = [
        'birthDay.date_format' => "la date de naissance dois respecter le format Année-Mois-jour",
        'avatar.image' => "la photo de profile  dois respecter le format d'image ",
        'account.email.required' => "le champ email est requis",
        'account.email.email' => "il faut respecter le format de mail",
        'account.email.unique' => "l'email déjà pris",
        'account.username.required' => "le champ login est requis",
        'account.username.unique' => "le nom d'utilisateur déjà pris",
        'account.password.required' => "le champ mote de passe est requis",
        'account.password.min' => "la longueur du mot de passe doit être d'au moins 6 caractères",
    ];

    public function index()
    {
        return Customer::all();
    }

    public function store(Request $request)
    {
        // filter unwanted inputs from request
        $customer = $request->all();
        // convert the json to php array
        $account = json_decode($request->get('account'), true);
        $customer['account'] = $account;
        // build address object
        $address = json_decode($request->get('address'), true);

        $validator = Validator::make($customer, [
            'gender' => 'in:m,f',
            'birthDay' => 'date_format:Y-m-d',
            'avatar' => 'image',
            // these fields required to create account when the customer can use the application
            'account.email' => 'required|email|unique:accounts,email',
            'account.username' => 'required|unique:accounts,username',
            'account.password' => 'required|min:6',
        ], self::VALIDATION_MESSAGES);

        // stop running function proccesses if the validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        // save the file in storage
        if ($request->hasFile("avatar")) {
            $avatar = $request->file('avatar');
            $fake_name = Str::slug(Str::random(7) . '_' . $avatar->getClientOriginalName());
            $path = \Storage::putFileAs('avatars', $avatar, $fake_name);
            $customer["avatar"] = $fake_name;
        }

        unset($customer['account']);
        $customer['account_id'] = Account::create($account)->id;
        $customer['address_id'] = Address::create($address)->id;
        $customer_id = Customer::create($customer)->id;

        // return the id of the resource just created
        return ['customer_id' => $customer_id];
    }

    public function show($customer_id)
    {
        return Customer::findOrFail($customer_id);
    }

    public function update(Request $request, $customer_id)
    {
        // check if the the requested resource exist in database
        $__o = Customer::findOrFail($customer_id);

        $account = json_decode($request->get('account'), true);
        $address = json_decode($request->get('address'), true);
        $customer = $request->all();
        $customer['account'] = $account;

        $validator = Validator::make($customer, [
            'gender' => 'in:m,f',
            'birthDay' => 'date_format:Y-m-d',
            'avatar' => 'image',
            'account.email' => 'required|email|unique:accounts,email,' . $__o->account->id,
            'account.username' => 'required|unique:accounts,username,' . $__o->account->id,
            'account.password' => 'min:6',
        ], self::VALIDATION_MESSAGES);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        if ($request->hasFile("avatar")) {
            // delete the old avatar
            \Storage::delete('avatars/' . $__o->avatar);
            // save the the received photo
            $avatar = $request->file('avatar');
            // generate a random name as prefix
            $fake_name = Str::slug(Str::random(7) . '_' . $avatar->getClientOriginalName(), '.');
            // save the file
            $path = \Storage::putFileAs('avatars', $avatar, $fake_name);
            $customer["avatar"] = $fake_name;
        }

        // update the account
        $__o->account()->first()->update($account);
        // the the customer profile
        $__o->update($customer);
        // finally the address
        $__o->address()->first()->update($address);


        return ['customer_id' => $__o->id];
    }

    public function destroy($customer_id)
    {
        $customer = Customer::findOrFail($customer_id);
        $customer->delete();
        return ['status' => 'success', 'deleted_resource_id' => $customer->id];
    }
}
