<?php

namespace App\DTOs;

use App\Classes\Funcionario;
use App\Enums\Cargo;
use App\Auxilios\Render;

readonly class FuncionarioStoreRequest
{
    public function __construct(
        public string $nome,
        public int|string $idade,
        public string $cargo,
        public float|string $salario
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

        if (validarVazio($salario)) {
            Render::erro("Salario não pode ser vazio", "salario", 400);
        }

        if (is_string($salario)) {
            $salario = (float) str_replace(',', '.', $salario);
        }
        
        if ($idade < 0) {
            Render::erro("Idade não pode ser menor que 0", "idade", 400);
        }

        if ($salario < 0) {
            Render::erro("Salario não pode ser negativo", "salario", 400);
        }

        if (validarVazio($cargo)) {
            Render::erro("Cargo não pode ser vazio", "cargo", 400);
        }

        if (!in_array($cargo, Cargo::getCargos())) {
            Render::erro("Cargo inválido", "cargo", 400);
        }

    }

    public function getFuncionario(): Funcionario|bool
    {
        if (!Render::hasErros()) {
            return new Funcionario($this->nome, (int)$this->idade, Cargo::from($this->cargo), (float) str_replace(',', '.',$this->salario), null);
        } else {
            return false;
        }
    }
}