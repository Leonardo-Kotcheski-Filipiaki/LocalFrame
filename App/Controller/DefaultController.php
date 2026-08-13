<?php

namespace App\Controller;

use Core\Bases\ControllerBase;

class DefaultController extends ControllerBase
{
    public function index()
    {
        self::render("index");
    }
}
