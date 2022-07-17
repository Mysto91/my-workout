<?php

namespace App\Tests\Card;

use App\Tests\TestCase;

class UserPutTest extends TestCase
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
     * @return array<string,string|array<string,mixed>>
     */
    private function formatBody(): array
    {
        $faker = $this->faker;

        return [
            'username' => $faker->userName(),
            'password' => $faker->password(),
            'name' => $faker->name(),
            'firstname' => $faker->firstName(),
            'email' => $faker->email(),
            'role' => [
                'label' => $faker->randomElement(['admin', 'visitor'])
            ],
        ];
    }

    /**
     * @param array<string|integer> $expected
     * @param array<string|integer> $actual
     *
     * @return void
     */
    private function assertUser(array $expected, array $actual): void
    {
        $this->assertIsInt($actual['id']);
        $this->assertSame($expected['name'], $actual['name']);
        $this->assertSame($expected['firstname'], $actual['firstname']);
        $this->assertSame($expected['email'], $actual['email']);
        $this->assertSame($expected['username'], $actual['username']);
    }

    public function testIfPutWithEmptyRoleNotWork(): void
    {
        $body = $this->formatBody();
        $body['role'] = [];

        $response = $this->httpPut($this->getUrl($this->userAdminId), $this->getHeaders($this->token), [], $body);
        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 422);
        $this->assertSame('role: The role is not in valid format.', $output['detail']);
    }

    public function testIfPutWithInvalidRoleNotWork(): void
    {
        $body = $this->formatBody();
        $body['role'] = [
            'label' => 'wrong'
        ];

        $response = $this->httpPut($this->getUrl($this->userAdminId), $this->getHeaders($this->token), [], $body);
        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 422);
        $this->assertSame('role: The role does not exist.', $output['detail']);
    }

    public function testIfPutWithAlreadyExistingUsernameNotWork(): void
    {
        $users = $this->getUsers();

        $body = $this->formatBody();
        $body['username'] = $users[0]->getUsername();

        $response = $this->httpPut($this->getUrl($users[1]->getId()), $this->getHeaders($this->token), [], $body);

        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 422);
        $this->assertSame('username: The username already exists.', $output['detail']);
    }

    public function testIfPutWithAlreadyExistingEmailNotWork(): void
    {
        $users = $this->getUsers();

        $body = $this->formatBody();
        $body['email'] = $users[0]->getEmail();

        $response = $this->httpPut($this->getUrl($users[1]->getId()), $this->getHeaders($this->token), [], $body);

        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 422);
        $this->assertSame('email: The email already exists.', $output['detail']);
    }

    public function testIfPutWithEmptyUsernameNotWork(): void
    {
        $body = $this->formatBody();
        $body['username'] = '';

        $response = $this->httpPut($this->getUrl($this->userAdminId), $this->getHeaders($this->token), [], $body);

        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 422);
        $this->assertSame('username: This value should not be blank.', $output['detail']);
    }

    public function testIfPutWithEmptyPasswordNotWork(): void
    {
        $body = $this->formatBody();
        $body['password'] = '';

        $response = $this->httpPut($this->getUrl($this->userAdminId), $this->getHeaders($this->token), [], $body);

        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 422);
        $this->assertSame('password: This value should not be blank.', $output['detail']);
    }

    public function testIfPutWithEmptyNameNotWork(): void
    {
        $body = $this->formatBody();
        $body['name'] = '';

        $response = $this->httpPut($this->getUrl($this->userAdminId), $this->getHeaders($this->token), [], $body);

        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 422);
        $this->assertSame('name: This value should not be blank.', $output['detail']);
    }

    public function testIfPutWithEmptyFirstNameNotWork(): void
    {
        $body = $this->formatBody();
        $body['firstname'] = '';

        $response = $this->httpPut($this->getUrl($this->userAdminId), $this->getHeaders($this->token), [], $body);

        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 422);
        $this->assertSame('firstname: This value should not be blank.', $output['detail']);
    }

    public function testIfPutWithEmptyEmailNotWork(): void
    {
        $body = $this->formatBody();
        $body['email'] = '';

        $response = $this->httpPut($this->getUrl($this->userAdminId), $this->getHeaders($this->token), [], $body);

        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 422);
        $this->assertSame('email: This value should not be blank.', $output['detail']);
    }

    public function testIfPutWithInvalidEmailNotWork(): void
    {
        $body = $this->formatBody();
        $body['email'] = 'wrong';

        $response = $this->httpPut($this->getUrl($this->userAdminId), $this->getHeaders($this->token), [], $body);

        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 422);
        $this->assertStringContainsString('not a valid email.', $output['detail']);
    }

    public function testIfPutAnotherUserWithVisitorUserNotWork(): void
    {
        $users = $this->getUsers('visitor');

        $body = $this->formatBody();
        $token = $this->getToken($this->authenticate($users[0]->getUsername(), 'visitor'));

        $response = $this->httpPut($this->getUrl($users[1]->getId()), $this->getHeaders($token), [], $body);

        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 403);
        $this->assertStringContainsString('Access Denied.', $output['detail']);
    }

    public function testIfPutWork(): void
    {
        $users = $this->getUsers('visitor');
        $user = $users[0];

        $body = $this->formatBody();
        $body['username'] = $user->getUsername();
        $body['password'] = 'visitor';

        $response = $this->httpPut($this->getUrl($user->getId()), $this->getHeaders($this->token), [], $body);
        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 200);
        $this->assertUser($body, $output);
    }

    public function testIfPutOwnUserWithVisitorUserWork(): void
    {
        $users = $this->getUsers('visitor');
        $user = $users[0];

        $body = $this->formatBody();

        $token = $this->getToken($this->authenticate($user->getUsername(), 'visitor'));

        $response = $this->httpPut($this->getUrl($user->getId()), $this->getHeaders($token), [], $body);
        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 200);
        $this->assertUser($body, $output);
    }
}
