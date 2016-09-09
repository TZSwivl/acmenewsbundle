<?php

namespace Acme\NewsBundle\Controller;

use Acme\NewsBundle\Service\AcmeNewsManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ViewController extends Controller
{
    /**
     * Displays a news listing page
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request): Response
    {
        $page = $request->query->get('page', 1);
        $format = $request->getRequestFormat();

        switch ($format) {
            case 'xml':
                $newsPerPage = $this->getParameter('acme_news.news_per_page.xml');
                $template = 'AcmeNewsBundle:View:list.xml.twig';
                break;
            default:
                $newsPerPage = $this->getParameter('acme_news.news_per_page.html');
                $template = 'AcmeNewsBundle:View:list.html.twig';
                break;
        }

        // Получаем из сервиса новости с указанной страницей
        $newsEntitySet = $this->getNewsManager()->getNewsForListing($page, $newsPerPage);

        // В зависимости от запрошенного формата страницы рендерим темплейт нужного формата (html | xml)
        return $this->render(
            $template,
            [
                'newsEntitySet' => $newsEntitySet,
                'page' => $page,
                'perPage' => $newsPerPage
            ]
        );
    }

    /**
     * Displays a full news page
     *
     * @param int $newsId
     *
     * @return Response
     */
    public function fullNewsAction(int $newsId): Response
    {
        $news = $this->getNewsManager()->getOneNews($newsId);

        if(!$news) throw $this->createNotFoundException();

        // Id's for prev & next pages
        $siblings = $this->getNewsManager()->getNewsSiblings($newsId);

        return $this->render(
            'AcmeNewsBundle:View:full_news.html.twig',
            [
                'news' => $news,
                'prevId' => $siblings['prev'],
                'nextId' => $siblings['next'],
            ]
        );
    }

    /**
     * Displays a pagination block on news listing pages
     *
     * @param int $page
     * @param int $perPage
     *
     * @return Response
     */
    public function listPaginationAction(int $page, int $perPage): Response
    {
        $currentPage = $page;
        $lastPage = $this->getNewsManager()->getLastPaginationPage($perPage);
        $paginationTail = min(3, floor($lastPage/2)); // tail of pagination set in both sides

        // Calculate correct range of pagination page indexes
        if($currentPage <= ($paginationTail + 1)) {
            $rangeFirst = 1;
            $rangeLast = min($lastPage, $paginationTail * 2 + 1);
        } elseif ($currentPage > $lastPage - ($paginationTail + 1)) {
            $rangeFirst = max(1, $lastPage - ($paginationTail * 2));
            $rangeLast = $lastPage;
        } else {
            $rangeFirst = $currentPage - $paginationTail;
            $rangeLast = $currentPage + $paginationTail;
        }

        $pagesRange = range($rangeFirst, $rangeLast, 1);

        return $this->render(
            'AcmeNewsBundle:View:inc/list_pagination.html.twig',
            [
                'currentPage' => $currentPage,
                'lastPage' => $lastPage,
                'pagesRange' => $pagesRange
            ]
        );
    }

    /**
     * Displays block of supplemental news
     *
     * @param int $newsId Current news
     *
     * @return Response
     */
    public function supplementalNewsAction(int $newsId): Response
    {
        $newsSet = $this->getNewsManager()->getSupplementalNews(
            $newsId,
            $this->getParameter('acme_news.news_per_page.in_block')
        );

        return $this->render(
            'AcmeNewsBundle:View:inc/supplemental_news.html.twig',
            [
                'newsSet' => $newsSet
            ]
        );
    }

    private function getNewsManager(): AcmeNewsManagerInterface
    {
        return $this->get('acme_news_manager');
    }
}