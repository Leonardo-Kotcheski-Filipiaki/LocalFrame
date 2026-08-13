<?php

use App\Auxilios\Essentials\Router;
Router::get('/', "DefaultController@index");

//Clientes
Router::get('/clientes', 'ClientesController@index');
Router::get('/clientes/criar', 'ClientesController@create');
Router::post('/clientes', 'ClientesController@store');
Router::get('/clientes/editar/{id}', 'ClientesController@edit');
Router::post('/clientes/update/{id}', 'ClientesController@update');
Router::get('/clientes/excluir/{id}', 'ClientesController@delete');

//Funcionarios
Router::get('/funcionarios', 'FuncionariosController@index');
Router::get('/funcionarios/criar', 'FuncionariosController@create');
Router::post('/funcionarios', 'FuncionariosController@store');
Router::get('/funcionarios/editar/{id}', 'FuncionariosController@edit');
Router::post('/funcionarios/update/{id}', 'FuncionariosController@update');
Router::get('/funcionarios/excluir/{id}', 'FuncionariosController@delete');

//Produtos
Router::get('/produtos', "ProdutosController@index");
Router::get('/produtos/criar', "ProdutosController@create");
Router::post('/produtos', "ProdutosController@store");
Router::get('/produtos/editar/{id}', "ProdutosController@edit");
Router::post('/produtos/update/{id}', "ProdutosController@update");
Router::get('/produtos/excluir/{id}', "ProdutosController@delete");

//Vendas
Router::get("/vendas", "VendasController@index");
Router::get("/vendas/criar", "VendasController@create");
Router::post("/vendas", "VendasController@store");
Router::get("/vendas/excluir/{id}", "VendasController@delete");

Router::dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

