<?php

namespace App\DTOs;

use App\Classes\Cliente;
use App\Enums\Nivel;
use Core\Essentials\Render;

readonly class ClienteUpdateRequest
{
    public function __construct(
        public string $id,
        public string $nome,
        public int|string $idade,
        public string $nivel,
        public float|string $dinheiro
    ) {
        if (validarVazio($id)) {
            Render::toast("Id não pode ser vazio", 'a');
        }

        if (validarVazio($nome)) {
            Render::toast("Nome inválido", 'a');
        }

        if (validarVazio($idade)) {
            Render::toast("Idade não pode ser vazia", 'a');
        }

        if (is_string($idade)) {
            $idade = (int) $idade;
        }
        
        if (validarVazio($dinheiro)) {
            Render::toast("Dinheiro não pode ser vazio", 'a');
        }

        if (is_string($dinheiro)) {
            $dinheiro = (float) str_replace(',', '.', $dinheiro);
        }

        if ($idade < 0) {
            Render::toast("Idade não pode ser menor que 0", 'a');
        }

        if (validarVazio($nivel)) {
            Render::toast("Nível não pode ser vazio", 'a');
        }
        
        if (!in_array($nivel, Nivel::getStringNiveis())) {
            Render::toast("Nível inválido", 'a');
        }
    }

    public function getCliente(): Cliente|bool
    {
        if (!Render::hasToasts()) {
            return new Cliente($this->nome, (int) $this->idade, Nivel::from($this->nivel), (float) str_replace(',', '.', $this->dinheiro), $this->id);
        }
        return false;
    }
}