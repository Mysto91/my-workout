<?php

namespace App\Tests;

use ApiTestCase\JsonApiTestCase;
use App\DataFixtures\CardFixtures;
use App\DataFixtures\RoleFixtures;
use App\DataFixtures\UserFixtures;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;

class TestCase extends JsonApiTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    protected string $jwt;

    protected function setUp(): void
    {
        parent::setUp();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();

        $this->initDatabase();
        $this->jwt = $this->getJWT();
    }

    /**
     * @param string $url
     * @param array<string> $params
     *
     * @return string
     */
    protected function getUrlWithParams(string $url, array $params): string
    {
        if (!$params) {
            return $url;
        }

        $paramsConcat = '';

        foreach ($params as $key => $param) {
            $paramsConcat = "{$key}={$param}&{$paramsConcat}";
        }

        return "{$url}?{$paramsConcat}";
    }

    /**
     * @return void
     */
    protected function initDatabase(): void
    {
        $this->databaseTool->loadFixtures([
            RoleFixtures::class,
            UserFixtures::class,
            CardFixtures::class
        ]);
    }

    /**
     * @param array $body
     *
     * @return string
     */
    protected function getJWT(array $body = []): string
    {
        if(empty($body)) {
            $body = [
                'username' => 'admin',
                'password' => 'admin'
            ];
        }

        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'username' => 'admin',
                    'password' => 'admin'
                ]
            )
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);

        return $response['token'] ?? null;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }
}
