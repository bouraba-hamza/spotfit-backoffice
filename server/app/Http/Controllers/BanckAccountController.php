<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BanckAccountController extends Controller
{

    public function createAcoount(Request $request)
    {
        \Stripe\Stripe::setApiKey('sk_test_A2o5D96F7v1MDIQ8VUXghqAZ00U6SEtx8t');

        $data =  $request->all();

    try {

            $account = \Stripe\Account::create([
                'country' => 'MA',
                'type' => 'custom',
                'business_type' => 'individual',
                'requested_capabilities' => ['card_payments', 'transfers'],
            ]);

       $accountLink = \Stripe\AccountLink::create([
            'account' => $account->id,
            'failure_url' => 'http://localhost:8000?failure',
            'success_url' => 'http://localhost:8000?success',
            'type' => 'custom_account_verification',
        ]);

            Log::info($account);
            Log::info($accountLink);

    }
    catch (Exception $e) {

        Log::info($e->getMessage());
            abort(500, $e->getMessage());
            return false;

    }


// 2: Create account link.
//        var accountLink = await stripe.accountLinks.create({
//      account: account.id,
//      success_url: 'http://localhost:4242?success',
//      failure_url: 'http://localhost:4242?failure',
//      type: 'custom_account_verification',
//      collect: 'eventually_due',
//    });
//  } catch (err) {
//console.log(err);
//res.status(400)
//res.send({ error: err })
//return;
//}
//
//res.send(accountLink);


    }


}
