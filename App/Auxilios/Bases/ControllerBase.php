<?php

namespace App\Auxilios\Bases;

use App\Auxilios\Essentials\Render;

class ControllerBase
{
    protected static function render(string $arquivo, array $dados = [])
    {
        Render::render($arquivo, $dados);
    }

    protected static function erro(string $mensagem, string|null $tipo = null, int $status = 400)
    {
        Render::erro($mensagem, $tipo, $status);
    }

    protected static function toast(string $mensagem, string|null $tipo = null)
    {
        Render::toast($mensagem, $tipo);
    }
}
