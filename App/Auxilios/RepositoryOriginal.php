<?php

namespace App\Auxilios;

use App\Classes\Cliente;
use App\Classes\Funcionario;
use App\Classes\Produto;
use App\Enums\Nivel;

class Repository 
{
    /**
     * Armazena todos os clientes cadastrados
     * @var Cliente[]
     */
    private array $listaClientes = [];

    /**
     * Armazena todos os funcionarios cadastrados
     * @var Funcionario[]
     */
    private array $listaFuncionario = [];

    /**
     * Armazena todos os produtos cadastrados
     * @var Produto[]
     */
    private array $listaProduto = [];

    /**
     * Armazena todos os níveis cadastrados no ENUM
     * @var string[]
     */
    private array $listaNiveis;

    public function __construct(){
        $this->listaNiveis = Nivel::getStringNiveis();
        if (!isset($_SESSION['Clientes']) || empty($_SESSION['Clientes'])) {
            $_SESSION['Clientes'] = [];
        }
        if (!isset($_SESSION['Funcionarios']) || empty($_SESSION['Funcionarios'])) {
            $_SESSION['Funcionarios'] = [];
        }
        if (!isset($_SESSION['Produtos']) || empty($_SESSION['Produtos'])) {
            $_SESSION['Produtos'] = [];
        }

        $this->listaClientes = $_SESSION['Clientes'];
        $this->listaFuncionario = $_SESSION['Funcionarios'];
        $this->listaProduto = $_SESSION['Produtos'];
    }

    public function atualizarLista(Cliente|Funcionario|Produto $classe) : bool
    {
        $existe = false;
        switch ($classe) {
            case is_a($classe, Cliente::class):
                foreach ($this->listaClientes as $index => $cliente) {
                    if ($cliente->getId() == $classe->getId()) {
                        $existe = true;
                        $this->listaClientes[$index] = $classe;
                        break;
                    }
                }
                if (!$existe) $this->listaClientes[] = $classe;
                break;
            case is_a($classe, Funcionario::class):
                foreach ($this->listaFuncionario as $index => $funcionario) {
                    if ($funcionario->getId() == $classe->getId()) {
                        $existe = true;
                        $this->listaFuncionario[$index] = $classe;
                        break;
                    }
                }
                if (!$existe) $this->listaFuncionario[] = $classe;
                break;
            case is_a($classe, Produto::class):
                foreach ($this->listaProduto as $index => $produto) {
                    if ($produto->getId() == $classe->getId()) {
                        $existe = true;
                        $this->listaProduto[$index] = $classe;
                        break;
                    }
                }
                if (!$existe) $this->listaProduto[] = $classe;
                break;
        }
        $this->saveInSessionLists();
        return true;
    }

    public function getListaClientes() : array
    {
        return $this->listaClientes;
    }

    public function getListaFuncionario() : array
    {
        return $this->listaFuncionario;
    }

    public function getListaProduto() : array
    {
        return $this->listaProduto;
    }

    public function getListaNiveis() : array
    {
        return $this->listaNiveis;
    }

    private function saveInSessionLists()
    {
        $_SESSION['Clientes'] = $this->listaClientes;
        $_SESSION['Funcionarios'] = $this->listaFuncionario;
        $_SESSION['Produtos'] = $this->listaProduto;
    }

}