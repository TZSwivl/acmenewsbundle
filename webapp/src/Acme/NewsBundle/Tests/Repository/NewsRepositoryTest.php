<?php

namespace Acme\NewsBundle\Tests\Repository;

use Acme\NewsBundle\Repository\NewsRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NewsRepositoryTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private static $em;

    /**
     * @var NewsRepository
     */
    private static $newsRepo;

    public static function setUpBeforeClass()
    {
        // Setup database connection data
        parent::setUpBeforeClass();

        self::bootKernel();

        self::$em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        self::$newsRepo = self::$em->getRepository('AcmeNewsBundle:News');

        // Load test fixtures
        $fixtureFile = __DIR__ . '/../../DBFixtures/news.sql';

        ob_start();
        require_once $fixtureFile;
        $sql = ob_get_clean();

        self::$em->getConnection()->executeQuery($sql);
    }

    public function testCountPublishedNews()
    {
        $expected = 1;
        $actual = self::$newsRepo->countPublishedNews();

        $this->assertEquals($expected, $actual);
    }

    public function testGetPrevNews()
    {
        $expectedData = [
            [1, 0],
            [5, 4],
            [4, 3],
            [19, 17],
            [21, 20],
        ];

        foreach($expectedData as $set) {
            $newsId = $set[0];
            $expected = $set[1];
            $actual = self::$newsRepo->getPrevNews($newsId);
            $this->assertEquals($expected, $actual);
        }
    }

    public function testGetNextNews()
    {
        $expectedData = [
            [1, 0],
            [5, 4],
            [4, 3],
            [19, 17],
            [21, 20],
        ];

        foreach($expectedData as $set) {
            $newsId = $set[0];
            $expected = $set[1];
            $actual = self::$newsRepo->getNextNews($newsId);
            $this->assertEquals($expected, $actual);
        }
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        self::$em->close();
        self::$em = null; // avoid memory leaks
    }
}