<?php

namespace App\Helpers;

class AppUrlHelper
{
    /**
     * Get URL for any app
     */
    public static function appUrl($app)
    {
        return config("app_urls.apps.{$app}.url");
    }

    /**
     * Get login URL for any app
     */
    public static function loginUrl($app = 'administration')
    {
        return config("app_urls.apps.{$app}.login");
    }

    /**
     * Get logout URL for any app
     */
    public static function logoutUrl($app = 'administration')
    {
        return config("app_urls.apps.{$app}.logout");
    }

    /**
     * Get dashboard URL for any app
     */
    public static function dashboardUrl($app = 'administration')
    {
        return config("app_urls.apps.{$app}.dashboard");
    }

    /**
     * Get all app URLs
     */
    public static function allApps()
    {
        return config('app_urls.apps', []);
    }

    /**
     * Check if URL belongs to an app
     */
    public static function belongsToApp($url, $app)
    {
        $appUrl = config("app_urls.apps.{$app}.url");
        return str_contains($url, parse_url($appUrl, PHP_URL_HOST));
    }

    /**
     * Get app name from URL
     */
    public static function getAppFromUrl($url)
    {
        foreach (config('app_urls.apps', []) as $app => $config) {
            if (str_contains($url, parse_url($config['url'], PHP_URL_HOST))) {
                return $app;
            }
        }
        return 'administration';
    }
}
