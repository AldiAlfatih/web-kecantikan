<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('brand'),
                TextEntry::make('category'),
                TextEntry::make('price')
                    ->money(),
                TextEntry::make('stock')
                    ->numeric(),
                TextEntry::make('skin_type')
                    ->badge(),
                ImageEntry::make('image')
                    ->label('Gambar Produk')
                    ->disk('public')
                    ->columnSpanFull()
                    ->url(fn (Product $record) => $record->image ? Storage::disk('public')->url($record->image) : null)
                    ->openUrlInNewTab(),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
