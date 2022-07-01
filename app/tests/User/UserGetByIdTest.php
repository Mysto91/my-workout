<?php

namespace App\Tests\Card;

use App\Tests\TestCase;

class UserGetByIdTest extends TestCase
{
    private string $url = '/api/users';

    private function getUrl($userId)
    {
        return "{$this->url}/{$userId}";
    }

    /**
     * @param array<string|integer> $user
     *
     * @return void
     */
    private function assertUser(array $user, int $expectedUserId): void
    {
        $this->assertIsInt($user['id']);
        $this->assertSame($expectedUserId, $user['id']);
        $this->assertIsString($user['name']);
        $this->assertIsString($user['firstname']);
        $this->assertIsString($user['email']);
        $this->assertIsString($user['username']);
        $this->assertArrayNotHasKey('password', $user);
    }

    public function testIfGetWork(): void
    {
        $userId = $this->userVisitorId;
        $jwt = $this->getJWT($this->authenticate("visitor_{$userId}", 'visitor'));

        $response = $this->httpGet($this->getUrl($userId), $this->getHeaders($jwt));
        $user = json_decode($response->getContent(), true);

        $this->assertUser($user, $userId);
    }

    public function testIfGetWithAdminUserWork(): void
    {
        $response = $this->httpGet($this->getUrl($this->userVisitorId), $this->getHeaders($this->jwt));
        $user = json_decode($response->getContent(), true);

        $this->assertUser($user, $this->userVisitorId);
    }

    public function testIfGetAnotherUserWithVisitorUserNotWork(): void
    {
        $userId = $this->userVisitorId + 1;
        $jwt = $this->getJWT($this->authenticate("visitor_{$userId}", 'visitor'));

        $response = $this->httpGet($this->getUrl($this->userVisitorId), $this->getHeaders($jwt));

        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 403);
        $this->assertSame('Access Denied.', $output['detail']);
    }
}
