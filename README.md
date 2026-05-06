# BaksDev Product Category

[![Version](https://img.shields.io/badge/version-7.4.8-blue)](https://github.com/baks-dev/products-category/releases)
![php 8.4+](https://img.shields.io/badge/php-min%208.4-red.svg)
[![packagist](https://img.shields.io/badge/packagist-green)](https://packagist.org/packages/baks-dev/products-category)

Модуль категорий продукции

## Установка

``` bash
$ composer require baks-dev/products-category
```

## Дополнительно

Установка конфигурации и файловых ресурсов:

``` bash
$ php bin/console baks:assets:install
```

Добавить директорию и установить права для загрузки обложек категорий:

``` bash
$ sudo mkdir <path_to_project>/public/upload/product_category_cover
$ sudo sudo chmod 773 <path_to_project>/public/upload/product_category_cover
``` 

Изменения в схеме базы данных с помощью миграции

``` bash
$ php bin/console doctrine:migrations:diff
$ php bin/console doctrine:migrations:migrate
```

## Тестирование

``` bash
$ php bin/phpunit --group=products-category
```

## Лицензия ![License](https://img.shields.io/badge/MIT-green)

The MIT License (MIT). Обратитесь к [Файлу лицензии](LICENSE.md) за дополнительной информацией.