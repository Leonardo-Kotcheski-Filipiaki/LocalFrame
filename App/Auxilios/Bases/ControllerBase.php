<?php

namespace App\Auxilios\Bases;

use App\Auxilios\Essentials\Render;

class ControllerBase
{
    protected static function render(string $arquivo, array $dados = [])
    {
        Render::render($arquivo, $dados);
    }

    protected static function erro(string $mensagem, int $status = 400)
    {
        Render::erro($mensagem, $status);
    }

    protected static function toast(string $mensagem, string $tipo = 'a')
    {
        Render::toast($mensagem, $tipo);
    }
}
