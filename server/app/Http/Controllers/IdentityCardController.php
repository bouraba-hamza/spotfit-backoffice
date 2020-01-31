<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use JWTAuth;

class IdentityCardController extends Controller
{
    public function getIdentityCard($side)
    {
        // retrieve the customer
        $account = JWTAuth::parseToken()->authenticate();
        if(!$account)
            abort(404);
        $customer =  $account->accountable()->first();

        $side = strtolower($side);
        $filename = $side === 'front' ? $customer->IDF : $customer->IDB;

        $path = storage_path('app/identity-cards/customers/' . $filename);

        if (!$filename || !File::exists($path)) {
            abort(404);
        }

        return response()->make(File::get($path), 200, ['Content-type' => File::mimeType($path)]);
    }
}
