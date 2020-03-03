<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\GymService;
use App\Services\PartnerService;
use Illuminate\Http\Request;

class HistoryController extends Controller
{


    private $authService;

    /**
     * HistoryController constructor.
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }


    /**
     * Retrieve clients that pass a session in specific gym partner
     * @params $gymId
     * @return array
     * */
    public function getVisits()
    {
        $account = $this->authService->connected();
        $role_name = $account->getRoleNames()->first(); // partner, receptionist, supervisor
        $u = $account->accountable()->first();
        $r = ["GYMS_COUNT" => 1, "data" => [

        ]];

        switch ($role_name) {
            case 'partner':
                if (PartnerService::justHasOneGym($u->id)) {
                    $r["data"][0]["gymId"] = $u->gyms()->first()->id;
                    $r["data"][0]["gymName"] = $u->gyms()->first()->name;
                    $r["data"][0]["visitors"] = GymService::visitors($u->gyms()->first()->id);
                } else {
                    $gyms = $u->gyms;
                    $r["GYMS_COUNT"] = count($gyms);
                    $i=0;
                    $gyms->each(function ($g) use (&$r,&$i) {
                        $r["data"][$i]["gymId"] = $g->id;
                        $r["data"][$i]["gymName"] = $g->name;
                        $r["data"][$i]["visitors"] = GymService::visitors($g->id);
                        $i++;
                    });
                }
                break;
            case 'supervisor':
            case 'receptionist':
                $r["data"][0]["gymId"] = $u->gym->id;
                $r["data"][0]["gymName"] = $u->gym->name;
                $r["data"][0]["visitors"] = GymService::visitors($u->gym->id);
                break;
        }
        return $r;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
