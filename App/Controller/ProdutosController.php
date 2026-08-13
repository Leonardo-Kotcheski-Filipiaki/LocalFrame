<?php

namespace App\Controller;

use Core\Bases\ControllerBase;
use Core\Essentials\Request;
use Core\Essentials\Session;
use App\Classes\Produto;
use App\DTOs\ProdutoStoreRequest;
use App\DTOs\ProdutoUpdateRequest;
use App\Enums\TipoProduto;

class ProdutosController extends ControllerBase
{
    public function index() 
    {
        self::render("produtos/index",[
            "produtos" => Produto::buscarTodos()
        ]);
    }

    public function create() 
    {
        self::render("produtos/create", [
            "tiposProdutos" => TipoProduto::getStringTipoProdutos(),
            "dados" => Session::oldFormData() ?? []
        ]);
    }

    public function store(Request $request) 
    {
        $produto = (new ProdutoStoreRequest(...$request->all()))->getProduto();
        if ($produto == false) {
            redirect("/produtos/criar", true);
        }

        if ($produto->salvar()) {
            self::toast("Produto salvo com sucesso", 's');
            redirect("/produtos", false);
        }

        self::toast("Erro ao salvar produto", 'a');
        redirect("/produtos/criar", true);
    }

    public function edit(string $id) 
    {
        self::render('produtos/edit', [
            "dados" => Session::oldFormData(),
            "produto" => Produto::buscar($id),
            "tiposProdutos" => TipoProduto::getStringTipoProdutos()
        ]);
    }

    public function update(string $id, Request $request) 
    {
        $produto = (new ProdutoUpdateRequest($id, ...$request->all()))->getProduto();
        if ($produto == false) {
            redirect("/produtos/editar/" . $id, true);
        }

        if ($produto->salvar()) {
            self::toast("Produto salvo com sucesso", 's');
            redirect("/produtos", false);
        }

        self::toast("Erro ao salvar produto", 'a');
        redirect("/produtos/editar/" . $id, true);
        
    }

    public function delete(string $id) 
    {
        $produto = Produto::buscar($id);
        if (!$produto) {
            self::toast("Produto não encontrado", 'a');
            redirect("/produtos", false);
        }

        if ($produto->remover()) {
            self::toast("Produto removido com sucesso", 's');
        } else {
            self::erro("Erro ao remover produto");
        }
        redirect("/produtos", false);
    }
}