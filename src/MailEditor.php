<?php

namespace Statikbe\NovaMailEditor;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class MailEditor extends Tool
{
    /**
     * The locales array for the tool.
     *
     * @var array
     */
    public static $locales;

    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('nova-mail-editor', __DIR__.'/../dist/js/tool.js');
        Nova::style('nova-mail-editor', __DIR__.'/../dist/css/tool.css');
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\View\View
     */
    public function renderNavigation()
    {
        return view('nova-mail-editor::navigation');
    }

    /**
     * @param  array  $locales
     */
    public static function setLocales(array $locales)
    {
        static::$locales = $locales;
    }

    /**
     * @return array
     */
    public static function getLocales(): array
    {
        return static::$locales;
    }
}
