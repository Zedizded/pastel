<?php

namespace Pastel\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PlatformController extends Controller
{
    public function indexAction()
    {
        return $this->render('PastelPlatformBundle:Default:index.html.twig');
    }
}
