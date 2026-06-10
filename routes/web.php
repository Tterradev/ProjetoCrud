<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::group([], function(){
    
    //Listar usuarios cadastrados.
    //Possibilitar uma pesquisa dentro os usuarios cadastrados.
    Route::get('/usuarios', [UserController::class, 'index', 'role' => 'user.index']);

    //Exibir o formulário de cadastro de usuário.
    Route::get('/usuarios/cadastrar', [UserController::class, 'create', 'role' => 'user.create']);

    //Criar formulário
    Route::post('/usuarios', [UserController::class, 'insert', 'role' => 'user.create']);

    //Exibir formulario para editar um usuário ja cadastrado
    Route::get('/usuario/{id}/editar', [UserController::class, 'edit', 'role' => 'user.update']);

    //Atualizar um user ja cadastrado
    Route::put('/usuarios', [UserController::class, 'update', 'role' => 'user.update']);

    //Deletar um usuario
    Route::delete('/usuarios', [UserController::class, 'delete', 'role' => 'user.delete']);
    
});