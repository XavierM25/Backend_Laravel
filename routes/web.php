<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('Cliente')->group(function () {
    Route::get('/index', function () {
        return redirect()->away('http://localhost:3000/Cliente/index.php');
    })->name('cliente.home');

    Route::get('/edit', function () {
        return redirect()->away('http://localhost:3000/Cliente/editcliente.php'); // URL completa de la vista de editar cliente
    });
});

Route::prefix('Admin')->group(function () {
    Route::get('/index', function () {
        return redirect()->away('http://localhost:3000/Admin/index.php'); // URL completa de la vista del Admin
    })->name('admin.home');
});

Route::get('/login', function () {
    return redirect()->away('http://localhost:3000/Login/login.php');
})->name('login');


