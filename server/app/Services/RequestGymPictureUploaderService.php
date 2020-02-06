<?php


namespace App\Services;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RequestGymPictureUploaderService
{
    const BASE_DIR = "public/gyms/requested";

    public function store($file)
    {
        $fake_name = \Str::random(100) . '.' . $file->getClientOriginalExtension();
        $path = Storage::putFileAs(self::BASE_DIR, $file, $fake_name);
        return ["fakeName" => $fake_name, "path" => $path];
    }

    public function update($fakeName, $file)
    {
        // delete the old photo
        $this->remove($fakeName);
        // generate a random name as prefix
        $fake_name = \Str::random(100) . '.' . $file->getClientOriginalExtension();
        // save the file
        $path = \Storage::putFileAs(self::BASE_DIR, $file, $fake_name);
        return ["fakeName" => $fake_name, "path" => $path];
    }

    public function remove($fakeName)
    {
        Storage::delete(self::BASE_DIR . '/' . $fakeName);
    }
}
