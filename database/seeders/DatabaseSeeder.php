<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            PlanSeeder::class,
            PlatformSettingSeeder::class,
            AddonSeeder::class,
            DemoDataSeeder::class,
            DocumentTemplateSeeder::class,
        ]);
    }
}
