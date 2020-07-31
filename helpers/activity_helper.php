<?php

/*
 * --------------------------------------------------------------------------
 * Helpers for Activities
 * --------------------------------------------------------------------------
*/

if (!function_exists('activity')) {
    function activity($description)
    {
        return app(Transmissor\Services\ActivityService::class)->log($description);
    }
}
