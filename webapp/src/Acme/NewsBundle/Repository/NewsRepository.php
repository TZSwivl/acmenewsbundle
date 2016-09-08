<?php

namespace Acme\NewsBundle\Repository;

use Acme\NewsBundle\Entity\News;
use Doctrine\ORM\EntityRepository;

class NewsRepository extends EntityRepository
{
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