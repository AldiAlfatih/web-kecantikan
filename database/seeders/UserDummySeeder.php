<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserPcaProfile;
use Illuminate\Support\Facades\Hash;

class UserDummySeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin FaceShop',
                'email' => 'admin@faceshop.test',
                'password' => 'admin12345',
                'role' => 'admin',
                'phone' => '081234567890',
                'address' => 'Jl. Ahmad Yani No. 1, Parepare',
                'profile' => [
                    'skin_type' => 'normal',
                    'tone' => 'light',
                    'undertone' => 'neutral',
                    'skin_problem' => 'kusam',
                    'vein_color' => 'mixed',
                ],
                'pca' => [
                    'skin_tone_level' => 2,
                    'undertone' => 'neutral',
                    'vein_color' => 'mixed',
                    'eye_contrast' => 'bright',
                    'hair_color' => 'dark brown',
                    'hue' => 'neutral',
                    'value' => 'light',
                    'chroma' => 'bright',
                    'season' => 'Light Spring',
                    'confidence' => 'high',
                ],
            ],

            [
                'name' => 'Aulia Putri',
                'email' => 'aulia@example.com',
                'password' => 'password123',
                'role' => 'user',
                'phone' => '081111111111',
                'address' => 'Jl. Bau Massepe No. 10, Parepare',
                'profile' => [
                    'skin_type' => 'kering',
                    'tone' => 'fair',
                    'undertone' => 'cool',
                    'skin_problem' => 'kusam, kemerahan',
                    'vein_color' => 'blue_purple',
                ],
                'pca' => [
                    'skin_tone_level' => 1,
                    'undertone' => 'cool',
                    'vein_color' => 'blue_purple',
                    'eye_contrast' => 'muted',
                    'hair_color' => 'black',
                    'hue' => 'cool',
                    'value' => 'light',
                    'chroma' => 'muted',
                    'season' => 'Soft Summer',
                    'confidence' => 'high',
                ],
            ],

            [
                'name' => 'Nadira Safitri',
                'email' => 'nadira@example.com',
                'password' => 'password123',
                'role' => 'user',
                'phone' => '082222222222',
                'address' => 'Jl. Jenderal Sudirman No. 5, Parepare',
                'profile' => [
                    'skin_type' => 'berminyak',
                    'tone' => 'light',
                    'undertone' => 'warm',
                    'skin_problem' => 'jerawat, pori-pori besar',
                    'vein_color' => 'green_olive',
                ],
                'pca' => [
                    'skin_tone_level' => 2,
                    'undertone' => 'warm',
                    'vein_color' => 'green_olive',
                    'eye_contrast' => 'bright',
                    'hair_color' => 'dark brown',
                    'hue' => 'warm',
                    'value' => 'light',
                    'chroma' => 'bright',
                    'season' => 'Light Spring',
                    'confidence' => 'high',
                ],
            ],

            [
                'name' => 'Rani Maharani',
                'email' => 'rani@example.com',
                'password' => 'password123',
                'role' => 'user',
                'phone' => '083333333333',
                'address' => 'Jl. Andi Makkasau No. 21, Parepare',
                'profile' => [
                    'skin_type' => 'kombinasi',
                    'tone' => 'medium',
                    'undertone' => 'neutral',
                    'skin_problem' => 'kusam, flek',
                    'vein_color' => 'mixed',
                ],
                'pca' => [
                    'skin_tone_level' => 3,
                    'undertone' => 'neutral',
                    'vein_color' => 'mixed',
                    'eye_contrast' => 'muted',
                    'hair_color' => 'black',
                    'hue' => 'neutral',
                    'value' => 'medium',
                    'chroma' => 'muted',
                    'season' => 'Soft Autumn',
                    'confidence' => 'medium',
                ],
            ],

            [
                'name' => 'Salsa Ramadhani',
                'email' => 'salsa@example.com',
                'password' => 'password123',
                'role' => 'user',
                'phone' => '084444444444',
                'address' => 'Jl. Lasinrang No. 8, Parepare',
                'profile' => [
                    'skin_type' => 'sensitif',
                    'tone' => 'light',
                    'undertone' => 'cool',
                    'skin_problem' => 'kemerahan',
                    'vein_color' => 'blue_purple',
                ],
                'pca' => [
                    'skin_tone_level' => 2,
                    'undertone' => 'cool',
                    'vein_color' => 'blue_purple',
                    'eye_contrast' => 'muted',
                    'hair_color' => 'soft black',
                    'hue' => 'cool',
                    'value' => 'light',
                    'chroma' => 'muted',
                    'season' => 'Light Summer',
                    'confidence' => 'high',
                ],
            ],

            [
                'name' => 'Dewi Anggraini',
                'email' => 'dewi@example.com',
                'password' => 'password123',
                'role' => 'user',
                'phone' => '085555555555',
                'address' => 'Jl. Veteran No. 14, Makassar',
                'profile' => [
                    'skin_type' => 'normal',
                    'tone' => 'tan',
                    'undertone' => 'warm',
                    'skin_problem' => 'flek',
                    'vein_color' => 'green_olive',
                ],
                'pca' => [
                    'skin_tone_level' => 4,
                    'undertone' => 'warm',
                    'vein_color' => 'green_olive',
                    'eye_contrast' => 'bright',
                    'hair_color' => 'dark brown',
                    'hue' => 'warm',
                    'value' => 'medium',
                    'chroma' => 'bright',
                    'season' => 'Warm Autumn',
                    'confidence' => 'high',
                ],
            ],

            [
                'name' => 'Maya Lestari',
                'email' => 'maya@example.com',
                'password' => 'password123',
                'role' => 'user',
                'phone' => '086666666666',
                'address' => 'Jl. Perintis Kemerdekaan No. 3, Makassar',
                'profile' => [
                    'skin_type' => 'berminyak',
                    'tone' => 'deep',
                    'undertone' => 'warm',
                    'skin_problem' => 'jerawat',
                    'vein_color' => 'green_olive',
                ],
                'pca' => [
                    'skin_tone_level' => 5,
                    'undertone' => 'warm',
                    'vein_color' => 'green_olive',
                    'eye_contrast' => 'bright',
                    'hair_color' => 'black',
                    'hue' => 'warm',
                    'value' => 'dark',
                    'chroma' => 'bright',
                    'season' => 'Deep Autumn',
                    'confidence' => 'high',
                ],
            ],

            [
                'name' => 'Intan Permata',
                'email' => 'intan@example.com',
                'password' => 'password123',
                'role' => 'user',
                'phone' => '087777777777',
                'address' => 'Jl. Urip Sumoharjo No. 19, Makassar',
                'profile' => [
                    'skin_type' => 'kering',
                    'tone' => 'medium',
                    'undertone' => 'neutral',
                    'skin_problem' => 'kusam',
                    'vein_color' => 'mixed',
                ],
                'pca' => [
                    'skin_tone_level' => 3,
                    'undertone' => 'neutral',
                    'vein_color' => 'mixed',
                    'eye_contrast' => 'bright',
                    'hair_color' => 'dark brown',
                    'hue' => 'neutral',
                    'value' => 'medium',
                    'chroma' => 'bright',
                    'season' => 'True Autumn',
                    'confidence' => 'medium',
                ],
            ],

            [
                'name' => 'Citra Amalia',
                'email' => 'citra@example.com',
                'password' => 'password123',
                'role' => 'user',
                'phone' => '088888888888',
                'address' => 'Jl. AP Pettarani No. 11, Makassar',
                'profile' => [
                    'skin_type' => 'kombinasi',
                    'tone' => 'fair',
                    'undertone' => 'cool',
                    'skin_problem' => 'pori-pori besar',
                    'vein_color' => 'blue_purple',
                ],
                'pca' => [
                    'skin_tone_level' => 1,
                    'undertone' => 'cool',
                    'vein_color' => 'blue_purple',
                    'eye_contrast' => 'bright',
                    'hair_color' => 'black',
                    'hue' => 'cool',
                    'value' => 'light',
                    'chroma' => 'bright',
                    'season' => 'Bright Winter',
                    'confidence' => 'high',
                ],
            ],

            [
                'name' => 'Fitri Handayani',
                'email' => 'fitri@example.com',
                'password' => 'password123',
                'role' => 'user',
                'phone' => '089999999999',
                'address' => 'Jl. Ratulangi No. 27, Parepare',
                'profile' => [
                    'skin_type' => 'sensitif',
                    'tone' => 'tan',
                    'undertone' => 'neutral',
                    'skin_problem' => 'kemerahan, flek',
                    'vein_color' => 'mixed',
                ],
                'pca' => [
                    'skin_tone_level' => 4,
                    'undertone' => 'neutral',
                    'vein_color' => 'mixed',
                    'eye_contrast' => 'muted',
                    'hair_color' => 'dark brown',
                    'hue' => 'neutral',
                    'value' => 'medium',
                    'chroma' => 'muted',
                    'season' => 'Soft Autumn',
                    'confidence' => 'medium',
                ],
            ],
        ];

        foreach ($users as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'phone' => $data['phone'],
                'address' => $data['address'],
            ]);

            UserProfile::create([
                'user_id' => $user->id,
                'skin_type' => $data['profile']['skin_type'],
                'tone' => $data['profile']['tone'],
                'undertone' => $data['profile']['undertone'],
                'skin_problem' => $data['profile']['skin_problem'],
                'vein_color' => $data['profile']['vein_color'],
            ]);

            UserPcaProfile::create([
                'user_id' => $user->id,
                'skin_tone_level' => $data['pca']['skin_tone_level'],
                'undertone' => $data['pca']['undertone'],
                'vein_color' => $data['pca']['vein_color'],
                'eye_contrast' => $data['pca']['eye_contrast'],
                'hair_color' => $data['pca']['hair_color'],
                'hue' => $data['pca']['hue'],
                'value' => $data['pca']['value'],
                'chroma' => $data['pca']['chroma'],
                'season' => $data['pca']['season'],
                'confidence' => $data['pca']['confidence'],
            ]);
        }
    }
}