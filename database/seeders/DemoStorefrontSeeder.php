<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoStorefrontSeeder extends Seeder
{
    /** Add realistic, repeatable storefront data without clearing existing records. */
    public function run(): void
    {
        $now = now();
        $image = function (string $key, string $kind = 'product') use ($now): int {
            $name = 'demo-' . $kind . '-' . $key;
            DB::table('uploads')->updateOrInsert(['file_original_name' => $name], [
                'file_name' => 'https://picsum.photos/seed/flixshop-' . $name . '/800/800',
                'user_id' => 1, 'file_size' => 0, 'extension' => 'jpg', 'type' => 'image',
                'deleted_at' => null, 'updated_at' => $now, 'created_at' => $now,
            ]);
            return (int) DB::table('uploads')->where('file_original_name', $name)->value('id');
        };

        $categoryNames = [
            'mobile-phones' => 'Mobile Phones', 'mens-fashion' => 'Menâ€™s Fashion',
            'home-appliances' => 'Home Appliances', 'grocery' => 'Grocery & Food',
            'beauty-personal-care' => 'Beauty & Personal Care', 'electronics' => 'Electronics & Audio',
            'home-living' => 'Home & Living', 'sports-outdoor' => 'Sports & Outdoor',
            'baby-kids' => 'Baby & Kids', 'computers-laptops' => 'Computers & Laptops',
        ];
        $categories = [];
        foreach ($categoryNames as $slug => $name) {
            $asset = $image($slug, 'category');
            DB::table('categories')->updateOrInsert(['slug' => $slug], [
                'name' => $name, 'parent_id' => 0, 'level' => 0, 'order_level' => 1,
                'banner' => (string) $asset, 'icon' => (string) $asset, 'featured' => 1,
                'top' => 1, 'digital' => 0, 'updated_at' => $now,
            ]);
            $id = (int) DB::table('categories')->where('slug', $slug)->value('id');
            $categories[$slug] = $id;
            DB::table('category_translations')->updateOrInsert(['category_id' => $id, 'lang' => 'en'], [
                'name' => $name, 'updated_at' => $now, 'created_at' => $now,
            ]);
            DB::table('home_categories')->updateOrInsert(['category_id' => $id], [
                'status' => 1, 'updated_at' => $now, 'created_at' => $now,
            ]);
        }

        $brandNames = ['Samsung', 'Xiaomi', 'OPPO', 'vivo', 'realme', 'Apple', 'Infinix', 'Walton', 'Sony', 'JBL', 'Philips', 'Singer', 'Vision', 'RFL', 'Miyako', 'Nestle', 'Pran', 'Unilever', 'Nivea', 'Aarong', 'Bata', 'Apex', 'Adidas', 'Puma', 'LEGO', 'HP', 'Lenovo', 'ASUS', 'Acer', 'Dell', 'Logitech', 'Chaldal', 'Fresh', 'Radhuni', 'Bombay Sweets', 'Ponds', 'Sunsilk', 'Garnier', 'Lakme', 'Vaseline', 'Neutrogena', 'Dove', 'The Ordinary', 'Maybelline', 'Anker', 'Baseus', 'Wilson', 'Fisher Price'];
        $brands = [];
        foreach ($brandNames as $brand) {
            $slug = str($brand)->slug()->toString();
            $logo = $image($slug, 'brand');
            DB::table('brands')->updateOrInsert(['slug' => $slug], [
                'name' => $brand, 'logo' => (string) $logo, 'top' => 1, 'updated_at' => $now,
                'created_at' => $now,
            ]);
            $id = (int) DB::table('brands')->where('slug', $slug)->value('id');
            $brands[$slug] = $id;
            DB::table('brand_translations')->updateOrInsert(['brand_id' => $id, 'lang' => 'en'], [
                'name' => $brand, 'updated_at' => $now, 'created_at' => $now,
            ]);
        }

        $catalog = [
            ['mobile-phones', ['Samsung Galaxy A55 5G 256GB', 'Samsung Galaxy A35 5G 128GB', 'Redmi Note 13 Pro 5G', 'Redmi Note 13 8GB/256GB', 'OPPO Reno 11F 5G', 'vivo V30e 5G', 'Walton NEXG N75', 'Infinix Note 40 Pro', 'realme C67 8GB/128GB', 'Apple iPhone 13 128GB'], ['samsung','samsung','xiaomi','xiaomi','oppo','vivo','walton','infinix','realme','apple'], [45999,38999,32999,24999,36990,34999,18990,27999,21999,69999], 'piece'],
            ['mens-fashion', ['Premium Cotton Panjabi â€“ Navy', 'Classic Oxford Casual Shirt', 'Slim Fit Denim Jeans', 'Cotton Polo Shirt â€“ Forest Green', 'Everyday Chino Trousers', 'Embroidered Eid Panjabi', 'Lightweight Bomber Jacket', 'Formal Leather Belt', 'Combed Cotton T-Shirt Pack', 'Traditional Cotton Lungi'], ['aarong','aarong','bata','aarong','aarong','aarong','adidas','bata','aarong','aarong'], [2490,1790,2290,990,1890,3490,4290,850,1290,690], 'piece'],
            ['home-appliances', ['Walton 1.5 Ton Inverter AC', 'Singer 230L Refrigerator', 'Miyako 1.8L Rice Cooker', 'Philips HD9200 Air Fryer', 'Walton 32-inch Smart TV', 'Vision 20L Microwave Oven', 'Miyako Electric Kettle 1.8L', 'Singer Front Load Washing Machine', 'Philips Steam Iron GC1740', 'Walton Blender & Juicer Set'], ['walton','singer','miyako','philips','walton','vision','miyako','singer','philips','walton'], [64900,38900,2490,11900,26900,10900,1590,57900,2990,4990], 'piece'],
            ['grocery', ['Chaldal Premium Miniket Rice 5kg', 'Pran UHT Milk 1L â€“ Pack of 6', 'Fresh Soybean Oil 5L', 'Radhuni Pure Turmeric Powder 200g', 'NestlÃ© Nido Fortified Milk 2.5kg', 'Pran Mango Juice 1L â€“ Pack of 4', 'Fresh Red Lentils 1kg', 'Aarong Dairy Ghee 400g', 'Bombay Sweets Chanachur 300g', 'Fresh Brown Eggs â€“ 12 pieces'], ['rfl','pran','unilever','rfl','nestle','pran','rfl','aarong','rfl','aarong'], [480,540,890,110,1890,360,145,620,120,165], 'pack'],
            ['beauty-personal-care', ['Nivea Soft Moisturizing Cream 200ml', 'Pondâ€™s Bright Beauty Face Wash 100g', 'Sunsilk Black Shine Shampoo 650ml', 'Garnier Micellar Cleansing Water', 'LakmÃ© 9 to 5 Primer + Matte Lip Color', 'Vaseline Intensive Care Lotion 400ml', 'Neutrogena Ultra Sheer SPF 50+', 'Dove Beauty Cream Bar â€“ Pack of 4', 'The Ordinary Niacinamide 10% + Zinc', 'Maybelline Fit Me Matte Foundation'], ['nivea','unilever','unilever','unilever','nivea','unilever','nivea','unilever','nivea','nivea'], [650,320,590,850,1190,690,1650,440,1450,1290], 'piece'],
            ['electronics', ['Sony WH-CH520 Wireless Headphones', 'JBL Tune 520BT Bluetooth Headphones', 'Sony SRS-XB100 Portable Speaker', 'JBL Go 4 Waterproof Speaker', 'Logitech M221 Silent Wireless Mouse', 'Philips TAT1108 True Wireless Earbuds', 'Anker 20W USB-C Fast Charger', 'Baseus 10000mAh Power Bank', 'JBL Wave Buds 2 Earbuds', 'Sony MDR-EX15AP Wired Earphones'], ['sony','jbl','sony','jbl','logitech','philips','samsung','xiaomi','jbl','sony'], [5490,5990,6990,4990,1890,2990,1690,2490,5990,990], 'piece'],
            ['home-living', ['RFL Premium 16-Piece Dinner Set', 'Cotton Bedsheet Set â€“ King Size', 'RFL Storage Rack 4-Tier', 'Miyako Non-stick Frying Pan 28cm', 'LED Table Lamp with USB Port', 'Memory Foam Pillow Pair', 'RFL 20L Insulated Water Jug', 'Modern Wall Clock â€“ Walnut', 'Stainless Steel Knife Set â€“ 6 pcs', 'Decorative Indoor Planter Set'], ['rfl','aarong','rfl','miyako','philips','aarong','rfl','rfl','rfl','aarong'], [2490,1890,3290,990,1450,1590,1250,890,1190,750], 'piece'],
            ['sports-outdoor', ['Adidas Runfalcon 3.0 Running Shoes', 'Puma Essentials Training T-Shirt', 'Yoga Mat 6mm with Carry Strap', 'Adjustable Dumbbell Set 20kg', 'Wilson Championship Tennis Balls â€“ 3 pack', 'Nylon Camping Tent for 2 People', 'Football Size 5 â€“ Match Quality', 'Stainless Steel Sports Water Bottle', 'Resistance Bands Set â€“ 5 Levels', 'Cycling Helmet with LED Rear Light'], ['adidas','puma','adidas','puma','adidas','puma','adidas','puma','adidas','puma'], [6990,1890,990,5490,1290,4990,1590,850,750,2290], 'piece'],
            ['baby-kids', ['LEGO Classic Creative Bricks Box', 'Baby Dove Sensitive Moisture Wash 400ml', 'Cotton Baby Romper Set â€“ 3 pcs', 'Fisher-Price Stack & Explore Blocks', 'Soft Baby Blanket â€“ Organic Cotton', 'Kidsâ€™ School Backpack â€“ Dinosaur', 'Johnsonâ€™s Baby Lotion 200ml', 'LEGO City Police Car Chase', 'Kidsâ€™ Watercolor Art Set â€“ 24 Colors', 'Baby Feeding Bottle 250ml â€“ BPA Free'], ['lego','unilever','aarong','lego','aarong','lego','unilever','lego','lego','philips'], [2690,590,1290,1490,1190,1690,390,3490,490,550], 'piece'],
            ['computers-laptops', ['HP 15s Core i5 13th Gen Laptop', 'Lenovo IdeaPad Slim 3 Ryzen 5', 'ASUS VivoBook 15 Core i3 Laptop', 'Dell Inspiron 14 Core i5 Laptop', 'Logitech K380 Bluetooth Keyboard', 'HP 280 Silent Wireless Mouse', 'Lenovo Tab M10 4th Gen 10.1-inch', 'Acer Aspire 5 Core i5 Laptop', 'Logitech C270 HD Webcam', 'ASUS 24-inch Full HD Monitor'], ['hp','lenovo','asus','dell','logitech','hp','lenovo','acer','logitech','asus'], [78900,65900,57900,89900,3290,1190,24900,72900,2990,22900], 'piece'],
        ];

        $missingBrands = collect($catalog)->flatMap(fn ($group) => $group[2])->unique()->diff(array_keys($brands));
        if ($missingBrands->isNotEmpty()) {
            throw new \RuntimeException('Demo catalog has no seeded brand for: ' . $missingBrands->implode(', '));
        }

        $productIds = [];
        foreach ($catalog as $groupIndex => [$categorySlug, $names, $brandSlugs, $prices, $unit]) {
            foreach ($names as $index => $name) {
                $number = $groupIndex * 10 + $index + 1;
                $slug = 'demo-' . str($name)->slug()->toString();
                $asset = $image($slug);
                $price = $prices[$index];
                $stock = 18 + (($number * 13) % 120);
                $discount = ($number % 3 === 0) ? 10 : (($number % 4 === 0) ? 5 : 0);
                $data = [
                    'name' => $name, 'added_by' => 'admin', 'user_id' => 1,
                    'category_id' => $categories[$categorySlug], 'brand_id' => $brands[$brandSlugs[$index]],
                    'photos' => (string) $asset, 'thumbnail_img' => (string) $asset,
                    'tags' => str_replace(' ', ',', strtolower($name)),
                    'description' => $name . ' â€” original product with reliable quality and convenient delivery across Bangladesh. Carefully selected for everyday use. Price shown in Bangladeshi Taka (à§³).',
                    'unit_price' => $price, 'purchase_price' => round($price * 0.72, 2),
                    'variant_product' => 0, 'attributes' => '[]', 'choice_options' => '[]', 'colors' => '[]',
                    'variations' => '[]', 'todays_deal' => ($number % 10 === 0 ? 1 : 0),
                    'published' => 1, 'approved' => 1, 'stock_visibility_state' => 'quantity',
                    'cash_on_delivery' => 1, 'featured' => ($number % 4 === 0 ? 1 : 0), 'seller_featured' => 0,
                    'current_stock' => $stock, 'unit' => $unit, 'min_qty' => 1,
                    'low_stock_quantity' => 5, 'discount' => $discount, 'discount_type' => 'percent',
                    'tax' => 0, 'tax_type' => 'percent', 'shipping_type' => 'flat_rate',
                    'shipping_cost' => '60', 'est_shipping_days' => 3, 'num_of_sale' => 12 + (($number * 7) % 240),
                    'meta_title' => $name, 'meta_description' => 'Shop ' . $name . ' in Bangladesh.',
                    'meta_img' => (string) $asset, 'slug' => $slug, 'rating' => 4.1 + (($number % 9) / 10),
                    'digital' => 0, 'auction_product' => 0, 'wholesale_product' => 0,
                    'updated_at' => $now, 'created_at' => $now,
                ];
                DB::table('products')->updateOrInsert(['slug' => $slug], $data);
                $productId = (int) DB::table('products')->where('slug', $slug)->value('id');
                $productIds[] = $productId;
                DB::table('product_translations')->updateOrInsert(['product_id' => $productId, 'lang' => 'en'], [
                    'name' => $name, 'unit' => $unit, 'description' => $data['description'],
                    'updated_at' => $now, 'created_at' => $now,
                ]);
                DB::table('product_stocks')->updateOrInsert(['product_id' => $productId, 'variant' => ''], [
                    'sku' => 'FLIX-DEMO-' . str_pad((string) $number, 3, '0', STR_PAD_LEFT),
                    'price' => round($price * (1 - $discount / 100), 2), 'qty' => $stock,
                    'image' => $asset, 'updated_at' => $now, 'created_at' => $now,
                ]);
            }
        }

        $customerEmail = 'demo.customer@flixshop.test';
        if (!DB::table('users')->where('email', $customerEmail)->exists()) {
            DB::table('users')->insert([
                'name' => 'Nusrat Jahan', 'email' => $customerEmail, 'user_type' => 'customer',
                'email_verified_at' => $now, 'password' => bcrypt('password'),
                'address' => 'House 12, Road 7, Dhanmondi', 'country' => 'BD', 'state' => 'Dhaka',
                'city' => 'Dhaka', 'postal_code' => '1209', 'phone' => '+8801700000001',
                'banned' => 0, 'updated_at' => $now, 'created_at' => $now,
            ]);
        }
        $customerId = (int) DB::table('users')->where('email', $customerEmail)->value('id');
        foreach (array_slice($productIds, 0, 40) as $index => $productId) {
            $rating = 4 + ($index % 2);
            DB::table('reviews')->updateOrInsert(['product_id' => $productId, 'user_id' => $customerId], [
                'rating' => $rating,
                'comment' => ['Good quality and exactly as described.', 'Fast delivery to Dhaka. Very happy with this purchase.', 'Great value for money; would recommend.', 'Packaging was neat and the product works perfectly.'][$index % 4],
                'status' => 1, 'viewed' => 1, 'updated_at' => $now,
                'created_at' => $now->copy()->subDays(($index % 28) + 1),
            ]);
        }

        $dealBanner = $image('flash-sale', 'banner');
        DB::table('flash_deals')->updateOrInsert(['slug' => 'flixshop-demo-flash-sale'], [
            'title' => 'This Weekâ€™s Best Deals', 'start_date' => $now->copy()->subDay()->timestamp,
            'end_date' => $now->copy()->addDays(14)->timestamp, 'status' => 1, 'featured' => 1,
            'background_color' => '#fff1f2', 'text_color' => '#111827', 'banner' => (string) $dealBanner,
            'updated_at' => $now, 'created_at' => $now,
        ]);
        $dealId = (int) DB::table('flash_deals')->where('slug', 'flixshop-demo-flash-sale')->value('id');
        foreach (array_slice($productIds, 0, 12) as $productId) {
            DB::table('flash_deal_products')->updateOrInsert(['flash_deal_id' => $dealId, 'product_id' => $productId], [
                'discount' => 10, 'discount_type' => 'percent', 'updated_at' => $now, 'created_at' => $now,
            ]);
        }

        $bdtId = DB::table('currencies')->where('code', 'BDT')->value('id');
        $settings = [
            'site_name' => 'FlixShop Bangladesh', 'system_name' => 'FlixShop',
            'home_default_currency' => (string) ($bdtId ?: 1), 'system_default_currency' => (string) ($bdtId ?: 1),
            'currency_format' => '1', 'symbol_format' => '1', 'no_of_decimals' => '2',
            'product_activation' => '1', 'vendor_system_activation' => '1', 'show_vendors' => '1',
            'best_selling' => '1', 'cash_payment' => '1', 'paypal_payment' => '1', 'stripe_payment' => '1',
            'payumoney_payment' => '1', 'sslcommerz_payment' => '1', 'wallet_system' => '1',
            'coupon_system' => '1', 'conversation_system' => '1', 'guest_checkout_active' => '1',
            'pickup_point' => '1', 'razorpay' => '1', 'paystack' => '1', 'iyzico' => '1',
            'nagad' => '1', 'bkash' => '1', 'proxypay' => '1', 'instamojo_payment' => '1',
            'voguepay' => '1', 'payhere' => '1', 'ngenius' => '1', 'maintenance_mode' => '0',
            'email_verification' => '0', 'home_slider_images' => json_encode([$image('home-slide-1','banner'), $image('home-slide-2','banner'), $image('home-slide-3','banner')]),
            'home_slider_links' => json_encode(['/shop','/deals','/shop']),
            'home_banner1_images' => json_encode([$image('home-banner-1','banner'), $image('home-banner-2','banner')]),
            'home_banner1_links' => json_encode(['/shop?category=mobile-phones','/shop?category=home-appliances']),
        ];
        foreach ($settings as $type => $value) {
            DB::table('business_settings')->updateOrInsert(['type' => $type], [
                'value' => $value, 'lang' => null, 'updated_at' => $now, 'created_at' => $now,
            ]);
        }

        $bd = DB::table('countries')->where('code', 'BD')->first();
        if ($bd) {
            DB::table('countries')->where('id', $bd->id)->update(['name' => 'Bangladesh', 'status' => 1, 'updated_at' => $now]);
            $countryId = (int) $bd->id;
        } else {
            $countryId = (int) DB::table('countries')->insertGetId(['code' => 'BD', 'name' => 'Bangladesh', 'status' => 1, 'created_at' => $now, 'updated_at' => $now]);
        }
        foreach (['Dhaka', 'Chattogram', 'Khulna', 'Sylhet', 'Rajshahi'] as $cityName) {
            DB::table('states')->updateOrInsert(['country_id' => $countryId, 'name' => $cityName], ['status' => 1, 'updated_at' => $now, 'created_at' => $now]);
            $stateId = (int) DB::table('states')->where('country_id', $countryId)->where('name', $cityName)->value('id');
            DB::table('cities')->updateOrInsert(['state_id' => $stateId, 'name' => $cityName], ['status' => 1, 'cost' => 60, 'updated_at' => $now, 'created_at' => $now]);
        }
        $dhakaStateId = (int) DB::table('states')->where('country_id', $countryId)->where('name', 'Dhaka')->value('id');
        $dhakaCityId = (int) DB::table('cities')->where('state_id', $dhakaStateId)->where('name', 'Dhaka')->value('id');
        DB::table('addresses')->updateOrInsert(['user_id' => $customerId, 'address' => 'House 12, Road 7, Dhanmondi'], [
            'country_id' => $countryId, 'state_id' => $dhakaStateId, 'city_id' => $dhakaCityId,
            'postal_code' => '1209', 'phone' => '+8801700000001', 'set_default' => 1,
            'updated_at' => $now, 'created_at' => $now,
        ]);

        // Payment switches are enabled for UI coverage; real gateway credentials still belong in .env.
    }
}
