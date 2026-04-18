<?php

namespace App\Helpers;

use App\Models\Product;
use App\Models\Setting;

class WhatsAppHelper
{
    public static function generateOrderUrl(Product $product): string
    {
        $phone = Setting::get('whatsapp_number', '94777626013');
        $template = Setting::get('whatsapp_message_template',
            "Hello Hamdha,\n\nI'm interested in this product:\n• Model: {model}\n• Name: {name}\n• Price: Rs. {price}\n• Fabric: {fabric}\n• Link: {url}\n\nPlease confirm availability."
        );

        $message = str_replace(
            ['{model}', '{name}', '{price}', '{fabric}', '{url}'],
            [
                $product->model_number,
                $product->name,
                number_format($product->discount_price ?? $product->price),
                $product->fabric?->name ?? 'N/A',
                route('products.show', $product->slug),
            ],
            $template
        );

        return 'https://wa.me/'.$phone.'?text='.urlencode($message);
    }
}
