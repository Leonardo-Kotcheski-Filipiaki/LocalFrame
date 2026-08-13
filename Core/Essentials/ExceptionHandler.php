<?php

namespace Core\Essentials;

use Core\Essentials\Render;
use Throwable;

class ExceptionHandler 
{
    /**
     * This class may show a 404.phtml screen with the exception message readable and more beautiful displayed
     */

    public static function handle(Throwable $exception) {
        $mensagem = "Arquivo: " . $exception->getFile() . '<br>';
        $mensagem .= "Linha: " . $exception->getLine() . '<br>';
        $mensagem .= "Mensagem: " . $exception->getMessage() . '<br>';
        $mensagem .= "Trace: " . $exception->getTraceAsString() . '<br>';
        $mensagem .= "Código de Erro: " . $exception->getCode() . '<br>';

        Render::erro($mensagem, 500);
    }
}