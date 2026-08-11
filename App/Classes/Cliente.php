<?php

namespace App\Classes;

use App\Classes\Pessoa;
use App\Enums\Nivel;
use App\Classes\Compra;

class Cliente extends Pessoa
{

    private Nivel $nivel;

    private float $dinheiro;

    public function __construct(string $nome, int $idade, Nivel $nivel, float $dinheiro, ?string $id)
    {
        parent::__construct($nome, $idade, $id);
        $this->nivel = $nivel;
        $this->dinheiro = $dinheiro;
    }

    public function getNivel() : Nivel
    {
        return $this->nivel;
    }

    public function getStringNivel() : string
    {
        return $this->nivel->value ?? "Não possui";
    }

    public function getDinheiro() : float
    {
        return $this->dinheiro;
    }

    public static function buscarTodos() : array
    {
        return repo()->getListaClientes();
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