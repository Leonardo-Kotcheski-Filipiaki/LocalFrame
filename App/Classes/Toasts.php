<?php

namespace App\Classes;

class Toasts
{

    public function __construct(
        public string $mensagem,
        public string $tipo
    ) {}
}