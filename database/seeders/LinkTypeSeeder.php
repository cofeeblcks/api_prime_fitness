<?php

namespace Database\Seeders;

use App\Models\LinkType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LinkTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        LinkType::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $linkTypes = [
            [
                'id' => 1,
                'name' => 'WhatsApp',
                'icon' => 'whatsapp',
            ],
            [
                'id' => 2,
                'name' => 'Facebook',
                'icon' => 'facebook-01',
            ],
            [
                'id' => 3,
                'name' => 'Instagram',
                'icon' => 'instagram',
            ],
            [
                'id' => 4,
                'name' => 'X',
                'icon' => 'new-twitter',
            ],
            [
                'id' => 5,
                'name' => 'Youtube',
                'icon' => 'youtube',
            ],
        ];

        foreach ($linkTypes as $linkType) {
            LinkType::create($linkType);
        }
    }
}
