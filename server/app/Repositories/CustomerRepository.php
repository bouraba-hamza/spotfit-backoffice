<?php


namespace App\Repositories;

use App\Customer;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CustomerRepository extends BaseRepository
{
    protected $account;
    protected $address;

    public function __construct(Customer $customer, AccountRepository $accountRepository, AddressRepository $addressRepository)
    {
        parent::__construct($customer);
        $this->account = $accountRepository;
        $this->address = $addressRepository;
    }

    public function update($id, array $args)
    {
        $customer = $this->find($id);
        // update the account
        if(isset($args["account"]))
            $customer->account()->first()->update($args["account"]);
        // finally the address
        if(isset($args['address']))
        {

            $address = $customer->address()->first();
            if(!$address) {
                $customer->address()->save(new \App\Address($args['address']));
            } else {
                $address->update($args['address']);
            }
        }
        // the the customer profile
        parent::update($id, $args);
        return $customer;
    }

    public function insert(array $args)
    {
        // create a empty customer
        $customer = parent::insert(["qrcode" => (string)Str::uuid(), "avatar" => 'a' . Arr::random([1, 2, 3, 4]) . '.png' ]);

        // account for to access the app
        $account = new \App\Account($args);
        $customer->account()->save($account);
        return $customer;
    }
}
