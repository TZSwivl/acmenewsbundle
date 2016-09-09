<?php

namespace Acme\NewsBundle\Service;

use Acme\NewsBundle\Entity\News;

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

    /**
     * @var array New Cache during cache rebuilding process
     */
    private $newCache = [];

    /**
     * @var bool Flags whether cache rebuild on shutdown already planned
     */
    private $flagRebuildPlanned = false;

    const CACHE_NAMESPACE                   = 'an_';

    const CACHE_KEY_NEWS_FOR_LISTING        = 'nfl_';
    const CACHE_KEY_ONE_NEWS                = 'kon_';
    const CACHE_KEY_SUPPLEMENTAL_NEWS       = 'sn_';
    const CACHE_KEY_LAST_PAGINATION_PAGE    = 'lpp_';
    const CACHE_KEY_NEWS_SIBLINGS           = 'kns_';

    public function __construct(AcmeNewsManagerInterface $newsManager, \Memcached $memcached)
    {
        $this->newsManager = $newsManager;
        $this->memcached = $memcached;
        $this->memcached->setOption(\Memcached::OPT_PREFIX_KEY, self::CACHE_NAMESPACE);
    }

    /**
     * See AcmeNewsManager::getNewsForListing()
     *
     * @param int $page
     * @param int $newsPerPage
     * @param bool $flushMC
     *
     * @return array
     */
    public function getNewsForListing(int $page, int $newsPerPage, $flushMC  = false): array
    {
        $mkey = self::CACHE_KEY_NEWS_FOR_LISTING . $page . '_' . $newsPerPage;
        $set = $this->memcached->get($mkey);

        if(!$set || $flushMC) {
            $set = $this->newsManager->getNewsForListing($page, $newsPerPage);
            $this->memcached->set($mkey, $set);
        }

        return $set;
    }

    /**
     * See AcmeNewsManager::getOneNews()
     *
     * @param int $newsId
     * @param bool $flushMC
     *
     * @return News
     */
    public function getOneNews(int $newsId, $flushMC  = false): News
    {
        $mkey = self::CACHE_KEY_ONE_NEWS . $newsId;
        $news = $this->memcached->get($mkey);

        if(!$news || $flushMC) {
            $news = $this->newsManager->getOneNews($newsId);
            $this->memcached->set($mkey, $news);
        }

        return $news;
    }

    /**
     * See AcmeNewsManager::getSupplementalNews()
     *
     * @param int $newsId
     * @param int $perBlock
     * @param bool $flushMC
     *
     * @return array
     */
    public function getSupplementalNews(int $newsId, int $perBlock, $flushMC  = false): array
    {
        $mkey = self::CACHE_KEY_SUPPLEMENTAL_NEWS . $newsId . '_' . $perBlock;
        $set = $this->memcached->get($mkey);

        if(!$set || $flushMC) {
            $set = $this->newsManager->getSupplementalNews($newsId, $perBlock);
            $this->memcached->set($mkey, $set);
        }

        return $set;
    }

    /**
     * See AcmeNewsManager::getLastPaginationPage()
     *
     * @param int $perPage
     * @param bool $flushMC
     *
     * @return int
     */
    public function getLastPaginationPage(int $perPage, $flushMC  = false): int
    {
        $mkey = self::CACHE_KEY_LAST_PAGINATION_PAGE . $perPage;
        $page = $this->memcached->get($mkey);

        if(!$page || $flushMC) {
            $page = $this->newsManager->getLastPaginationPage($perPage);
            $this->memcached->set($mkey, $page);
        }

        return $page;
    }

    /**
     * See AcmeNewsManager::getNewsSiblings()
     *
     * @param int $newsId
     * @param bool $flushMC
     *
     * @return array
     */
    public function getNewsSiblings(int $newsId, $flushMC  = false): array
    {
        $mkey = self::CACHE_KEY_NEWS_SIBLINGS . $newsId;
        $siblings = $this->memcached->get($mkey);

        if(!$siblings || $flushMC) {
            $siblings = $this->newsManager->getNewsSiblings($newsId);
            $this->memcached->set($mkey, $siblings);
        }

        return $siblings;
    }

    /**
     * See AcmeNewsManager::addNews()
     *
     * @param News $news
     * @param bool $flush
     *
     * @return News
     */
    public function addNews(News $news, bool $flush = true): News
    {
        $result = $this->newsManager->addNews($news, $flush);
        // Очищаем весь кеш
        $this->rebuildAllNewsCache();

        return $result;
    }

    /**
     * See AcmeNewsManager::updateNews()
     *
     * @param News $news
     * @param bool $flush
     *
     * @return News
     */
    public function updateNews(News $news, bool $flush = true): News
    {
        $result = $this->newsManager->updateNews($news, $flush);
        // Очищаем весь кеш
        $this->rebuildAllNewsCache();

        return $result;
    }

    /**
     * See AcmeNewsManager::deleteNews()
     *
     * @param News $news
     * @param bool $flush
     *
     * @return bool
     */
    public function deleteNews(News $news, bool $flush = true): bool
    {
        $result = $this->newsManager->deleteNews($news, $flush);
        // Очищаем весь кеш
        $this->rebuildAllNewsCache();

        return $result;
    }

    /**
     * Public - is bad, yes
     */
    public function rebuildCacheDuringShutdown()
    {
        // getLastPaginationPage
        // getNewsForListing

        // getOneNews
        // getSupplementalNews
        // getNewsSiblings

        /**
         * @ToDo Not implemented yet
         * Для проектов с небольшим кол-вом новостей - можно перестраивать кеш прямо в этом методе
         * Для проектов с большим кол-вом новостей - добавлять задачи по перестройке кеша в очередь
         */
    }

    /**
     * Чтобы можно было вызывать много раз (при массовом добавлении новостей по одной, например)
     */
    private function rebuildAllNewsCache()
    {
        if($this->flagRebuildPlanned === false) {
            register_shutdown_function([$this, 'rebuildCacheDuringShutdown']);
            $this->flagRebuildPlanned = true;
        }
    }

    /**
     * See AcmeNewsManager::isNewsAlreadyInDb()
     *
     * @param \SimpleXMLElement $item
     *
     * @return bool
     */
    public function isNewsAlreadyInDb(\SimpleXMLElement $item): bool
    {
        return $this->newsManager->isNewsAlreadyInDb($item);
    }
}