<?php

namespace App\Http\Controllers;

use App\Account;
use Illuminate\Http\Request;
use JWTAuth;
use JWTException;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Boolean;


class AuthController extends Controller
{


    public function getWhatsapp(){

//        $phone = $request->query('phone');
//        $text = $request->query('text');
        header("Location: whatsapp://send?phone=212624019136&text=Bonjour,%20je%20voudrais%20plus%20d’informations%20concernant%20votre%20application%20spotfi");
//        exit();
//        return redirect()->to('whatsapp://send?phone=212624019136&text=Bonjour,%20je%20voudrais%20plus%20d’informations%20concernant%20votre%20application%20spotfi')->send();
//        return ["success" => ["les informations d'identification valides: "]];

    }
    public function SigninWithTwitter(Request $request){

        $credentials = $request->all();

        Log::info($credentials);

        $account = Account::where("username", "=", trim($credentials["username"] ?? NULL))->first();

        if (!$account) {
            return ["new_account" => ["yes"]];
        }

        try {
            $token = JWTAuth::fromUser($account);
        } catch (JWTException $e) {
            return response()->json(['errors' => ["Impossible de générer Impossible de créer un clé d'authentification"]]);
        }

        // who is the owner
        $account_owner = $account->accountable()->first();

        // mark this pass
        $account->update(["lastLogin" => now()]);

        // reformat the response
        $account_owner["jwtToken"] = $this->formatToken($token);
        return $account_owner;
    }

    public function SigninWithGoogle(Request $request){

        $credentials = $request->only('email', 'uid');

        $account = Account::where("email", "=", trim($credentials["email"] ?? NULL))->first();

        if (!$account) {
            return ["new_account" => ["yes"]];
        }

        try {
            $token = JWTAuth::fromUser($account);
        } catch (JWTException $e) {
            return response()->json(['errors' => ["Impossible de générer Impossible de créer un clé d'authentification"]]);
        }

        // who is the owner
        $account_owner = $account->accountable()->first();

        // mark this pass
        $account->update(["lastLogin" => now()]);

        // reformat the response
        $account_owner["jwtToken"] = $this->formatToken($token);
        return $account_owner;
    }

    public function login(Request $request, $emailVerification = true)
    {
        $credentials = $request->only('username', 'password', 'email');

        // verify if the user exist
        $account = Account::where("username", "=", trim($credentials["username"] ?? NULL))
            ->orWhere("email", "=", trim($credentials["username"] ?? NULL))
            ->orWhere("email", "=", trim($credentials["email"] ?? NULL))
            ->first();
        // in case request user dosn't exist
        if (!$account) {
            return ["errors" => ["les informations d'identification invalides: "]];
        }

        // verify the password
        $truePass = \Hash::check($credentials["password"], $account->password);

        // the password dosn't match
        if (!$truePass) {
            return ["errors" => ["les informations d'identification invalides: "]];
        }

        if($emailVerification) {
            // email must to be verified
            if (!$account->email_verified_at != null)
                return ["errors" => ["vérifier votre boîte de réception pour confirmer la propriété de l'email"]];
        }

        // disabled account forbidden to access app
        if ($account->disabled === 1) {
            return ["errors" => ["le compte est désactivé maintenant, veuillez contacter l'administrateur"]];
        }

        try {
            $token = JWTAuth::fromUser($account);
        } catch (JWTException $e) {
            return response()->json(['errors' => ["Impossible de générer Impossible de créer un clé d'authentification"]]);
        }

        // who is the owner
        $account_owner = $account->accountable()->first();

        // mark this pass
        $account->update(["lastLogin" => now()]);

        // reformat the response
        $account_owner["jwtToken"] = $this->formatToken($token);
        return $account_owner;
    }

    public function authenticateCustomer(Request $request)
    {
        return $this->login($request, false);
    }

    private function formatToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }

    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function getAuthenticatedUser(Request $request)
    {

        try {

            if (!$account = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        $token = JWTAuth::getToken();
        $formatedToken = $this->formatToken($token . "");
        // who is the owner
        $account_owner = $account->accountable()->first();
        // reformat the response
        $account_owner["jwtToken"] = $formatedToken;
        return $account_owner;
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json($this->formatToken($token));
    }

    public function verifyEmail($code)
    {
        // check if the tag exists
        $ev = \DB::table('email_verification')->where('code', $code)->first();

        // in case tag code dosn't belongs to any account
        if (!$ev)
            return ["errors", ["NOT FOUND"]];

        // case two the code expired or used in the past by someone
        if ($ev->done == 1)
            return ["errors", ["verification code expired or already consumed, please request a new one."]];

        // get the account from the email
        $account = Account::where('email', $ev->email)->first();

        if (!$account)
            return ["errors", ["INTERNAL ERRORS"]];

        // update the account and the verification code ticket
        $account->email_verified_at = now();
        $account->save();

        \DB::table('email_verification')
            ->where('code', $code)
            ->update(['done' => 1, 'updated_at' => now()]);

        $redirect_url = config('app.frontend_url') . "/login?accountId={$account->id}&verified=1";
        return redirect($redirect_url);
    }
}
