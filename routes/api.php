<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\Activities\CategoriaController;
use App\Http\Controllers\Admin\Activities\SubcategoriaController;
use App\Http\Controllers\Admin\Activities\HorarioController;
use App\Http\Controllers\Admin\HabilidadController;
use App\Http\Controllers\Admin\RankingController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\InscripcionController;
use App\Http\Controllers\Admin\ReporteController;

//Rutas Login
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('recover-password', [AuthController::class, 'recoverPassword']);
Route::post('validate-otp', [AuthController::class, 'validateOtp']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

// Rutas para Administración
Route::middleware(['auth:sanctum'])->group(function () {
    // Rutas módulo Usuarios
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::patch('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    //Rutas módulo Videos
    Route::get('/videos', [VideoController::class, 'index']);
    Route::get('/videos/{id}', [VideoController::class, 'show']);
    Route::post('/videos', [VideoController::class, 'store']);
    Route::patch('/videos/{id}', [VideoController::class, 'update']);
    Route::delete('/videos/{id}', [VideoController::class, 'destroy']);
    Route::post('/videos/{id}/like', [VideoController::class, 'like']);
    Route::delete('/videos/{id}/like', [VideoController::class, 'unlike']);

    // Rutas módulo Actividades
    // Categorías
    Route::get('/categories', [CategoriaController::class, 'index']);
    Route::get('/categories/{id}', [CategoriaController::class, 'show']);
    Route::post('/categories', [CategoriaController::class, 'store']);
    Route::patch('/categories/{id}', [CategoriaController::class, 'update']);
    Route::delete('/categories/{id}', [CategoriaController::class, 'destroy']);
    Route::get('/categories/{categoria_id}/subcategories', [CategoriaController::class, 'getSubcategorias']);

    // Subcategorías
    Route::get('/subcategories', [SubcategoriaController::class, 'index']);
    Route::get('/subcategories/{id}', [SubcategoriaController::class, 'show']);
    Route::post('/subcategories', [SubcategoriaController::class, 'store']);
    Route::patch('/subcategories/{id}', [SubcategoriaController::class, 'update']);
    Route::delete('/subcategories/{id}', [SubcategoriaController::class, 'destroy']);
    Route::get('subcategories/{subcategoria_id}/vacancies', [SubcategoriaController::class, 'getVacantes']);

    // Horarios
    Route::get('/schedules', [HorarioController::class, 'index']);
    Route::get('/schedules/{id}', [HorarioController::class, 'show']);
    Route::post('/schedules', [HorarioController::class, 'store']);
    Route::patch('/schedules/{id}', [HorarioController::class, 'update']);
    Route::delete('/schedules/{id}', [HorarioController::class, 'destroy']);
    Route::get('/subcategories/{subcategoria_id}/schedules', [HorarioController::class, 'getHorariosBySubcategoriaId']);
    Route::post('/subcategories/{subcategoria_id}/schedules', [HorarioController::class, 'store']);

    // Rutas módulo Habilidades
    Route::get('/habilities', [HabilidadController::class, 'index']);
    Route::get('/habilities/{id}', [HabilidadController::class, 'show']);
    Route::post('/habilities', [HabilidadController::class, 'store']);
    Route::patch('/habilities/{id}', [HabilidadController::class, 'update']);
    Route::delete('/habilities/{id}', [HabilidadController::class, 'destroy']);
    Route::get('/users/{id}/habilities', [HabilidadController::class, 'getUserHabilities']);
    Route::get('/users/{id}/habilities-with-position', [HabilidadController::class, 'getUserHabilitiesWithPosition']);

    // Rutas módulo Ranking
    Route::get('/ranking', [RankingController::class, 'index']);
    Route::get('/ranking/{id}', [RankingController::class, 'show']);
    Route::delete('/ranking/{id}', [RankingController::class, 'destroy']);

    //Rutas módulo Inscripciones
    Route::get('/inscripciones', [InscripcionController::class, 'index']);
    Route::post('/inscripciones', [InscripcionController::class, 'store']);
    Route::get('/inscripciones/{id}', [InscripcionController::class, 'show']);
    Route::patch('/inscripciones/{id}', [InscripcionController::class, 'update']);
    Route::delete('/inscripciones/{id}', [InscripcionController::class, 'destroy']);

    //Rutas módulo Reportes
    Route::get('/reportes', [ReporteController::class, 'index']);
    Route::post('/reportes', [ReporteController::class, 'store']);
    Route::get('/reportes/{id}', [ReporteController::class, 'show']);
    Route::get('/reportes/download/{id}', [ReporteController::class, 'download']);
    Route::delete('/reportes/{id}', [ReporteController::class, 'destroy']);
    Route::post('/reportes/preview', [ReporteController::class, 'preview']);
});

//Cliente rutas
Route::group(["middleware" => ["auth:sanctum"]
], function () {
    Route::get('profile', [AuthController::class, 'profile']);
    Route::patch('/profile/update/{id}', [AuthController::class, 'update']);
    Route::get('logout', [AuthController::class, 'logout']);
});