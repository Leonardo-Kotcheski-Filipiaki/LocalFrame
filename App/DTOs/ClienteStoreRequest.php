<?php

namespace App\DTOs;

use App\Classes\Cliente;
use App\Enums\Nivel;
use App\Auxilios\Essentials\Render;

readonly class ClienteStoreRequest
{
    public function __construct(
        public string $nome,
        public int|string $idade,
        public string $nivel,
        public float|string $dinheiro
    ) {
        if (validarVazio($nome)) {
            Render::erro("Nome inválido", "nome", 400);
        }

        if (validarVazio($idade)) {
            Render::erro("Idade não pode ser vazia", "idade", 400);
        }

        if (is_string($idade)) {
            $idade = (int) $idade;
        }

        if (validarVazio($dinheiro)) {
            Render::erro("Dinheiro não pode ser vazio", "dinheiro", 400);
        }

        if (is_string($dinheiro)) {
            $dinheiro = (float) str_replace(',', '.', $dinheiro);
        }
        
        if ($idade < 0) {
            Render::erro("Idade não pode ser menor que 0", "idade", 400);
        }

        if ($dinheiro < 0) {
            Render::erro("Dinheiro não pode ser negativo", "dinheiro", 400);
        }

        if (validarVazio($nivel)) {
            Render::erro("Nível não pode ser vazio", "nivel", 400);
        }

        if (!in_array($nivel, Nivel::getStringNiveis())) {
            Render::erro("Nível inválido", "nivel", 400);
        }

    }

    public function getCliente(): Cliente|bool
    {
        if (!Render::hasErros()) {
            return new Cliente($this->nome, (int)$this->idade, Nivel::from($this->nivel), (float) str_replace(',', '.',$this->dinheiro), null);
        } else {
            return false;
        }
    }
}