<?php

namespace Database\Seeders;

use App\Models\Resource;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('email', 'admin@turing-ia.com')->firstOrFail();

        $resources = [
            [
                'topic' => 'Desarrollo web',
                'title' => 'Introducción a Vue 3 y la Composition API',
                'excerpt' => 'Aprende cómo la Composition API organiza la lógica de tus componentes y por qué facilita reutilizar código en proyectos grandes.',
                'content' => [
                    'Vue 3 introdujo la Composition API como una forma de agrupar la lógica por funcionalidad y no por tipo de opción. En lugar de repartir el estado entre data, methods y computed, se declara todo junto dentro de setup o con la sintaxis script setup.',
                    'Con ref y reactive se define el estado reactivo, con computed los valores derivados y con watch los efectos secundarios. Además, la lógica se puede extraer a funciones llamadas composables, que se reutilizan entre componentes sin mixins ni jerarquías complejas.',
                ],
                'days' => 1,
                'cover' => 'introduccion-vue-3.jpg',
            ],
            [
                'topic' => 'Desarrollo web',
                'title' => 'Tailwind CSS: diseño responsive sin salir del HTML',
                'excerpt' => 'Un recorrido por las clases utilitarias de Tailwind y los breakpoints para construir interfaces adaptables rápidamente.',
                'content' => [
                    'Tailwind CSS propone un enfoque utility-first: en lugar de escribir hojas de estilo propias, se combinan clases pequeñas directamente en el marcado. Esto reduce el cambio de contexto y mantiene los estilos junto al componente que los usa.',
                    'Para el diseño responsive se utilizan prefijos como sm, md y lg. Una cuadrícula que muestra una columna en móvil, dos en tablet y tres en escritorio se resuelve con tres clases, sin media queries manuales.',
                ],
                'days' => 2,
                'cover' => 'tailwind-responsive.jpg',
            ],
            [
                'topic' => 'Desarrollo web',
                'title' => 'Buenas prácticas de rendimiento en aplicaciones web modernas',
                'excerpt' => 'Carga diferida, optimización de imágenes y reducción de peticiones: pasos concretos para mejorar la velocidad percibida.',
                'content' => [
                    'El rendimiento web se mide por la experiencia del usuario: cuánto tarda en ver contenido y cuánto en poder interactuar. Métricas como LCP, INP y CLS ayudan a cuantificarlo y a detectar cuellos de botella.',
                    'Las mejoras más rentables suelen ser servir imágenes en formatos modernos y con tamaños adecuados, dividir el código en fragmentos que se cargan bajo demanda y evitar peticiones innecesarias a la API usando paginación y caché.',
                ],
                'days' => 3,
                'cover' => 'rendimiento-web.jpg',
            ],
            [
                'topic' => 'Bases de datos',
                'title' => 'Normalización de bases de datos: de 1FN a 3FN',
                'excerpt' => 'Entiende las tres primeras formas normales con ejemplos y descubre cómo evitar redundancia y anomalías de actualización.',
                'content' => [
                    'Normalizar consiste en organizar las tablas para eliminar redundancia y dependencias inconvenientes. La primera forma normal exige valores atómicos y sin grupos repetidos; la segunda, que cada atributo dependa de toda la clave primaria.',
                    'La tercera forma normal pide que los atributos no clave no dependan de otros atributos no clave. Separar temas, autores y recursos en tablas relacionadas por claves foráneas es un ejemplo cotidiano de 3FN.',
                ],
                'days' => 4,
                'cover' => 'normalizacion-bd.jpg',
            ],
            [
                'topic' => 'Bases de datos',
                'title' => 'Índices en MySQL: cómo acelerar tus consultas',
                'excerpt' => 'Cuándo crear un índice, cómo leer un plan con EXPLAIN y qué errores comunes terminan haciendo las consultas más lentas.',
                'content' => [
                    'Un índice permite a MySQL localizar filas sin recorrer toda la tabla. Las columnas usadas con frecuencia en WHERE, JOIN y ORDER BY son las primeras candidatas, como las claves foráneas y los campos de fecha de publicación.',
                    'La instrucción EXPLAIN muestra si una consulta usa un índice o hace un escaneo completo. Conviene recordar que cada índice adicional ocupa espacio y vuelve más costosas las escrituras, así que se crean con criterio.',
                ],
                'days' => 5,
                'cover' => 'indices-mysql.jpg',
            ],
            [
                'topic' => 'Bases de datos',
                'title' => 'Modelado de datos para analítica: tablas de hechos y dimensiones',
                'excerpt' => 'Introducción al modelo dimensional y su utilidad para construir reportes y tableros de analítica avanzada.',
                'content' => [
                    'En analítica, el modelo dimensional organiza la información en tablas de hechos, que guardan medidas como ventas o visitas, y tablas de dimensiones, que describen el contexto: tiempo, producto o cliente.',
                    'Este diseño simplifica las consultas de agregación y mejora el rendimiento de los tableros. Un buen punto de partida es definir primero la pregunta de negocio y, a partir de ella, elegir el nivel de detalle de la tabla de hechos.',
                ],
                'days' => 6,
                'cover' => 'modelado-analitica.jpg',
            ],
            [
                'topic' => 'Backend',
                'title' => 'Diseño de APIs REST con Laravel',
                'excerpt' => 'Rutas, controladores y validación: cómo estructurar una API REST clara, consistente y fácil de mantener con Laravel.',
                'content' => [
                    'Una API REST modela recursos con URLs predecibles y verbos HTTP con significado: GET para consultar, POST para crear, PUT para actualizar y DELETE para eliminar. Laravel facilita esto con rutas de recursos y controladores dedicados.',
                    'Conviene validar siempre la entrada, devolver códigos de estado coherentes y paginar las colecciones. Así, los clientes de la API reciben respuestas consistentes y los errores son fáciles de interpretar.',
                ],
                'days' => 7,
                'cover' => 'apis-rest-laravel.jpg',
            ],
            [
                'topic' => 'Backend',
                'title' => 'Autenticación con tokens usando Laravel Sanctum',
                'excerpt' => 'Cómo emitir y revocar tokens de acceso personal para proteger una API consumida por una aplicación Vue.',
                'content' => [
                    'Laravel Sanctum ofrece un sistema ligero de autenticación por tokens, ideal para SPAs y aplicaciones móviles. Al iniciar sesión se genera un token que el cliente envía en la cabecera Authorization como Bearer.',
                    'Las rutas protegidas se agrupan con el middleware auth:sanctum, y el cierre de sesión revoca el token actual. Sobre esta base se pueden añadir roles, como administrador y usuario, para controlar quién puede escribir.',
                ],
                'days' => 8,
                'cover' => 'sanctum-laravel.jpg',
            ],
            [
                'topic' => 'Backend',
                'title' => 'Automatización de tareas con colas y jobs en Laravel',
                'excerpt' => 'Mueve trabajos pesados fuera de la petición web con colas y programa tareas recurrentes para automatizar procesos.',
                'content' => [
                    'Las colas permiten ejecutar en segundo plano tareas lentas, como enviar correos o procesar archivos, para que la respuesta al usuario sea inmediata. Cada tarea se encapsula en un job que un worker procesa de forma independiente.',
                    'Además, el programador de tareas de Laravel permite definir procesos recurrentes en código, como generar reportes cada noche. Es una base sencilla para automatizar flujos de trabajo sin depender de múltiples scripts sueltos.',
                ],
                'days' => 9,
                'cover' => 'colas-jobs-laravel.jpg',
            ],
        ];

        foreach ($resources as $item) {
            $topic = Topic::where('name', $item['topic'])->firstOrFail();

            Resource::firstOrCreate(
                ['title' => $item['title']],
                [
                    'excerpt' => $item['excerpt'],
                    'content' => implode("\n\n", $item['content']),
                    'cover_url' => asset('images/covers/' . $item['cover']),
                    'topic_id' => $topic->id,
                    'author_id' => $author->id,
                    'published_at' => now()->subDays($item['days'])->setTime(9, 0),
                ]
            );
        }
    }
}