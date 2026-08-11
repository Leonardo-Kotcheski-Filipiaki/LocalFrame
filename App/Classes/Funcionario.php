<?php

namespace App\Classes;

use App\Classes\Pessoa;
use App\Enums\Cargo;

class Funcionario extends Pessoa
{

    private Cargo $cargo;

    private float $salario;

    public Venda|null $venda;

    public function __construct(string $nome, int $idade, Cargo $cargo, float $salario, ?string $id = null)
    {
        parent::__construct($nome, $idade, $id);
        $this->cargo = $cargo;
        $this->salario = $salario;
        $this->venda = new Venda();
    }

    public function getCargo() : Cargo
    {
        return $this->cargo;
    }

    public function getStringCargo() : string
    {
        return $this->cargo->value;
    }

    public function getSalario() : float
    {
        return $this->salario;
    }

    public function __toString() : string
    {
        return parent::__toString() . " | Cargo: " . $this->getStringCargo() . " | Salário: R$" . number_format($this->getSalario(), 2, ',', '.');
    }

    public static function buscarTodos() : array
    {
        return repo()->getListaFuncionario() ?? [];
    }

    public function salvar() : bool {
        return repo()->atualizarLista($this);
    }

    public static function buscar(string $id) : self | null {
        return repo()->buscarPorId($id, self::class);
    }

    public function remover(string|null $id = null) : bool {
        if (!$id) $id = $this->id;
        return repo()->removerPorId($id, self::class);
    }

}