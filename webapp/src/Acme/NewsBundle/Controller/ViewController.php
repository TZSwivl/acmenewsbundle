<?php

namespace Acme\NewsBundle\Controller;

use Acme\NewsBundle\Service\AcmeNewsManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ViewController extends Controller
{
    /**
     * @var AcmeNewsManager
     */
    private $newsManager;

    public function __construct()
    {
        $this->newsManager = $this->get('acme_news_manager');
    }

    /**
     * Display news listing page
     *
     * @param Request $request
     * @param string $_format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $format = $request->getRequestFormat();

        switch ($format) {
            case 'xml':
                $newsPerPage = $this->getParameter('news_per_xml_page');
                $template = 'view/list_xml.twig';
                break;
            default:
                $newsPerPage = $this->getParameter('news_per_html_page');
                $template = 'view/list_html.twig';
                break;
        }

        // Получаем из сервиса новости с указанной страницей
        $newsEntitySet = $this->newsManager->getNewsSetForListing($page, $newsPerPage);

        // В зависимости от запрошенного формата страницы рендерим темплейт нужного формата (html | xml)
        return $this->render($template, ['newsEntitySet' => $newsEntitySet]);
    }
}