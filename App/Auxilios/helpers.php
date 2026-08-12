<?php

/**
 * @author Leonardo Filipiaki
 * @date 07/08/2026
 * @version 1.0
 * @description Funções auxiliares do sistema
 */

use App\Auxilios\Session;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists("validarVazio")) {
    function validarVazio(mixed $dados): bool
    {
        if (is_array($dados))
        {
            foreach ($dados as $dado) {
                if (empty($dado)) {
                    return true;
                }
            }
        } else {
            if (empty($dados)) {
                return true;
            }
        }

        if (is_string($dados))
        {
            return strlen(trim($dados)) <= 0;
        }

        if ($dados === null)
        {
            return true;
        }

        if (empty($dados)) 
        {
            return true;
        }
        return false;
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url, bool $salvarDadosFormulario = false)
    {
        if ($salvarDadosFormulario) {
            Session::saveOldFormData($_POST);
        } else {
            Session::remove("oldFormData");
        }
        header("Location: " . $url);
        exit;
    }
}