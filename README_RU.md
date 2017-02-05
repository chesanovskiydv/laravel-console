<p align="right">
<a href="README.md">Описание на английском</a> | Описание на русском 
</p>

# Laravel 5 Console


[![Latest Stable Version][ico-stable-version]][link-stable-packagist]
[![Latest Unstable Version][ico-unstable-version]][link-unstable-packagist]
[![License][ico-license]](LICENSE.md)

Этот пакет содержит набор полезных консольных команд для Laravel.
 
### Содержание

- [Установка](#Установка)
- [Настройка в Laravel](#Настройка-в-laravel)
- [Список команд](#Список-команд)
    - [ComposerUpdate](#composerupdate)
- [Лицензия](#Лицензия)

### Установка

Установите этот пакет с помощью composer используя следующую команду:

```bash
composer require bwt-team/laravel-console
```

### Настройка в Laravel

После обновления composer добавьте service provider в массив `providers` в `config/app.php`. 

```php
BwtTeam\LaravelConsole\Commands\ConsoleServiceProvider::class
```

Этот service provider предоставит возможность опубликовать конфигурационный файл, чтоб изменить настройки консольных команд.

```bash
php artisan vendor:publish --provider="BwtTeam\LaravelConsole\Commands\ConsoleServiceProvider" --tag=config
```

Нужные консольные команды необходимо зарегистрировать в файле `app/Console/Kernel.php`.

```php
protected $commands = [
    //commands
    \BwtTeam\LaravelConsole\Commands\ComposerUpdate::class
];
```

### Список команд

#### ComposerUpdate

Эта команда позволяет выполнять ларавелевские команды в зависимости от текущего окружения приложения. Например, эту команду можно использовать чтоб не генерировать вспомогательные файлы для ide на продакшен сервере.
Набор команд для каждого окружения можно установить в конфигурационном файле.

### Лицензия

Этот пакет использует лицензию [MIT](LICENSE.md).

[ico-stable-version]: https://poser.pugx.org/bwt-team/laravel-console/v/stable?format=flat-square
[ico-unstable-version]: https://poser.pugx.org/bwt-team/laravel-console/v/unstable?format=flat-square
[ico-license]: https://poser.pugx.org/bwt-team/laravel-console/license?format=flat-square

[link-stable-packagist]: https://packagist.org/packages/bwt-team/laravel-console
[link-unstable-packagist]: https://packagist.org/packages/bwt-team/laravel-console#dev-develop