<?php

namespace App\Tests\Card;

use App\Tests\TestCase;
use Faker\Factory;

class UserGetTest extends TestCase
{
    private string $url = '/api/users';

    /**
     * @param array<array<string|integer>> $users
     *
     * @return void
     */
    private function assertUsers(array $users): void
    {
        foreach ($users as $user) {
            $this->assertUser($user);
        }
    }

    /**
     * @param array<string|integer> $user
     *
     * @return void
     */
    private function assertUser(array $user): void
    {
        $this->assertIsInt($user['id']);
        $this->assertIsString($user['name']);
        $this->assertIsString($user['firstname']);
        $this->assertIsString($user['email']);
        $this->assertIsString($user['username']);
        $this->assertArrayNotHasKey('password', $user);
    }

    public function testIfGetWork(): void
    {
        $response = $this->httpGet($this->url, $this->getHeaders($this->jwt));
        $users = json_decode($response->getContent(), true);

        $this->assertNotEquals(0, $users);
        $this->assertResponseCode($response, 200);
        $this->assertUsers($users);
    }

    public function testIfGetWithVisitorUserNotWork(): void
    {
        $visitorJwt = $this->getJWT(['username' => 'visitor', 'password' => 'visitor']);

        $response = $this->httpGet($this->url, $this->getHeaders("Bearer {$visitorJwt}"));

        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 403);
        $this->assertSame('Access Denied.', $output['detail']);
    }
}
