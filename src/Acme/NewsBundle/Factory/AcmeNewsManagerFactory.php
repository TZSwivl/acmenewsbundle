<?php

namespace Acme\NewsBundle\Factory;

use Acme\NewsBundle\Repository\NewsRepository;
use Acme\NewsBundle\Service\AcmeNewsManager;
use Acme\NewsBundle\Service\AcmeNewsManagerInterface;
use Acme\NewsBundle\Service\AcmeNewsManagerMemcachedDecorator;

final class AcmeNewsManagerFactory
{
    /**
     * Фабричный метод для создания сервиса AcmeNewsManager
     *
     * В зависимости от настроек acme_news.memcached в конфиге возвращает или простой или кеширующий сервис
     *
     * @param NewsRepository $newsRepository
     * @param bool $memcacheEnabled
     * @param \Memcached|null $memcached
     *
     * @return AcmeNewsManagerInterface Acme News Manager
     * 
     * @throws \Exception
     */
    public static function createNewsManager(
        NewsRepository $newsRepository,
        bool $memcacheEnabled,
        \Memcached $memcached = null
    )
    {
        // Создаем NewsManager с основным функционалом
        $newsManager = new AcmeNewsManager(
            $newsRepository
        );

        // Если в настройках бандла включен memcached, добавляем кеширование
        if($memcacheEnabled) {

            if(!($memcached instanceof \Memcached)) {
                throw new \Exception('AcmeNewsManagerFactory must get \Memcached instance!');
            }

            $newsManager = new AcmeNewsManagerMemcachedDecorator($newsManager, $memcached);
        }

        return $newsManager;
    }
}