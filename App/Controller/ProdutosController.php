<?php

namespace App\Controller;

use App\Auxilios\Render;
use App\Auxilios\Request;
use App\Auxilios\Session;
use App\Classes\Produto;
use App\DTOs\ProdutoStoreRequest;
use App\DTOs\ProdutoUpdateRequest;
use App\Enums\TipoProduto;

class ProdutosController
{
    public function index() 
    {
        render("produtos/index",[
            "produtos" => Produto::buscarTodos()
        ]);
    }

    public function create() 
    {
        render("produtos/create", [
            "tiposProdutos" => TipoProduto::getStringTipoProdutos(),
            "dados" => Session::oldFormData() ?? []
        ]);
    }

    public function store(Request $request) 
    {
        $produto = (new ProdutoStoreRequest(...$request->all()))->getProduto();
        if ($produto != false) {
            if ($produto->salvar()) {
                Render::toast("Produto salvo com sucesso", "success");
                redirect("produtos", false);
            } else {
                Render::toast("Erro ao salvar produto", "error");
                redirect("produtos/criar", true);
            }
        } else {
            redirect("produtos/criar", true);
        }
    }

    public function edit(string $id) 
    {
        render('produtos/edit', [
            "dados" => Session::oldFormData(),
            "produto" => Produto::buscar($id),
            "tiposProdutos" => TipoProduto::getStringTipoProdutos()
        ]);
    }

    public function update(string $id, Request $request) 
    {
        $produto = Produto::buscar($id);
        if (!$produto) {
            Render::erro("Produto não encontrado", null, 404);
            redirect("/produtos", false);
        }
        $produto = (new ProdutoUpdateRequest($id, ...$request->all()))->getProduto();
        if ($produto == false) {
            Render::erro("Erro ao validar dados");
            redirect("/produtos/editar/" . $id, true);
        } 

        if ($produto->salvar()) {
            Render::toast("Produto salvo com sucesso", "success");
            redirect("/produtos", false);
        } else {
            Render::erro("Erro ao salvar produto");
            redirect("/produtos/editar/" . $id, true);
        }
        
    }

    public function delete(string $id) 
    {
        $produto = Produto::buscar($id);
        if (!$produto) {
            Render::erro("Produto não encontrado", null, 404);
            redirect("/produtos", false);
        }

        if ($produto->remover()) {
            Render::toast("Produto removido com sucesso", "success");
        } else {
            Render::erro("Erro ao remover produto");
        }
        redirect("/produtos", false);
    }
}