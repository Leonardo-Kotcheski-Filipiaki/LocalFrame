<?php

namespace App\Classes;

use Core\Bases\ClasseBase;
use App\Enums\TipoCompra;
use Core\Notations\Ignorar;
use Override;

class Venda extends ClasseBase
{
    protected string $id;

    private string $idCliente;

    private string $idFuncionario;

    private string $idProduto;

    /**
     * Tipo de compras válidos para cadastrar dívida
     * @var TipoCompra[];
     */
    #[Ignorar]
    private array $tiposCompraValidosParaDivida = array(TipoCompra::C,
                                                        TipoCompra::CR);

    #[Ignorar]
    private string|bool|null $retornoDivida = null;

    private string $tipoCompra;

    private int|null $quantidadeParcelas;

    private int $quantidadeProduto;

    public function __construct(string $idCliente, string $idFuncionario, string $idProduto, string $tipoCompra, int|null $quantidadeParcelas = null, int $quantidadeProduto, string|null $id = null) {
        $this->idCliente = $idCliente;
        $this->idFuncionario = $idFuncionario;
        $this->idProduto = $idProduto;
        $this->tipoCompra = $tipoCompra;
        $this->quantidadeParcelas = $quantidadeParcelas;
        $this->quantidadeProduto = $quantidadeProduto;
        $this->id = $id ?? uniqid();
    }

    public function getId(): string
    {
        return $this->id;
    }

    #[Override]
    public static function buscarTodos(): array
    {
        $vendas = parent::buscarTodos();
        foreach ($vendas as &$venda) {
            $venda->cliente = Cliente::buscar($venda->idCliente)->getNome();
            $venda->funcionario = Funcionario::buscar($venda->idFuncionario)->getNome();
            $venda->produto = Produto::buscar($venda->idProduto)->getProduto();
        }
        return $vendas;
    }

    //Getters

    public function getCliente() : string {
        return $this->cliente;
    }

    public function getFuncionario() : string {
        return $this->funcionario;
    }

    public function getProduto() : string {
        return $this->produto;
    }

    public function getTipoCompra() : string {
        return $this->tipoCompra;
    }

    public function getQuantidadeParcelas(): int|null
    {
        return $this->quantidadeParcelas ?? null;
    }

    public function getQuantidadeProduto() : int 
    {
        return $this->quantidadeProduto;
    }

    public function getValor(): float 
    {
        return Produto::buscar($this->idProduto)->getValor() * $this->getQuantidadeProduto();
    }

}