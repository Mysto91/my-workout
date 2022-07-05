<?php

namespace App\Tests\Measure;

use App\Tests\TestCase;

class MeasurePutTest extends TestCase
{
    private string $url = '/api/measures';

    /**
     * @param integer $measureId
     * @return string
     */
    private function getUrl(int $measureId): string
    {
        return "{$this->url}/{$measureId}";
    }

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
    private function assertMeasure(array $measure, int $measureId, ?int $userId = null): void
    {
        $this->assertSame($measureId, $measure['id']);
        $this->assertIsNumeric($measure['weight']);
        $this->assertIsNumeric($measure['muscleWeight']);
        $this->assertIsString($measure['measurementDate']);
        $this->assertIsNumeric($measure['boneMass']);
        $this->assertIsNumeric($measure['bodyWater']);
        $this->assertIsString($measure['createdAt']);
        $this->assertIsString($measure['updatedAt']);

        if ($userId) {
            $this->assertSame($this->getIri('users', $userId), $measure['user']);
        }
    }

    public function testIfPutWithVisitorUserWork(): void
    {
        $users = $this->getUsers('visitor');
        $user = $users[0];
        $userId = $user->getId();

        $token = $this->getToken($this->authenticate($user->getUsername(), 'visitor'));

        $userMeasures = $this->getMeasuresByUserId($userId);
        $measure = $userMeasures[array_rand($userMeasures, 1)];
        $measureId = $measure->getId();

        $body = $this->formatBody($userId);

        $response = $this->httpPut($this->getUrl($measureId), $this->getHeaders($token), [], $body);
        $measure = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 200);
        $this->assertMeasure($measure, $measureId, $userId);
    }

    public function testIfPutAnotherUserMeasureWithAdminUserWork(): void
    {
        $otherUsers = $this->getUsers('visitor');
        $otherUser = $otherUsers[0];
        $otherUserId = $otherUser->getId();

        $userMeasures = $this->getMeasuresByUserId($otherUserId);
        $measure = $userMeasures[array_rand($userMeasures, 1)];
        $measureId = $measure->getId();

        $body = $this->formatBody($otherUserId);

        $response = $this->httpPut($this->getUrl($measureId), $this->getHeaders($this->token), [], $body);
        $measure = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 200);
        $this->assertMeasure($measure, $measureId);
    }

    public function testIfPutAnotherUserMeasureWithVisitorUserNotWork(): void
    {
        $users = $this->getUsers('visitor');

        $visitorUser = $users[0];
        $otherUser = $users[1];

        $token = $this->getToken($this->authenticate($visitorUser->getUsername(), 'visitor'));

        $otherUserMeasures = $this->getMeasuresByUserId($otherUser->getId());

        $response = $this->httpPut($this->getUrl($otherUserMeasures[0]->getId()), $this->getHeaders($token));
        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 403);
        $this->assertSame('Access Denied.', $output['detail']);
    }

    public function testIfPutWithoutAuthenticationNotWork(): void
    {
        $response = $this->httpPut($this->getUrl(1), $this->getHeaders());

        $this->assertResponseCode($response, 401);
        $this->assertJson(json_encode(['code' => '401', 'message' => 'JWT Token not found']));
    }

    public function testIfPutWithNotExistingMeasureNotWork(): void
    {
        $response = $this->httpPut($this->getUrl(99999), $this->getHeaders($this->token));
        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 404);
        $this->assertSame('Not Found', $output['detail']);
    }
}
