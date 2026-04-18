<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $navCategories = cache()->remember('nav_categories', 3600, function () {
                return Category::topLevel()
                    ->visible()
                    ->ordered()
                    ->with(['children' => fn ($q) => $q->visible()->ordered()])
                    ->get();
            });

            $siteSettings = cache()->remember('site_settings_global', 3600, function () {
                return [
                    'announcement_bar_items' => Setting::getJson('announcement_bar_items'),
                    'contact_phone' => Setting::get('contact_phone'),
                    'social_instagram' => Setting::get('social_instagram'),
                    'social_facebook' => Setting::get('social_facebook'),
                    'social_tiktok' => Setting::get('social_tiktok'),
                    'footer_tagline' => Setting::get('footer_tagline'),
                    'footer_info_links' => Setting::getJson('footer_info_links'),
                    'footer_customer_care_links' => Setting::getJson('footer_customer_care_links'),
                    'whatsapp_number' => Setting::get('whatsapp_number'),
                ];
            });

            $view->with('navCategories', $navCategories);
            $view->with('siteSettings', $siteSettings);
        });
    }
}
