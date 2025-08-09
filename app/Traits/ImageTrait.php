<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait ImageTrait
{
    public static function uploadImage($image, $path)
    {
        $path = $image->store($path, 'public');
        return $path;
    }

    public static function updateImage($object, $file, $input)
    {
        if (request()->hasFile($input)) {
            if ($object != null) {
                Storage::disk('public')->delete($object);
            }
            return $data[$input] = ImageTrait::uploadImage(request()->file($input), $file);
        } else {
            return $data[$input] = $object;
        }
    }

    public static function deleteFile($object, $disk = 'public')
    {
        if ($object != null) {
            Storage::disk($disk)->delete($object);
        }
        return;
    }

    public static function deleteFiles($objects, $fileAttribute, $disk = 'public')
    {
        foreach ($objects as $object) {
            $filePath = $object->{$fileAttribute};
            if ($filePath && Storage::disk($disk)->exists($filePath)) {
                Storage::disk($disk)->delete($filePath);
            }
            $object->delete();
        }
        return true;
    }
}
