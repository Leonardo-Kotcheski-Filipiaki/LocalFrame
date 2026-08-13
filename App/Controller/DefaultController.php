<?php

namespace App\Controller;

use App\Auxilios\Bases\ControllerBase;

class DefaultController extends ControllerBase
{
    public function index()
    {
        self::render("index");
    }
}
