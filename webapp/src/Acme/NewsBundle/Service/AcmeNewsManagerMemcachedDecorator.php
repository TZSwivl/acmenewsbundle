<?php

namespace Acme\NewsBundle\Service;

use Acme\NewsBundle\Entity\News;
use Doctrine\ORM\Tools\Pagination\Paginator;

class AcmeNewsManagerMemcachedDecorator implements AcmeNewsManagerInterface
{
    /**
     * @var AcmeNewsManagerInterface
     */
    private $newsManager;

    /**
     * @var \Memcached
     */
    private $memcached;

    public function __construct(AcmeNewsManagerInterface $newsManager, \Memcached $memcached)
    {
        $this->newsManager = $newsManager;
        $this->memcached = $memcached;
    }

    public function getNewsForListing(int $page, int $newsPerPage): Paginator
    {
        $set = $this->newsManager->getNewsForListing($page, $newsPerPage);

        return $set;
    }

    public function getOneNews(int $newsId): News
    {
        $news = $this->newsManager->getOneNews($newsId);

        return $news;
    }

    public function getNewsForBlock(int $newsId, int $perBlock): array
    {
        $set = $this->newsManager->getNewsForBlock($newsId, $perBlock);

        return $set;
    }

    public function addNews(News $news): bool
    {
        $result = $this->newsManager->addNews($news);

        return $result;
    }

    public function updateNews(News $news): bool
    {
        $result = $this->newsManager->updateNews($news);

        return $result;
    }

    public function deleteNews(News $news): bool
    {
        $result = $this->newsManager->deleteNews($news);

        return $result;
    }
}