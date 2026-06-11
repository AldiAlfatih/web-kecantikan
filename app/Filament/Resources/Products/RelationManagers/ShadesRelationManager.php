<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShadesRelationManager extends RelationManager
{
    protected static string $relationship = 'shades';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('shade_name')
                ->label('Nama Shade')
                ->required(),

            Select::make('tone')
                ->label('Skintone')
                ->options([
                    'fair'   => 'Sangat terang (Fair)',
                    'light'  => 'Terang (Light)',
                    'medium' => 'Sedang / Kuning langsat (Medium)',
                    'tan'    => 'Sawo matang terang (Tan)',
                    'deep'   => 'Sawo matang gelap (Deep)',
                    'dark'   => 'Gelap (Dark)',
                ])
                ->required(),

            Select::make('undertone')
                ->label('Undertone')
                ->options([
                    'warm' => 'Warm',
                    'neutral' => 'Neutral',
                    'cool' => 'Cool',
                ])
                ->required(),

            // ✅ STOCK SHADE
            TextInput::make('stock')
                ->label('Stok Shade')
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->required(),

            TextInput::make('hex_color')
                ->label('Hex Color')
                ->placeholder('#d9a18b')
                ->required()
                ->live()
                ->extraInputAttributes([
                    'id' => 'hex_color_input',
                    'x-on:hex-picked.window' => '
                        $el.value = $event.detail.hex;
                        $el.dispatchEvent(new Event("input", { bubbles: true }));
                        $el.dispatchEvent(new Event("change", { bubbles: true }));
                    ',
                ])
                ->afterStateUpdated(function ($state, $set) {
                    // ✅ sync preview saat hex_color berubah (termasuk dari kamera)
                    if ($state) {
                        $set('hex_color_preview', $state);
                    }
                })
                ->hintAction(
                    Action::make('pickHex')
                        ->label('Buka Hex Picker')
                        ->modalHeading('Ambil HEX dari Kamera')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup')
                        ->modalContent(fn () => view('filament.hex-picker-modal'))
                ),

            ColorPicker::make('hex_color_preview')
                ->label('Preview / Koreksi (opsional)')
                ->dehydrated(false)
                ->live()
                ->afterStateHydrated(function ($set, $get) {
                    // ✅ saat form kebuka, samakan preview dengan hex_color
                    $set('hex_color_preview', $get('hex_color'));
                })
                ->afterStateUpdated(function ($state, $set) {
                    // ✅ kalau preview diganti manual, update hex_color juga
                    if ($state) {
                        $set('hex_color', $state);
                    }
                }),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('shade_name')
            ->columns([
                TextColumn::make('shade_name')->label('Shade')->searchable(),
                TextColumn::make('tone')->label('Tone')->searchable(),
                TextColumn::make('undertone')->label('Undertone')->searchable(),
                TextColumn::make('hex_color')->label('Hex')->searchable(),
                TextColumn::make('stock')->label('Stok')->sortable(), // ✅ tampilkan stok
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}