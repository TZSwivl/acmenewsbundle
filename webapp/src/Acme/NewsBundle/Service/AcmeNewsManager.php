<?php

namespace Acme\NewsBundle\Service;

use Acme\NewsBundle\Repository\NewsRepository;

class AcmeNewsManager
{
    /**
     * @var NewsRepository
     */
    private $newsRepository;

    /**
     * @var \Memcached
     */
    private $memcached;

    public function __construct(NewsRepository $newsRepository, \Memcached $memcached)
    {
        $this->memcached = $memcached;
        $this->newsRepository = $newsRepository;
    }

    public function getNewsSetForListing($page, $newsPerPage)
    {
        // сервис кеширует данные в мемкеше, получает данные от репозитория
        // также в сервисе есть методы инвалидации кеша этих запросов новостей
        // ключи кеширования хранить в сервисе, но кешировать через доктрину
        // сделать метод инвалидации кеша по ключам (кеша вывода новостей постранично)
        // рандомный вывод новостей тоже кешировать по ключу, который является хешем урла страницы
        // Этот кеш тоже инвалидировать при добавлении новой новости
        // Как массово инвалидировать по неймспейсу?
        // Массово инвалидировать как раз не нужно, чтобы не вызывать racing conditions при одновременном массовом обращении за новыми данными
        // Инвалидировать будем по каждому ключу, в общем методе инвалидации, который будет вызываться при добавлении новости
    }
}