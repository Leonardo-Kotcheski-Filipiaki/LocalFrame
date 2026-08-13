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
            Render::erro("Cliente não encontrado");
        } else {
            $cliente = Cliente::buscar($cliente);
        }

        if (empty($funcionario) OR Funcionario::buscar($funcionario) == null) {
            Render::erro("Funcionario não encontrado");
        } else {
            $funcionario = Funcionario::buscar($funcionario);
        }

        if (empty($produto) OR Produto::buscar($produto) == null) {
            Render::erro("Produto não encontrado");
        } else {
            $produto = Produto::buscar($produto);
        }

        if (empty($tipoCompra) OR !in_array($tipoCompra, TipoCompra::getStringTipos())) {
            Render::erro("Tipo de compra inválido");
        }

        if (is_string($quantidadeParcelas)) {
            $quantidadeParcelas = (int)$quantidadeParcelas;
            $quantidadeParcelas < 1 AND $tipoCompra != "Débito" ? Render::erro("Quantidade de parcelas não pode ser menor que 1") : null;
        }

        if (empty($quantidadeVendida) OR $quantidadeVendida <= 0) {
            Render::erro("Quantidade de produto inválida");
        }

        if ($funcionario->getCargo() != Cargo::VENDEDOR) {
            Render::erro("Funcionario não pode vender");
        }

        if ($tipoCompra == TipoCompra::D->value) {
            if ($cliente->getDinheiro() < ($produto->getValor() * $quantidadeVendida)) {
                Render::erro("Cliente não tem dinheiro suficiente");
            }
        } else if ($tipoCompra == TipoCompra::CR->value) {
            if ($cliente->getNivel()->value < ($produto->getValor() * $quantidadeVendida)) {
                Render::erro("Crédito do cliente insuficiente");
            }
        }

    }

    public function getVenda(): Venda|bool {
        if (Render::hasErros()) {
            return false;
        }
        return new Venda(
            $this->cliente,
            $this->funcionario,
            $this->produto,
            $this->tipoCompra,
            $this->tipoCompra == "Débito" ? null : (int)$this->quantidadeParcelas,
            (int)$this->quantidadeVendida
        );
    }
}
