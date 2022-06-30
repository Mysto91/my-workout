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

    public function testIfGetWork(): void
    {
        $response = $this->httpGet($this->url, $this->getHeaders($this->jwt));
        $cards = json_decode($response->getContent(), true);

        $this->assertCount(10, $cards);
        $this->assertResponseCode($response, 200);
        $this->assertCards($cards);
    }

    public function testIfGetWithPaginationWork(): void
    {
        $params = [
            'page' => 2
        ];

        $response = $this->httpGet($this->url, $this->getHeaders($this->jwt), $params);

        $cards = json_decode($response->getContent(), true);

        $this->assertCount(10, $cards);
        $this->assertSame($cards[0]['id'], 11);
        $this->assertSame($cards[count($cards) - 1]['id'], 20);
        $this->assertResponseCode($response, 200);
    }

    public function testIfGetWithParamPointWork(): void
    {
        $params = [
            'point' => 5
        ];

        $response = $this->httpGet($this->url, $this->getHeaders($this->jwt), $params);

        $cards = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 200);
        $this->assertContainsEquals($params['point'], array_column($cards, 'point'));
    }

    public function testIfGetWithParamTitleWork(): void
    {
        $params = [
            'title' => 'Ms'
        ];

        $response = $this->httpGet($this->url, $this->getHeaders($this->jwt), $params);

        $cards = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 200);

        foreach ($cards as $card) {
            $this->assertStringContainsString($params['title'], $card['title']);
        }
    }

    public function testIfGetWithParamDescriptionWork(): void
    {
        $params = [
            'description' => 'vol'
        ];

        $response = $this->httpGet($this->url, $this->getHeaders($this->jwt), $params);

        $cards = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 200);

        foreach ($cards as $card) {
            $this->assertStringContainsString($params['description'], strtolower($card['description']));
        }
    }

    public function testIfGetWithoutAuthenticationNotWork(): void
    {
        $response = $this->httpGet($this->url, $this->getHeaders());

        $this->assertResponseCode($response, 401);
        $this->assertJson(json_encode(['code' => '401', 'message' => 'JWT Token not found']));
    }
}
