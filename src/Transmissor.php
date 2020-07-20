<?php

namespace Transmissor;

use Illuminate\Filesystem\Filesystem;

use Muleta\Packagist\Traits\PackageVersionTrait;

class Transmissor
{
    use PackageVersionTrait;

    protected $filesystem;
    protected $packageName = TransmissorProvider::pathVendor;

    /**
     * The current locale, cached in memory
     *
     * @var string
     */
    private $locale;

    public function __construct()
    {
        $this->filesystem = app(Filesystem::class);

        $this->findVersion();
    }
}
