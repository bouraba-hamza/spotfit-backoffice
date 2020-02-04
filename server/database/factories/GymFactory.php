<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Gym;
use Faker\Generator as Faker;

$factory->define(Gym::class, function (Faker $faker) {
    $logos = [
        "bEARTordideciaRbeQuIthomoraNTaT.PNG",
        "edULATHlOMaCIoNasTaMBUSHINTiNgt.PNG",
        "EpROlEmaTiVERsIEThUTIoUaLitYliT.PNG",
        "IndbAkeRyPErShENDaRKbOIsEWBRUal.png",
        "INgLiuMpTOMoNfERSEqUiNSDaLMANEN.PNG",
        "iSEPAteOustOrYlogAstrOaTenteNtA.PNG",
        "iSoraMenTaCkLeGinGEATeXterylAcO.PNG",
        "jeCTIblEFaCHItriStorAbLaNtAGeoL.PNG",
        "lu8te8hbr8j5hg6k625ezaor2ip6vs.jpg",
        "oChotionScOBsEignaterclARblarca.PNG",
        "ROWLASEXpirAlYPEREMiNgHtYPoLeNT.PNG",
        "siVErsIDerMambErANGReShINGSTIte.PNG",
        "ThuRENiSGROThEDImaCkRiAngLIEraN.PNG",
        "xhIcsIuMEOnOUSIomERiTaLaTErNuSi.PNG",
    ];

    $covers = [
        "FLoCOsintigHTAlEASEyESteDItIONS.PNG",
        "GmaiNLITernerTIckruiNTImerskINS.PNG",
        "mEoPERIGHtunAuMeMiGETeNTuSoMIDS.PNG",
        "mpeNoCUTErcHOriAlTAtElRyMAcuSTo.png",
        "oRyDropOnaCHAnoTHOWNsMiNaRDwati.PNG",
        "SHESTAXIANtIoNiahARiNICerSKAftM.PNG",
        "UnchlRYNouSerONINTIOuSeaTERvinC.PNG",
    ];

    $gymName = $faker->words(3, true);
    return [
        "group_id" => function () use ($gymName) {
            return factory(App\Group::class)->create(['name' => $gymName,])->id;
        },
        "logo" => $faker->randomElement($logos),
        "name" => $gymName,
        "rate" => $faker->numberBetween(1, 5),
        "qrcode" => $faker->uuid,
        "summary" => $faker->realText(150),
        "planning" => json_encode([]),
        "class_id" => function () {
            return App\Classe::inRandomOrder()->first("id")->id;
        },
        "covers" => json_encode($faker->randomElements($covers, $faker->numberBetween(2, 5))),
    ];
});
