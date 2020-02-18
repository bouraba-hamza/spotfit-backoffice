<?php

use \App\Http\Controllers\CustomerController;
use \App\Http\Controllers\AuthController;
use \App\Http\Controllers\IdentityCardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::get("/gyms", [\App\Http\Controllers\GymController::class, 'fetch']);

Route::get("/gyms/{gymId}", [\App\Http\Controllers\GymController::class, 'getById']);
Route::get("/makeGymInFavoriteList/{gymId}", [\App\Http\Controllers\GymController::class, 'likeGym']);
Route::get("/favorites-gyms/{howBig}", [\App\Http\Controllers\GymController::class, 'getFavoritesGyms']);
Route::post("/request-gym", [\App\Http\Controllers\GymController::class, 'requestGym']);
Route::get("/sponsorship-code", [\App\Http\Controllers\GymController::class, 'getSponsorshipCode']);
Route::get("/facilities", [\App\Http\Controllers\FacilitieController::class, 'index']);
Route::get("/activities", [\App\Http\Controllers\ActivitieController::class, 'index']);
Route::get("/gyms-cities", [\App\Http\Controllers\GymController::class, 'getCities']);
Route::post("/search", [\App\Http\Controllers\GymController::class, 'search']);
Route::get("/classes", [\App\Http\Controllers\ClasseController::class, 'fetch']);
Route::get("/subscriptions", [\App\Http\Controllers\CustomerController::class, 'getSubscriptions']);
Route::get("/everywhere-subscription-prices", [\App\Http\Controllers\SubscriptionController::class, 'getEverywherePassPrices']);
Route::get("/notifications", [\App\Http\Controllers\CustomerController::class, 'getNotifications']);
Route::get("/plans", [\App\Http\Controllers\SubscriptionController::class, 'index']);

/**
 * Auth
 */
Route::get('/me', 'AuthController@getAuthenticatedUser');
Route::post('/login', 'AuthController@login');
Route::post('/login/customer', [AuthController::class, 'authenticateCustomer']);
Route::post('/login/customersignInMethod', [AuthController::class, 'SigninWithGoogle']);
Route::post('/logout', 'AuthController@logout');
//Route::get("/gymbyid/{gym_id}", "GymController@getSubscriptionTypeByGym");
Route::get("/gymSubscriptionClass", "GymController@getGymSubscriptionClass");

Route::post("/createAcoount", "BanckAccountController@createAcoount");

Route::post('/register', [CustomerController::class, 'storeclient']);
Route::post('/registerSignInMethod', [CustomerController::class, 'storeClientFromSignInMethod']);

// php storage link

/**
 * PASSWORD
 */

Route::post('/passwoupdateSubscriptionrd/update', 'PasswordController@update');
Route::get('/password/{ticket}/verify', 'PasswordController@verify');
// send reset password link to email passed as parameter
Route::post('/reset-password', 'PasswordController@sendResetLink');

/**
 *  EMAIL VERIFICATION
 * */
Route::get('/verify-email/{code}', [AuthController::class, 'verifyEmail']);
Route::get('/token/refresh', 'AuthController@refresh');

   /**
     * Base64ToPngs
     */
    Route::get("/base64ToPng", "Base64ToPngController@index");
    Route::put('/base64ToPng/{base64ToPng_id}', 'Base64ToPngController@destroy');
    Route::post('/base64ToPng', 'Base64ToPngController@store');
    //Route::post('/base64ToPng/{name}/{code}', 'Base64ToPngController@store');
    Route::post('/base64ToPng/{base64ToPng_id}', 'Base64ToPngController@update');
    Route::get('/base64ToPng/{base64ToPng_id}', 'Base64ToPngController@show');


Route::group(['prefix' => 'v1', 'middleware' => [/* 'jwt' , /* 'jwt.refresh' */]], function () {

    Route::get('/customer/setup-intent', "CustomerController@getSetupIntent");
    Route::post('/customer/payments', 'CustomerController@postPaymentMethods');
    Route::get('/customer/payment-methods', 'CustomerController@getPaymentMethods');
    Route::post('/customer/remove-payment', 'CustomerController@removePaymentMethod');
    Route::put('/customer/subscription', 'CustomerController@updateSubscription');
});

/**
 * Base64ToPngs
 */
Route::get("/base64ToPng", "Base64ToPngController@index");
Route::put('/base64ToPng/{base64ToPng_id}', 'Base64ToPngController@destroy');
Route::post('/base64ToPng', 'Base64ToPngController@store');
//Route::post('/base64ToPng/{name}/{code}', 'Base64ToPngController@store');
Route::post('/base64ToPng/{base64ToPng_id}', 'Base64ToPngController@update');
Route::get('/base64ToPng/{base64ToPng_id}', 'Base64ToPngController@show');

Route::group(['middleware' => ['jwt', 'role:customer']], function () {
    Route::post('/update-infos', [CustomerController::class, 'editProfile']);
    Route::post('/identity-card/upload', [CustomerController::class, 'uploadIdentityCard']);
    Route::get('/identity/{side}', [ IdentityCardController::class, 'getIdentityCard' ]);
});

Route::group(['middleware' => ['jwt', /* 'jwt.refresh' */]], function () {

    /**
     * Homes
     */
    Route::get("/home", "HomeController@index");
    Route::put('/home/{home_id}', 'HomeController@destroy');
    Route::post('/home', 'HomeController@store');
    Route::post('/home/{home_id}', 'HomeController@update');
    Route::get('/home/{home_id}', 'HomeController@show');

    /**
     * Activities
     */
    Route::get("/activitie", "ActivitieController@index");
    Route::put('/activitie/{activitie_id}', 'ActivitieController@destroy');
    Route::post('/activitie', 'ActivitieController@store');
    Route::post('/activitie/{activitie_id}', 'ActivitieController@update');
    Route::get('/activitie/{activitie_id}', 'ActivitieController@show');


    /**
     * Facilities
     */
    Route::get("/facilitie", "FacilitieController@index");
    Route::put('/facilitie/{facilitie_id}', 'FacilitieController@destroy');
    Route::post('/facilitie', 'FacilitieController@store');
    Route::post('/facilitie/{facilitie_id}', 'FacilitieController@update');
    Route::get('/facilitie/{facilitie_id}', 'FacilitieController@show');


    /**
     * Gyms
     */
    Route::get("/gym", "GymController@index");
    Route::get("/subtype", "GymController@getType");
    Route::put('/gym/{gym_id}', 'GymController@destroy');
    Route::post('/gym', 'GymController@store');
    Route::post('/gym/{gym_id}', 'GymController@update');
    Route::get('/gym/{gym_id}', 'GymController@show');
    Route::get("/gymbyid/{gym_id}", [\App\Http\Controllers\GymController::class, 'getSubscriptionTypeByGym']);

    //   Route::post('/gym/base64ToPng/{name}/{code}', 'Base64ToPngController@store');


    /**
     * Factures
     */
    Route::get("/equipement", "EquipementController@index");
    Route::put('/equipement/{equipement_id}', 'EquipementController@destroy');
    Route::post('/equipement', 'EquipementController@store');
    Route::post('/equipement/{equipement_id}', 'EquipementController@update');
    Route::get('/equipement/{equipement_id}', 'EquipementController@show');

    /**
     * Factures
     */
    Route::get("/facture", "FactureController@index");
    Route::put('/facture/{facture_id}', 'FactureController@destroy');
    Route::post('/facture', 'FactureController@store');
    Route::post('/facture/{facture_id}', 'FactureController@update');
    Route::get('/facture/{facture_id}', 'FactureController@show');

});

Route::group(['middleware' => ['jwt', 'role:admin']], function () {

    /* USERS PROFILE PICTURES */
    Route::get('profile-picture/{filename}', 'ProfilePictureController@getAvatar');



    /**
     * Admin
     */
    Route::get('/admins', 'AdminController@index');
    Route::post('/admins', 'AdminController@store');
    Route::post('/admins/{admin_id}', 'AdminController@update');
    Route::get('/admins/{admin_id}', 'AdminController@show');

    /**
     * Supervisor
     */
    Route::get('/supervisors', 'SupervisorController@index');
    Route::post('/supervisors', 'SupervisorController@store');
    Route::post('/supervisors/{supervisor_id}', 'SupervisorController@update');
    Route::get('/supervisors/{supervisor_id}', 'SupervisorController@show');

    /**
     * Receptionist
     */
    Route::get('/receptionists', 'ReceptionistController@index');
    Route::post('/receptionists', 'ReceptionistController@store');
    Route::post('/receptionists/{receptionist_id}', 'ReceptionistController@update');
    Route::get('/receptionists/{receptionist_id}', 'ReceptionistController@show');

    /**
     * Partner
     */
    Route::get('/partners', 'PartnerController@index');
    Route::post('/partners', 'PartnerController@store');
    Route::post('/partners/{partner_id}', 'PartnerController@update');
    Route::get('/partners/{partner_id}', 'PartnerController@show');
    Route::delete('/partners/{partner_id}', 'PartnerController@destroy');

    /**
     * Customer
     */
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/{customer_id}', [CustomerController::class, 'show']);
    Route::put('/becomeAmbassador/{customer_id}/{promote}', [CustomerController::class, 'becomeAmbassador']);

    /**
     * Trainer
     */
    Route::get('/trainers', 'TrainerController@index');
    Route::post('/trainers', 'TrainerController@store');
    Route::post('/trainers/{trainer_id}', 'TrainerController@update');
    Route::get('/trainers/{trainer_id}', 'TrainerController@show');
    Route::delete('/trainers/{trainer_id}', 'TrainerController@destroy');

    /**
     * Account
     */
    Route::put('/accounts/{id}/disable', 'AccountController@disable');
    Route::put('/accounts/{id}/enable', 'AccountController@enable');


    /**
     * Class
     */
    Route::resource('/class', 'ClasseController');

    /**
     * Subscription
     */
    Route::resource('/pass', 'SubscriptionController');

    /**
     * Settings
     */
    Route::apiResource('settings', 'SettingController')->except([
        'store', 'destroy'
    ])->parameters([
        'settings' => 'key'
    ]);


});




Route::get("/gym", "GymController@index");

/**
 * Groups
 */
Route::get("/group", "GroupController@index");
Route::put('/group/{group_id}', 'GroupController@destroy');
Route::post('/group', 'GroupController@store');
Route::post('/group/{group_id}', 'GroupController@update');
Route::get('/group/{group_id}', 'GroupController@show');


/**
 * Gyms
 */
/*
Route::get("/gym", "GymController@index_");
Route::get("/gym/show", "GymController@show");
Route::get("/gym/{gym_id}", "GymController@get");
Route::get("/gym/admin/{id_gerant}", "GymController@getByAdmin");
Route::post("/gym/add", "GymController@store");
Route::post("/gym/update", "GymController@update");
Route::delete("/gym/del/{id}", "GymController@destroy");
*/
/**
 * Equipements
 */
Route::get("/equipements", "EquipementController@index");
Route::get("/equipements/show", "EquipementController@show");
Route::get("/equipements/{equipement_id}", "EquipementController@get");
Route::post("/equipements", "EquipementController@store");
Route::put("/equipements", "EquipementController@update");
Route::post("/equipements/update", "EquipementController@update");
Route::delete("/equipements/del/{equipement_id}", "EquipementController@destroy");

