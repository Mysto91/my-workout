<?php

namespace App\Tests\Card;

use App\Tests\TestCase;
use Faker\Factory;

class UserPostTest extends TestCase
{
    private string $url = '/api/users';

    /**
     * @return array<string,string|array<string,mixed>>
     */
    private function formatBody(): array
    {
        $faker = Factory::create();

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

    public function testIfPostWork(): void
    {
        $body = $this->formatBody();

        $response = $this->httpPost($this->url, $this->getHeaders(), [], $body);
        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 201);
        $this->assertUser($body, $output);
    }

    public function testIfPostWithEmptyRoleNotWork(): void
    {
        $body = $this->formatBody();
        $body['role'] = [];


        $response = $this->httpPost($this->url, $this->getHeaders(), [], $body);
        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 400);
        $this->assertSame('role: The role is not in valid format.', $output['detail']);
    }

    public function testIfPostWithInvalidRoleNotWork(): void
    {
        $body = $this->formatBody();
        $body['role'] = [
            'label' => 'wrong'
        ];

        $response = $this->httpPost($this->url, $this->getHeaders(), [], $body);
        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 400);
        $this->assertSame('role: The role does not exist.', $output['detail']);
    }

    public function testIfPostWithAlreadyExistingUsernameNotWork(): void
    {
        $body = $this->formatBody();

        $response = $this->httpPost($this->url, $this->getHeaders(), [], $body);

        $body['email'] = 'othermail@test.com';

        $response = $this->httpPost($this->url, $this->getHeaders(), [], $body);

        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 422);
        $this->assertSame('username: The username already exists.', $output['detail']);
    }

    public function testIfPostWithAlreadyExistingEmailNotWork(): void
    {
        $body = $this->formatBody();

        $response = $this->httpPost($this->url, $this->getHeaders(), [], $body);

        $body['username'] = 'otherusername';

        $response = $this->httpPost($this->url, $this->getHeaders(), [], $body);

        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 422);
        $this->assertSame('email: The email already exists.', $output['detail']);
    }

    public function testIfPostWithEmptyUsernameNotWork(): void
    {
        $body = $this->formatBody();
        $body['username'] = '';

        $response = $this->httpPost($this->url, $this->getHeaders(), [], $body);

        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 422);
        $this->assertSame('username: This value should not be blank.', $output['detail']);
    }

    public function testIfPostWithEmptyPasswordNotWork(): void
    {
        $body = $this->formatBody();
        $body['password'] = '';

        $response = $this->httpPost($this->url, $this->getHeaders(), [], $body);

        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 422);
        $this->assertSame('password: This value should not be blank.', $output['detail']);
    }

    public function testIfPostWithEmptyNameNotWork(): void
    {
        $body = $this->formatBody();
        $body['name'] = '';

        $response = $this->httpPost($this->url, $this->getHeaders(), [], $body);

        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 422);
        $this->assertSame('name: This value should not be blank.', $output['detail']);
    }

    public function testIfPostWithEmptyFirstNameNotWork(): void
    {
        $body = $this->formatBody();
        $body['firstname'] = '';

        $response = $this->httpPost($this->url, $this->getHeaders(), [], $body);

        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 422);
        $this->assertSame('firstname: This value should not be blank.', $output['detail']);
    }

    public function testIfPostWithEmptyEmailNotWork(): void
    {
        $body = $this->formatBody();
        $body['email'] = '';

        $response = $this->httpPost($this->url, $this->getHeaders(), [], $body);

        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 422);
        $this->assertSame('email: This value should not be blank.', $output['detail']);
    }

    public function testIfPostWithInvalidEmailNotWork(): void
    {
        $body = $this->formatBody();
        $body['email'] = 'wrong';

        $response = $this->httpPost($this->url, $this->getHeaders(), [], $body);

        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 422);
        $this->assertStringContainsString('not a valid email.', $output['detail']);
    }
}
