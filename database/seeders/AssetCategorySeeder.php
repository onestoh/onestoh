<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssetCategory;

class AssetCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Passenger Cars', 'slug' => 'passenger-cars', 'icon' => 'fa-car', 'type' => 'vehicle'],
            ['name' => 'SUVs & 4x4', 'slug' => 'suvs-4x4', 'icon' => 'fa-truck-monster', 'type' => 'vehicle'],
            ['name' => 'Vans & Minibuses', 'slug' => 'vans-minibuses', 'icon' => 'fa-shuttle-van', 'type' => 'vehicle'],
            ['name' => 'Pickups & Light Trucks', 'slug' => 'pickups-light-trucks', 'icon' => 'fa-truck-pickup', 'type' => 'vehicle'],
            ['name' => 'Heavy Trucks & Trailers', 'slug' => 'heavy-trucks-trailers', 'icon' => 'fa-truck', 'type' => 'vehicle'],
            ['name' => 'Excavators & Loaders', 'slug' => 'excavators-loaders', 'icon' => 'fa-digging', 'type' => 'machinery'],
            ['name' => 'Tractors & Farm Equipment', 'slug' => 'tractors-farm-equipment', 'icon' => 'fa-tractor', 'type' => 'machinery'],
            ['name' => 'Cranes & Lifting Equipment', 'slug' => 'cranes-lifting', 'icon' => 'fa-cog', 'type' => 'machinery'],
            ['name' => 'Generators & Power Equipment', 'slug' => 'generators-power', 'icon' => 'fa-bolt', 'type' => 'machinery'],
            ['name' => 'Compactors & Road Equipment', 'slug' => 'compactors-road', 'icon' => 'fa-road', 'type' => 'machinery'],
            ['name' => 'Motorcycles & Tuk-tuks', 'slug' => 'motorcycles-tuk-tuks', 'icon' => 'fa-motorcycle', 'type' => 'vehicle'],
            ['name' => 'Special Equipment', 'slug' => 'special-equipment', 'icon' => 'fa-tools', 'type' => 'machinery'],
        ];

        foreach ($categories as $category) {
            AssetCategory::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
