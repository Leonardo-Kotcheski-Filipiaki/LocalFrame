<?php

namespace App\Classes;

use App\Classes\Pessoa;
use App\Enums\Cargo;
use Core\Notations\Depends;
use Core\Notations\Table;

#[Table(table: "funcionario"), Depends(table: "pessoa", idColumn: "id", localIdColumn: "idPessoa")]
class Funcionario extends Pessoa
{

    private Cargo $cargo;

    private float $salario;

    public function __construct(string $nome, int $idade, Cargo $cargo, float $salario, ?string $id = null)
    {
        parent::__construct($nome, $idade, $id);
        $this->cargo = $cargo;
        $this->salario = $salario;
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

}