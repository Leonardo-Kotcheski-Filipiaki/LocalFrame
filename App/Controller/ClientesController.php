<?php

namespace App\Controller;

use Core\Bases\ControllerBase;
use Core\Essentials\Request;
use Core\Essentials\Session;
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
            redirect("clientes/criar", true);
        }
        if ($cliente->salvar()) {
            self::toast("Cliente salvo com sucesso", 's');
            redirect("clientes", false);
        }
        
        self::toast("Erro ao salvar cliente", 'a');
        redirect("clientes/criar", true);
        
    }

    public function edit(string $id)
    {
        $cliente = Cliente::buscar($id);
        if ($cliente == false) {
            self::toast("Cliente não encontrado", 'a');
            redirect("clientes", false);
        }
        
        self::render('clientes/editar', ["cliente" => $cliente, 'niveis' => Nivel::getStringNiveis()]);
    }

    public function update(Request $request, string $id)
    {
        $cliente = (new ClienteUpdateRequest($id, ...$request->all()))->getCliente();
        if ($cliente == false) {
            redirect("/clientes/editar/$id", true);
        }
        if ($cliente->salvar()) {
            self::toast("Cliente atualizado com sucesso", "s");
            redirect("/clientes", false);
        }
        
        self::toast("Erro ao atualizar cliente", 'a');
        redirect("/clientes/editar/$id", true);
        

    }

    public function delete(string $id)
    {
        $cliente = Cliente::buscar($id);
        if ($cliente == false) {
            self::toast("Cliente não encontrado", 'a');
            redirect("clientes", false);
        }

        if ($cliente->remover()) {
            self::toast("Cliente excluído com sucesso", "s");
            redirect('/clientes', false);
        } else {
            self::toast("Erro ao excluir cliente", 'a');
            redirect('/clientes', true);
        }

    }
}
