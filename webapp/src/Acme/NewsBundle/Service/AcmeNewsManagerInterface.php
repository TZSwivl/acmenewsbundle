<?php

namespace Acme\NewsBundle\Service;

use Acme\NewsBundle\Entity\News;

interface AcmeNewsManagerInterface
{
    public function getNewsForListing(int $page, int $perPage): array;

    public function getOneNews(int $newsId): News;

    public function getSupplementalNews(int $newsId, int $perBlock): array;

    public function getLastPaginationPage(int $perPage): int;

    public function getNewsSiblings(int $newsId): array;

    public function addNews(News $news, bool $flush = true): News;

    public function updateNews(News $news, bool $flush = true): News;

    public function deleteNews(News $news, bool $flush = true): bool;
}