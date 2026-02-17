<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class BilingualProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create bilingual categories
        $categories = [
            ['name' => 'Electronics / គ្រឿងអេឡិចត្រូនិច', 'description' => 'Electronic devices and accessories / គ្រឿងអេឡិចត្រូនិច និងគ្រឿងបន្លាស់', 'is_active' => true],
            ['name' => 'Food & Beverages / អាហារ និងភេសជ្ជៈ', 'description' => 'Food and drink products / ផលិតផលអាហារ និងភេសជ្ជៈ', 'is_active' => true],
            ['name' => 'Clothing / សម្លៀកបំពាក់', 'description' => 'Apparel and fashion items / សម្លៀកបំពាក់ និងម៉ូដ', 'is_active' => true],
            ['name' => 'Health & Beauty / សុខភាព និងសម្រស់', 'description' => 'Health and beauty products / ផលិតផលសុខភាព និងសម្រស់', 'is_active' => true],
            ['name' => 'Home & Living / ផ្ទះ និងការរស់នៅ', 'description' => 'Home and living essentials / របស់ចាំបាច់ក្នុងផ្ទះ', 'is_active' => true],
            ['name' => 'Stationery / សម្ភារៈការិយាល័យ', 'description' => 'Office and school supplies / សម្ភារៈការិយាល័យ និងសាលា', 'is_active' => true],
            ['name' => 'Snacks / ចំណីអាហារ', 'description' => 'Snacks and quick bites / ចំណីអាហារ និងអាហារឆាប់ៗ', 'is_active' => true],
        ];

        // Clear existing categories and create new ones
        Category::truncate();
        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create comprehensive bilingual products
        $products = [
            // Electronics / គ្រឿងអេឡិចត្រូនិច
            [
                'category_id' => 1,
                'name' => 'Smartphone / ទូរស័ព្ទស្មាតហ្វូន',
                'description' => 'Latest Android smartphone with dual camera / ទូរស័ព្ទអេនដ្រូអ៊ីតថ្មីបំផុត ជាមួយកាមេរ៉ាទ្វេ',
                'sku' => 'ELEC-001',
                'price' => 299.99,
                'cost' => 200.00,
                'stock' => 25,
                'min_stock' => 5,
                'discount' => 10,
                'is_active' => true
            ],
            [
                'category_id' => 1,
                'name' => 'Wireless Earbuds / កាសស្តាប់ឥតខ្សែ',
                'description' => 'Bluetooth wireless earbuds with charging case / កាសស្តាប់ប៊្លូធូសឥតខ្សែ ជាមួយប្រអប់បញ្ចូលថ្ម',
                'sku' => 'ELEC-002',
                'price' => 79.99,
                'cost' => 45.00,
                'stock' => 40,
                'min_stock' => 8,
                'discount' => 15,
                'is_active' => true
            ],
            [
                'category_id' => 1,
                'name' => 'Power Bank / ថ្មបម្រុង',
                'description' => '10000mAh portable power bank / ថ្មបម្រុងចល័ត 10000mAh',
                'sku' => 'ELEC-003',
                'price' => 35.99,
                'cost' => 20.00,
                'stock' => 60,
                'min_stock' => 12,
                'is_active' => true
            ],
            [
                'category_id' => 1,
                'name' => 'USB Cable / ខ្សែ USB',
                'description' => 'Fast charging USB-C cable / ខ្សែ USB-C សម្រាប់បញ្ចូលថ្មលឿន',
                'sku' => 'ELEC-004',
                'price' => 12.99,
                'cost' => 6.00,
                'stock' => 100,
                'min_stock' => 20,
                'discount' => 5,
                'is_active' => true
            ],
            [
                'category_id' => 1,
                'name' => 'Bluetooth Speaker / ឧបករណ៍បំពងសំឡេងប៊្លូធូស',
                'description' => 'Portable Bluetooth speaker with bass / ឧបករណ៍បំពងសំឡេងប៊្លូធូសចល័ត ជាមួយសំឡេងបាស',
                'sku' => 'ELEC-005',
                'price' => 59.99,
                'cost' => 35.00,
                'stock' => 30,
                'min_stock' => 6,
                'discount' => 20,
                'is_active' => true
            ],

            // Food & Beverages / អាហារ និងភេសជ្ជៈ
            [
                'category_id' => 2,
                'name' => 'Instant Coffee / កាហ្វេរហ័ស',
                'description' => '3-in-1 instant coffee mix / កាហ្វេរហ័ស 3 ក្នុង 1',
                'sku' => 'FOOD-001',
                'price' => 8.99,
                'cost' => 4.50,
                'stock' => 80,
                'min_stock' => 15,
                'is_active' => true
            ],
            [
                'category_id' => 2,
                'name' => 'Green Tea / តែបៃតង',
                'description' => 'Premium green tea bags / តែបៃតងគុណភាពខ្ពស់',
                'sku' => 'FOOD-002',
                'price' => 12.99,
                'cost' => 7.00,
                'stock' => 50,
                'min_stock' => 10,
                'discount' => 8,
                'is_active' => true
            ],
            [
                'category_id' => 2,
                'name' => 'Energy Drink / ភេសជ្ជៈថាមពល',
                'description' => 'Refreshing energy drink / ភេសជ្ជៈថាមពលធ្វើឱ្យស្រស់',
                'sku' => 'FOOD-003',
                'price' => 2.99,
                'cost' => 1.50,
                'stock' => 120,
                'min_stock' => 25,
                'is_active' => true
            ],
            [
                'category_id' => 2,
                'name' => 'Coconut Water / ទឹកដូង',
                'description' => 'Fresh coconut water / ទឹកដូងស្រស់',
                'sku' => 'FOOD-004',
                'price' => 1.99,
                'cost' => 1.00,
                'stock' => 90,
                'min_stock' => 20,
                'discount' => 12,
                'is_active' => true
            ],
            [
                'category_id' => 2,
                'name' => 'Mineral Water / ទឹកសុទ្ធ',
                'description' => '500ml mineral water bottle / ទឹកសុទ្ធ 500ml',
                'sku' => 'FOOD-005',
                'price' => 0.99,
                'cost' => 0.40,
                'stock' => 200,
                'min_stock' => 50,
                'is_active' => true
            ],

            // Clothing / សម្លៀកបំពាក់
            [
                'category_id' => 3,
                'name' => 'Cotton T-Shirt / អាវយឺតកប្បាស',
                'description' => '100% cotton comfortable t-shirt / អាវយឺតកប្បាស 100% ស្រួល',
                'sku' => 'CLO-001',
                'price' => 18.99,
                'cost' => 10.00,
                'stock' => 45,
                'min_stock' => 8,
                'discount' => 15,
                'is_active' => true
            ],
            [
                'category_id' => 3,
                'name' => 'Jeans / ខោជីន',
                'description' => 'Classic blue denim jeans / ខោជីនពណ៌ខៀវបុរាណ',
                'sku' => 'CLO-002',
                'price' => 45.99,
                'cost' => 25.00,
                'stock' => 30,
                'min_stock' => 6,
                'is_active' => true
            ],
            [
                'category_id' => 3,
                'name' => 'Baseball Cap / មួកបេសបល',
                'description' => 'Adjustable baseball cap / មួកបេសបលអាចកែបាន',
                'sku' => 'CLO-003',
                'price' => 15.99,
                'cost' => 8.00,
                'stock' => 35,
                'min_stock' => 7,
                'discount' => 10,
                'is_active' => true
            ],
            [
                'category_id' => 3,
                'name' => 'Sneakers / ស្បែកជើងកីឡា',
                'description' => 'Comfortable sports sneakers / ស្បែកជើងកីឡាស្រួល',
                'sku' => 'CLO-004',
                'price' => 89.99,
                'cost' => 50.00,
                'stock' => 20,
                'min_stock' => 4,
                'discount' => 25,
                'is_active' => true
            ],

            // Health & Beauty / សុខភាព និងសម្រស់
            [
                'category_id' => 4,
                'name' => 'Face Mask / ម៉ាស់មុខ',
                'description' => 'Moisturizing face mask / ម៉ាស់មុខផ្តល់សំណើម',
                'sku' => 'HEAL-001',
                'price' => 3.99,
                'cost' => 2.00,
                'stock' => 75,
                'min_stock' => 15,
                'is_active' => true
            ],
            [
                'category_id' => 4,
                'name' => 'Hand Sanitizer / ជែលសម្អាតដៃ',
                'description' => '70% alcohol hand sanitizer / ជែលសម្អាតដៃអាល់កុល 70%',
                'sku' => 'HEAL-002',
                'price' => 4.99,
                'cost' => 2.50,
                'stock' => 100,
                'min_stock' => 20,
                'discount' => 5,
                'is_active' => true
            ],
            [
                'category_id' => 4,
                'name' => 'Shampoo / សាប៊ូកក់សក់',
                'description' => 'Herbal shampoo for all hair types / សាប៊ូកក់សក់ឱសថសម្រាប់សក់គ្រប់ប្រភេទ',
                'sku' => 'HEAL-003',
                'price' => 12.99,
                'cost' => 7.00,
                'stock' => 40,
                'min_stock' => 8,
                'is_active' => true
            ],
            [
                'category_id' => 4,
                'name' => 'Toothbrush / ច្រាសដុសធ្មេញ',
                'description' => 'Soft bristle toothbrush / ច្រាសដុសធ្មេញរោមទន់',
                'sku' => 'HEAL-004',
                'price' => 2.99,
                'cost' => 1.20,
                'stock' => 80,
                'min_stock' => 16,
                'is_active' => true
            ],

            // Home & Living / ផ្ទះ និងការរស់នៅ
            [
                'category_id' => 5,
                'name' => 'Scented Candle / ទៀនក្លិន',
                'description' => 'Lavender scented candle / ទៀនក្លិនឡាវេនឌ័រ',
                'sku' => 'HOME-001',
                'price' => 9.99,
                'cost' => 5.00,
                'stock' => 50,
                'min_stock' => 10,
                'discount' => 20,
                'is_active' => true
            ],
            [
                'category_id' => 5,
                'name' => 'Plant Pot / ចាំងដាំដើម',
                'description' => 'Ceramic plant pot with drainage / ចាំងដាំដើមសេរ៉ាមិច មានរន្ធបង្ហូរទឹក',
                'sku' => 'HOME-002',
                'price' => 16.99,
                'cost' => 9.00,
                'stock' => 30,
                'min_stock' => 6,
                'is_active' => true
            ],
            [
                'category_id' => 5,
                'name' => 'Picture Frame / ស៊ុមរូបថត',
                'description' => 'Wooden picture frame 8x10 / ស៊ុមរូបថតឈើ 8x10',
                'sku' => 'HOME-003',
                'price' => 22.99,
                'cost' => 12.00,
                'stock' => 25,
                'min_stock' => 5,
                'is_active' => true
            ],
            [
                'category_id' => 5,
                'name' => 'Kitchen Towel / កន្សែងផ្ទះបាយ',
                'description' => 'Absorbent kitchen towel set / កន្សែងផ្ទះបាយស្រូបទឹក',
                'sku' => 'HOME-004',
                'price' => 7.99,
                'cost' => 4.00,
                'stock' => 60,
                'min_stock' => 12,
                'discount' => 15,
                'is_active' => true
            ],

            // Stationery / សម្ភារៈការិយាល័យ
            [
                'category_id' => 6,
                'name' => 'Notebook / សៀវភៅកត់ត្រា',
                'description' => 'A4 lined notebook 200 pages / សៀវភៅកត់ត្រា A4 មានបន្ទាត់ 200 ទំព័រ',
                'sku' => 'STAT-001',
                'price' => 5.99,
                'cost' => 3.00,
                'stock' => 70,
                'min_stock' => 14,
                'is_active' => true
            ],
            [
                'category_id' => 6,
                'name' => 'Ballpoint Pen / ប៊ិចបាល់ពយិន',
                'description' => 'Blue ink ballpoint pen / ប៊ិចបាល់ពយិនទឹកថ្នាំខៀវ',
                'sku' => 'STAT-002',
                'price' => 1.99,
                'cost' => 0.80,
                'stock' => 150,
                'min_stock' => 30,
                'is_active' => true
            ],
            [
                'category_id' => 6,
                'name' => 'Highlighter / ប៊ិចបន្លិច',
                'description' => 'Yellow highlighter marker / ប៊ិចបន្លិចពណ៌លឿង',
                'sku' => 'STAT-003',
                'price' => 2.99,
                'cost' => 1.50,
                'stock' => 80,
                'min_stock' => 16,
                'discount' => 10,
                'is_active' => true
            ],
            [
                'category_id' => 6,
                'name' => 'Sticky Notes / ក្រដាសស្ទីគ័រ',
                'description' => 'Colorful sticky notes pack / ក្រដាសស្ទីគ័រចម្រុះពណ៌',
                'sku' => 'STAT-004',
                'price' => 4.99,
                'cost' => 2.50,
                'stock' => 90,
                'min_stock' => 18,
                'is_active' => true
            ],

            // Snacks / ចំណីអាហារ
            [
                'category_id' => 7,
                'name' => 'Potato Chips / ដំឡូងបារាំងចៀន',
                'description' => 'Crispy potato chips / ដំឡូងបារាំងចៀនកកេប',
                'sku' => 'SNACK-001',
                'price' => 2.49,
                'cost' => 1.20,
                'stock' => 100,
                'min_stock' => 20,
                'is_active' => true
            ],
            [
                'category_id' => 7,
                'name' => 'Chocolate Bar / ចុកូឡាត',
                'description' => 'Milk chocolate bar / ចុកូឡាតទឹកដោះគោ',
                'sku' => 'SNACK-002',
                'price' => 3.99,
                'cost' => 2.00,
                'stock' => 80,
                'min_stock' => 16,
                'discount' => 5,
                'is_active' => true
            ],
            [
                'category_id' => 7,
                'name' => 'Cookies / នំខូគី',
                'description' => 'Assorted cookies pack / នំខូគីចម្រុះ',
                'sku' => 'SNACK-003',
                'price' => 4.99,
                'cost' => 2.50,
                'stock' => 60,
                'min_stock' => 12,
                'is_active' => true
            ],
            [
                'category_id' => 7,
                'name' => 'Dried Fruit / ផ្លែឈើស្ងួត',
                'description' => 'Mixed dried fruit snack / ផ្លែឈើស្ងួតចម្រុះ',
                'sku' => 'SNACK-004',
                'price' => 6.99,
                'cost' => 3.50,
                'stock' => 45,
                'min_stock' => 9,
                'discount' => 12,
                'is_active' => true
            ],
            [
                'category_id' => 7,
                'name' => 'Instant Noodles / មីកញ្ចប់',
                'description' => 'Spicy instant noodles / មីកញ្ចប់ហឹរ',
                'sku' => 'SNACK-005',
                'price' => 1.49,
                'cost' => 0.70,
                'stock' => 120,
                'min_stock' => 24,
                'is_active' => true
            ],
        ];

        // Clear existing products and create new ones
        Product::truncate();
        foreach ($products as $product) {
            Product::create($product);
        }
    }
}