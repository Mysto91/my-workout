<?php

namespace App\Tests\Card;

use App\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CardGetTest extends TestCase
{
    private string $url = 'api/cards';

    /**
     * @param array<string> $params
     *
     * @return string
     */
    public function getUrl(array $params = []): string
    {
        return $this->getUrlWithParams($this->url, $params);
    }

    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getUrl());

        dd($crawler->getResponse());

        $this->assertResponseIsSuccessful();
    }
}
