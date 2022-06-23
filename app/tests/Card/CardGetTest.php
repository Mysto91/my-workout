<?php

namespace App\Tests\Card;

use App\Tests\TestCase;

class CardGetTest extends TestCase
{
    private string $url = '/api/cards';

    /**
     * @param array<array<string|integer>> $cards
     *
     * @return void
     */
    private function assertCards(array $cards): void
    {
        foreach ($cards as $card) {
            $this->assertCard($card);
        }
    }

    /**
     * @param array<string|integer> $card
     *
     * @return void
     */
    private function assertCard(array $card): void
    {
        $this->assertIsInt($card['id']);
        $this->assertIsString($card['title']);
        $this->assertIsInt($card['point']);
        $this->assertIsString($card['description']);
        $this->assertIsString($card['startDate']);
        $this->assertIsString($card['endDate']);
    }

    /**
     * @param array<string> $params
     *
     * @return string
     */
    public function getUrl(array $params = []): string
    {
        return $this->getUrlWithParams($this->url, $params);
    }

    public function testIfGetWork(): void
    {
        $this->client->request('GET', $this->getUrl(), [], [], $this->getHeaders($this->jwt));

        $response = $this->client->getResponse();
        $cards = json_decode($response->getContent(), true);

        $this->assertCount(10, $cards);
        $this->assertResponseCode($response, 200);
        $this->assertCards($cards);
    }

    public function testIfGetWithoutAuthenticationNotWork(): void
    {
        $this->client->request('GET', $this->getUrl());

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, 401);
        $this->assertJson(json_encode(['code' => '401', 'message' => 'JWT Token not found']));
    }
}
