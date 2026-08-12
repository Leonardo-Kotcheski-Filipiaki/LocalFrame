<?php

namespace App\Controller;

use App\Auxilios\ControllerBase;
use App\Auxilios\Request;
use App\Auxilios\Session;
use App\Classes\Cliente;
use App\Classes\Funcionario;
use App\Classes\Produto;
use App\Classes\Venda;
use App\DTOs\VendasStoreRequest;
use App\Enums\TipoCompra;

class VendasController extends ControllerBase
{

    public function index()
    {
        self::render('vendas/index', [
            'vendas' => Venda::buscarTodos()
        ]);
    }

    public function create()
    {
        self::render('vendas/create', [
            'clientes' => Cliente::buscarTodos(),
            'funcionarios' => Funcionario::buscarTodos(),
            'produtos' => Produto::buscarTodos(),
            "tiposCompra" => TipoCompra::getStringTipos(),
            "dados" => Session::oldFormData() ?? []
        ]);
    }

    public function store(Request $request) 
    {
        $venda = (new VendasStoreRequest(...$request->all()))->getVenda();
        if ($venda != false) {
            if ($venda->salvar()) {
                self::toast("Venda salva com sucesso", "success");
                redirect("vendas", false);
            } else {
                self::toast("Erro ao salvar venda", "error");
                redirect("vendas/criar", true);
            }
        } else {
            redirect("vendas/criar", true);
        }
    }

    public function edit(string $id) 
    {
        self::render('vendas/edit');
    }

    public function update(string $id, Request $request) 
    {

    }

    public function delete(string $id) 
    {
        self::render('vendas/delete');
    }

}