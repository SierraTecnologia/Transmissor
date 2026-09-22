<?php

namespace Transmissor\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Transmissor\Facades\Transmissor as TransmissorFacade;
use Transmissor\Test\TestCase;
use Transmissor\Transmissor;

class TransmissorAlertTest extends TestCase
{
    public function testAlertReturnsChannelResults(): void
    {
        $transmissor = new Transmissor();
        $results = $transmissor->alert('Test Title', 'Test Message', ['foo' => 'bar']);

        $this->assertIsArray($results);
        $this->assertArrayHasKey('slack', $results);
        $this->assertArrayHasKey('discord', $results);
        $this->assertArrayHasKey('email', $results);
        $this->assertArrayHasKey('log', $results);
    }

    public function testAlertServiceDown(): void
    {
        $transmissor = new Transmissor();
        $results = $transmissor->alertServiceDown('Central API', 'https://central.ricasolucoes.com.br', 'Timeout 30s', 504);

        $this->assertIsArray($results);
        $this->assertArrayHasKey('log', $results);
        $this->assertTrue($results['log']['success']);
    }

    public function testAlertServiceRecovered(): void
    {
        $transmissor = new Transmissor();
        $results = $transmissor->alertServiceRecovered('Central API', 'https://central.ricasolucoes.com.br', '5m 12s');

        $this->assertIsArray($results);
        $this->assertArrayHasKey('log', $results);
        $this->assertTrue($results['log']['success']);
    }

    public function testSendSlackWithMockClient(): void
    {
        $mock = new MockHandler([
            new Response(200, [], 'ok'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        config()->set('sitec.transmissor.slack.webhook_url', 'https://hooks.slack.com/services/mock');

        $transmissor = new Transmissor();
        $transmissor->setHttpClient($client);

        $result = $transmissor->sendSlack('Serviço Caiu', 'Erro 500 detectado', ['host' => 'srv-01'], 'critical');

        $this->assertTrue($result['success']);
        $this->assertNull($result['error']);
    }

    public function testSendDiscordWithMockClient(): void
    {
        $mock = new MockHandler([
            new Response(204, [], ''),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        config()->set('sitec.transmissor.discord.webhook_url', 'https://discord.com/api/webhooks/mock');

        $transmissor = new Transmissor();
        $transmissor->setHttpClient($client);

        $result = $transmissor->sendDiscord('Serviço OK', 'Sistema online', ['uptime' => '99.9%'], 'info');

        $this->assertTrue($result['success']);
        $this->assertNull($result['error']);
    }

    public function testFacadeResolvesCorrectly(): void
    {
        $results = TransmissorFacade::alert('Facade Test', 'Testing facade dispatch');
        $this->assertIsArray($results);
        $this->assertArrayHasKey('log', $results);
    }
}
