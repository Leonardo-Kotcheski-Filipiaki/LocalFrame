<?php

namespace App\DTOs;

use App\Auxilios\Essentials\Render;
use App\Classes\Cliente;
use App\Classes\Funcionario;
use App\Classes\Produto;
use App\Classes\Venda;
use App\Enums\Cargo;
use App\Enums\TipoCompra;

class VendasStoreRequest {
    
    public function __construct(
        public string $cliente,
        public string $funcionario,
        public string $produto,
        public string $tipoCompra,
        public int|string $quantidadeParcelas,
        public int|string $quantidadeVendida
    ) {
        if (empty($cliente) OR Cliente::buscar($cliente) == null) {
            Render::toast("Cliente não encontrado", 'a');
        } else {
            $cliente = Cliente::buscar($cliente);
        }

        if (empty($funcionario) OR Funcionario::buscar($funcionario) == null) {
            Render::toast("Funcionario não encontrado", 'a');
        } else {
            $funcionario = Funcionario::buscar($funcionario);
        }

        if (empty($produto) OR Produto::buscar($produto) == null) {
            Render::toast("Produto não encontrado", 'a');
        } else {
            $produto = Produto::buscar($produto);
        }

        if (empty($tipoCompra) OR !in_array($tipoCompra, TipoCompra::getStringTipos())) {
            Render::toast("Tipo de compra inválido", 'a');
        }

        if (is_string($quantidadeParcelas)) {
            $quantidadeParcelas = (int)$quantidadeParcelas;
            $quantidadeParcelas < 1 AND $tipoCompra != "Débito" ? Render::toast("Quantidade de parcelas não pode ser menor que 1", 'a') : null;
        }

        if (empty($quantidadeVendida) OR $quantidadeVendida <= 0) {
            Render::toast("Quantidade de produto inválida", 'a');
        }

        if ($funcionario->getCargo() != Cargo::VENDEDOR) {
            Render::toast("Funcionario não pode vender", 'a');
        }
        if ($tipoCompra == TipoCompra::D->value) {
            if ($cliente->getDinheiro() < ($produto->getValor() * ((int)$quantidadeVendida))) {
                Render::toast("Cliente não tem dinheiro suficiente", 'a');
            }
        } else if ($tipoCompra == TipoCompra::CR->value) {
            if ($cliente->getNivel()->value < ($produto->getValor() * ((int)$quantidadeVendida))) {
                Render::toast("Crédito do cliente insuficiente", 'a');
            }
        }

    }

    public function getVenda(): Venda|bool {
        if (!Render::hasToasts()) {
            return new Venda(
            $this->cliente,
            $this->funcionario,
            $this->produto,
            $this->tipoCompra,
            $this->tipoCompra == "Débito" ? null : (int)$this->quantidadeParcelas,
            (int)$this->quantidadeVendida
            );
        }
        return false;
    }
}
