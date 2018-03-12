<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SecurityController
 *
 * @package App\Controller
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class SecurityController extends BaseController
{
    /**
     * @Route("/api/login", name="login_check")
     */
    public function login()
    {
    }
}