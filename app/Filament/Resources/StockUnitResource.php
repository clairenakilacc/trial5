<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockUnitResource\Pages;
use App\Filament\Resources\StockUnitResource\RelationManagers;
use App\Models\StockUnit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockUnitResource extends Resource
{
    protected static ?string $model = StockUnit::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationGroup = 'Classification';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->placeholder('Unit of equipment stock like in Tray, Carton, etc. ')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                ->formatStateUsing(fn (string $state): string => ucwords(strtolower($state)))

                    ->searchable(),
                 Tables\Columns\TextColumn::make('created_at')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        // Format the date and time
                        return $state ? $state->format('F j, Y h:i A') : null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                 Tables\Columns\TextColumn::make('user.name')
                    ->label('Created by')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockUnits::route('/'),
            'create' => Pages\CreateStockUnit::route('/create'),
            'edit' => Pages\EditStockUnit::route('/{record}/edit'),
        ];
    }
}
