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
        if ($funcionario == false) {
            Render::erro("Erro ao validar dados");
            redirect("funcionarios/criar", true);
        } 
        if ($funcionario->salvar()) {
            Render::toast("Funcionario salvo com sucesso", "success");
            redirect("funcionarios", false);
        } else {
            Render::erro("Erro ao salvar funcionario");
            redirect("funcionarios/criar", true);
        }
    }

    public function edit(string $id)
    {
        $funcionario = Funcionario::buscar($id);
        if (!$funcionario) {
            Render::erro("Funcionario não encontrado", null, 404);
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
            Render::erro("Funcionario não encontrado", null, 404);
            redirect("/funcionarios", false);
        }
        $funcionario = (new FuncionarioUpdateRequest($id, ...$request->all()))->getFuncionario();
        if ($funcionario == false) {
            Render::erro("Erro ao validar dados");
            redirect("/funcionarios/editar/" . $id, true);
        } 
        if ($funcionario->salvar()) {
            Render::toast("Funcionario salvo com sucesso", "success");
            redirect("/funcionarios", false);
        } else {
            Render::erro("Erro ao salvar funcionario");
            redirect("/funcionarios/editar/" . $id, true);
        }
    }

    public function delete(string $id)
    {
        $funcionario = Funcionario::buscar($id);
        if (!$funcionario) {
            Render::erro("Funcionario não encontrado", null, 404);
            redirect("/funcionarios", false);
        }
        if ($funcionario->remover()) {
            Render::toast("Funcionario removido com sucesso", "success");
            redirect("/funcionarios", false);
        } else {
            Render::erro("Erro ao remover funcionario");
            redirect("/funcionarios", false);
        }
    }
}
