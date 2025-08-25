<?php

namespace App;

use Bref\SymfonyBridge\BrefKernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;

//class Kernel extends BaseKernel
class Kernel extends BrefKernel
{
    use MicroKernelTrait;
}
