Acme News Project
========================

Данный проект - результат выполнения следующего тестового задания:

>     Задание N1
> 
>     Сделать на Symfony 2 небольшое приложение, состоящее из одного бандла Новости (AcmeNewsBundle), который должен уметь:
>     1. Выводить список новостей с постраничной навигацией по ссылке вида /news?page=2 в формате HTML. При этом верстка должна наиболее полно отражать семантическое представление данных.
>     2. Тоже самое, но в формате XML при запросе по ссылке вида /news.xml?page=2. При этом получаемый XML-файл должен быть валидным, иметь корректные заголовки и структуру вида:
>     <list>
>         <item id="идентификатор новости" date="дата новости">
>                 <announce>Краткий текст новости</announce>
>             <description>Полный текст новости</description>
>         </item>
>         <item>
>             ....
>         </item>
>         </list>
>     3. Выводить полнотекст новости в HTML-виде по ссылке вида /news/123, где 123 - ID новости. Под текстом новости должен быть блок со списком любых других новостей, который может присутствовать и на других страницах сайта.
> 
>     В процессе выполнения задания необходимо показать знания и навыки владения OOP, ORM Doctrine 2 и Symfony 2 в целом.
> 
>     Исходные данные - таблица с полями:
>         1. Идентификатор новости
>         2. Дата новости
>         3. Опубликовано да/нет
>         4. Краткий текст новости
>         5. Полный текст новости
>      
>     Решение необходимо представить в виде готового проекта, опубликованного на github, содержащего:
>        1. сущности, репозитории, сервисы и т.д.
>        2. бандл AcmeNewsBundle
>        3. само приложение
>        4. дамп БД
>        5. в readme проекта должны быть описаны шаги развертывания и любые ваши комментарии

Ознакомиться с работой приложения можно по адресу http://swivl.this.biz/

Структура проекта
--------------

Основная функциональность сосредоточена в бандле AcmeNewsBundle.

На уровне приложения хранятся и используются, главным образом:

  * конфигурационные файлы приложения app/config;

  * миграции БД;

  * настройки PHPUnit;

Установка
--------------

### Установка для локальной  разработки с использованием Vagrant

1. Создать произвольную папку на компьютере, где будут храниться исходные коды
2. Перейти в созданную папку, выполнить 
```
git clone https://github.com/TZSwivl/acmenewsbundle.git .
```

3. Прописать в /etc/hosts (или в аналоге в Windows) запись
```
192.168.33.10 tzswivl.com www.tzswivl.com
```
4. Запустить виртуальную машину командой
```
vagrant up
```
5. Зайти в консоль виртуальной машины и залить дампы в уже созданные Vagrant'ом mysql базы данных:
```
# Для sss пароля не потребуется
vagrant ssh
mysql -u dbuser -pveryverystrongandlongpassword symfony < /home/ubuntu/www/app/DbMigrations/1.sql 
mysql -u dbuser -pveryverystrongandlongpassword symfony < /home/ubuntu/www/app/DbMigrations/2.sql 
```
После выполнения указанных действий сайт должен быть доступен в браузере по адресу http://tzswivl.com/.

###Установка на произвольный хост

####Требования к серверу:
* web-server (nginx, apache)
* mysql-client, mysql-server
* php >= 7.0 (+ php7.0-xml php7.0-intl php7.0-mysql, php-memcached)
* memcached
* unzip

1. В root папке вашего сервера выполнить
```
git clone https://github.com/TZSwivl/acmenewsbundle.git .
```
2. Создать пустые базы данных symfony, symfony_test
3. Залить в них дампы 1.sql и 2.sql из папки app/DbMigrations
    
Настройка
--------------
1. Если создавали БД и пользователей вручую (а не доверили это Vagrant'у) - нужно прописать эти данные  в файлах конфигурации app/config/parameters.yml и app/config/config_test.yml  
2. В разделе acme_news в файле конфигурации app/config/config.php можно исправить настройки бандла AcmeNewsBundle, заданные по умолчанию, если они вас не устраивают.
В частности, количество новостей на страницах html и xml лент новостей, в блоке дополнительных новостей, настройки кеширования.
3. Можно импортировать свежие новости в БД из rss ленты http://news.liga.net/all/rss.xml выполнив консульную команду
```
php bin/console acme:news:import:rss
```
    
Тестирование
--------------
Запуск тестов нужно выполнять с помощью команды
```
php phpunit.phar
```
Если запускать тесты отдельной утилитой phpunit, могут выскакивать ошибки с жалобами на обращение к необъявленным методам в классах тестов.

Примечания
--------------
