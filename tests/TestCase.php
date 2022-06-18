<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestCase extends WebTestCase
{
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
}
