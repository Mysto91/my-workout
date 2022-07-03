<?php

namespace App\Tests\Measure;

use App\Tests\TestCase;

class MeasureGetByIdTest extends TestCase
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

    public function testIfGetWithVisitorUserWork(): void
    {
        $users = $this->getUsers('visitor');
        $user = $users[0];
        $userId = $user->getId();

        $token = $this->getToken($this->authenticate($user->getUsername(), 'visitor'));

        $userMeasures = $this->getMeasuresByUserId($userId);
        $measure = $userMeasures[array_rand($userMeasures, 1)];
        $measureId = $measure->getId();

        $response = $this->httpGet($this->getUrl($measureId), $this->getHeaders($token));
        $measure = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 200);
        $this->assertMeasure($measure, $measureId, $userId);
    }

    public function testIfGetWork(): void
    {
        $measures = $this->getMeasures();
        $measureId = $measures[0]->getId();

        $response = $this->httpGet($this->getUrl($measureId), $this->getHeaders($this->token));
        $measure = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 200);
        $this->assertMeasure($measure, $measureId);
    }

    public function testIfGetAnotherUserMeasureWithVisitorUserWork(): void
    {
        $users = $this->getUsers('visitor');

        $visitorUser = $users[0];
        $otherUser = $users[1];

        $token = $this->getToken($this->authenticate($visitorUser->getUsername(), 'visitor'));

        $otherUserMeasures = $this->getMeasuresByUserId($otherUser->getId());

        $response = $this->httpGet($this->getUrl($otherUserMeasures[0]->getId()), $this->getHeaders($token));
        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 403);
        $this->assertSame('Access Denied.', $output['detail']);
    }

    public function testIfGetWithoutAuthenticationNotWork(): void
    {
        $response = $this->httpGet($this->url, $this->getHeaders());

        $this->assertResponseCode($response, 401);
        $this->assertJson(json_encode(['code' => '401', 'message' => 'JWT Token not found']));
    }

    public function testIfGetWithNotExistingMeasureNotWork(): void
    {
        $response = $this->httpGet($this->getUrl(99999), $this->getHeaders($this->token));
        $output = json_decode($response->getContent(), true);

        $this->assertResponseCode($response, 404);
        $this->assertSame('Not Found', $output['detail']);
    }
}
