<?php

namespace Acme\NewsBundle\Service;

use Acme\NewsBundle\Entity\News;
use Acme\NewsBundle\Repository\NewsRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class AcmeNewsManager implements AcmeNewsManagerInterface
{
    /**
     * @var NewsRepository
     */
    private $newsRepository;

    /**
     * News per page on HTML list
     *
     * @var int
     */
    private $perPageHtml;

    /**
     * News per page on XML list
     *
     * @var int
     */
    private $perPageXml;

    /**
     * News per supplemental news block on full news page
     *
     * @var int
     */
    private $perPageInBlock;

    public function __construct(
        NewsRepository $newsRepository,
        $perPageHtml,
        $perPageXml,
        $perPageInBlock
    )
    {
        $this->newsRepository = $newsRepository;
        $this->perPageHtml = $perPageHtml;
        $this->perPageXml = $perPageXml;
        $this->perPageInBlock = $perPageInBlock;
    }

    public function getNewsForListing(int $page, int $perPage): Paginator
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

        // Итого. Базово сервис получает данные от репозитория. Декорированный сервис кеширует эти данные по ключу
        // Также в декорированном сервисе при публикации новости кеш очищается
        // Переписать импортер новостей на использование НьюсМенеджера (для инвалиации кеша, в случае необходимости)

        // При публикации новости генерировать наборы нужных кешей
            // постраничные выводы html
            // постраничные выводы xml
            // Отдельные страницы новостей
        // В примечаниях указать, что реализован вариант кеширования для случая очень высоких нагрузок,
            // когда возможны rasing conditions при построении кеша (и не смотря на то, что включен http и другой)
            // Также указать, что если сайт с большим кол-вом контента - то задачи по генерации нужно ставить в очередь,
            // но я этого уже не реализовывал (из-за дефицита времени, главным образом)

        // Разбираем конфиг в файле Extinsion
        // создаем соответствующие параметры и задаем их значения на основе конфига
        // В сервайс.хмл определяем сервис НьюсМенеджера, который создается фабрикой НьюсМенеджерФабрика
            // фабрика принимает на вход параметры "включен_ли_мемкеш" и "айди_сервиса_мемкеша" и как результат
            // создает или чистый (без мемкеша) или декорированный мемкешом НьюсМенеджер
    }

    public function getOneNews(int $newsId): News
    {

    }

    public function getNewsForBlock(int $newsId, int $perBlock): array
    {

    }

    public function addNews(News $news): bool
    {

    }

    public function updateNews(News $news): bool
    {

    }

    public function deleteNews(News $news): bool
    {

    }
}