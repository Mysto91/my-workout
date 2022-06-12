<?php

namespace App\Tests\Service;

use App\Tests\TestCase;

class CardGetTest extends TestCase
{
    private $url = 'api/cards';

    public function getUrl($params = [])
    {
        return $this->getUrlWithParams($this->url, $params);
    }

    public function testSomething()
    {
        $client = static::createClient();
        $client->request('GET', $this->getUrl());

        $this->assertResponseIsSuccessful();
    }
}