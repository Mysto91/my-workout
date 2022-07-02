<?php

namespace App\Tests;

use ApiTestCase\JsonApiTestCase;
use App\DataFixtures\CardFixtures;
use App\DataFixtures\MeasureFixtures;
use App\DataFixtures\RoleFixtures;
use App\DataFixtures\UserFixtures;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Component\HttpFoundation\Response;

class TestCase extends JsonApiTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    protected string $token;

    protected int $userAdminId = 1;

    protected int $userVisitorId = 2;

    protected static bool $initialized = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (!self::$initialized) {
            $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
            $this->initDatabase();
            putenv("JWT={$this->getToken()}");
            self::$initialized = true;
        }

        $this->token = getenv('JWT');
    }

    /**
     * @param string $jwt
     *
     * @return array<string>
     */
    protected function getHeaders(string $jwt = ''): array
    {
        return [
            'HTTP_Authorization' => "Bearer {$jwt}",
            'CONTENT_TYPE' => 'application/json'
        ];
    }

    /**
     * @return void
     */
    protected function initDatabase(): void
    {
        $this->databaseTool->loadFixtures([
            RoleFixtures::class,
            UserFixtures::class,
            CardFixtures::class,
            MeasureFixtures::class
        ]);
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return array<string>
     */
    protected function authenticate(string $username, string $password): array
    {
        return [
            'username' => $username,
            'password' => $password
        ];
    }

    /**
     * @param array<string> $body
     *
     * @return string
     */
    protected function getToken(array $body = []): string
    {
        if (empty($body)) {
            $body = $this->authenticate('admin', 'admin');
        }

        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($body)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);

        return $response['token'] ?? '';
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }

    /**
     * @param string $url
     * @param array<string,string|int> $headers
     * @param array<string,string|int> $params
     *
     * @return Response
     */
    protected function httpGet(string $url, array $headers = [], array $params = []): Response
    {
        $this->client->request('GET', $url, $params, [], $headers);
        return $this->client->getResponse();
    }

    /**
     * @param string $url
     * @param array<string,string|int> $headers
     * @param array<string,string|int> $params
     * @param array<string,string|array<string,mixed>> $body
     *
     * @return Response
     */
    protected function httpPost(string $url, array $headers = [], array $params = [], array $body = []): Response
    {
        $this->client->request('POST', $url, $params, [], $headers, json_encode($body));
        return $this->client->getResponse();
    }

    /**
     * @param string $url
     * @param array<string,string|int> $headers
     * @param array<string,string|int> $params
     * @param array<string,string|array<string,mixed>> $body
     *
     * @return Response
     */
    protected function httpPut(string $url, array $headers = [], array $params = [], array $body = []): Response
    {
        $this->client->request('PUT', $url, $params, [], $headers, json_encode($body));
        return $this->client->getResponse();
    }
}
