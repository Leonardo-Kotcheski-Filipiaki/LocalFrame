<?php

namespace App\DTOs;

use App\Auxilios\Render;
use App\Classes\Cliente;
use App\Classes\Funcionario;
use App\Classes\Produto;
use App\Classes\Venda;
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
        }

        if (empty($funcionario) OR Funcionario::buscar($funcionario) == null) {
            Render::erro("Funcionario não encontrado");
        }

        if (empty($produto) OR Produto::buscar($produto) == null) {
            Render::erro("Produto não encontrado");
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
