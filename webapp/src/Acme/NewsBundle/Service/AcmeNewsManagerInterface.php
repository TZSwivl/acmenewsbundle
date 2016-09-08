<?php

namespace Acme\NewsBundle\Service;

use Acme\NewsBundle\Entity\News;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface AcmeNewsManagerInterface
{
    public function getNewsForListing(int $page, int $perPage): Paginator;

    public function getOneNews(int $newsId): News;

    public function getNewsForBlock(int $newsId, int $perBlock): array;

    public function addNews(News $news): bool;

    public function updateNews(News $news): bool;

    public function deleteNews(News $news): bool;
}