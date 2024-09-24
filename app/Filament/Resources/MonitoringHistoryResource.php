<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonitoringHistoryResource\Pages;
use App\Filament\Resources\MonitoringHistoryResource\RelationManagers;
use App\Models\MonitoringHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MonitoringHistoryResource extends Resource
{
    protected static ?string $model = MonitoringHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-arrow-down';
    protected static ?string $navigationGroup = 'Monitoring';
    protected static ?int $navigationSort = 3;



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
            'index' => Pages\ListMonitoringHistories::route('/'),
            'create' => Pages\CreateMonitoringHistory::route('/create'),
            'edit' => Pages\EditMonitoringHistory::route('/{record}/edit'),
        ];
    }
}
