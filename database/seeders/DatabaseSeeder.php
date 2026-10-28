<?php

namespace Database\Seeders;

use App\Models\ChocolateFactory;
use App\Models\Product;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@rutadelchocolate.bo'],
            ['name' => 'Administrador', 'password' => 'chocolate123', 'is_admin' => true, 'role' => 'admin'],
        );
        $guide = User::updateOrCreate(
            ['email' => 'guia@rutadelchocolate.bo'],
            ['name' => 'Guía Demo', 'password' => 'chocolate123', 'role' => 'guide', 'phone' => '70000000'],
        );

        // Coordenadas aproximadas: ajústalas desde el panel de administración.
        // Datos del documento (Cap. II). Las coordenadas son aproximadas: ajústalas en Admin → Fábricas.
        $factories = [
            [
                'name' => 'Chocolates Para Ti',
                'slug' => 'para-ti',
                'tagline_es' => 'Con museo del chocolate abierto al público',
                'tagline_en' => 'With a chocolate museum open to the public',
                'description_es' => 'Perteneciente a SOLUR S.R.L., inició actividades el 27 de mayo de 1990, impulsada por los hermanos Gastón y Fernando Solares y Jaime Urriolagoitia. Usa principalmente cacao boliviano, incluido cacao silvestre de Guanay, y cuenta con una experiencia tipo museo que muestra su historia y producción.',
                'description_en' => 'Part of SOLUR S.R.L., it started operating on 27 May 1990, driven by brothers Gastón and Fernando Solares and Jaime Urriolagoitia. It mainly uses Bolivian cacao, including wild cacao from Guanay, and offers a museum-style experience about its history and production.',
                'address' => 'Calle Monteagudo, frente al Polideportivo Garcilazo',
                'phone' => '6455689', 'has_museum' => true,
                'visit_hours_es' => 'Consultar horarios de visita', 'visit_hours_en' => 'Ask for visiting hours',
                'lat' => -19.0466, 'lng' => -65.2395,
                'founded_year' => 1990, 'color' => '#7a3b1e', 'route_order' => 1,
                'stores' => [
                    ['name' => 'Tienda y museo Para Ti', 'address' => 'Calle Monteagudo, frente al Polideportivo Garcilazo', 'lat' => -19.0466, 'lng' => -65.2395, 'hours' => null, 'phone' => '6455689', 'is_factory_outlet' => true],
                ],
            ],
            [
                'name' => 'Chocolates Taboada',
                'slug' => 'taboada',
                'tagline_es' => 'La fábrica más antigua en funcionamiento',
                'tagline_en' => 'The oldest factory still in operation',
                'description_es' => 'Fundada en 1948 como emprendimiento familiar de Jorge Taboada Moscoso y su familia. Comenzó de manera artesanal y fue incorporando maquinaria y ampliando su variedad de productos; mantiene una tradición chocolatera familiar.',
                'description_en' => 'Founded in 1948 as a family business by Jorge Taboada Moscoso and his family. It started as a craft operation and gradually added machinery and a wider product range, keeping its family chocolate tradition.',
                'address' => 'Calle Daniel Campos #82, Zona Surapata (frente al portón de La Normal, cerca de la plazuela Carlos Gardel)',
                'phone' => '+591 4 64 54680',
                'visit_hours_es' => 'Lun–Sáb 8:00–12:00 y 14:00–18:00', 'visit_hours_en' => 'Mon–Sat 8:00–12:00 and 14:00–18:00',
                'lat' => -19.0407, 'lng' => -65.2593,
                'founded_year' => 1948, 'color' => '#a0522d', 'route_order' => 2,
                'stores' => [
                    ['name' => 'Tienda en fábrica Taboada', 'address' => 'Calle Daniel Campos #82, Zona Surapata', 'lat' => -19.0407, 'lng' => -65.2593, 'hours' => 'Lun–Sáb 8:00–12:00 y 14:00–18:00', 'phone' => '+591 4 64 54680', 'is_factory_outlet' => true],
                ],
            ],
            [
                'name' => 'Chocolates Mi Bombón',
                'slug' => 'mi-bombon',
                'tagline_es' => 'Bombones artesanales y semi artesanales',
                'tagline_en' => 'Artisan and semi-artisan bonbons',
                'description_es' => 'Opera desde 2008 como emprendimiento familiar dirigido por Filemón Díaz. Produce bombones (su producto principal), tabletas, grageas y trufas, con producción artesanal y semi artesanal.',
                'description_en' => 'Operating since 2008 as a family business led by Filemón Díaz. It makes bonbons (its main product), bars, dragées and truffles, with artisan and semi-artisan production.',
                'address' => 'Calle Loa #954',
                'visit_hours_es' => 'Consultar horarios de visita', 'visit_hours_en' => 'Ask for visiting hours',
                'lat' => -19.0464, 'lng' => -65.2623,
                'founded_year' => 2008, 'color' => '#c46a2f', 'route_order' => 3,
                'stores' => [
                    ['name' => 'Tienda Mi Bombón', 'address' => 'Calle Loa #954', 'lat' => -19.0464, 'lng' => -65.2623, 'hours' => null, 'is_factory_outlet' => true],
                ],
            ],
            [
                'name' => 'Chocolates Sucre',
                'slug' => 'chocolates-sucre',
                'tagline_es' => 'Empresa netamente chuquisaqueña desde 1965',
                'tagline_en' => 'A purely Chuquisaca company since 1965',
                'description_es' => 'Fundada en 1965 para aportar a la tradición chocolatera de Sucre con productos artesanales y semiindustriales que rescatan el sabor tradicional del cacao boliviano. Hoy comercializa tabletas, chocolates con leche y bombones en los nueve departamentos del país.',
                'description_en' => 'Founded in 1965 to contribute to the chocolate tradition of Sucre with artisan and semi-industrial products that recover the traditional taste of Bolivian cacao. It now sells bars, milk chocolates and bonbons in all nine departments of the country.',
                'address' => 'Sucre (dirección por confirmar)',
                'visit_hours_es' => 'Consultar horarios de visita', 'visit_hours_en' => 'Ask for visiting hours',
                'lat' => -19.0442, 'lng' => -65.2597,
                'founded_year' => 1965, 'color' => '#5c2a14', 'route_order' => 4,
                'stores' => [
                    ['name' => 'Tienda Chocolates Sucre', 'address' => 'Sucre (dirección por confirmar)', 'lat' => -19.0442, 'lng' => -65.2597, 'hours' => null, 'is_factory_outlet' => true],
                ],
            ],
        ];

        // Retira las fábricas de ejemplo de versiones anteriores (o las oculta si ya tienen pedidos).
        foreach (ChocolateFactory::whereIn('slug', ['flor-de-cacao', 'cacao-chuquisaca', 'el-ceibo'])->get() as $old) {
            $old->stores()->whereHas('orders')->exists()
                ? $old->update(['is_active' => false])
                : $old->delete();
        }

        $bySlug = [];
        foreach ($factories as $data) {
            $stores = $data['stores'];
            unset($data['stores']);
            $factory = ChocolateFactory::updateOrCreate(['slug' => $data['slug']], $data);
            foreach ($stores as $store) {
                $factory->stores()->updateOrCreate(['name' => $store['name']], $store);
            }
            // Oculta puntos de recojo de versiones anteriores que ya no figuran.
            $factory->stores()->whereNotIn('name', array_column($stores, 'name'))->update(['is_active' => false]);
            $bySlug[$factory->slug] = $factory;
        }

        $meetingEs = 'Plaza 25 de Mayo, frente a la Catedral';
        $meetingEn = '25 de Mayo Square, in front of the Cathedral';
        $tours = [
            [
                'slug' => 'ruta-completa',
                'name_es' => 'Ruta completa del chocolate',
                'name_en' => 'The full chocolate route',
                'description_es' => 'Visita las cuatro fábricas de la ruta en una sola jornada, con guía, degustaciones en cada parada y registro de checkpoints en tu pasaporte.',
                'description_en' => 'Visit all four factories on the route in a single day, with a guide, tastings at every stop and checkpoint stamps in your passport.',
                'duration_minutes' => 300, 'price' => 200, 'capacity' => 16,
                'departure_times' => ['09:00', '14:00'], 'weekdays' => [1, 2, 3, 4, 5, 6],
                'factories' => ['para-ti', 'taboada', 'mi-bombon', 'chocolates-sucre'],
            ],
            [
                'slug' => 'clasicos-de-sucre',
                'name_es' => 'Clásicos de Sucre',
                'name_en' => 'Sucre classics',
                'description_es' => 'Las dos casas chocolateras más tradicionales de la ciudad: museo, historia, producción y degustación de bombones.',
                'description_en' => "The city's two most traditional chocolate houses: museum, history, production and a bonbon tasting.",
                'duration_minutes' => 150, 'price' => 120, 'capacity' => 20,
                'departure_times' => ['09:30', '15:00'], 'weekdays' => [1, 2, 3, 4, 5, 6, 7],
                'factories' => ['para-ti', 'taboada'],
            ],
            [
                'slug' => 'cacao-y-origen',
                'name_es' => 'Cacao y origen',
                'name_en' => 'Cacao and origin',
                'description_es' => 'Un recorrido por tres casas que trabajan con cacao boliviano, con museo y cata guiada.',
                'description_en' => 'A tour of three houses working with Bolivian cacao: a guided tasting by percentages and origins.',
                'duration_minutes' => 180, 'price' => 150, 'capacity' => 12,
                'departure_times' => ['10:00', '16:00'], 'weekdays' => [2, 3, 4, 5, 6],
                'factories' => ['chocolates-sucre', 'mi-bombon', 'para-ti'],
            ],
            [
                'slug' => 'chocolate-al-atardecer',
                'name_es' => 'Chocolate al atardecer',
                'name_en' => 'Chocolate at sunset',
                'description_es' => 'Un recorrido corto de tarde para cerrar el día con bombones y una vista de la ciudad blanca.',
                'description_en' => 'A short afternoon route to end the day with bonbons and a view of the White City.',
                'duration_minutes' => 120, 'price' => 130, 'capacity' => 12,
                'departure_times' => ['17:00'], 'weekdays' => [4, 5, 6, 7],
                'factories' => ['mi-bombon', 'taboada'],
            ],
        ];

        foreach ($tours as $data) {
            $data += ['meeting_point_es' => $meetingEs, 'meeting_point_en' => $meetingEn, 'meeting_lat' => -19.0477, 'meeting_lng' => -65.2593];
            $slugs = $data['factories'];
            unset($data['factories']);
            $tour = Tour::updateOrCreate(['slug' => $data['slug']], $data);
            $tour->factories()->sync(collect($slugs)->mapWithKeys(
                fn ($slug, $i) => [$bySlug[$slug]->id => ['position' => $i + 1]]
            )->all());
        }

        $products = [
            ['para-ti', 'bombones-surtidos-para-ti', 'Bombones surtidos', 'Assorted bonbons', 'Caja de 24 bombones con rellenos clásicos.', 'Box of 24 bonbons with classic fillings.', 85, '300 g', null, 'Bombones'],
            ['para-ti', 'tableta-leche-para-ti', 'Tableta de leche', 'Milk chocolate bar', 'Chocolate con leche cremoso de receta tradicional.', 'Creamy milk chocolate from a traditional recipe.', 18, '100 g', '35%', 'Tabletas'],
            ['para-ti', 'figuras-sucre-para-ti', 'Figuras de Sucre', 'Sucre figures', 'Chocolates con forma de monumentos de la ciudad.', "Chocolates shaped like the city's monuments.", 45, '200 g', null, 'Figuras'],
            ['taboada', 'caja-regalo-taboada', 'Caja de regalo', 'Gift box', 'Selección de bombones decorados a mano.', 'A selection of hand-decorated bonbons.', 120, '400 g', null, 'Bombones'],
            ['taboada', 'trufas-taboada', 'Trufas de cacao', 'Cacao truffles', 'Trufas de ganache espolvoreadas con cacao.', 'Ganache truffles dusted with cacao.', 60, '250 g', '55%', 'Trufas'],
            ['chocolates-sucre', 'tableta-sucre-70', 'Tableta 70% cacao', '70% cacao bar', 'Tableta amarga de cacao boliviano.', 'Dark bar of Bolivian cacao.', 25, '100 g', '70%', 'Tabletas'],
            ['chocolates-sucre', 'bombones-sucre', 'Bombones de Sucre', 'Sucre bonbons', 'Caja de 12 bombones artesanales.', 'Box of 12 artisan bonbons.', 55, '150 g', null, 'Bombones'],
            ['mi-bombon', 'bombones-mi-bombon', 'Bombones hechos a mano', 'Handmade bonbons', 'Caja de 16 bombones rellenos.', 'Box of 16 filled bonbons.', 70, '220 g', null, 'Bombones'],
            ['mi-bombon', 'figuras-mi-bombon', 'Figuras de chocolate', 'Chocolate figures', 'Figuras decorativas de chocolate con leche.', 'Decorative milk chocolate figures.', 35, '120 g', '35%', 'Figuras'],
        ];

        foreach ($products as [$factory, $slug, $es, $en, $des, $den, $price, $weight, $cacao, $variety]) {
            Product::updateOrCreate(['slug' => $slug], [
                'factory_id' => $bySlug[$factory]->id,
                'name_es' => $es, 'name_en' => $en,
                'description_es' => $des, 'description_en' => $den,
                'price' => $price, 'stock' => 50, 'weight' => $weight, 'cacao' => $cacao, 'variety' => $variety,
            ]);
        }

        $this->seedActivitiesAndEvents($guide);
    }

    /** Actividades por parada y algunos eventos de ejemplo (Cap. III del documento). */
    private function seedActivitiesAndEvents(User $guide): void
    {
        $quiz = [
            'para-ti' => ['¿En qué año inició Chocolates Para Ti?', 'In what year did Chocolates Para Ti start?', ['1948', '1990', '2008'], ['1948', '1990', '2008'], 1],
            'taboada' => ['¿En qué año fue fundada Chocolates Taboada?', 'In what year was Chocolates Taboada founded?', ['1948', '1965', '1990'], ['1948', '1965', '1990'], 0],
            'mi-bombon' => ['¿Cuál es el producto principal de Mi Bombón?', 'What is the main product of Mi Bombón?', ['Bombones', 'Grageas', 'Café'], ['Bonbons', 'Dragées', 'Coffee'], 0],
            'chocolates-sucre' => ['¿En qué año fue fundada Chocolates Sucre?', 'In what year was Chocolates Sucre founded?', ['1965', '1990', '2008'], ['1965', '1990', '2008'], 0],
        ];

        foreach (\App\Models\TourStop::with('factory')->get() as $stop) {
            $slug = $stop->factory->slug;
            $name = $stop->factory->name;
            $defs = [
                ['checkpoint', "Checkpoint en {$name}", "Checkpoint at {$name}", 'Toma una foto en la fábrica para registrar tu llegada.', 'Take a photo at the factory to log your arrival.', 10, null],
                ['photo', 'Foto del proceso', 'Process photo', 'Captura el momento que más te guste de la visita.', 'Capture your favourite moment of the visit.', 10, null],
            ];
            if (isset($quiz[$slug])) {
                [$qes, $qen, $oes, $oen, $ans] = $quiz[$slug];
                $defs[] = ['quiz', 'Trivia de la fábrica', 'Factory trivia', 'Responde y suma puntos.', 'Answer and earn points.', 15,
                    ['question_es' => $qes, 'question_en' => $qen, 'options_es' => $oes, 'options_en' => $oen, 'answer' => $ans]];
            }
            foreach ($defs as [$type, $nes, $nen, $des, $den, $pts, $config]) {
                $stop->activities()->firstOrCreate(['type' => $type, 'name_es' => $nes], [
                    'name_en' => $nen, 'description_es' => $des, 'description_en' => $den, 'max_points' => $pts, 'config' => $config,
                ]);
            }
        }

        foreach (['ruta-completa' => [2, '09:00', 16], 'clasicos-de-sucre' => [3, '09:30', 20]] as $slug => [$days, $time, $cap]) {
            $tour = Tour::where('slug', $slug)->first();
            if (! $tour || $tour->events()->exists()) {
                continue;
            }
            $event = $tour->events()->create(['guide_id' => $guide->id, 'date' => today()->addDays($days), 'start_time' => $time, 'capacity' => $cap]);
            $at = \Carbon\Carbon::createFromFormat('H:i', $time);
            foreach ($tour->stops as $i => $stop) {
                $event->stops()->create(['tour_stop_id' => $stop->id, 'position' => $i + 1, 'estimated_time' => $at->copy()->addMinutes(50 * $i)->format('H:i')]);
            }
        }
    }
}
