<?php

namespace App\Classes;

use App\Auxilios\ClasseBase;

class Pessoa extends ClasseBase
{

    protected ?string $id = null;

    protected string $nome = '';  // Valor padrão

    protected int $idade = 0;     // Valor padrão

    public function __construct(string $nome, int $idade, ?string $id)
    {
        $this->id = $id ?? uniqid();
        $this->nome = $nome;
        $this->idade = $idade;
    }

    public function getId() : ?string
    {
        return $this->id ?? null;
    }

    public function getNome() : string
    {
        return $this->nome;
    }

    public function getIdade() : int
    {
        return $this->idade;
    }

    public function __toString() : string
    {
        return "ID: " . ($this->getId() ?? "N/A") . " | Nome: " . $this->getNome() . " | Idade: ". $this->getIdade();
    }

}