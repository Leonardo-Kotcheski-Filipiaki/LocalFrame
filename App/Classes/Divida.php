<?php

namespace App\Classes;

use Core\Bases\ClasseBase;
use App\Enums\TipoCompra;
use App\Auxilios\Essentials\Render;

/**
 * Esta classe deve ser vinculada apenas no array duvidas da classe Cliente
 */
class Divida extends ClasseBase
{

    private string $id;

    private string $idCliente;

    private string $idCompra;

    private TipoCompra $tipoCompra;

    private float $valor;

    private int $quantidadeParcela;

    private int $quantidadeParcelaPaga;

    private bool $paga;

    /**
     * Tipo de compras válidos para cadastrar dívida
     * @var TipoCompra[];
     */
    private array $tiposCompraValidos = array(TipoCompra::C,
                                              TipoCompra::CR);

    public function __construct(TipoCompra $tipoCompra, float $valor, int $quantidadeParcela, string $idCliente, string $idCompra)
    {
        if (!in_array($tipoCompra, $this->tiposCompraValidos)) {
            Render::erro("O tipo de compra selecionado não pode ser cadastrado como dívida. \n Tipo selecionado: " . $tipoCompra->value, null, 400);
        }
        $this->tipoCompra = $tipoCompra;
        $this->idCliente = $idCliente;
        $this->idCompra = $idCompra;
        $this->valor = $valor;
        $this->quantidadeParcela = $quantidadeParcela;
        $this->quantidadeParcelaPaga = 0;
        $this->paga = false;
        $this->id = uniqid();
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getIdCliente() : string
    {
        return $this->idCliente;
    }

    public function getIdCompra(): string
    {
        return $this->idCompra;
    }

    public function getTipoCompra() : TipoCompra
    {
        return $this->tipoCompra;
    }

    public function getStringTipoCompra() : string
    {
        return $this->tipoCompra->value;
    }

    public function getValor() : float
    {
        return $this->valor;
    }

    public function getQuantidadeParcela() : int
    {
        return $this->quantidadeParcela;
    }

    public function getQuantidadeParcelaPaga() : int
    {
        return $this->quantidadeParcelaPaga;
    }

    public function getPaga() : bool
    {
        return $this->paga;
    }

}