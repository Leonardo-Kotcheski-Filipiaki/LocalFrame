<?php

/**
 * @author Leonardo Filipiaki
 * @date 07/08/2026
 * @version 1.0
 * @description Carrega todas as classes PHP automaticamente
 */

spl_autoload_register(function ($classe) {

    $classe = str_replace('\\', '/', $classe);
    // Liste todas as pastas onde suas classes podem estar
    $diretorios = [
        __DIR__ . '/',
    ];

    // Percorre cada diretório procurando o arquivo da classe
    foreach ($diretorios as $diretorio) {
        $arquivo = $diretorio . $classe . '.php';
        if (file_exists($arquivo)) {
            require_once $arquivo;
            return; // Interrompe a busca assim que encontra
        }
    }
});


