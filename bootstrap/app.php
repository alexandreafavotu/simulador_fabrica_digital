<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // 1. Seus apelidos de rotas existentes (Mantidos)
        $middleware->alias([
            'setor' => \App\Http\Middleware\CheckSetorAluno::class,
            'professor' => \App\Http\Middleware\CheckProfessor::class,
        ]);

        // 2. O NOVO PORTEIRO (CheckUserActive)
        // O comando 'append' adiciona essa verificação em TODAS as rotas web automaticamente.
        // Assim que o aluno bloqueado clicar em qualquer link, ele cai.
        $middleware->web(append: [
            \App\Http\Middleware\CheckUserActive::class,
            \App\Http\Middleware\CheckRelogio::class, // <--- ADICIONE ESTA LINHA
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();