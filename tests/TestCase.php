<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestCase extends WebTestCase
{
    /**
     * @param string $url
     * @param array $params
     *
     * @return void
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
