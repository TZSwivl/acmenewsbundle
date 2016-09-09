<?php

namespace Acme\NewsBundle\Repository;

use Acme\NewsBundle\Entity\News;
use Doctrine\ORM\EntityRepository;

class NewsRepository extends EntityRepository
{
    /**
     * Возвращает кол-во опубликованных новостей в БД
     *
     * @return int
     */
    public function countPublishedNews(): int
    {
        return $this
            ->createQueryBuilder('n')
            ->select('count(1)')
            ->where('n.isPublished = 1')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Возвращает ID предыдущей доступной новости
     *
     * @param int $newsId
     *
     * @return int
     */
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

    /**
     * Возвращает ID следующей доступной новости
     *
     * @param int $newsId

     * @return int
     */
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

    /**
     * Добавляет новость в БД
     *
     * @param News $news
     * @param bool $flush
     *
     * @return News
     */
    public function addNews(News $news, bool $flush = true): News
    {
        $em = $this->getEntityManager();
        $em->persist($news);

        if($flush) $em->flush();

        return $news;
    }

    /**
     * Обновляет новость из БД
     *
     * @param News $news
     * @param bool $flush
     *
     * @return News
     */
    public function updateNews(News $news, bool $flush = true): News
    {
        $em = $this->getEntityManager();

        if($flush) $em->flush();

        return $news;
    }

    /**
     * Удаляет новость из БД
     *
     * @param News $news
     * @param bool $flush
     *
     * @return bool
     */
    public function deleteNews(News $news, bool $flush = true)
    {
        $em = $this->getEntityManager();
        $em->remove($news);

        if($flush) $em->flush();

        return true;
    }
}