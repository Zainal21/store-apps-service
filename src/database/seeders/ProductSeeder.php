<?php

namespace Database\Seeders;

use App\Models\Product;
use Faker\Factory as Faker;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_id');
        $sources = [];
        for ($i=0; $i < 2000; $i++) { 
            $product_category = ProductCategory::create([
                'categories_name' => $faker->sentence(3),
                'categories_description' => $faker->sentence(12),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            Product::create([
                'product_code' => $this->create_product_code(),
                'product_name' =>  $faker->sentence(3),
                'product_description' =>  $faker->sentence(3),
                'product_category_id' => $product_category->id,
                'price_sale' => 100000,
                'discount' =>  mt_rand(1,3),
                'discount_persentage' => 10,
                'is_available' => 1,
            ]);
        }
    }

    protected function create_product_code()
    {
        $day = date('d');
        $month = date('m');
        $years = date('Y');
        $yearFormat = date('y');
        $baseOrdered = "00000";
        $data = Product::selectRaw('max(RIGHT(product_code, 4)) as last_order')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $years)
                ->whereDay('created_at', $day)
                ->orderBy(DB::raw('max(RIGHT(product_code, 4))', 'DESC'))->take(1)->first();
        if ($data) $baseOrdered = $data->last_order;
        $nextOrdered = abs($baseOrdered) + 1;
        $uniqueCode = 'PRD' . $day . $month . $yearFormat . sprintf('%05d', $nextOrdered);
        return $uniqueCode;
    }
}
