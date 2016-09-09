<?php

namespace Acme\NewsBundle\Tests\Service;

use Acme\NewsBundle\Entity\News;
use Acme\NewsBundle\Repository\NewsRepository;
use Acme\NewsBundle\Service\AcmeNewsManager;

class AcmeNewsManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetLastPaginationPage()
    {
        $newsRepo = $this->createMock(NewsRepository::class)
            ->method('countPublishedNews')
            ->willReturn(47);

        $perPage = 7;

        $newsManager = new AcmeNewsManager($newsRepo);
        $expected = 7;
        $actual = $newsManager->getLastPaginationPage($perPage);

        $this->assertEquals($expected, $actual);
    }

    public function testGetNewsSiblings()
    {
        $newsRepo = $this->createMock(NewsRepository::class)
            ->method('getPrevNews')
            ->willReturn(37)
            ->method('getNextNews')
            ->willReturn(42);

        $newsId = 333;

        $newsManager = new AcmeNewsManager($newsRepo);
        $expected = ['prev' => 37, 'next' => 42];
        $actual = $newsManager->getNewsSiblings($newsId);

        $this->assertEquals($expected, $actual);
    }
}