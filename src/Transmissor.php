<?php

namespace Transmissor;

use GuzzleHttp\Client;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Muleta\Packagist\Traits\PackageVersionTrait;
use Throwable;

class Transmissor
{
    use PackageVersionTrait;

    protected $filesystem;
    protected ?Client $httpClient = null;

    public function __construct()
    {
        $this->packageName = TransmissorProvider::pathVendor;

        if (function_exists('app') && app()->bound(Filesystem::class)) {
            $this->filesystem = app(Filesystem::class);
        }

        $this->findVersion();
    }

    public function getHttpClient(): Client
    {
        if ($this->httpClient === null) {
            $this->httpClient = new Client([
                'timeout' => 10,
                'connect_timeout' => 5,
                'http_errors' => false,
            ]);
        }

        return $this->httpClient;
    }

    public function setHttpClient(Client $client): self
    {
        $this->httpClient = $client;

        return $this;
    }

    /**
     * Dispatch an alert through all configured channels.
     *
     * @param  string  $title
     * @param  string  $message
     * @param  array  $context  Additional context metadata
     * @param  string  $level  'info', 'warning', 'danger', 'critical'
     * @return array<string, array{success: bool, error: ?string}>
     */
    public function alert(string $title, string $message, array $context = [], string $level = 'critical'): array
    {
        $results = [];

        // 1. Slack
        $results['slack'] = $this->sendSlack($title, $message, $context, $level);

        // 2. Discord
        $results['discord'] = $this->sendDiscord($title, $message, $context, $level);

        // 3. Email
        $results['email'] = $this->sendEmail($title, $message, $context, $level);

        // 4. Log
        $results['log'] = $this->sendLog($title, $message, $context, $level);

        return $results;
    }

    /**
     * Specific alert for when a service or monitor goes down.
     *
     * @param  string  $serviceName
     * @param  string  $url
     * @param  string  $reason
     * @param  int|null  $httpStatus
     * @param  array  $context
     * @return array
     */
    public function alertServiceDown(string $serviceName, string $url, string $reason, ?int $httpStatus = null, array $context = []): array
    {
        $title = "🚨 [ALERTA] Serviço FORA DO AR: {$serviceName}";
        $statusStr = $httpStatus ? " (HTTP {$httpStatus})" : '';
        $message = "O serviço '{$serviceName}' está fora do ar!\n• URL: {$url}\n• Motivo: {$reason}{$statusStr}\n• Horário: " . date('Y-m-d H:i:s');

        $fullContext = array_merge([
            'service' => $serviceName,
            'url' => $url,
            'status' => 'down',
            'http_status' => $httpStatus,
            'reason' => $reason,
            'timestamp' => date('c'),
        ], $context);

        return $this->alert($title, $message, $fullContext, 'critical');
    }

    /**
     * Specific alert for when a service or monitor recovers.
     *
     * @param  string  $serviceName
     * @param  string  $url
     * @param  string|null  $duration
     * @param  array  $context
     * @return array
     */
    public function alertServiceRecovered(string $serviceName, string $url, ?string $duration = null, array $context = []): array
    {
        $title = "✅ [RECUPERADO] Serviço ONLINE: {$serviceName}";
        $durationStr = $duration ? "\n• Duração da queda: {$duration}" : '';
        $message = "O serviço '{$serviceName}' voltou a responder com sucesso!\n• URL: {$url}{$durationStr}\n• Horário: " . date('Y-m-d H:i:s');

        $fullContext = array_merge([
            'service' => $serviceName,
            'url' => $url,
            'status' => 'recovered',
            'duration' => $duration,
            'timestamp' => date('c'),
        ], $context);

        return $this->alert($title, $message, $fullContext, 'info');
    }

    /**
     * Send alert to Slack incoming webhook.
     *
     * @param  string  $title
     * @param  string  $message
     * @param  array  $context
     * @param  string  $level
     * @return array{success: bool, error: ?string}
     */
    public function sendSlack(string $title, string $message, array $context = [], string $level = 'critical'): array
    {
        $webhookUrl = $this->getSlackWebhookUrl();
        if (empty($webhookUrl)) {
            return ['success' => false, 'error' => 'Slack webhook URL not configured'];
        }

        $color = match ($level) {
            'critical', 'danger' => '#dc3545',
            'warning' => '#ffc107',
            'info', 'success' => '#28a745',
            default => '#17a2b8',
        };

        $fields = [];
        foreach ($context as $key => $val) {
            if (is_scalar($val) && ! empty($val)) {
                $fields[] = [
                    'title' => ucfirst((string) $key),
                    'value' => (string) $val,
                    'short' => strlen((string) $val) < 30,
                ];
            }
        }

        $payload = [
            'username' => 'Transmissor Alertas',
            'icon_emoji' => match ($level) {
                'critical', 'danger' => ':rotating_light:',
                'warning' => ':warning:',
                'info', 'success' => ':white_check_mark:',
                default => ':bell:',
            },
            'text' => "*{$title}*\n{$message}",
            'attachments' => [
                [
                    'color' => $color,
                    'title' => $title,
                    'text' => $message,
                    'fields' => $fields,
                    'ts' => time(),
                ],
            ],
        ];

        try {
            $response = $this->getHttpClient()->post($webhookUrl, [
                'json' => $payload,
                'headers' => ['Content-Type' => 'application/json'],
            ]);

            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();

            if ($statusCode >= 200 && $statusCode < 300) {
                return ['success' => true, 'error' => null];
            }

            return ['success' => false, 'error' => "HTTP {$statusCode}: {$body}"];
        } catch (Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send alert to Discord webhook.
     *
     * @param  string  $title
     * @param  string  $message
     * @param  array  $context
     * @param  string  $level
     * @return array{success: bool, error: ?string}
     */
    public function sendDiscord(string $title, string $message, array $context = [], string $level = 'critical'): array
    {
        $webhookUrl = $this->getDiscordWebhookUrl();
        if (empty($webhookUrl)) {
            return ['success' => false, 'error' => 'Discord webhook URL not configured'];
        }

        $color = match ($level) {
            'critical', 'danger' => 14431557, // #dc3545
            'warning' => 16761095, // #ffc107
            'info', 'success' => 2664261, // #28a745
            default => 1548984, // #17a2b8
        };

        $fields = [];
        foreach ($context as $key => $val) {
            if (is_scalar($val) && ! empty($val)) {
                $fields[] = [
                    'name' => ucfirst((string) $key),
                    'value' => (string) $val,
                    'inline' => strlen((string) $val) < 30,
                ];
            }
        }

        $payload = [
            'username' => 'Transmissor Alertas',
            'embeds' => [
                [
                    'title' => $title,
                    'description' => $message,
                    'color' => $color,
                    'fields' => $fields,
                    'timestamp' => date('c'),
                ],
            ],
        ];

        try {
            $response = $this->getHttpClient()->post($webhookUrl, [
                'json' => $payload,
                'headers' => ['Content-Type' => 'application/json'],
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode >= 200 && $statusCode < 300) {
                return ['success' => true, 'error' => null];
            }

            return ['success' => false, 'error' => "HTTP {$statusCode}"];
        } catch (Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send alert via Email.
     *
     * @param  string  $title
     * @param  string  $message
     * @param  array  $context
     * @param  string  $level
     * @return array{success: bool, error: ?string}
     */
    public function sendEmail(string $title, string $message, array $context = [], string $level = 'critical'): array
    {
        $email = $this->getAlertsEmail();
        if (empty($email)) {
            return ['success' => false, 'error' => 'Alerts email not configured'];
        }

        if (! class_exists(Mail::class)) {
            return ['success' => false, 'error' => 'Mail facade not available'];
        }

        try {
            Mail::raw($message, function ($mail) use ($email, $title) {
                $mail->to($email)->subject('[Transmissor] ' . $title);
            });

            return ['success' => true, 'error' => null];
        } catch (Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send alert to Log.
     *
     * @param  string  $title
     * @param  string  $message
     * @param  array  $context
     * @param  string  $level
     * @return array{success: bool, error: ?string}
     */
    public function sendLog(string $title, string $message, array $context = [], string $level = 'critical'): array
    {
        if (! class_exists(Log::class)) {
            return ['success' => false, 'error' => 'Log facade not available'];
        }

        $logLevel = match ($level) {
            'critical', 'danger' => 'critical',
            'warning' => 'warning',
            'info' => 'info',
            default => 'notice',
        };

        try {
            Log::channel('sitec-transmissor')->$logLevel("[Transmissor] {$title} - {$message}", $context);

            return ['success' => true, 'error' => null];
        } catch (Throwable) {
            try {
                Log::$logLevel("[Transmissor] {$title} - {$message}", $context);

                return ['success' => true, 'error' => null];
            } catch (Throwable $e2) {
                return ['success' => false, 'error' => $e2->getMessage()];
            }
        }
    }

    protected function getSlackWebhookUrl(): ?string
    {
        if (function_exists('config')) {
            $url = config('sitec.transmissor.slack.webhook_url')
                ?: config('transmissor.slack.webhook_url')
                ?: config('logging.channels.slack.url');
            if (! empty($url)) {
                return $url;
            }
        }

        return getenv('TRANSMISSOR_SLACK_WEBHOOK_URL') ?: getenv('LOG_SLACK_WEBHOOK_URL') ?: null;
    }

    protected function getDiscordWebhookUrl(): ?string
    {
        if (function_exists('config')) {
            $url = config('sitec.transmissor.discord.webhook_url') ?: config('transmissor.discord.webhook_url');
            if (! empty($url)) {
                return $url;
            }
        }

        return getenv('TRANSMISSOR_DISCORD_WEBHOOK_URL') ?: null;
    }

    protected function getAlertsEmail(): ?string
    {
        if (function_exists('config')) {
            $email = config('sitec.transmissor.email.to')
                ?: config('transmissor.alerts_email')
                ?: config('central.alerts_email');
            if (! empty($email)) {
                return $email;
            }
        }

        return getenv('TRANSMISSOR_ALERTS_EMAIL') ?: getenv('CENTRAL_ALERTS_EMAIL') ?: null;
    }
}
