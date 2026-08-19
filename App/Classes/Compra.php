<?php

namespace App\Classes;

use Core\Bases\ClasseBase;
use App\Enums\TipoCompra;

class Compra extends ClasseBase
{
    private string $id;

    private TipoCompra $tipoCompra;

    private string $idProduto;

    private int $quantidade;

    private string $idVendedor;

    public function __construct(TipoCompra $tipoCompra, string $idProduto, int $quantidade, string $idVendedor)
    {
        $this->tipoCompra = $tipoCompra;
        $this->idProduto = $idProduto;
        $this->quantidade = $quantidade;
        $this->idVendedor = $idVendedor;
        $this->id = uniqid();
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getTipoCompra() : TipoCompra
    {
        return $this->tipoCompra;
    }

    public function getStringTipoCompra() : string
    {
        return $this->tipoCompra->value;
    }

    public function getIdProduto() : string
    {
        return $this->idProduto;
    }

    public function getQuantidade() : int
    {
        return $this->quantidade;
    }

    public function getIdVendedor() : string
    {
        return $this->idVendedor;
    }

}