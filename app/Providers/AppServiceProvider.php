<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\ConfiguracaoSimulacao;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Definindo o Portão de Permissão para Cadastro de Clientes
        Gate::define('aluno-cadastra-cliente', function ($user) {
            // Se for Professor ou Proprietário, o acesso é sempre liberado
            if ($user->tipo == 'proprietario' || $user->tipo == 'professor') {
                return true;
            }

            // Se for Aluno, o sistema consulta a nossa tabela de configurações
            if ($user->tipo == 'aluno') {
                $permissao = ConfiguracaoSimulacao::where('chave', 'aluno_cadastra_cliente')->first();
                return $permissao ? (bool) $permissao->valor : false;
            }

            return false;
        });
    }
}