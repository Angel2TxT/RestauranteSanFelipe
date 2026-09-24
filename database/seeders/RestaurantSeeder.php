<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\Item;
use App\Models\Product;
use App\Models\Slider;
use App\Models\Table;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUsers();
        $this->seedSliders();
        // Limpiar catálogo antes de recrear
        \Illuminate\Support\Facades\DB::table('item_order')->delete();
        Order::query()->delete();
        Item::query()->delete();
        Product::query()->delete();
        Category::query()->delete();
        $categories = $this->seedCategories();
        $products = $this->seedProducts($categories);
        $this->seedTables();
        $this->seedSampleOrders($products);
    }

    private function seedUsers(): void
    {
        $users = [
            [
                'email' => 'admin@sanfelipe.test',
                'name' => 'Admin',
                'last_name' => 'San Felipe',
                'role' => 1,
                'phone' => '4431000001',
                'address' => 'Av. Principal 100, Morelia',
                'image' => 'images/users/avatar-admin.jpg',
            ],
            [
                'email' => 'cliente@sanfelipe.test',
                'name' => 'María',
                'last_name' => 'García',
                'role' => 0,
                'phone' => '4431000002',
                'address' => 'Calle Reforma 45, Morelia',
                'image' => 'images/users/avatar-cliente.jpg',
            ],
            [
                'email' => 'empleado@sanfelipe.test',
                'name' => 'Carlos',
                'last_name' => 'López',
                'role' => 2,
                'phone' => '4431000003',
                'address' => 'Col. Centro, Morelia',
                'image' => 'images/users/avatar-empleado.jpg',
            ],
            [
                'email' => 'repartidor@sanfelipe.test',
                'name' => 'Luis',
                'last_name' => 'Hernández',
                'role' => 3,
                'phone' => '4431000004',
                'address' => 'Col. Industrial, Morelia',
                'image' => 'images/users/avatar-empleado.jpg',
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ])
            );
        }
    }

    private function seedSliders(): void
    {
        Slider::query()->delete();

        $sliders = [
            [
                'title' => 'Bienvenido a San Felipe',
                'description' => 'Sabores tradicionales michoacanos en un ambiente familiar.',
                'link' => '/shop',
                'text_link' => 'Ver menú',
                'image' => 'images/sliders/slider-bienvenida.jpg',
            ],
            [
                'title' => 'Nuestro menú del día',
                'description' => 'Platillos caseros preparados con ingredientes frescos.',
                'link' => '/shop',
                'text_link' => 'Ordenar ahora',
                'image' => 'images/sliders/slider-menu.jpg',
            ],
            [
                'title' => 'Ideal para compartir',
                'description' => 'Ven con tu familia y disfruta de una comida inolvidable.',
                'link' => '/shop',
                'text_link' => 'Explorar',
                'image' => 'images/sliders/slider-familia.jpg',
            ],
        ];

        foreach ($sliders as $slider) {
            Slider::create($slider);
        }
    }

    private function seedCategories(): array
    {
        $items = [
            ['name' => 'Desayunos', 'icon' => 'sun', 'image' => 'images/categories/cat-desayunos.jpg'],
            ['name' => 'Comidas', 'icon' => 'dinner', 'image' => 'images/categories/cat-comidas.jpg'],
            ['name' => 'Antojitos', 'icon' => 'heart', 'image' => 'images/categories/cat-antojitos.jpg'],
            ['name' => 'Bebidas', 'icon' => 'glass', 'image' => 'images/categories/cat-bebidas.jpg'],
            ['name' => 'Postres', 'icon' => 'star', 'image' => 'images/categories/cat-postres.jpg'],
            ['name' => 'Especiales', 'icon' => 'star-empty', 'image' => 'images/categories/cat-especiales.jpg'],
        ];

        $map = [];
        foreach ($items as $item) {
            $category = new Category();
            $category->name = $item['name'];
            $category->icon = $item['icon'];
            $category->image = $item['image'];
            $category->save();
            $map[$item['name']] = $category;
        }

        return $map;
    }

    private function seedProducts(array $categories): array
    {
        $catalog = [
            // Desayunos
            ['cat' => 'Desayunos', 'name' => 'Huevos al gusto', 'description' => 'Huevos estrellados o revueltos con frijoles y tortillas.', 'label' => 'Popular', 'price' => 85.00, 'image' => 'images/products/desayuno-huevos.jpg'],
            ['cat' => 'Desayunos', 'name' => 'Huevos con chorizo', 'description' => 'Huevos revueltos con chorizo artesanal y salsa verde.', 'label' => 'Casero', 'price' => 95.00, 'image' => 'images/products/huevoconchorizo.jpg'],
            ['cat' => 'Desayunos', 'name' => 'Huevos con jamón', 'description' => 'Clásico desayuno con jamón, frijoles y pan tostado.', 'label' => null, 'price' => 90.00, 'image' => 'images/products/huevosconjamon.png'],
            ['cat' => 'Desayunos', 'name' => 'Chilaquiles rojos', 'description' => 'Totopos bañados en salsa roja con crema y queso.', 'label' => 'Nuevo', 'price' => 110.00, 'image' => 'images/products/chilaquiles.jpg'],

            // Comidas
            ['cat' => 'Comidas', 'name' => 'Mole de pollo', 'description' => 'Pollo bañado en mole casero con arroz y frijoles.', 'label' => 'Tradicional', 'price' => 145.00, 'image' => 'images/products/mole-pollo.jpg'],
            ['cat' => 'Comidas', 'name' => 'Milanesa de res', 'description' => 'Milanesa empanizada con papas fritas y ensalada.', 'label' => 'Popular', 'price' => 155.00, 'image' => 'images/products/milanesa-res.jpg'],
            ['cat' => 'Comidas', 'name' => 'Carne asada', 'description' => 'Arrachera a la parrilla con guacamole y tortillas.', 'label' => 'Chef', 'price' => 180.00, 'image' => 'images/products/carne-asada.jpg'],
            ['cat' => 'Comidas', 'name' => 'Enchiladas verdes', 'description' => 'Tortillas rellenas de pollo con salsa verde y queso.', 'label' => null, 'price' => 125.00, 'image' => 'images/products/enchiladas.jpg'],
            ['cat' => 'Comidas', 'name' => 'Sopa azteca', 'description' => 'Caldo de jitomate con tiras de tortilla, aguacate y queso.', 'label' => null, 'price' => 75.00, 'image' => 'images/products/sopa-azteca.jpg'],
            ['cat' => 'Comidas', 'name' => 'Ensalada fresca', 'description' => 'Mix de hojas verdes, vegetales y aderezo de la casa.', 'label' => 'Ligero', 'price' => 95.00, 'image' => 'images/products/ensalada.jpg'],

            // Antojitos
            ['cat' => 'Antojitos', 'name' => 'Tacos al pastor', 'description' => 'Orden de 4 tacos con piña, cebolla y cilantro.', 'label' => 'Top', 'price' => 70.00, 'image' => 'images/products/tacos-pastor.jpg'],
            ['cat' => 'Antojitos', 'name' => 'Quesadillas', 'description' => 'Tortillas de maíz con queso y guisado a elegir.', 'label' => null, 'price' => 55.00, 'image' => 'images/products/quesadillas.jpg'],
            ['cat' => 'Antojitos', 'name' => 'Guacamole', 'description' => 'Aguacate fresco con totopos y pico de gallo.', 'label' => null, 'price' => 65.00, 'image' => 'images/products/guacamole.jpg'],

            // Bebidas
            ['cat' => 'Bebidas', 'name' => 'Agua de horchata', 'description' => 'Refrescante agua de horchata natural (500 ml).', 'label' => null, 'price' => 35.00, 'image' => 'images/products/agua-horchata.jpg'],
            ['cat' => 'Bebidas', 'name' => 'Jugo de naranja', 'description' => 'Jugo natural recién exprimido.', 'label' => 'Natural', 'price' => 40.00, 'image' => 'images/products/jugo-naranja.jpg'],
            ['cat' => 'Bebidas', 'name' => 'Café americano', 'description' => 'Café de grano tostado, taza grande.', 'label' => null, 'price' => 30.00, 'image' => 'images/products/cafe-americano.jpg'],
            ['cat' => 'Bebidas', 'name' => 'Refresco', 'description' => 'Refresco embotellado (355 ml).', 'label' => null, 'price' => 28.00, 'image' => 'images/products/refresco.jpg'],

            // Postres
            ['cat' => 'Postres', 'name' => 'Flan napolitano', 'description' => 'Flan casero con caramelo.', 'label' => 'Casero', 'price' => 45.00, 'image' => 'images/products/flan.jpg'],
            ['cat' => 'Postres', 'name' => 'Helado de vainilla', 'description' => 'Dos bolas de helado cremoso de vainilla.', 'label' => null, 'price' => 40.00, 'image' => 'images/products/helado-vainilla.jpg'],
            ['cat' => 'Postres', 'name' => 'Pastel de chocolate', 'description' => 'Rebanada de pastel húmedo de chocolate.', 'label' => 'Dulce', 'price' => 55.00, 'image' => 'images/products/pastel-chocolate.jpg'],

            // Especiales
            ['cat' => 'Especiales', 'name' => 'Hamburguesa San Felipe', 'description' => 'Carne 180g, queso, vegetales y papas fritas.', 'label' => 'Especial', 'price' => 140.00, 'image' => 'images/products/hamburguesa.jpg'],
            ['cat' => 'Especiales', 'name' => 'Pizza familiar', 'description' => 'Pizza mediana de pepperoni o hawaiana.', 'label' => 'Para compartir', 'price' => 190.00, 'image' => 'images/products/pizza.jpg'],
        ];

        $products = [];
        foreach ($catalog as $row) {
            $product = new Product();
            $product->name = $row['name'];
            $product->description = $row['description'];
            $product->label = $row['label'];
            $product->price = $row['price'];
            $product->image = $row['image'];
            $product->category_id = $categories[$row['cat']]->id;
            $product->save();
            $products[] = $product;
        }

        return $products;
    }

    private function seedTables(): void
    {
        Table::query()->delete();

        foreach (range(1, 10) as $n) {
            Table::create([
                'name' => "Mesa {$n}",
                'status' => $n <= 8 ? 'available' : 'occupied',
            ]);
        }
    }

    private function seedSampleOrders(array $products): void
    {
        $client = User::where('email', 'cliente@sanfelipe.test')->first();
        if (!$client || count($products) < 4) {
            return;
        }

        $today = Carbon::now('America/Mexico_City')->toDateString();
        $samples = [
            ['status' => 'pending', 'type' => 'delivery', 'indices' => [0, 10, 13]],
            ['status' => 'in_progress', 'type' => 'dine_in', 'table' => 9, 'indices' => [4, 11, 15]],
            ['status' => 'ready_for_delivery', 'type' => 'pickup', 'indices' => [5, 14]],
            ['status' => 'completed', 'type' => 'dine_in', 'table' => 10, 'indices' => [6, 18]],
        ];

        foreach ($samples as $sample) {
            $lines = [];
            $total = 0;
            foreach ($sample['indices'] as $i) {
                $p = $products[$i];
                $qty = 1;
                $total += (float) $p->price * $qty;
                $lines[] = ['product' => $p, 'qty' => $qty];
            }

            $order = new Order();
            $order->total = $total;
            $order->notes = 'Pedido de demostración';
            $order->status = $sample['status'];
            $order->fecha = $today;
            $order->user_id = $client->id;
            $order->order_type = $sample['type'];
            $order->table_id = $sample['table'] ?? null;
            $order->save();

            foreach ($lines as $line) {
                $p = $line['product'];
                $item = new Item();
                $item->name = $p->name;
                $item->price = $p->price;
                $item->qty = $line['qty'];
                $item->image = $p->image;
                $item->product_id = $p->id;
                $item->fecha = $today;
                $item->save();

                $order->items()->attach($item->id, [
                    'qty' => $line['qty'],
                    'fecha' => $today,
                ]);
            }
        }
    }
}
