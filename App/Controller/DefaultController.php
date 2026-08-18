<?php

namespace App\Controller;

use Core\Bases\ControllerBase;
use Core\Essentials\Database;

class DefaultController extends ControllerBase
{
    public function index()
    {
        self::render("index");
    }
}
