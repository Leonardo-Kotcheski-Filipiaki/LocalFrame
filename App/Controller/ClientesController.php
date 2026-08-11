<?php

namespace App\Controller;

use App\Auxilios\Render;
use App\Auxilios\Request;
use App\Auxilios\Session;
use App\Classes\Cliente;
use App\DTOs\ClienteStoreRequest;
use App\DTOs\ClienteUpdateRequest;

class ClientesController
{
    public function index()
    {
        $clientes = Cliente::buscarTodos();
        render("clientes/index", ['clientes' => $clientes]);
    }

    public function create()
    {
        render("clientes/create", [
            'niveis' => repo()->getListaNiveis(),
            'dados' => Session::oldFormData() ?? []
        ]);
    }

    public function store(Request $request)
    {
        $cliente = (new ClienteStoreRequest(...$request->all()))->getCliente();
        if ($cliente != false) {
            if ($cliente->salvar()) {
                Render::toast("Cliente salvo com sucesso", "success");
                redirect("clientes", false);
            } else {
                Render::toast("Erro ao salvar cliente", "error");
                redirect("clientes/criar", true);
            }
        } else {
            redirect("clientes/criar", true);
        }
    }

    public function edit(string $id)
    {
        $cliente = Cliente::buscar($id);
        if ($cliente == false) {
            Render::toast("Cliente não encontrado", "error");
            redirect("clientes", false);
        }
        
        render('clientes/editar', ["cliente" => $cliente, 'niveis' => repo()->getListaNiveis()]);
    }

    public function update(Request $request, string $id)
    {
        $cliente = (new ClienteUpdateRequest($id, ...$request->all())->getCliente());
        if ($cliente != false) {
            if ($cliente->salvar()) {
                Render::toast("Cliente atualizado com sucesso", "success");
                redirect("/clientes", false);
            } else {
                Render::toast("Erro ao atualizar cliente", "error");
                redirect("/clientes/editar/$id", true);
            }
        } else {
            redirect("/clientes/editar/$id", true);
        }

    }

    public function delete(string $id)
    {
        $cliente = Cliente::buscar($id);
        if ($cliente == false) {
            Render::toast("Cliente não encontrado", "error");
            redirect("clientes", false);
        }

        if ($cliente->remover()) {
            Render::toast("Cliente excluído com sucesso", "success");
            redirect('/clientes', false);
        } else {
            Render::toast("Erro ao excluir cliente", "error");
            redirect('/clientes', true);
        }

    }
}
