<?php

namespace App\Tests\Service;

use App\Tests\TestCase;

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
        $client->request('GET', $this->getUrl());

        $this->assertResponseIsSuccessful();
    }
}
