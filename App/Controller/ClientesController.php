<?php

namespace App\Controller;

use App\Auxilios\Render;
use App\Auxilios\Request;
use App\Auxilios\Session;
use App\Classes\Cliente;
use App\DTOs\ClienteStoreRequest;
use App\DTOs\ClienteUpdateRequest;
use App\Enums\Nivel;

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
            'niveis' => Nivel::getStringNiveis(),
            'dados' => Session::oldFormData() ?? []
        ]);
    }

    public function store(Request $request)
    {
        $cliente = (new ClienteStoreRequest(...$request->all()))->getCliente();
        if ($cliente == false) {
            Render::erro("Erro ao salvar cliente", "error");
            redirect("clientes/criar", true);
        }
        if ($cliente->salvar()) {
            Render::toast("Cliente salvo com sucesso", "success");
            redirect("clientes", false);
        }
        
        redirect("clientes/criar", true);
        
    }

    public function edit(string $id)
    {
        $cliente = Cliente::buscar($id);
        if ($cliente == false) {
            Render::erro("Cliente não encontrado", null, 404);
            redirect("clientes", false);
        }
        
        render('clientes/editar', ["cliente" => $cliente, 'niveis' => Nivel::getStringNiveis()]);
    }

    public function update(Request $request, string $id)
    {
        $cliente = (new ClienteUpdateRequest($id, ...$request->all()))->getCliente();
        if ($cliente == false) {
            Render::erro("Erro ao atualizar cliente", null, 404);
            redirect("/clientes/editar/$id", true);
        }
        if ($cliente->salvar()) {
            Render::toast("Cliente atualizado com sucesso", "success");
            redirect("/clientes", false);
        }
        
        Render::erro("Erro ao atualizar cliente");
        redirect("/clientes/editar/$id", true);
        

    }

    public function delete(string $id)
    {
        $cliente = Cliente::buscar($id);
        if ($cliente == false) {
            Render::erro("Cliente não encontrado", null, 404);
            redirect("clientes", false);
        }

        if ($cliente->remover()) {
            Render::toast("Cliente excluído com sucesso", "success");
            redirect('/clientes', false);
        } else {
            Render::erro("Erro ao excluir cliente");
            redirect('/clientes', true);
        }

    }
}
