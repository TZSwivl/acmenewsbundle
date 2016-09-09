<?php

namespace Acme\NewsBundle\Command;

use Acme\NewsBundle\Entity\News;
use Acme\NewsBundle\Service\AcmeNewsManagerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportRssCommand extends ContainerAwareCommand
{
    /**
     * @var AcmeNewsManagerInterface
     */
    private $newsManager;

    protected function configure()
    {
        $this
            ->setName('acme:news:import:rss')
            ->setDescription('Imports news from news.liga.net RSS feed')
            ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->newsManager = $this->getContainer()->get('acme_news_manager');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ObjectManager $em */
        try {
            $output->writeln('Получаем rss ленту новостей http://news.liga.net/all/rss.xml');
            $rssXmlObj = simplexml_load_file('http://news.liga.net/all/rss.xml');

            $output->writeln(['Начинаем обработку новостей', '']);

            // Для каждой новости
            foreach($rssXmlObj->channel->item as $item) {
                // Проверяем по заголовку и дате, нет ли новости уже в базе
                if($this->newsManager->isNewsAlreadyInDb($item)) continue;

                // Переходим по урлу полной новости
                if(empty((string)$item->link)) continue;

                $fullNewsHtml = file_get_contents($item->link);

                if(empty($fullNewsHtml)) continue;

                // Получаем полный текст новости (в том числе и картинку)
                // preg_match
                preg_match('|<img id="material-image.*/>|Us', $fullNewsHtml, $imgMatches);
                preg_match('|<div class="text _ga1_on_">(.*)</div>|Us', $fullNewsHtml, $txtMatches);

                if(empty($txtMatches[1])) continue;

                $fullText = $txtMatches[1];

                // Если найден рисунок - добавляем в текст новости
                if(!empty($imgMatches[0])) {
                    $fullText = $imgMatches[0] . $fullText;
                }

                // Заменяем пути в html (в том числе и в картинке, если пути не абсолютные - добавляем ссылку на liga.net)
                $fullText = str_replace(
                    ['src="/', 'href="/', 'width="380" height="230"'],
                    ['src="http://news.liga.net/', 'href="http://news.liga.net/', ''],
                    $fullText
                );

                // Сохраняем в базе данных полный набор новости
                $news = new News();
                $news
                    ->setCreatedAt(\DateTime::createFromFormat('D, d M Y H:i:s O', $item->pubDate))
                    ->setExcerpt('<p>' . $item->title . '</p><p>' . $item->description . '</p>')
                    ->setIsPublished(true)
                    ->setFullText($fullText)
                ;

                $this->newsManager->addNews($news);

                // Выводим информацию на экран о сохранённой новости (Новость "Название" сохранена в БД. Просмотреть новость (ссылка)
                $output->writeln([
                    'Добавлена новость за ' . $item->pubDate . ':',
                    $item->title,
                    ''
                ]);
            }
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }
}