# Laravel Nova Mail Editor

The Laravel Nova Mail Editor allows you to easily edit the content of your transactional mails.

This package provides a Nova tool for the Laravel Mail Template Engine. Check the documentation of the [Laravel Mail Template Engine](https://github.com/statikbe/laravel-mail-template-engine) 
for more info.

## Features

- Add and edit mail templates in Nova
- Translate mail templates
- Add variable data to mails

For the entire list of features take a look at the documentation of the [Laravel Mail Template Engine](https://github.com/statikbe/laravel-mail-template-engine).

## Installation

The package can be installed through Composer.

```
composer require statikbe/laravel-nova-mail-editor
```

Next enable the tool in Nova. 
Go to `app/Providers/NovaServiceProvider.php` and add the TranslationManager to the tools.
```php
use Statikbe\NovaMailEditor\MailEditor;

    public function tools()
    {
        return [
            new MailEditor,
        ];
    }

```


## Configuration

### Supported locales
There are two ways to change the supported locales.
 
#### Option 1
Publish the config file with the command below and configure it with your supported locales.

```shell
php artisan vendor:publish --tag=nova-mail-editor
```

E.g.
```php
    /*
    |--------------------------------------------------------------------------
    | Mail editor supported locales
    |--------------------------------------------------------------------------
    |
    | The application locale determines the possible locales that can be used.
    | You are free to fill this array with any of the locales which will be
    | supported by the editor.
    |
    |
    */
    'supported_locales' => [
        'en',
    ],
```

#### Option 2
If your application already has a config that declares your locales then you are able to set the supported locales in 
any service provider. Create a new one or use the `app/Providers/AppServiceProvider.php` and set the supported locales 
as an array in the boot function as follows:

```php
use Statikbe\NovaMailEditor\MailEditor;

public function boot()
{
    $locales = ['en', 'nl']; //Or any other way of retrieving the locales;
    MailEditor::setLocales($locales);
}
```

### Mail template config
More information about the mail template config can be found in the base package: [Laravel Mail Template Engine](https://github.com/statikbe/laravel-mail-template-engine).


## License
The MIT License (MIT). Please see [license file](LICENSE.md) for more information.
