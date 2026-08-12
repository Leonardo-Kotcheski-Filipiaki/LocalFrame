<?php

namespace App\Controller;

use App\Auxilios\ControllerBase;
use App\Auxilios\Request;
use App\Auxilios\Session;
use App\Classes\Cliente;
use App\DTOs\ClienteStoreRequest;
use App\DTOs\ClienteUpdateRequest;
use App\Enums\Nivel;

class ClientesController extends ControllerBase
{
    public function index()
    {
        $clientes = Cliente::buscarTodos();
        self::render("clientes/index", ['clientes' => $clientes]);
    }

    public function create()
    {
        self::render("clientes/create", [
            'niveis' => Nivel::getStringNiveis(),
            'dados' => Session::oldFormData() ?? []
        ]);
    }

    public function store(Request $request)
    {
        $cliente = (new ClienteStoreRequest(...$request->all()))->getCliente();
        if ($cliente == false) {
            self::erro("Erro ao salvar cliente", "error");
            redirect("clientes/criar", true);
        }
        if ($cliente->salvar()) {
            self::toast("Cliente salvo com sucesso", "success");
            redirect("clientes", false);
        }
        
        redirect("clientes/criar", true);
        
    }

    public function edit(string $id)
    {
        $cliente = Cliente::buscar($id);
        if ($cliente == false) {
            self::erro("Cliente não encontrado", null, 404);
            redirect("clientes", false);
        }
        
        self::render('clientes/editar', ["cliente" => $cliente, 'niveis' => Nivel::getStringNiveis()]);
    }

    public function update(Request $request, string $id)
    {
        $cliente = (new ClienteUpdateRequest($id, ...$request->all()))->getCliente();
        if ($cliente == false) {
            self::erro("Erro ao atualizar cliente", null, 404);
            redirect("/clientes/editar/$id", true);
        }
        if ($cliente->salvar()) {
            self::toast("Cliente atualizado com sucesso", "success");
            redirect("/clientes", false);
        }
        
        self::erro("Erro ao atualizar cliente");
        redirect("/clientes/editar/$id", true);
        

    }

    public function delete(string $id)
    {
        $cliente = Cliente::buscar($id);
        if ($cliente == false) {
            self::erro("Cliente não encontrado", null, 404);
            redirect("clientes", false);
        }

        if ($cliente->remover()) {
            self::toast("Cliente excluído com sucesso", "success");
            redirect('/clientes', false);
        } else {
            self::erro("Erro ao excluir cliente");
            redirect('/clientes', true);
        }

    }
}
