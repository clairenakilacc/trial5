<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonitoringHistoryResource\Pages;
use App\Filament\Resources\MonitoringHistoryResource\RelationManagers;
use App\Models\MonitoringHistory;
use App\Models\User;
use App\Models\Equipment;
use App\Models\StockUnit;
use App\Models\Category;
use App\Models\Facility;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MonitoringHistoryResource extends Resource
{
    protected static ?string $model = MonitoringHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-arrow-down';
    protected static ?string $navigationGroup = 'Monitoring';
    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    /*public static function getNavigationBadge(): ?string
    {
        
    // Check if the user is authenticated and has the 'panel_user' role
    if (Auth::check() && Auth::user()->hasRole('panel_user')) {
        // Count only the records where 'user_id' matches the logged-in user's ID
        return static::getModel()::where('user_id', Auth::id())->count();
    }

    // If the user is not a 'panel_user', return the total count
    return static::getModel()::count();
}*/

   
   /* public static function table(Table $table): Table
    {
        $user = auth()->user(); // Retrieve the currently authenticated user
        $isPanelUser = $user ? $user->hasRole('panel_user') : false; // Check if the user has the 'panel_user' role safely

        $actions = [];

        // Add EditAction only if the user is not a panel_user
        if (!$isPanelUser) {
            $actions[] = Tables\Actions\EditAction::make();
        }
        
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
*/

public static function table(Table $table): Table
    {
        $user = auth()->user();
        $isPanelUser = $user ? $user->hasRole('panel_user') : false;

        $actions = [];

        // Add EditAction only if the user is not a panel_user
        if (!$isPanelUser) {
            $actions[] = Tables\Actions\EditAction::make();
        }

        return $table
            ->columns([
                //Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date Added')
                    ->dateTime('F j, Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Monitored By')
                    ->sortable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('equipment.description')
                    ->label('Equipment')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('equipment.facility.name')
                    ->label('Facility')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('equipment.status')
                    ->label('Status')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Working' => 'success',
                        'For Repair' => 'warning',
                        'For Replacement' => 'primary',
                        'Lost' => 'danger',
                        'For Disposal' => 'primary',
                        'Disposed' => 'danger',
                        'Borrowed' => 'indigo',
                        'Excess' => 'gray',
                        default => 'secondary',  

                    }),
                Tables\Columns\TextColumn::make('equipment.serial_no')
                    ->label('Serial No.')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('equipment.property_no')
                    ->label('Property No.')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('equipment.control_no')
                    ->label('Control No.')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('equipment.remarks')
                    ->label('Remarks')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('actions_taken')
                    ->label('Actions Taken')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                
            ])
            ->filters([
                // Add your custom filters if necessary
            ])
            ->actions($actions)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define relations here if needed
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