<?php

namespace App\Classes;

use App\Auxilios\Bases\ClasseBase;
use App\Enums\TipoProduto;

class Produto extends ClasseBase
{
    protected string $id;

    private string $produto;

    private string $descricao;

    private TipoProduto $tipoProduto;

    private int $quantidade;

    private float $valor;

    public function __construct(string $produto, string $descricao, TipoProduto $tipoProduto, int $quantidade, float $valor, ?string $id = null)
    {
        $this->id = $id ?? uniqid();
        $this->produto = $produto;
        $this->descricao = $descricao;
        $this->tipoProduto = $tipoProduto;
        $this->quantidade = $quantidade;
        $this->valor = $valor;
    }
    public function getId(): string
    {
        return $this->id;
    }

    public function getProduto() : string
    {
        return $this->produto;
    }

    public function getDescricao() : string
    {
        return $this->descricao;
    }

    public function getTipoProduto() : TipoProduto
    {
        return $this->tipoProduto;
    }

    public function getStringTipoProduto() : string
    {
        return $this->tipoProduto->value;
    }

    public function getQuantidade() : int
    {
        return $this->quantidade;
    }

    public function getValor() : float
    {
        return $this->valor;
    }
}