<?php

namespace App\DTOs;

use App\Classes\Funcionario;
use App\Enums\Cargo;
use Core\Essentials\Render;

readonly class FuncionarioUpdateRequest
{
    public function __construct(
        public string $id,
        public string $nome,
        public int|string $idade,
        public string $cargo,
        public float|string $salario
    ) {
        if (validarVazio($id)) {
            Render::toast("ID inválido", 'a');
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

        if (validarVazio($salario)) {
            Render::toast("Salario não pode ser vazio", 'a');
        }

        if (is_string($salario)) {
            $salario = (float) str_replace(',', '.', $salario);
        }
        
        if ($idade < 0) {
            Render::toast("Idade não pode ser menor que 0", 'a');
        }

        if ($salario < 0) {
            Render::toast("Salario não pode ser negativo", 'a');
        }

        if (validarVazio($cargo)) {
            Render::toast("Cargo não pode ser vazio", 'a');
        }

        if (!in_array($cargo, Cargo::getCargos())) {
            Render::toast("Cargo inválido", 'a');
        }

    }

    public function getFuncionario(): Funcionario|bool
    {
        if (!Render::hasToasts()) {
            return new Funcionario($this->nome, (int)$this->idade, Cargo::from($this->cargo), (float) str_replace(',', '.',$this->salario), $this->id);
        }
        return false;
    }
}