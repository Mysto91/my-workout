<?php

namespace App\Tests\Card;

use App\Tests\TestCase;

class CardGetTest extends TestCase
{
    private string $url = '/api/cards';

    /**
     * @param array<string> $params
     *
     * @return string
     */
    public function getUrl(array $params = []): string
    {
        return $this->getUrlWithParams($this->url, $params);
    }

    public function testIfGetWork()
    {
        $this->client->request('GET', $this->getUrl() , [], [], ['HTTP_Authorization' => $this->jwt, 'CONTENT_TYPE' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, 200);
        $this->assertJson(json_encode(['code' => '401', 'message' => 'JWT Token not found']));
    }

    public function testIfGetWithoutAuthenticationNotWork(): void
    {
        $this->client->request('GET', $this->getUrl());
        
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, 401);
        $this->assertJson(json_encode(['code' => '401', 'message' => 'JWT Token not found']));
    }
}
