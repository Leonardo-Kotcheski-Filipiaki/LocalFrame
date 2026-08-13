<?php

namespace Core\Notifications;
/**
 * Summary of Erros
 * Fatals
 */
class Erros
{

    public function __construct(
        public string $mensagem,
        public int $codigo = 400
    ){}
}