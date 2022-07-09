<?php

namespace App\Tests;

use ApiTestCase\JsonApiTestCase;
use App\DataFixtures\CardFixtures;
use App\DataFixtures\MeasureFixtures;
use App\DataFixtures\RoleFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Measure;
use App\Entity\Role;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Faker\Factory;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Component\HttpFoundation\Response;

class TestCase extends JsonApiTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;
    protected string $token;
    protected int $userAdminId = 1;
    protected int $userVisitorId = 2;
    protected static bool $initialized = false;
    private ?EntityManager $entityManager;
    protected \Faker\Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        if (!self::$initialized) {
            $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
            $this->initDatabase();
            putenv("JWT={$this->getToken()}");
            self::$initialized = true;
        }

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->token = getenv('JWT');
        $this->faker = Factory::create();
    }

    /**
     * @param string $jwt
     *
     * @return array<string>
     */
    protected function getHeaders(string $jwt = ''): array
    {
        return [
            'HTTP_Authorization' => "Bearer {$jwt}",
            'CONTENT_TYPE' => 'application/json'
        ];
    }

    /**
     * @param string $class
     *
     * @return EntityRepository
     */
    protected function getRepository(string $class): EntityRepository
    {
        /** @phpstan-ignore-next-line */
        return $this->entityManager->getRepository($class);
    }

    /**
     * @param int $userId
     *
     * @return array<Measure>
     */
    protected function getMeasuresByUserId(int $userId): array
    {
        return $this->getRepository(Measure::class)->findByUser($userId);
    }

    /**
     * @return array<Measure>
     */
    protected function getMeasures(): array
    {
        return $this->getRepository(Measure::class)->findAll();
    }

    /**
     * @param string|null $roleLabel
     *
     * @return array<User>
     */
    protected function getUsers(?string $roleLabel = null): array
    {
        if ($roleLabel) {
            $role = $this->getRepository(Role::class)->findByLabel($roleLabel);
            return $this->getRepository(User::class)->findByRole($role[0]->getId());
        }

        return $this->getRepository(User::class)->findAll();
    }

    /**
     * @param string $entity
     * @param integer $id
     *
     * @return string
     */
    protected function getIri(string $entity, int $id): string
    {
        return "/api/{$entity}/{$id}";
    }

    /**
     * @return void
     */
    protected function initDatabase(): void
    {
        $this->databaseTool->loadFixtures([
            RoleFixtures::class,
            UserFixtures::class,
            CardFixtures::class,
            MeasureFixtures::class
        ]);
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return array<string>
     */
    protected function authenticate(string $username, string $password): array
    {
        return [
            'username' => $username,
            'password' => $password
        ];
    }

    /**
     * @param array<string> $body
     *
     * @return string
     */
    protected function getToken(array $body = []): string
    {
        if (empty($body)) {
            $body = $this->authenticate('admin', 'admin');
        }

        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($body)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);

        return $response['token'] ?? '';
    }

    /**
     * @return DateTime
     */
    protected function getToday(): DateTime
    {
        return new DateTime();
    }

    /**
     * @param string $date
     * @return DateTime
     */
    protected function getDate(string $date): DateTime
    {
        return new DateTime($date);
    }

    protected function assertSameDate(DateTime $expectedDate, DateTime $actualDate): void
    {
        $this->assertSame($expectedDate->getTimestamp(), $actualDate->getTimestamp());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }

    /**
     * @param string $url
     * @param array<string,string|int> $headers
     * @param array<string,string|int> $params
     *
     * @return Response
     */
    protected function httpGet(string $url, array $headers = [], array $params = []): Response
    {
        $this->client->request('GET', $url, $params, [], $headers);
        return $this->client->getResponse();
    }

    /**
     * @param string $url
     * @param array<string,string|int> $headers
     * @param array<string,string|int> $params
     * @param array<string,string|array<string,mixed>> $body
     *
     * @return Response
     */
    protected function httpPost(string $url, array $headers = [], array $params = [], array $body = []): Response
    {
        $this->client->request('POST', $url, $params, [], $headers, json_encode($body));
        return $this->client->getResponse();
    }

    /**
     * @param string $url
     * @param array<string,string|int> $headers
     * @param array<string,string|int> $params
     * @param array<string,string|array<string,mixed>> $body
     *
     * @return Response
     */
    protected function httpPut(string $url, array $headers = [], array $params = [], array $body = []): Response
    {
        $this->client->request('PUT', $url, $params, [], $headers, json_encode($body));
        return $this->client->getResponse();
    }
}
