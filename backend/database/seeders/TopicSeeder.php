<?php

namespace Database\Seeders;

use App\Models\Topic;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        $topics = [
            ['name' => 'Desarrollo web', 'description' => 'Frontend, frameworks, estilos y rendimiento en aplicaciones web modernas.'],
            ['name' => 'Bases de datos', 'description' => 'Modelado, normalización, optimización de consultas y datos para analítica.'],
            ['name' => 'Backend', 'description' => 'APIs, autenticación, arquitectura de servicios y automatización del lado del servidor.'],
        ];

        foreach ($topics as $topic) {
            Topic::firstOrCreate(['name' => $topic['name']], ['description' => $topic['description']]);
        }
    }
}
