<?php


namespace App\Services;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileAvatarService
{
    const BASE_DIR = "public/avatars/customers";

    public function store($avatar)
    {
        $fake_name = \Str::random(70) . '.' . $avatar->getClientOriginalExtension();
        $path = Storage::putFileAs(self::BASE_DIR, $avatar, $fake_name);
        return ["fakeName" => $fake_name, "path" => $path];
    }

    public function update($fakeName, $avatar)
    {
        // delete the old avatar
        $this->remove($fakeName);
        // generate a random name as prefix
        $fake_name = \Str::random(70) . '.' . $avatar->getClientOriginalExtension();
        // save the file
        $path = \Storage::putFileAs(self::BASE_DIR, $avatar, $fake_name);
        return ["fakeName" => $fake_name, "path" => $path];
    }

    public function remove($fakeName)
    {
        // don't remove the default avatars
        if (!preg_match("/^a\d\.png$/", $fakeName))
            Storage::delete(self::BASE_DIR . '/' . $fakeName);
    }
}
