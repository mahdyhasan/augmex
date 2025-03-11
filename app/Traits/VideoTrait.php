<?php
namespace App\Traits;

use Illuminate\Support\Facades\File;

trait VideoTrait
{
    /**
     * Save the uploaded image in the given directory and return the location.
     *
     * @param string $folder_name
     * @param $video
     * @return string|null
     */
    public function save_video(string $folder_name, $video): ?string
    {
        if (isset($video)) {
            if (!File::isDirectory('public/' . $folder_name)) {
                File::makeDirectory(('public/' . $folder_name), 0777, true, true);
            }

            $video_extension = $video->getClientOriginalExtension();
            $fileName = uniqid('', false) . '.' . $video_extension;
            $location = 'public/' . $folder_name . '/' . $fileName;
            $video->storeAs($folder_name, $fileName, ['disk' => 'my_files']);
            return $location;
        }

        return null;
    }

    public function deleteVideo($url): ?bool
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
