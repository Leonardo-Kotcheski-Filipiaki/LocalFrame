<?php

namespace App\Controller;

use Core\Bases\ControllerBase;
use Core\Essentials\Request;
use Core\Essentials\Session;
use App\Classes\Funcionario;
use App\DTOs\FuncionarioStoreRequest;
use App\DTOs\FuncionarioUpdateRequest;
use App\Enums\Cargo;

class FuncionariosController extends ControllerBase
{
    public function index()
    {
        self::render("funcionarios/index",
        [
            'funcionarios' => Funcionario::buscarTodosNoBanco()
        ]);
    }

    public function create()
    {
        self::render("funcionarios/create",[
            'cargos' => Cargo::getCargos(),
            'dados' => Session::oldFormData()
        ]);
    }

    public function store(Request $request)
    {
        $funcionario = (new FuncionarioStoreRequest(...$request->all()))->getFuncionario();
        if ($funcionario == false) {
            redirect("funcionarios/criar", true);
        } 
        if ($funcionario->salvarNoBanco()) {
            self::toast("Funcionario salvo com sucesso", "s");
            redirect("funcionarios", false);
        } else {
            self::toast("Erro ao salvar funcionario", "a");
            redirect("funcionarios/criar", true);
        }
    }

    public function edit(string $id)
    {
        $funcionario = Funcionario::buscarNoBanco($id);
        if (!$funcionario) {
            self::toast("Funcionario não encontrado", 'a');
            redirect("funcionarios", false);
        }
        self::render("funcionarios/editar",[
            'funcionario' => $funcionario,
            'cargos' => Cargo::getCargos(),
            'dados' => Session::oldFormData()
        ]);
    }

    public function update(string $id, Request $request)
    {
        $funcionario = (new FuncionarioUpdateRequest($id, ...$request->all()))->getFuncionario();
        if ($funcionario == false) {
            redirect("/funcionarios/editar/" . $id, true);
        } 
        if ($funcionario->salvarNoBanco()) {
            self::toast("Funcionario salvo com sucesso", 's');
            redirect("/funcionarios", false);
        }

        self::toast("Erro ao salvar funcionario", 'a');
        redirect("/funcionarios/editar/" . $id, true);
    }

    public function delete(string $id)
    {
        $funcionario = Funcionario::buscarNoBanco($id);
        if (!$funcionario) {
            self::toast("Funcionario não encontrado", 'a');
            redirect("/funcionarios", false);
        }
        if ($funcionario->removerNoBanco()) {
            self::toast("Funcionario removido com sucesso", 's');
            redirect("/funcionarios", false);
        } else {
            self::toast("Erro ao remover funcionario", 'a');
            redirect("/funcionarios", false);
        }
    }
}
