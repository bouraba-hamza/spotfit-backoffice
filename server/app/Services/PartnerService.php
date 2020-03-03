<?php


namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Boolean;

class PartnerService
{
    public static function justHasOneGym(int $partner_id)
    {
        return PartnerService::isSingle($partner_id);
    }

    public static function isSingle(int $partner_id)
    {
        return \App\Partner::whereId($partner_id)->single()->count() === 1;
    }
}
