<?php

namespace App\Controller;

use App\Auxilios\ControllerBase;
use App\Auxilios\Request;
use App\Auxilios\Session;
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
        if ($produto != false) {
            if ($produto->salvar()) {
                self::toast("Produto salvo com sucesso", "success");
                redirect("produtos", false);
            } else {
                self::toast("Erro ao salvar produto", "error");
                redirect("produtos/criar", true);
            }
        } else {
            redirect("produtos/criar", true);
        }
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
        $produto = Produto::buscar($id);
        if (!$produto) {
            self::erro("Produto não encontrado", null, 404);
            redirect("/produtos", false);
        }
        $produto = (new ProdutoUpdateRequest($id, ...$request->all()))->getProduto();
        if ($produto == false) {
            self::erro("Erro ao validar dados");
            redirect("/produtos/editar/" . $id, true);
        } 

        if ($produto->salvar()) {
            self::toast("Produto salvo com sucesso", "success");
            redirect("/produtos", false);
        } else {
            self::erro("Erro ao salvar produto");
            redirect("/produtos/editar/" . $id, true);
        }
        
    }

    public function delete(string $id) 
    {
        $produto = Produto::buscar($id);
        if (!$produto) {
            self::erro("Produto não encontrado", null, 404);
            redirect("/produtos", false);
        }

        if ($produto->remover()) {
            self::toast("Produto removido com sucesso", "success");
        } else {
            self::erro("Erro ao remover produto");
        }
        redirect("/produtos", false);
    }
}