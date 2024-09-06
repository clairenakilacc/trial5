<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ForRepairResource\Pages;
use App\Filament\Resources\ForRepairResource\RelationManagers;
use App\Models\ForRepair;
use App\Models\Equipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ForRepairResource extends Resource
{
    protected static ?string $model = ForRepair::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?string $navigationGroup = 'Importants';
    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Details')
                    ->schema([
                        Forms\Components\Grid::make(2) // Create a 2-column grid
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->required(),

                                Forms\Components\Select::make('equipment_id')
                                    ->relationship('equipment', 'description')
                                    ->required()
                                    ->helperText('Leave blank if inapplicable.')
                                    ->disabled()
                                    ->options(function () {
                                        return Equipment::all()
                                            ->pluck('description', 'id')
                                            ->filter(fn($value) => !is_null($value));
                                    }),

                                Forms\Components\Select::make('facility_id')
                                    ->relationship('facility', 'name')
                                    ->required()
                                    ->disabled(),

                                Forms\Components\Select::make('status')
                                    ->options([
                                        'Critical' => 'Critical',
                                        'Out of Stock' => 'Out of Stock',
                                    ]),

                                Forms\Components\TextInput::make('remarks')
                                    ->required()
                                    ->columnSpan(2), // Span both columns
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('equipment_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('facility_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('remarks')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                ])
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
            'index' => Pages\ListForRepairs::route('/'),
            // 'create' => Pages\CreateForRepair::route('/create'),
            'edit' => Pages\EditForRepair::route('/{record}/edit'),
        ];
    }
}
