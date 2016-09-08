<?php

namespace Acme\NewsBundle\Service;

use Acme\NewsBundle\Entity\News;
use Acme\NewsBundle\Repository\NewsRepository;

class AcmeNewsManager implements AcmeNewsManagerInterface
{
    /**
     * @var NewsRepository
     */
    private $newsRepository;

    public function __construct(
        NewsRepository $newsRepository
    )
    {
        $this->newsRepository = $newsRepository;
    }

    /**
     * Returns news set for listing page
     *
     * @param int $page
     * @param int $perPage
     *
     * @return array
     */
    public function getNewsForListing(int $page, int $perPage): array
    {
        $newsSet = $this->newsRepository->findBy(
            ['isPublished' => true],
            ['createdAt' => 'DESC'],
            $perPage,               // limit
            ($page - 1) * $perPage  // offset
        );

        return $newsSet;
    }

    /**
     * Return particular news
     *
     * @param int $newsId
     *
     * @return News
     */
    public function getOneNews(int $newsId): News
    {
        /** @var News $news */
        $news = $this->newsRepository->find($newsId);

        return $news;
    }

    /**
     * Returns supplemental news set by random selection algorithm
     *
     * Not "order by rand()" because that operation in MySQL has O(rand()) complexity
     *
     * @param int $newsId
     * @param int $perBlock
     * @return array
     */
    public function getSupplementalNews(int $newsId, int $perBlock): array
    {
        // Resulting set
        $newsSet = [];

        $countNews = $this->newsRepository->countPublishNews();

        // set of random offsets
        $randOffsets = [];

        for($i = 1; $i <= $perBlock; $i++) {
            $randOffsets[] = mt_rand(1, $countNews);
        }

        // get random news based on random offsets in table
        foreach($randOffsets as $offset) {
            $result = $this->newsRepository->findBy(
                ['isPublished' => true],
                ['createdAt' => 'DESC'],
                1,      // limit
                $offset // offset
            );

            if(count($result) > 0) {
                $newsSet[] = $result[0];
            }
        }

        return $newsSet;
    }

    /**
     * Returns last pagination page (on news list) for given $perPage param
     *
     * @param int $perPage
     *
     * @return int last pagination page
     */
    public function getLastPaginationPage(int $perPage): int
    {
        $countNews = $this->newsRepository->countPublishNews();

        return $countNews ? ceil($countNews / $perPage) : 1;
    }

    /**
     * Finds prev & next siblings for given news
     *
     * @param int $newsId
     *
     * @return array
     */
    public function getNewsSiblings(int $newsId): array
    {
        $siblings = ['prev' => false, 'next' => false];

        $prevNews = $this->newsRepository->getPrevNews($newsId);

        if($prevNews) $siblings['prev'] = $prevNews;

        $nextNews = $this->newsRepository->getNextNews($newsId);

        if($nextNews) $siblings['next'] = $nextNews;

        return $siblings;
    }

    /**
     * Adds news to DB
     *
     * @param News $news
     * @param bool $flush
     *
     * @return News
     */
    public function addNews(News $news, bool $flush = true): News
    {
        $news = $this->newsRepository->addNews($news, $flush);

        return $news;
    }

    /**
     * Updates news in DB
     *
     * @param News $news
     * @param bool $flush
     *
     * @return News
     */
    public function updateNews(News $news, bool $flush = true): News
    {
        $news = $this->newsRepository->updateNews($news, $flush);

        return $news;
    }

    /**
     * Deletes news from DB
     *
     * @param News $news
     * @param bool $flush
     *
     * @return bool
     */
    public function deleteNews(News $news, bool $flush = true): bool
    {
        $result = $this->newsRepository->deleteNews($news, $flush);

        return $result;
    }

    /**
     * Check if news already exists in DB
     *
     * @param \SimpleXMLElement $item
     *
     * @return bool
     */
    public function isNewsAlreadyInDb(\SimpleXMLElement $item): bool
    {
        $existingNews = $this->newsRepository->findOneBy([
            'createdAt' => \DateTime::createFromFormat('D, d M Y H:i:s O', $item->pubDate)
        ]);

        if($existingNews) return true;

        return false;
    }
}