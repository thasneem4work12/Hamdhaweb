<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Fabric;
use App\Models\PriceBucket;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SizeChart;
use App\Services\ImageService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class DemoCatalogSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['products', 'products/thumbs', 'size-charts', 'categories'] as $dir) {
            $path = storage_path("app/public/{$dir}");
            if (! is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }

        $this->seedFabrics();
        $this->seedPriceBuckets();
        $abayaChart = $this->seedSizeChart('Abaya Size Guide', '#3d2b2b');
        $hijabChart = $this->seedSizeChart('Hijab Size Guide', '#4a3f3f');

        $samples = [
            'plain-abaya' => [
                ['name' => 'Classic Plain Abaya', 'price' => 12500],
                ['name' => 'Minimal Linen Abaya', 'price' => 14200],
            ],
            'embroidery-abaya' => [
                ['name' => 'Floral Embroidery Abaya', 'price' => 18500],
                ['name' => 'Pearl Detail Abaya', 'price' => 21000],
            ],
            'beads-abaya' => [
                ['name' => 'Crystal Beaded Abaya', 'price' => 22500],
            ],
            'wedding-abaya' => [
                ['name' => 'Luxury Starlit Bloom Embellished Open Abaya', 'price' => 12750, 'model_number' => 'HM-0008'],
                ['name' => 'Premium Textured Dewdrop Bloom Open Abaya', 'price' => 10250, 'model_number' => 'HM-0009'],
            ],
            'cotton-hijab' => [
                ['name' => 'Soft Cotton Hijab', 'price' => 3200],
            ],
            'georgette-hijab' => [
                ['name' => 'Flow Georgette Hijab', 'price' => 3800],
            ],
        ];

        $fabricIds = Fabric::pluck('id', 'slug');
        $variant = 0;

        foreach ($samples as $categorySlug => $products) {
            $category = Category::where('slug', $categorySlug)->first();
            if (! $category) {
                continue;
            }

            $isAbaya = str_contains($categorySlug, 'abaya');
            $chart = $isAbaya ? $abayaChart : $hijabChart;
            $fabricSlug = $isAbaya ? 'nida' : 'chiffon';

            foreach ($products as $data) {
                $slug = Str::slug($data['name']);
                $product = Product::firstOrNew(['slug' => $slug]);
                $product->fill([
                    'name' => $data['name'],
                    'price' => $data['price'],
                    'description' => '<p>Premium custom-made piece. Contact us on WhatsApp to confirm fabric, colour, and measurements.</p>',
                    'fabric_id' => $fabricIds[$fabricSlug] ?? $fabricIds->first(),
                    'colors' => 'Black, Navy, Brown',
                    'is_visible' => true,
                    'is_featured' => $variant % 3 === 0,
                ]);
                if (! $product->exists && ! empty($data['model_number'])) {
                    $product->model_number = $data['model_number'];
                }
                $product->save();

                $product->categories()->sync([
                    $category->id => ['is_primary' => true],
                    $category->parent_id => ['is_primary' => false],
                ]);

                if ($chart) {
                    $product->sizeCharts()->sync([$chart->id => ['sort_order' => 0]]);
                }

                $this->ensureProductImages($product, $data['name'], $variant++);
            }
        }

        cache()->forget('featured_products');
        cache()->forget('new_arrivals');
        cache()->forget('nav_categories');
        cache()->forget('all_categories_filter');
    }

    protected function seedFabrics(): void
    {
        $fabrics = [
            ['name' => 'Nida', 'slug' => 'nida', 'sort_order' => 1],
            ['name' => 'Crepe', 'slug' => 'crepe', 'sort_order' => 2],
            ['name' => 'Chiffon', 'slug' => 'chiffon', 'sort_order' => 3],
            ['name' => 'Jersey', 'slug' => 'jersey', 'sort_order' => 4],
        ];

        foreach ($fabrics as $fabric) {
            Fabric::updateOrCreate(['slug' => $fabric['slug']], $fabric);
        }
    }

    protected function seedPriceBuckets(): void
    {
        $buckets = [
            ['label' => 'Under Rs. 10,000', 'min_price' => 0, 'max_price' => 9999, 'sort_order' => 1],
            ['label' => 'Rs. 10,000 – 20,000', 'min_price' => 10000, 'max_price' => 20000, 'sort_order' => 2],
            ['label' => 'Above Rs. 20,000', 'min_price' => 20001, 'max_price' => null, 'sort_order' => 3],
        ];

        foreach ($buckets as $bucket) {
            PriceBucket::updateOrCreate(
                ['label' => $bucket['label']],
                $bucket
            );
        }
    }

    protected function seedSizeChart(string $name, string $hex): ?SizeChart
    {
        $path = $this->createPlaceholderAsset($name, $hex, 'size-charts');

        return SizeChart::updateOrCreate(
            ['name' => $name],
            ['image_path' => $path, 'sort_order' => 0]
        );
    }

    protected function ensureProductImages(Product $product, string $label, int $variant): void
    {
        if ($product->cover_image_path && $product->images()->count() >= 2) {
            return;
        }

        $product->images()->each(function (ProductImage $image) {
            app(ImageService::class)->deleteImage($image->image_path);
            app(ImageService::class)->deleteImage($image->thumbnail_path);
            $image->delete();
        });

        if ($product->cover_image_path) {
            app(ImageService::class)->deleteImage($product->cover_image_path);
        }
        if ($product->cover_thumbnail_path) {
            app(ImageService::class)->deleteImage($product->cover_thumbnail_path);
        }

        $palette = ['#3d2b2b', '#5c4a4a', '#2a2424', '#6b5252'];
        $bg = $palette[$variant % count($palette)];

        $cover = $this->processPlaceholder($label, $bg);
        $hover = $this->processPlaceholder($label.' — detail', '#4a3f3f');

        $product->update([
            'cover_image_path' => $cover['image_path'],
            'cover_thumbnail_path' => $cover['thumbnail_path'],
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => $cover['image_path'],
            'thumbnail_path' => $cover['thumbnail_path'],
            'sort_order' => 0,
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => $hover['image_path'],
            'thumbnail_path' => $hover['thumbnail_path'],
            'sort_order' => 1,
        ]);
    }

    /**
     * @return array{image_path: string, thumbnail_path: string}
     */
    protected function processPlaceholder(string $label, string $bgHex): array
    {
        $uuid = Str::uuid();
        $fullPath = "products/{$uuid}.webp";
        $thumbPath = "products/thumbs/{$uuid}.webp";

        $image = Image::create(1080, 1350)->fill(ltrim($bgHex, '#'));
        $image->toWebp(quality: 85)->save(storage_path("app/public/{$fullPath}"));

        $thumb = Image::read(storage_path("app/public/{$fullPath}"));
        $thumb->scaleDown(540, 675);
        $thumb->toWebp(quality: 80)->save(storage_path("app/public/{$thumbPath}"));

        return [
            'image_path' => $fullPath,
            'thumbnail_path' => $thumbPath,
        ];
    }

    protected function createPlaceholderAsset(string $label, string $hex, string $dir): string
    {
        $uuid = Str::uuid();
        $tempPath = storage_path("app/temp-sc-{$uuid}.png");
        $image = Image::create(800, 1000)->fill(ltrim($hex, '#'));
        $image->save($tempPath);

        $out = "{$dir}/{$uuid}.webp";
        Image::read($tempPath)->toWebp(quality: 85)->save(storage_path("app/public/{$out}"));

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        return $out;
    }
}
