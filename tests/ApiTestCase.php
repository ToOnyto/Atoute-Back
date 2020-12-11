<?php

namespace App\Tests;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTestCase extends WebTestCase
{
    use FixturesTrait;

    protected const BASE_TOKEN = 'a54w4de4s51f484v5c1qc';
    protected const DIR_FIXTURES = './tests/Fixtures/';

    protected KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function jsonRequest(string $method, string $url, ?array $data = null, ?string $token = null): string
    {
        $headers = [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_Accept' => 'application/json',
            'HTTP_X_ATOUTE_AUTH_TOKEN' => $token
        ];

        $this->client->request($method, $url, [], [], $headers, $data ? json_encode($data) : null);

        return $this->client->getResponse()->getContent();
    }

    public function assertJsonEqualsToJson(string $response, string $status, string $message, array $data = []): void
    {
        $json = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];

        $this->assertJsonStringEqualsJsonString($response, json_encode($json));
    }

    public function assertJsonEqualsToJsonJwt(string $response, int $code, string $message): void
    {
        $json = [
            'code' => $code,
            'message' => $message
        ];

        $this->assertJsonStringEqualsJsonString($response, json_encode($json));
    }

    protected function assertHasValidationErrors(object $entity, int $number = 0): void
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($entity);
        $this->assertCount($number, $errors);
        self::tearDown(); // To handle multiple asserts in a row
    }

    protected function createUser(string $email, string $password, bool $isVerified = false)
    {
        return $this->jsonRequest('POST', '/auth/register', [
            'email' => $email,
            'password' => 'password',
            'role' => 'particular',
            'companyName' => 'The company',
            'is_verified' => $isVerified
        ]);
    }
}