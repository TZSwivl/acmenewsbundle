<?php

namespace Acme\NewsBundle\Repository;

use Acme\NewsBundle\Entity\News;
use Doctrine\ORM\EntityRepository;

class NewsRepository extends EntityRepository
{
    public function countPublishNews(): int
    {
        return $this
            ->createQueryBuilder('n')
            ->select('count(1)')
            ->where('n.isPublished = 1')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getPrevNews(int $newsId): int
    {
        return $this
            ->createQueryBuilder('n')
            ->select('n.id')
            ->where('n.id < :newsId')
            ->andWhere('n.isPublished = 1')
            ->orderBy('n.id DESC')
            ->setMaxResults(1)
            ->setParameter('newsId', $newsId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getNextNews(int $newsId): int
    {
        return $this
            ->createQueryBuilder('n')
            ->select('n.id')
            ->where('n.id > :newsId')
            ->andWhere('n.isPublished = 1')
            ->orderBy('n.id ASC')
            ->setMaxResults(1)
            ->setParameter('newsId', $newsId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function addNews(News $news, bool $flush = true): News
    {
        $em = $this->getEntityManager();
        $em->persist($news);

        if($flush) $em->flush();

        return $news;
    }

    public function updateNews(News $news, bool $flush = true): News
    {
        $em = $this->getEntityManager();

        if($flush) $em->flush();

        return $news;
    }

    public function deleteNews(News $news, bool $flush = true)
    {
        $em = $this->getEntityManager();
        $em->remove($news);

        if($flush) $em->flush();

        return true;
    }
}