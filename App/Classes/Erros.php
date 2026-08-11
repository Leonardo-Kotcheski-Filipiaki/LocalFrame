<?php

namespace App\Classes;

class Erros {

    public function __construct(
        public string $mensagem,
        public string|null $elementoHTML = null,
        public int $codigo = 400
    ){}
}