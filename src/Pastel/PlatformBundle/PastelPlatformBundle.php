<?php

namespace Pastel\PlatformBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class PastelPlatformBundle extends Bundle
{
	public function getParent()
    {
        return 'FOSUserBundle';
    }
}
