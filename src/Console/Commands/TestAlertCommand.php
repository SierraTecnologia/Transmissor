<?php

namespace Transmissor\Console\Commands;

use Illuminate\Console\Command;
use Transmissor\Facades\Transmissor;

class TestAlertCommand extends Command
{
    protected $signature = 'transmissor:test-alert {--channel=all : Canal específico para testar (slack, discord, email, log, all)}';

    protected $description = 'Envia um alerta de teste pelo Transmissor para verificar os canais configurados';

    public function handle(): int
    {
        $channel = $this->option('channel') ?: 'all';

        $this->info("🚀 Disparando alerta de teste pelo Transmissor (canal: {$channel})...");

        $results = Transmissor::alert(
            '🔔 [TESTE] Transmissor - Sistema de Monitoramento e Alertas',
            'Este é um disparo de teste para validar o funcionamento do Transmissor no ecossistema RicaSoluções.',
            [
                'ambiente' => function_exists('app') ? app()->environment() : 'cli',
                'servidor' => gethostname(),
                'horario' => date('d/m/Y H:i:s'),
                'origem' => 'artisan transmissor:test-alert',
            ],
            'info'
        );

        $rows = [];
        $hasSuccess = false;

        foreach ($results as $chan => $status) {
            $success = $status['success'];
            if ($success) {
                $hasSuccess = true;
            }
            $rows[] = [
                ucfirst($chan),
                $success ? '<info>✅ Enviado</info>' : '<comment>⚠️ Não enviado</comment>',
                $status['error'] ?: 'OK',
            ];
        }

        $this->table(['Canal', 'Status', 'Detalhe'], $rows);

        if ($hasSuccess) {
            $this->info('✨ Alerta de teste concluído com sucesso!');

            return Command::SUCCESS;
        }

        $this->warn('⚠️ Nenhum canal externo conseguiu entregar o alerta. Verifique suas credenciais no .env.');

        return Command::SUCCESS;
    }
}
