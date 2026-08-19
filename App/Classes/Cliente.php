<?php

namespace App\Classes;

use App\Enums\Nivel;
use Core\Notations\Depends;
use Core\Notations\OnDelete;
use Core\Notations\Table;

#[Table(table: "cliente"), Depends(table: "pessoa", idColumn: "id", localIdColumn: "idPessoa")]
#[OnDelete(true)]
class Cliente extends Pessoa
{
    private Nivel $nivel;
    private float $dinheiro;

    public function __construct(string $nome, int $idade, Nivel $nivel, float $dinheiro, ?string $id = null)
    {
        parent::__construct($nome, $idade, $id);
        $this->nivel = $nivel;
        $this->dinheiro = $dinheiro;
    }

    public function getNivel(): Nivel
    {
        return $this->nivel;
    }

    public function getStringNivel(): string
    {
        return $this->nivel->value ?? "Não possui";
    }

    public function getDinheiro(): float
    {
        return $this->dinheiro;
    }
}