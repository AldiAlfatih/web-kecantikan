<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductAndShadeSeeder extends Seeder
{
    public function run(): void
    {
        $products = [

            // =========================
            // WARDAH
            // =========================
            [
                'brand' => 'Wardah',
                'name' => 'Colorfit Perfect Glow Cushion',
                'category' => 'Cushion',
                'price' => 125000,
                'stock' => 120,
                'skin_type' => 'normal',
                'description' => 'Cushion dengan hasil glow natural.',
                'shades' => [
                    ['shade_name'=>'11 Porcelain','tone'=>'fair','undertone'=>'cool','hex_color'=>'#F4D8CC','stock'=>18],
                    ['shade_name'=>'12 Ivory','tone'=>'fair','undertone'=>'warm','hex_color'=>'#F1D1BF','stock'=>17],
                    ['shade_name'=>'21 Light Beige','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E7C2A6','stock'=>16],
                    ['shade_name'=>'22 Beige','tone'=>'medium','undertone'=>'warm','hex_color'=>'#DDB095','stock'=>15],
                    ['shade_name'=>'23 Natural','tone'=>'medium','undertone'=>'neutral','hex_color'=>'#D4A588','stock'=>14],
                ],
            ],
            [
                'brand' => 'Wardah',
                'name' => 'Colorfit Matte Foundation',
                'category' => 'Foundation',
                'price' => 99000,
                'stock' => 90,
                'skin_type' => 'berminyak',
                'description' => 'Foundation matte ringan untuk daily wear.',
                'shades' => [
                    ['shade_name'=>'10 Fair','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F4DACE','stock'=>16],
                    ['shade_name'=>'11 Ivory','tone'=>'fair','undertone'=>'warm','hex_color'=>'#F1D1BF','stock'=>15],
                    ['shade_name'=>'20 Light Beige','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E6C2A6','stock'=>14],
                    ['shade_name'=>'21 Natural','tone'=>'medium','undertone'=>'neutral','hex_color'=>'#D6A789','stock'=>13],
                    ['shade_name'=>'22 Warm Beige','tone'=>'medium','undertone'=>'warm','hex_color'=>'#CC9D7D','stock'=>12],
                    ['shade_name'=>'23 Honey','tone'=>'tan','undertone'=>'warm','hex_color'=>'#BC8967','stock'=>11],
                ],
            ],
            [
                'brand' => 'Wardah',
                'name' => 'Colorfit Velvet Powder Foundation',
                'category' => 'Compact Powder',
                'price' => 89000,
                'stock' => 100,
                'skin_type' => 'berminyak',
                'description' => 'Powder foundation untuk hasil halus dan matte.',
                'shades' => [
                    ['shade_name'=>'01 Light','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F1DACB','stock'=>16],
                    ['shade_name'=>'02 Beige','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E6C2A6','stock'=>14],
                    ['shade_name'=>'03 Natural','tone'=>'medium','undertone'=>'neutral','hex_color'=>'#D3A484','stock'=>12],
                    ['shade_name'=>'04 Honey','tone'=>'tan','undertone'=>'warm','hex_color'=>'#BF8D69','stock'=>9],
                ],
            ],
            [
                'brand' => 'Wardah',
                'name' => 'Colorfit Cover Concealer',
                'category' => 'Concealer',
                'price' => 75000,
                'stock' => 110,
                'skin_type' => 'sensitif',
                'description' => 'Concealer ringan untuk menutup noda dan bekas jerawat.',
                'shades' => [
                    ['shade_name'=>'01 Light','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F2D8CB','stock'=>20],
                    ['shade_name'=>'02 Beige','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E5C2A8','stock'=>18],
                    ['shade_name'=>'03 Natural','tone'=>'medium','undertone'=>'warm','hex_color'=>'#D4A587','stock'=>14],
                ],
            ],
            [
                'brand' => 'Wardah',
                'name' => 'Everyday BB Cream',
                'category' => 'BB Cream',
                'price' => 42000,
                'stock' => 140,
                'skin_type' => 'kering',
                'description' => 'BB cream ringan untuk sehari-hari.',
                'shades' => [
                    ['shade_name'=>'Light','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F2D8CC','stock'=>18],
                    ['shade_name'=>'Natural','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E7C4AA','stock'=>16],
                    ['shade_name'=>'Beige','tone'=>'medium','undertone'=>'neutral','hex_color'=>'#D5A688','stock'=>14],
                    ['shade_name'=>'Honey','tone'=>'tan','undertone'=>'warm','hex_color'=>'#C3906D','stock'=>12],
                ],
            ],

            // =========================
            // MAKE OVER
            // =========================
            [
                'brand' => 'Make Over',
                'name' => 'Powerstay Demi Matte Cover Cushion',
                'category' => 'Cushion',
                'price' => 199000,
                'stock' => 85,
                'skin_type' => 'kombinasi',
                'description' => 'Cushion dengan coverage medium to full.',
                'shades' => [
                    ['shade_name'=>'N10 Porcelain','tone'=>'fair','undertone'=>'cool','hex_color'=>'#F5DBD0','stock'=>14],
                    ['shade_name'=>'N20 Ivory','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F2D4C5','stock'=>13],
                    ['shade_name'=>'W22 Warm Ivory','tone'=>'light','undertone'=>'warm','hex_color'=>'#ECC9B3','stock'=>12],
                    ['shade_name'=>'N30 Natural Beige','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E2BDA0','stock'=>11],
                    ['shade_name'=>'W33 Warm Sand','tone'=>'medium','undertone'=>'warm','hex_color'=>'#D2A17F','stock'=>10],
                    ['shade_name'=>'W42 Honey','tone'=>'tan','undertone'=>'warm','hex_color'=>'#BE8C68','stock'=>8],
                ],
            ],
            [
                'brand' => 'Make Over',
                'name' => 'Powerstay Weightless Liquid Foundation',
                'category' => 'Foundation',
                'price' => 205000,
                'stock' => 75,
                'skin_type' => 'kombinasi',
                'description' => 'Longwear foundation.',
                'shades' => [
                    ['shade_name'=>'N10 Porcelain','tone'=>'fair','undertone'=>'cool','hex_color'=>'#F4DBCE','stock'=>13],
                    ['shade_name'=>'N20 Ivory','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F0D1C0','stock'=>12],
                    ['shade_name'=>'W22 Warm Ivory','tone'=>'light','undertone'=>'warm','hex_color'=>'#E9C5AD','stock'=>11],
                    ['shade_name'=>'N30 Natural Beige','tone'=>'light','undertone'=>'neutral','hex_color'=>'#DFB99C','stock'=>10],
                    ['shade_name'=>'W33 Warm Sand','tone'=>'medium','undertone'=>'warm','hex_color'=>'#CF9E7D','stock'=>9],
                    ['shade_name'=>'W42 Honey','tone'=>'tan','undertone'=>'warm','hex_color'=>'#BB8764','stock'=>8],
                    ['shade_name'=>'D50 Deep Tan','tone'=>'deep','undertone'=>'neutral','hex_color'=>'#9E6E52','stock'=>6],
                ],
            ],
            [
                'brand' => 'Make Over',
                'name' => 'Powerstay Total Cover Liquid Concealer',
                'category' => 'Concealer',
                'price' => 119000,
                'stock' => 70,
                'skin_type' => 'normal',
                'description' => 'Concealer cair untuk coverage tinggi.',
                'shades' => [
                    ['shade_name'=>'Light','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F0D8CC','stock'=>16],
                    ['shade_name'=>'Crème','tone'=>'light','undertone'=>'warm','hex_color'=>'#E7C4AA','stock'=>14],
                    ['shade_name'=>'Medium','tone'=>'medium','undertone'=>'neutral','hex_color'=>'#D5A78C','stock'=>12],
                ],
            ],
            [
                'brand' => 'Make Over',
                'name' => 'Powerstay Matte Powder Foundation',
                'category' => 'Compact Powder',
                'price' => 179000,
                'stock' => 65,
                'skin_type' => 'berminyak',
                'description' => 'Powder foundation matte untuk kontrol minyak.',
                'shades' => [
                    ['shade_name'=>'N20 Ivory','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F0D2C2','stock'=>12],
                    ['shade_name'=>'W22 Warm Ivory','tone'=>'light','undertone'=>'warm','hex_color'=>'#E8C4AB','stock'=>10],
                    ['shade_name'=>'N30 Natural Beige','tone'=>'light','undertone'=>'neutral','hex_color'=>'#DDB797','stock'=>9],
                    ['shade_name'=>'W33 Warm Sand','tone'=>'medium','undertone'=>'warm','hex_color'=>'#CC9B7A','stock'=>7],
                ],
            ],

            // =========================
            // EMINA
            // =========================
            [
                'brand' => 'Emina',
                'name' => 'Bare With Me Mineral Cushion',
                'category' => 'Cushion',
                'price' => 119000,
                'stock' => 100,
                'skin_type' => 'normal',
                'description' => 'Cushion ringan untuk hasil natural.',
                'shades' => [
                    ['shade_name'=>'Light','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F3DACC','stock'=>16],
                    ['shade_name'=>'Natural','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E7C4A8','stock'=>15],
                    ['shade_name'=>'Beige','tone'=>'medium','undertone'=>'neutral','hex_color'=>'#D8AA8B','stock'=>14],
                    ['shade_name'=>'Caramel','tone'=>'tan','undertone'=>'warm','hex_color'=>'#C5926F','stock'=>13],
                ],
            ],
            [
                'brand' => 'Emina',
                'name' => 'Beauty Bliss BB Cream',
                'category' => 'BB Cream',
                'price' => 42000,
                'stock' => 140,
                'skin_type' => 'kering',
                'description' => 'BB cream ringan untuk daily makeup.',
                'shades' => [
                    ['shade_name'=>'Light','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F1D7CB','stock'=>22],
                    ['shade_name'=>'Natural','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E6C3AA','stock'=>20],
                    ['shade_name'=>'Caramel','tone'=>'medium','undertone'=>'warm','hex_color'=>'#D0A282','stock'=>18],
                ],
            ],
            [
                'brand' => 'Emina',
                'name' => 'Daily Matte Loose Powder',
                'category' => 'Loose Powder',
                'price' => 62000,
                'stock' => 120,
                'skin_type' => 'berminyak',
                'description' => 'Loose powder untuk tampilan matte natural.',
                'shades' => [
                    ['shade_name'=>'Light Beige','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F0DACD','stock'=>20],
                    ['shade_name'=>'Natural Beige','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E4C2A9','stock'=>18],
                    ['shade_name'=>'Amber','tone'=>'medium','undertone'=>'warm','hex_color'=>'#CEA080','stock'=>15],
                ],
            ],

            // =========================
            // IMPLORA
            // =========================
            [
                'brand' => 'Implora',
                'name' => 'Perfect Shield BB Cream',
                'category' => 'BB Cream',
                'price' => 35000,
                'stock' => 150,
                'skin_type' => 'normal',
                'description' => 'BB cream praktis untuk sehari-hari.',
                'shades' => [
                    ['shade_name'=>'Natural','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E6C0A4','stock'=>24],
                    ['shade_name'=>'Beige','tone'=>'medium','undertone'=>'neutral','hex_color'=>'#D3A180','stock'=>22],
                    ['shade_name'=>'Caramel','tone'=>'tan','undertone'=>'warm','hex_color'=>'#BF8A67','stock'=>20],
                ],
            ],
            [
                'brand' => 'Implora',
                'name' => 'Liquid Concealer',
                'category' => 'Concealer',
                'price' => 32000,
                'stock' => 160,
                'skin_type' => 'normal',
                'description' => 'Concealer cair praktis dengan coverage medium.',
                'shades' => [
                    ['shade_name'=>'Ivory','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F1D8CC','stock'=>24],
                    ['shade_name'=>'Beige','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E5C1A8','stock'=>20],
                    ['shade_name'=>'Honey','tone'=>'medium','undertone'=>'warm','hex_color'=>'#D3A282','stock'=>18],
                ],
            ],

            // =========================
            // HANASUI
            // =========================
            [
                'brand' => 'Hanasui',
                'name' => 'Serum Cushion',
                'category' => 'Cushion',
                'price' => 79000,
                'stock' => 120,
                'skin_type' => 'normal',
                'description' => 'Cushion dengan hasil serum glow.',
                'shades' => [
                    ['shade_name'=>'Light','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F3D9CD','stock'=>18],
                    ['shade_name'=>'Natural','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E8C4A9','stock'=>17],
                    ['shade_name'=>'Beige','tone'=>'medium','undertone'=>'neutral','hex_color'=>'#D8A98A','stock'=>15],
                    ['shade_name'=>'Honey','tone'=>'tan','undertone'=>'warm','hex_color'=>'#C4926F','stock'=>14],
                    ['shade_name'=>'Caramel','tone'=>'deep','undertone'=>'neutral','hex_color'=>'#A7775A','stock'=>13],
                ],
            ],
            [
                'brand' => 'Hanasui',
                'name' => 'Perfect Cover Concealer',
                'category' => 'Concealer',
                'price' => 45000,
                'stock' => 140,
                'skin_type' => 'normal',
                'description' => 'Concealer praktis untuk sehari-hari.',
                'shades' => [
                    ['shade_name'=>'Light','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F2D8CC','stock'=>20],
                    ['shade_name'=>'Natural','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E7C4AA','stock'=>18],
                    ['shade_name'=>'Beige','tone'=>'medium','undertone'=>'neutral','hex_color'=>'#D5A789','stock'=>16],
                    ['shade_name'=>'Honey','tone'=>'tan','undertone'=>'warm','hex_color'=>'#C18F6D','stock'=>14],
                ],
            ],

            // =========================
            // SOMETHINC
            // =========================
            [
                'brand' => 'Somethinc',
                'name' => 'Copy Paste Breathable Mesh Cushion',
                'category' => 'Cushion',
                'price' => 169000,
                'stock' => 95,
                'skin_type' => 'normal',
                'description' => 'Breathable cushion.',
                'shades' => [
                    ['shade_name'=>'00 Affogato','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F4DDD0','stock'=>15],
                    ['shade_name'=>'01 Butter Cream','tone'=>'light','undertone'=>'warm','hex_color'=>'#EFCDB6','stock'=>14],
                    ['shade_name'=>'02 Praline','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E5C0A2','stock'=>13],
                    ['shade_name'=>'03 Custard','tone'=>'medium','undertone'=>'neutral','hex_color'=>'#D6A98A','stock'=>12],
                    ['shade_name'=>'04 Ginger','tone'=>'tan','undertone'=>'warm','hex_color'=>'#C28F6D','stock'=>11],
                    ['shade_name'=>'05 Cinnamon','tone'=>'deep','undertone'=>'neutral','hex_color'=>'#9D7155','stock'=>10],
                ],
            ],
            [
                'brand' => 'Somethinc',
                'name' => 'Copy Paste Concealer',
                'category' => 'Concealer',
                'price' => 119000,
                'stock' => 85,
                'skin_type' => 'sensitif',
                'description' => 'Concealer ringan untuk noda dan area bawah mata.',
                'shades' => [
                    ['shade_name'=>'Vanilla','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F1D9CC','stock'=>18],
                    ['shade_name'=>'Custard','tone'=>'light','undertone'=>'warm','hex_color'=>'#E7C3A8','stock'=>16],
                    ['shade_name'=>'Praline','tone'=>'medium','undertone'=>'neutral','hex_color'=>'#D2A486','stock'=>14],
                ],
            ],

            // =========================
            // SKINTIFIC
            // =========================
            [
                'brand' => 'Skintific',
                'name' => 'Cover All Perfect Cushion',
                'category' => 'Cushion',
                'price' => 179000,
                'stock' => 80,
                'skin_type' => 'kombinasi',
                'description' => 'High coverage cushion.',
                'shades' => [
                    ['shade_name'=>'01 Vanilla','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F2D9CB','stock'=>13],
                    ['shade_name'=>'02 Ivory','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E9C5A9','stock'=>12],
                    ['shade_name'=>'03 Petal','tone'=>'medium','undertone'=>'neutral','hex_color'=>'#D8A989','stock'=>11],
                    ['shade_name'=>'03A Almond','tone'=>'medium','undertone'=>'warm','hex_color'=>'#CE9C7B','stock'=>10],
                    ['shade_name'=>'04 Beige','tone'=>'tan','undertone'=>'warm','hex_color'=>'#C08A67','stock'=>9],
                ],
            ],

            // =========================
            // LUXCRIME
            // =========================
            [
                'brand' => 'Luxcrime',
                'name' => '2nd Skin Luminous Cushion',
                'category' => 'Cushion',
                'price' => 189000,
                'stock' => 85,
                'skin_type' => 'normal',
                'description' => 'Luminous cushion.',
                'shades' => [
                    ['shade_name'=>'01 Ivory','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F2D7C9','stock'=>14],
                    ['shade_name'=>'02 Light Beige','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E8C4A8','stock'=>13],
                    ['shade_name'=>'03 Natural','tone'=>'medium','undertone'=>'neutral','hex_color'=>'#D6A787','stock'=>12],
                    ['shade_name'=>'04 Warm Sand','tone'=>'tan','undertone'=>'warm','hex_color'=>'#C3906D','stock'=>11],
                    ['shade_name'=>'05 Honey','tone'=>'tan','undertone'=>'warm','hex_color'=>'#BC8967','stock'=>10],
                ],
            ],

            // =========================
            // Y.O.U
            // =========================
            [
                'brand' => 'Y.O.U',
                'name' => 'NoutriWear+ Flawless Cushion',
                'category' => 'Cushion',
                'price' => 159000,
                'stock' => 85,
                'skin_type' => 'kombinasi',
                'description' => 'Flawless cushion.',
                'shades' => [
                    ['shade_name'=>'Porcelain','tone'=>'fair','undertone'=>'cool','hex_color'=>'#F4D8CC','stock'=>14],
                    ['shade_name'=>'Ivory','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F0D1BF','stock'=>13],
                    ['shade_name'=>'Natural','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E6C2A6','stock'=>12],
                    ['shade_name'=>'Beige','tone'=>'medium','undertone'=>'neutral','hex_color'=>'#D4A587','stock'=>11],
                    ['shade_name'=>'Honey','tone'=>'tan','undertone'=>'warm','hex_color'=>'#C08B68','stock'=>10],
                ],
            ],

            // =========================
            // ESQA
            // =========================
            [
                'brand' => 'ESQA',
                'name' => 'Flawless Cushion Serum',
                'category' => 'Cushion',
                'price' => 199000,
                'stock' => 70,
                'skin_type' => 'normal',
                'description' => 'Luxury cushion.',
                'shades' => [
                    ['shade_name'=>'Vanilla','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F3DACC','stock'=>13],
                    ['shade_name'=>'Custard','tone'=>'light','undertone'=>'warm','hex_color'=>'#EBC8AE','stock'=>12],
                    ['shade_name'=>'Praline','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E1BCA0','stock'=>11],
                    ['shade_name'=>'Macchiato','tone'=>'medium','undertone'=>'neutral','hex_color'=>'#D4A688','stock'=>10],
                    ['shade_name'=>'Toffee','tone'=>'tan','undertone'=>'warm','hex_color'=>'#C08A66','stock'=>9],
                    ['shade_name'=>'Mocha','tone'=>'deep','undertone'=>'neutral','hex_color'=>'#9E6E52','stock'=>8],
                ],
            ],

            // =========================
            // BLP BEAUTY
            // =========================
            [
                'brand' => 'BLP Beauty',
                'name' => 'Face Base Cushion',
                'category' => 'Cushion',
                'price' => 189000,
                'stock' => 65,
                'skin_type' => 'normal',
                'description' => 'Cushion dengan hasil natural luminous.',
                'shades' => [
                    ['shade_name'=>'Light','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F2D9CB','stock'=>13],
                    ['shade_name'=>'Medium','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E7C4A8','stock'=>12],
                    ['shade_name'=>'Warm Beige','tone'=>'medium','undertone'=>'warm','hex_color'=>'#D5A585','stock'=>11],
                    ['shade_name'=>'Tan','tone'=>'tan','undertone'=>'warm','hex_color'=>'#C18C68','stock'=>10],
                    ['shade_name'=>'Deep','tone'=>'deep','undertone'=>'neutral','hex_color'=>'#9E7054','stock'=>8],
                ],
            ],

            // =========================
            // DEAR ME BEAUTY
            // =========================
            [
                'brand' => 'Dear Me Beauty',
                'name' => 'Airy Poreless Fluid Foundation',
                'category' => 'Foundation',
                'price' => 169000,
                'stock' => 65,
                'skin_type' => 'kombinasi',
                'description' => 'Foundation ringan dengan coverage buildable.',
                'shades' => [
                    ['shade_name'=>'01 Fair','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F2D8CA','stock'=>12],
                    ['shade_name'=>'02 Light Beige','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E6C1A5','stock'=>11],
                    ['shade_name'=>'03 Natural','tone'=>'medium','undertone'=>'neutral','hex_color'=>'#D3A382','stock'=>10],
                    ['shade_name'=>'04 Honey','tone'=>'tan','undertone'=>'warm','hex_color'=>'#C08B67','stock'=>9],
                ],
            ],

            // =========================
            // PURBASARI
            // =========================
            [
                'brand' => 'Purbasari',
                'name' => 'Luminous Matte Two Way Cake',
                'category' => 'Compact Powder',
                'price' => 42000,
                'stock' => 140,
                'skin_type' => 'berminyak',
                'description' => 'Two way cake dengan hasil luminous matte.',
                'shades' => [
                    ['shade_name'=>'01 Light','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F2DBCF','stock'=>20],
                    ['shade_name'=>'02 Natural','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E5C2A8','stock'=>18],
                    ['shade_name'=>'03 Kuning Langsat','tone'=>'medium','undertone'=>'warm','hex_color'=>'#D5A787','stock'=>16],
                    ['shade_name'=>'04 Sawo Matang','tone'=>'tan','undertone'=>'warm','hex_color'=>'#BF8D6B','stock'=>14],
                ],
            ],

            // =========================
            // AZARINE
            // =========================
            [
                'brand' => 'Azarine',
                'name' => 'Ceraspray Cushion',
                'category' => 'Cushion',
                'price' => 159000,
                'stock' => 75,
                'skin_type' => 'kombinasi',
                'description' => 'Cushion untuk tampilan natural sehat.',
                'shades' => [
                    ['shade_name'=>'01 Light','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F2D8CA','stock'=>12],
                    ['shade_name'=>'02 Natural','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E6C2A7','stock'=>11],
                    ['shade_name'=>'03 Beige','tone'=>'medium','undertone'=>'neutral','hex_color'=>'#D4A484','stock'=>10],
                    ['shade_name'=>'04 Honey','tone'=>'tan','undertone'=>'warm','hex_color'=>'#C08B68','stock'=>9],
                ],
            ],

            // =========================
            // MADAM GIE
            // =========================
            [
                'brand' => 'Madam Gie',
                'name' => 'Amazing Skin Cushion',
                'category' => 'Cushion',
                'price' => 59000,
                'stock' => 130,
                'skin_type' => 'normal',
                'description' => 'Cushion praktis untuk sehari-hari.',
                'shades' => [
                    ['shade_name'=>'01 Light','tone'=>'fair','undertone'=>'neutral','hex_color'=>'#F2D8CB','stock'=>18],
                    ['shade_name'=>'02 Natural','tone'=>'light','undertone'=>'neutral','hex_color'=>'#E6C2A8','stock'=>16],
                    ['shade_name'=>'03 Beige','tone'=>'medium','undertone'=>'neutral','hex_color'=>'#D5A585','stock'=>14],
                ],
            ],
        ];

        foreach ($products as $p) {
            $product = Product::create([
                'name'        => $p['name'],
                'brand'       => $p['brand'],
                'category'    => $p['category'],
                'price'       => $p['price'],
                'stock'       => $p['stock'],
                'skin_type'   => $p['skin_type'],
                'description' => $p['description'],
                'image'       => null,
            ]);

            $product->shades()->createMany($p['shades']);
        }
    }
}