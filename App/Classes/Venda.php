<?php

namespace App\Classes;

use App\Enums\Cargo;
use App\Enums\TipoCompra;
use App\Auxilios\Render;

class Venda
{
    private string $id;

    private string $idCliente;

    private string $idFuncionario;

    private string $idProduto;

    /**
     * Tipo de compras válidos para cadastrar dívida
     * @var TipoCompra[];
     */
    private array $tiposCompraValidosParaDivida = array(TipoCompra::C,
                                                        TipoCompra::CR);

    private string|bool|null $retornoDivida = null;

    private string $tipoCompra;

    private int|null $quantidadeParcelas;

    private int $quantidadeProduto;

    public function __construct(string $idCliente, string $idFuncionario, string $idProduto, string $tipoCompra, int|null $quantidadeParcelas = null, int $quantidadeProduto) {
        $this->idCliente = $idCliente;
        $this->idFuncionario = $idFuncionario;
        $this->idProduto = $idProduto;
        $this->tipoCompra = $tipoCompra;
        $this->quantidadeParcelas = $quantidadeParcelas;
        $this->quantidadeProduto = $quantidadeProduto;
    }

    public function salvar()
    {
        $okCompra = (new Compra(TipoCompra::from($this->tipoCompra), $this->idProduto, $this->quantidadeProduto, $this->idFuncionario))->salvar();
        if ($okCompra) {
            if (in_array(TipoCompra::from($this->tipoCompra), $this->tiposCompraValidosParaDivida)) {
                $okDivida = (new Divida(TipoCompra::from($this->tipoCompra), $this->idCliente, $this->idProduto, $this->quantidadeProduto, $this->idFuncionario))->salvar();
                if ($okDivida) {
                    return repo()->atualizarLista($this);
                } else {
                    return false;
                }
            } else {
                return repo()->atualizarLista($this);
            }
        } else {
            return false;
        }
    }

    public static function buscarTodos() : array
    {
        $vendas = repo()->getListaVendas();
        foreach ($vendas as &$venda) {
            $venda['cliente'] = Cliente::buscar($venda['idCliente'])->getNome();
            $venda['funcionario'] = Funcionario::buscar($venda['idFuncionario'])->getNome();
            $venda['produto'] = Produto::buscar($venda['idProduto'])->getProduto();
            $venda['valorTotal'] = Produto::buscar($venda['idProduto'])->getValor() * $venda['quantidadeProduto'];
        }
        return $vendas;
    }

}