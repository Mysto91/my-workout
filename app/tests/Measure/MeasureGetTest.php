<?php

namespace App\Tests\Measure;

use App\Tests\TestCase;

class MeasureGetTest extends TestCase
{
    private string $url = '/api/measures';

    /**
     * @param array<array<string|integer>> $measures
     * @param int|null $userId
     *
     * @return void
     */
    private function assertMeasures(array $measures, ?int $userId = null): void
    {
        foreach ($measures as $measure) {
            $this->assertMeasure($measure, $userId);
        }
    }

    /**
     * @param array<string|integer> $measure
     * @param int|null $userId
     *
     * @return void
     */
    private function assertMeasure(array $measure, ?int $userId = null): void
    {
        $this->assertIsInt($measure['id']);
        $this->assertIsNumeric($measure['weight']);
        $this->assertIsNumeric($measure['muscleWeight']);
        $this->assertIsString($measure['measurementDate']);
        $this->assertIsNumeric($measure['boneMass']);
        $this->assertIsNumeric($measure['bodyWater']);
        $this->assertIsString($measure['createdAt']);
        $this->assertIsString($measure['updatedAt']);

        if ($userId) {
            $this->assertSame("/api/users/{$userId}", $measure['user']);
        }
    }

    public function testIfGetWork(): void
    {
        $response = $this->httpGet($this->url, $this->getHeaders($this->token));
        $measures = json_decode($response->getContent(), true);

        $this->assertCount(count($this->getMeasures()), $measures);
        $this->assertResponseCode($response, 200);
        $this->assertMeasures($measures);
    }

    public function testIfGetWithVisitorUserWork(): void
    {
        $users = $this->getUsers('visitor');
        $user = $users[0];
        $userId = $user->getId();

        $token = $this->getToken($this->authenticate($user->getUsername(), 'visitor'));

        $response = $this->httpGet($this->url, $this->getHeaders($token));
        $measures = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 200);
        $this->assertMeasures($measures, $userId);
    }

    public function testIfGetWithoutAuthenticationNotWork(): void
    {
        $response = $this->httpGet($this->url, $this->getHeaders());

        $this->assertResponseCode($response, 401);
        $this->assertJson(json_encode(['code' => '401', 'message' => 'JWT Token not found']));
    }
}
