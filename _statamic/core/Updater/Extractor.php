<?php

namespace Statamic\Updater;

use Exception;
use Statamic\API\Zip;
use Statamic\API\Path;

class Extractor
{
    public function extract($version)
    {
        $zip = Path::makeRelative(temp_path('updates/statamic-'.$version.'.zip'));
        $target = temp_path('update-unzipped');

        try {
            Zip::extract($zip, $target);
        } catch (Exception $e) {
            throw new Exception("Couldn't extract contents of the the zip.", 0, $e);
        }
    }
}
