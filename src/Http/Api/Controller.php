<?php

namespace Transmissor\Http\Api;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Muleta\Packagist\Traits\ResponseControllerTrait;
use Transmissor\TransmissorProvider;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    use ResponseControllerTrait;
    protected $packageName = TransmissorProvider::pathVendor;

    public function __construct()
    {
        $this->findVersion();
    }

}
