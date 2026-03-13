<?php

namespace Database\Seeders;

use App\Models\Work;
use Illuminate\Database\Seeder;

class WorkSeeder extends Seeder
{
    public function run(): void
    {
        $works = [
            [
                'title' => 'SKALD COFFEE',
                'description' => 'Design and development of a one-page website for the Viking-themed café Skáld Coffee in Louny – dark Nordic design, custom line-art illustrations, and implementation in a Laravel/Tailwind stack.',
                'category' => 'web',
                'category_label' => 'Web Application',
                'layout' => 'pc--featured',
                'tags' => ['Laravel', 'Tailwind CSS', 'JS'],
                'image' => 'skald.png',
                'link' => 'https://www.kavarnalouny.cz/',
                'year' => 2026,
                'sort_order' => 1,
            ],
            [
                'title' => 'Graupner — Instalatér Louny',
                'description' => 'A business website for a plumber in Louny built with Laravel and Statamic, focused on clear service presentation and easy contact.',
                'category' => 'web',
                'category_label' => 'Web Application',
                'layout' => 'pc--tall',
                'tags' => ['Laravel', 'Statamic', 'Frontend', 'Backend'],
                'image' => 'graupner.png',
                'link' => 'https://www.louny-instalater.cz/',
                'year' => 2026,
                'sort_order' => 2,
            ],
            [
                'title' => 'BEAUTY STUDIO PeBe',
                'description' => 'Design and implementation of frontend UI/UX for the PEBE beauty studio website, focusing on elegant visual presentation of services, intuitive navigation, and responsive design.',
                'category' => 'ui',
                'category_label' => 'UI / UX and Frontend',
                'layout' => 'pc--wide',
                'tags' => ['Frontend', 'Prototype', 'Design System'],
                'image' => 'studio-pebe.webp',
                'link' => 'https://www.studio-pebe.cz/',
                'year' => 2025,
                'sort_order' => 3,
            ],
            [
                'title' => 'SKALD COFFEE ILLUSTRATIONS',
                'description' => 'Illustrations, design, and GIF creation for the SKALD COFFEE website.',
                'category' => 'graphic',
                'category_label' => 'Graphic Design',
                'layout' => 'pc--sq',
                'tags' => ['Artwork', 'Print'],
                'image' => 'skald-ilustrations.png',
                'link' => null,
                'year' => 2026,
                'sort_order' => 4,
            ],
            [
                'title' => 'HOKEJOVÁ ŠKOLA RADKA GARDONĚ',
                'description' => 'Design of web page in Figma.',
                'category' => 'ui',
                'category_label' => 'UX / UI Design',
                'layout' => 'pc--wide2',
                'tags' => ['Figma', 'UX/UI'],
                'image' => 'hokejova-skola.png',
                'link' => null,
                'year' => 2022,
                'sort_order' => 5,
            ],
            [
                'title' => 'Med Květový',
                'description' => 'Mark, type, palette, stationery.',
                'category' => 'brand',
                'category_label' => 'Brand Identity',
                'layout' => 'pc--sq',
                'tags' => ['Logo', 'Typography', 'Print'],
                'image' => 'med-kvetovy.png',
                'link' => null,
                'year' => 2024,
                'sort_order' => 6,
            ],
        ];

        foreach ($works as $data) {
            Work::query()->updateOrCreate(
                ['title' => $data['title']],
                $data,
            );
        }
    }
}
