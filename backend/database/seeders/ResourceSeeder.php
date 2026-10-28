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
                    'La diferencia con la Options API es fundamental: mientras que la Options API divide el componente por categorías (datos, métodos, ciclo de vida), la Composition API lo divide por responsabilidades. Si un componente gestiona autenticación y filtros, todo lo de autenticación queda junto, y todo lo de filtros también, sin saltar entre bloques.',
                    'Con ref y reactive se define el estado reactivo, con computed los valores derivados y con watch los efectos secundarios. La ventaja de ref es que funciona con tipos primitivos y se puede desestructurar sin perder reactividad si se usa el sufijo .value correctamente.',
                    'La verdadera potencia aparece con los composables: funciones que encapsulan lógica reactiva y se reutilizan entre componentes. Un composable como useAuth puede contener el estado del usuario, las funciones de login y logout, y los interceptores necesarios, todo en un solo archivo que cualquier componente importa.',
                    'Para migrar desde Options API no es necesario reescribir todo. La Composition API y la Options API conviven en el mismo proyecto, e incluso en el mismo componente. Se puede empezar por los componentes nuevos y dejar los antiguos hasta que haya tiempo de refactorizarlos.',
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
                    'La crítica más común es que el HTML se vuelve verboso. Es cierto al principio, pero la verbosidad es explícita: se ve exactamente qué estilos aplican sin navegar a otro archivo. Cuando un componente crece, se extraen componentes Vue en lugar de clases CSS, y la reutilización se mantiene.',
                    'Para el diseño responsive se utilizan prefijos como sm, md y lg. Una cuadrícula que muestra una columna en móvil, dos en tablet y tres en escritorio se resuelve con tres clases, sin media queries manuales. El breakpoint base es móvil, y los prefijos escalan hacia arriba.',
                    'La personalización se hace con el bloque @theme en el CSS principal. Ahí se definen colores de marca, tipografías y espaciados, y esas variables quedan disponibles como clases utilitarias. En Tailwind v4, este bloque reemplaza al antiguo tailwind.config.js.',
                    'Tailwind no es la mejor opción para todos los proyectos. Si el equipo no está familiarizado con utility-first, la curva de aprendizaje puede ser alta. Pero en proyectos con componentes reutilizables, donde el diseño se mantiene consistente, ahorra tiempo y reduce la deuda de CSS sin dueño.',
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
                    'LCP mide cuándo aparece el elemento más grande visible, normalmente la imagen principal o un bloque de texto. INP mide la latencia de interacción, es decir, cuánto tarda la página en responder a un clic o una pulsación. CLS mide cuánto se desplaza el contenido sin que el usuario lo provoque, lo que suele ocurrir cuando las imágenes no tienen dimensiones reservadas.',
                    'Las mejoras más rentables suelen ser servir imágenes en formatos modernos y con tamaños adecuados, dividir el código en fragmentos que se cargan bajo demanda y evitar peticiones innecesarias a la API usando paginación y caché.',
                    'Reservar espacio para imágenes, fuentes y anuncios antes de que carguen es la forma más efectiva de reducir CLS. Un contenedor con aspect-ratio fijo o con ancho y alto explícitos evita que el contenido salte cuando llega la imagen.',
                    'Para medir, Chrome DevTools tiene Lighthouse y la pestaña Performance. Para monitorear en producción, herramientas como Web Vitals de Google o Sentry capturan las métricas reales de los usuarios. Sin medición, cualquier optimización es una suposición.',
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
                    'Un ejemplo típico de violación de 1FN es guardar varios teléfonos en un solo campo separados por comas. Si después se quiere buscar por un teléfono específico, la consulta se complica y no se puede indexar. La solución es una tabla aparte de teléfonos con una clave foránea al usuario.',
                    'La tercera forma normal pide que los atributos no clave no dependan de otros atributos no clave. Separar temas, autores y recursos en tablas relacionadas por claves foráneas es un ejemplo cotidiano de 3FN. Si en la tabla de recursos se guardara también el nombre del tema, ese nombre dependería del tema y no del recurso, violando 3FN.',
                    'Normalizar reduce anomalías de actualización. Si el nombre de un tema cambia, con una tabla normalizada se actualiza en un solo lugar. Sin normalizar, habría que actualizar todas las filas de recursos que referencian ese tema, con riesgo de inconsistencias si alguna fila se queda atrás.',
                    'Normalizar tiene un costo: más tablas y más joins en las consultas. En sistemas de lectura intensiva, a veces se desnormaliza deliberadamente para evitar joins costosos, aceptando redundancia controlada. La regla práctica es normalizar primero y desnormalizar solo cuando el rendimiento lo exige y hay medición que lo respalde.',
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
                    'El tipo de índice más común es el B-tree, que mantiene los valores ordenados y permite búsquedas por igualdad, por rango y por orden. Un índice en la columna published_at acelera tanto el filtro por fecha como el ORDER BY sobre esa columna, sin necesidad de ordenar en memoria.',
                    'La instrucción EXPLAIN muestra si una consulta usa un índice o hace un escaneo completo. Si en la columna type aparece ALL, la consulta recorre toda la tabla. Si aparece ref, range o const, está usando un índice. Si en la columna key aparece NULL, no se está usando ningún índice.',
                    'Los índices compuestos siguen la regla del prefijo más a la izquierda. Un índice sobre (topic_id, published_at) acelera consultas que filtran por topic_id, o por topic_id y published_at, pero no las que filtran solo por published_at. El orden de las columnas importa.',
                    'Conviene recordar que cada índice adicional ocupa espacio y vuelve más costosas las escrituras, así que se crean con criterio. Un índice que nunca se usa en consultas reales solo agrega peso a los INSERT, UPDATE y DELETE. Revisar el rendimiento de los índices periódicamente es parte del mantenimiento de cualquier base de datos en producción.',
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
                    'El esquema estrella es la forma más común de este modelo. Una tabla de hechos central referencia varias tablas de dimensiones, y las consultas se resuelven con joins simples y agregaciones rápidas. El nombre viene de la forma visual: el centro es la tabla de hechos y las dimensiones irradian hacia afuera.',
                    'Una tabla de hechos típica tiene claves foráneas a las dimensiones y columnas numéricas que representan medidas. Las medidas más comunes son sumas (ventas totales), conteos (número de pedidos) y promedios (ticket promedio). Las dimensiones aportan el contexto que permite agrupar esas medidas.',
                    'Este diseño simplifica las consultas de agregación y mejora el rendimiento de los tableros. Un buen punto de partida es definir primero la pregunta de negocio y, a partir de ella, elegir el nivel de detalle de la tabla de hechos. Si el negocio pregunta ventas por región y mes, la tabla de hechos debe tener al menos claves a dimensión de tiempo y dimensión de región.',
                    'La diferencia con los sistemas transaccionales es clave: un modelo normalizado en 3FN minimiza redundancia para escrituras consistentes, mientras que un modelo dimensional acepta redundancia controlada para lecturas rápidas. Ambos conviven en arquitecturas modernas, con el sistema transaccional alimentando al analítico mediante procesos ETL o ELT.',
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
                    'La consistencia es más importante que la elegancia. Elegir nombres en plural (resources, topics), mantener el mismo formato de respuesta en todos los endpoints y usar siempre los códigos de estado correctos (200 para éxito, 201 para creación, 204 para eliminación, 422 para validación fallida, 401 para no autenticado, 403 para no autorizado) ahorra horas de confusión a quien consume la API.',
                    'Conviene validar siempre la entrada, devolver códigos de estado coherentes y paginar las colecciones. Así, los clientes de la API reciben respuestas consistentes y los errores son fáciles de interpretar. En Laravel, los FormRequest centralizan las reglas de validación y devuelven errores estructurados en JSON.',
                    'La paginación es especialmente importante. Un endpoint que devuelve todos los registros sin límite funciona bien con 10 elementos y se cae con 10 000. Laravel incluye paginación de fábrica con la función paginate, que devuelve metadatos como total, current_page, last_page y next_page_url.',
                    'Documentar los endpoints es parte del diseño, no un extra. Una colección de Postman versionada, un archivo OpenAPI o un README con ejemplos de request y response ahorran tiempo al equipo y facilitan las pruebas manuales. La documentación debe actualizarse con cada cambio de contrato, no al final del proyecto.',
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
                    'Sanctum resuelve dos casos con la misma base: cookies de sesión para SPAs en el mismo dominio y tokens personales para clientes externos. Para una API consumida por un frontend separado, los tokens son la opción natural porque no dependen de la sesión del navegador ni de CORS con credenciales.',
                    'Las rutas protegidas se agrupan con el middleware auth:sanctum, y el cierre de sesión revoca el token actual. Sobre esta base se pueden añadir roles, como administrador y usuario, para controlar quién puede escribir. El middleware personalizado de rol se registra como alias y se aplica en las rutas de escritura.',
                    'La diferencia con JWT es conceptual: Sanctum emite tokens opacos que se validan contra la base de datos, mientras que JWT emite tokens firmados que se validan con una clave secreta. Sanctum es más simple de implementar y revocar; JWT es útil cuando se necesita autenticación completamente sin estado a gran escala.',
                    'Para el frontend, el token se guarda en localStorage o sessionStorage y se adjunta en cada petición con un interceptor de axios. Al recibir un 401, el interceptor limpia el token y redirige al login. Ese patrón, combinado con un guard de rutas, evita que el usuario vea pantallas vacías cuando la sesión expira.',
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
                    'El flujo básico es simple: el controlador encola el job con dispatch, el job se guarda en una tabla o en un servicio como Redis, y un proceso worker lo toma y lo ejecuta. El usuario recibe una respuesta inmediata, sin esperar a que termine la tarea pesada.',
                    'Los jobs pueden reintentarse automáticamente si fallan. Cada job define cuántos intentos tiene y cuánto tiempo esperar entre ellos. Si después de los reintentos sigue fallando, se marca como fallido y queda en la tabla failed_jobs para revisión manual. Ese mecanismo evita perder tareas por errores transitorios de red o de servicios externos.',
                    'Además, el programador de tareas de Laravel permite definir procesos recurrentes en código, como generar reportes cada noche. En lugar de configurar cron en el servidor, se define el schedule en el archivo routes/console.php, y una sola entrada de cron invoca al scheduler de Laravel cada minuto.',
                    'Monitorear las colas es parte del trabajo. Herramientas como Horizon, de Laravel, muestran cuántos jobs están pendientes, cuántos fallaron y cuánto tiempo tardan. Sin monitoreo, una cola llena puede pasar inadvertida hasta que los usuarios notan que no reciben correos o que los reportes no se generan.',
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