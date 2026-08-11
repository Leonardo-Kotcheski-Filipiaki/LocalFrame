<?php

namespace App\Controller;

use App\Auxilios\Render;
use App\Auxilios\Request;
use App\Auxilios\Session;
use App\Classes\Funcionario;
use App\DTOs\FuncionarioStoreRequest;
use App\DTOs\FuncionarioUpdateRequest;
use App\Enums\Cargo;

class FuncionariosController
{
    public function index()
    {
        render("funcionarios/index",
        [
            'funcionarios' => Funcionario::buscarTodos()
        ]);
    }

    public function create()
    {
        render("funcionarios/create",[
            'cargos' => Cargo::getCargos(),
            'dados' => Session::oldFormData()
        ]);
    }

    public function store(Request $request)
    {
        $funcionario = (new FuncionarioStoreRequest(...$request->all()))->getFuncionario();
        if ($funcionario != false) {
            if ($funcionario->salvar()) {
                Render::toast("Funcionario salvo com sucesso", "success");
                redirect("funcionarios", false);
            } else {
                Render::toast("Erro ao salvar funcionario", "error");
                redirect("funcionarios/criar", true);
            }
        } else {
            redirect("funcionarios/criar", true);
        }
    }

    public function edit(string $id)
    {
        $funcionario = Funcionario::buscar($id);
        if (!$funcionario) {
            Render::toast("Funcionario não encontrado", "error");
            redirect("funcionarios", false);
        }
        render("funcionarios/editar",[
            'funcionario' => $funcionario,
            'cargos' => Cargo::getCargos(),
            'dados' => Session::oldFormData()
        ]);
    }

    public function update(string $id, Request $request)
    {
        $funcionario = Funcionario::buscar($id);
        if (!$funcionario) {
            Render::toast("Funcionario não encontrado", "error");
            redirect("/funcionarios", false);
        }
        $funcionario = (new FuncionarioUpdateRequest($id, ...$request->all()))->getFuncionario();
        if ($funcionario != false) {
            if ($funcionario->salvar()) {
                Render::toast("Funcionario salvo com sucesso", "success");
                redirect("/funcionarios", false);
            } else {
                Render::toast("Erro ao salvar funcionario", "error");
                redirect("/funcionarios/editar/" . $id, true);
            }
        } else {
            redirect("/funcionarios/editar/" . $id, true);
        }
    }

    public function delete(string $id)
    {
        $funcionario = Funcionario::buscar($id);
        if (!$funcionario) {
            Render::toast("Funcionario não encontrado", "error");
            redirect("/funcionarios", false);
        }
        if ($funcionario->remover()) {
            Render::toast("Funcionario removido com sucesso", "success");
            redirect("/funcionarios", false);
        } else {
            Render::toast("Erro ao remover funcionario", "error");
            redirect("/funcionarios", false);
        }
    }
}
