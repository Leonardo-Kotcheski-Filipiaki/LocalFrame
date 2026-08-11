<?php

namespace App\DTOs;

use App\Auxilios\Render;
use App\Classes\Produto;
use App\Enums\TipoProduto;

class ProdutoUpdateRequest
{
    public function __construct(
        public string $id,
        public string $produto,
        public string $descricao,
        public string $tipoProduto,
        public int|string $quantidade,
        public float|string $valor
    ) {

        if (empty($id) || !Produto::buscar($id)){
            Render::erro("Id inválido");
        }

        if (is_string($quantidade)) {
           $quantidade = (int) $quantidade;
        }

        if (is_string($valor)) {
            $valor = (float) str_replace(",", ".", $valor);
        }

        if (empty($produto) || strlen($produto) < 3 || strlen($produto) > 100) {
            Render::erro("Produto inválido");
        }

        if (empty($tipoProduto) || !in_array($tipoProduto, TipoProduto::getStringTipoProdutos())) {
            Render::erro("Tipo de produto inválido");
        }

        if ($quantidade <= 0 || $quantidade > 10000) {
            Render::erro("Quantidade inválida");
        }

        if ($valor <= 0 || $valor > 1000000) {
            Render::erro("Valor inválido");
        }
    }

    public function getProduto(): Produto|false
    {
        if (Render::hasErros()) {
            return false;
        }

        return new Produto(
            $this->produto,
            $this->descricao,
            TipoProduto::from($this->tipoProduto),
            (int) $this->quantidade,
            (float) str_replace(",", ".", $this->valor),
            $this->id
        );
    }

}