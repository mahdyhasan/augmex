<?php
namespace App\Traits;

use Illuminate\Support\Facades\File;

trait FileTrait
{
    /**
     * Save the uploaded image in the given directory and return the location.
     *
     * @param string $folder_name
     * @param $file
     * @return string|null
     */
    public function save_file(string $folder_name, $file): ?string
    {
        if (isset($file)) {
            if (!File::isDirectory('public/' . $folder_name)) {
                File::makeDirectory(('public/' . $folder_name), 0777, true, true);
            }

            $fileName = $file->getClientOriginalName();
            $location = 'public/' . $folder_name . '/' . $fileName;
            $file->storeAs($folder_name, $fileName, ['disk' => 'my_files']);
            return $location;
        }

        return null;
    }

    public function deleteFile($url): ?bool
    {
        if (isset($url)) {
            if (File::exists($url)) {
                File::delete($url);
                return true;
            }
            return false;
        }
        return null;
    }
}
