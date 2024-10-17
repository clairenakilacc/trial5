<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RequestListResource\Pages;
use App\Filament\Resources\RequestListResource\RelationManagers;
use App\Models\RequestList;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RequestListResource extends Resource
{
    protected static ?string $model = RequestList::class;
    protected static ?string $navigationLabel = 'Request List';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListRequestLists::route('/'),
            'create' => Pages\CreateRequestList::route('/create'),
            'edit' => Pages\EditRequestList::route('/{record}/edit'),
        ];
    }
}
