<?php
declare(strict_types=1);

namespace App\Tests\Application\RestApi;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * todo PUT, GET, DELETE tests
 */
abstract class AbstractApiTest extends WebTestCase
{
    protected const ENTITY_CLASS = '';

    private KernelBrowser $client;
    private ?EntityManagerInterface $entityManager;
    private ServiceEntityRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->client = static::createClient();
        
        $container = static::bootKernel()->getContainer();
        
        $this->entityManager = $container->get('doctrine')->getManager();
        $this->repository = $this->entityManager->getRepository(static::ENTITY_CLASS);
    }
    
    private function dbTruncate(): void
    {
        $connection = $this->entityManager->getConnection();
        
        $q = $connection->getDatabasePlatform()->getTruncateTableSql(
            $this->entityManager->getClassMetadata(static::ENTITY_CLASS)->getTableName()
        );
        $connection->executeUpdate($q);
    }

    private function prepareExpectedResult(string $path, mixed $search, mixed $replace): string
    {
        return str_replace($search, $replace, file_get_contents($path));
    }
    
    protected function getLastInsertId(): ?int
    {
        return (json_decode($this->client->getResponse()->getContent()))->data->id;
    }

    protected function getLastInsertEntity(): object
    {
        return $this->repository->find($this->getLastInsertId());
    }

    private function assertDbRecordCountIs(int $dbRecordCount): void
    {
        $this->assertSame(
            $dbRecordCount,
            $this->repository->count([])
        );
    }

    private function assertExpectedResponseContentEquals(string $expectedContent)
    {
        $this->assertEquals($expectedContent, $this->client->getResponse()->getContent());
    }

    private function assertEntityExists(): void
    {
        $this->assertNotNull($this->getLastInsertEntity());
    }

    private function assertEntityClassInstanceOf(): void
    {
        $this->assertInstanceOf(static::ENTITY_CLASS, $this->getLastInsertEntity());
    }

    public function testFirstRun(): void
    {
        $this->dbTruncate();
        $this->assertDbRecordCountIs(0);
    }

    /**
     * @dataProvider testPostProvider
     */
    public function testPost(
        string $uri,
        array $data,
        bool $expectedSuccessfulness,
        int $expectedStatusCode,
        string $expectedResponse,
        int $dbRecordCount
    ): void
    {
        $this->client->request('POST', $uri, $data);

        if ($expectedSuccessfulness) {
            $this->assertResponseIsSuccessful();
        }

        $this->assertResponseStatusCodeSame($expectedStatusCode);

        $lastInsertId = $this->getLastInsertId();


        $this->assertExpectedResponseContentEquals(
            $this->prepareExpectedResult(
                $expectedResponse,
                ['((lastInsertId))'],
                [$lastInsertId]
            )
        );

        $this->assertDbRecordCountIs($dbRecordCount);

        if ($lastInsertId !== null) {
            $this->assertEntityExists();
            $this->assertEntityClassInstanceOf();

            $this->assertExpectedValues($data);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

}
