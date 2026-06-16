<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Produk')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Produk')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('brand')
                        ->label('Brand')
                        ->required()
                        ->maxLength(255),

                    Select::make('category')
                        ->label('Kategori')
                        ->required()
                        ->searchable()
                        ->options([
                            'foundation'   => 'Foundation',
                            'concealer'    => 'Concealer',
                            'blush'        => 'Blush On',
                            'contour'      => 'Contour & Highlight',
                            'eyeshadow'    => 'Eyeshadow',
                            'eyeliner'     => 'Eyeliner',
                            'mascara'      => 'Maskara',
                            'lipstick'     => 'Lipstik',
                            'lipgloss'     => 'Lip Gloss',
                            'liptint'      => 'Lip Tint',
                            'primer'       => 'Primer',
                            'settingspray' => 'Setting Spray',
                            'powder'       => 'Bedak (Powder)',
                            'bronzer'      => 'Bronzer',
                            'skincare'     => 'Skincare',
                            'serum'        => 'Serum',
                            'moisturizer'  => 'Moisturizer',
                            'sunscreen'    => 'Sunscreen',
                            'toner'        => 'Toner',
                            'cleanser'     => 'Pembersih Wajah',
                            'mask'         => 'Masker Wajah',
                            'other'        => 'Lainnya',
                        ]),

                    TextInput::make('price')
                        ->label('Harga')
                        ->required()
                        ->numeric()
                        ->prefix('Rp'),

                    TextInput::make('stock')
                        ->label('Stok')
                        ->required()
                        ->numeric()
                        ->minValue(0),

                    Select::make('skin_type')
                        ->label('Jenis Kulit')
                        ->options([
                            'normal' => 'Normal',
                            'berminyak' => 'Berminyak',
                            'kering' => 'Kering',
                            'kombinasi' => 'Kombinasi',
                            'sensitif' => 'Sensitif',
                        ])
                        ->required(),
                    
                    Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true)
                        ->inline(false)
                        ->helperText('Nonaktifkan produk untuk menyembunyikannya dari katalog tanpa menghapusnya.'),

                    FileUpload::make('image')
                        ->label('Gambar Produk')
                        ->image()
                        ->disk('public')
                        ->directory('products')
                        ->visibility('public')
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->label('Deskripsi')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
