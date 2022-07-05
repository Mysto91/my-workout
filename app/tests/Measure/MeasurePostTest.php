<?php

namespace App\Tests\Card;

use App\Tests\TestCase;
use DateTime;

class MeasurePostTest extends TestCase
{
    private string $url = '/api/measures';

    /**
     * @return array<string,string|array<string,mixed>>
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
     * @param array<string|integer> $measure
     * @param int $measureId
     * @param int|null $userId
     *
     * @return void
     */
    private function assertMeasure(array $expected, array $actual): void
    {
        $this->assertEquals($expected['weight'], $actual['weight']);
        $this->assertEquals($expected['muscleWeight'], $actual['muscleWeight']);
        $this->assertEquals($expected['boneMass'], $actual['boneMass']);
        $this->assertEquals($expected['bodyWater'], $actual['bodyWater']);

        $now = new DateTime();
        $dateCreatedAt = new DateTime($actual['createdAt']);

        $this->assertSame($now->getTimestamp(), $dateCreatedAt->getTimestamp());
        $this->assertArrayNotHasKey('updatedAt', $actual);

        $expectedMeasurementDate = new DateTime($expected['measurementDate']);
        $actualMeasurementDate = new DateTime($actual['measurementDate']);

        $this->assertSame($expectedMeasurementDate->getTimestamp(), $actualMeasurementDate->getTimestamp());
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
}
