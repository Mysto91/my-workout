<?php

namespace App\Tests\Card;

use App\Tests\TestCase;

class MeasurePostTest extends TestCase
{
    private string $url = '/api/measures';

    /**
     * @return array<string,float|string>
     */
    private function formatBody(int $userId): array
    {
        $faker = $this->faker;

        return [
            'weight' => $faker->randomFloat(2, 60, 70),
            'muscleWeight' => $faker->randomFloat(2, 50, 60),
            'measurementDate' => $faker->dateTimeBetween('-1 year', '-1 day')->format('Y-m-d H:i:s'),
            'boneMass' => $faker->randomFloat(2, 5, 10),
            'bodyWater' => $faker->randomFloat(2, 50, 60),
            'user' => "/api/users/{$userId}",
        ];
    }

    /**
     * @param array<string|integer> $expected
     * @param array<string|integer> $actual
     *
     * @return void
     */
    private function assertMeasure(array $expected, array $actual): void
    {
        $this->assertEquals($expected['weight'], $actual['weight']);
        $this->assertEquals($expected['muscleWeight'], $actual['muscleWeight']);
        $this->assertEquals($expected['boneMass'], $actual['boneMass']);
        $this->assertEquals($expected['bodyWater'], $actual['bodyWater']);
        $this->assertEquals($expected['user'], $actual['user']);
        $this->assertSameDate($this->getToday(), $this->getDate($actual['createdAt']));
        $this->assertSameDate($this->getDate($expected['measurementDate']), $this->getDate($actual['measurementDate']));
        $this->assertArrayNotHasKey('updatedAt', $actual);
    }

    public function testIfPostWork(): void
    {
        $users = $this->getUsers();
        $user = $users[0];
        $userId = $user->getId();

        $body = $this->formatBody($userId);

        $response = $this->httpPost($this->url, $this->getHeaders($this->token), [], $body);
        $measure = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 201);
        $this->assertMeasure($body, $measure);
    }

    public function testIfPostWithVisitorUserWork(): void
    {
        $users = $this->getUsers('visitor');
        $user = $users[0];
        $userId = $user->getId();

        $token = $this->getToken($this->authenticate($user->getUsername(), 'visitor'));

        $body = $this->formatBody($userId);

        $response = $this->httpPost($this->url, $this->getHeaders($token), [], $body);
        $measure = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 201);
        $this->assertMeasure($body, $measure);
    }

    public function testIfPostWithWrongFormatWeightNotWork(): void
    {
        $users = $this->getUsers('admin');
        $user = $users[0];
        $userId = $user->getId();

        $body = $this->formatBody($userId);
        $body['weight'] = 'wrong';

        $response = $this->httpPost($this->url, $this->getHeaders($this->token), [], $body);
        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 400);
        $this->assertSame('The type of the "weight" attribute must be "float", "string" given.', $output['detail']);
    }

    public function testIfPostWithWrongFormatMuscleWeightNotWork(): void
    {
        $users = $this->getUsers('admin');
        $user = $users[0];
        $userId = $user->getId();

        $body = $this->formatBody($userId);
        $body['muscleWeight'] = 'wrong';

        $response = $this->httpPost($this->url, $this->getHeaders($this->token), [], $body);
        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 400);
        $this->assertSame('The type of the "muscleWeight" attribute must be "float", "string" given.', $output['detail']);
    }

    public function testIfPostWithWrongFormatMeasurementDateNotWork(): void
    {
        $users = $this->getUsers('admin');
        $user = $users[0];
        $userId = $user->getId();

        $body = $this->formatBody($userId);
        $body['measurementDate'] = 'wrong';

        $response = $this->httpPost($this->url, $this->getHeaders($this->token), [], $body);
        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 400);
        $this->assertSame('The date time is invalid format.', $output['hydra:description']);
    }

    public function testIfPostWithWrongFormatBoneMassNotWork(): void
    {
        $users = $this->getUsers('admin');
        $user = $users[0];
        $userId = $user->getId();

        $body = $this->formatBody($userId);
        $body['boneMass'] = 'wrong';

        $response = $this->httpPost($this->url, $this->getHeaders($this->token), [], $body);
        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 400);
        $this->assertSame('The type of the "boneMass" attribute must be "float", "string" given.', $output['detail']);
    }

    public function testIfPostWithWrongFormatBodyWaterNotWork(): void
    {
        $users = $this->getUsers('admin');
        $user = $users[0];
        $userId = $user->getId();

        $body = $this->formatBody($userId);
        $body['bodyWater'] = 'wrong';

        $response = $this->httpPost($this->url, $this->getHeaders($this->token), [], $body);
        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 400);
        $this->assertSame('The type of the "bodyWater" attribute must be "float", "string" given.', $output['detail']);
    }

    public function testIfPostWithWrongFormatIriUserNotWork(): void
    {
        $users = $this->getUsers('admin');
        $user = $users[0];
        $userId = $user->getId();

        $body = $this->formatBody($userId);
        $body['user'] = 'wrong';

        $response = $this->httpPost($this->url, $this->getHeaders($this->token), [], $body);
        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 400);
        $this->assertSame('Invalid IRI "wrong".', $output['detail']);
    }

    public function testIfPostWithNotExistingUserNotWork(): void
    {
        $body = $this->formatBody(99999);

        $response = $this->httpPost($this->url, $this->getHeaders($this->token), [], $body);
        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 400);
        $this->assertSame('Item not found for "/api/users/99999".', $output['detail']);
    }

    public function testIfPostWithoutAuthenticationNotWork(): void
    {
        $response = $this->httpPost($this->url, $this->getHeaders());

        $this->assertResponseCode($response, 401);
        $this->assertJson(json_encode(['code' => '401', 'message' => 'JWT Token not found']));
    }
}
