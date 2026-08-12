<?php

namespace App\Controller;

use App\Auxilios\ControllerBase;
use App\Auxilios\Request;
use App\Auxilios\Session;
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
            'funcionarios' => Funcionario::buscarTodos()
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
            self::erro("Erro ao validar dados");
            redirect("funcionarios/criar", true);
        } 
        if ($funcionario->salvar()) {
            self::toast("Funcionario salvo com sucesso", "success");
            redirect("funcionarios", false);
        } else {
            self::erro("Erro ao salvar funcionario");
            redirect("funcionarios/criar", true);
        }
    }

    public function edit(string $id)
    {
        $funcionario = Funcionario::buscar($id);
        if (!$funcionario) {
            self::erro("Funcionario não encontrado", null, 404);
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
        $funcionario = Funcionario::buscar($id);
        if (!$funcionario) {
            self::erro("Funcionario não encontrado", null, 404);
            redirect("/funcionarios", false);
        }
        $funcionario = (new FuncionarioUpdateRequest($id, ...$request->all()))->getFuncionario();
        if ($funcionario == false) {
            self::erro("Erro ao validar dados");
            redirect("/funcionarios/editar/" . $id, true);
        } 
        if ($funcionario->salvar()) {
            self::toast("Funcionario salvo com sucesso", "success");
            redirect("/funcionarios", false);
        } else {
            self::erro("Erro ao salvar funcionario");
            redirect("/funcionarios/editar/" . $id, true);
        }
    }

    public function delete(string $id)
    {
        $funcionario = Funcionario::buscar($id);
        if (!$funcionario) {
            self::erro("Funcionario não encontrado", null, 404);
            redirect("/funcionarios", false);
        }
        if ($funcionario->remover()) {
            self::toast("Funcionario removido com sucesso", "success");
            redirect("/funcionarios", false);
        } else {
            self::erro("Erro ao remover funcionario");
            redirect("/funcionarios", false);
        }
    }
}
