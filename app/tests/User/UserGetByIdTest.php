<?php

namespace App\Tests\Card;

use App\Tests\TestCase;

class UserGetByIdTest extends TestCase
{
    private string $url = '/api/users';

    /**
     * @param integer $userId
     *
     * @return string
     */
    private function getUrl(int $userId): string
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
        $users = $this->getUsers('visitor');
        $user = $users[0];
        $userId = $user->getId();

        $token = $this->getToken($this->authenticate($user->getUsername(), 'visitor'));

        $response = $this->httpGet($this->getUrl($userId), $this->getHeaders($token));
        $user = json_decode($response->getContent(), true);

        $this->assertUser($user, $userId);
    }

    public function testIfGetWithAdminUserWork(): void
    {
        $users = $this->getUsers('visitor');

        $response = $this->httpGet($this->getUrl($users[0]->getId()), $this->getHeaders($this->token));
        $user = json_decode($response->getContent(), true);

        $this->assertUser($user, $this->userVisitorId);
    }

    public function testIfGetAnotherUserWithVisitorUserNotWork(): void
    {
        $users = $this->getUsers('visitor');

        $visitorUser = $users[0];
        $otherUser = $users[1];

        $token = $this->getToken($this->authenticate($visitorUser->getUsername(), 'visitor'));

        $response = $this->httpGet($this->getUrl($otherUser->getId()), $this->getHeaders($token));

        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 403);
        $this->assertSame('Access Denied.', $output['detail']);
    }

    public function testIfGetWithNotExistingUserNotWork(): void
    {
        $response = $this->httpGet($this->getUrl(99999), $this->getHeaders($this->getToken()));

        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 404);
        $this->assertSame('Not Found', $output['detail']);
    }
}
