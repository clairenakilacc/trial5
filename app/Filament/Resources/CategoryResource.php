<?php
namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use App\Models\User;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;


use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ExportBulkAction; 
class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-funnel';
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
                    ->placeholder('Example: Keyboard, Mouse, Door')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();
        $isPanelUser = $user && $user->hasRole('panel_user');

        if (!$isPanelUser) {
            $bulkActions[] = ExportBulkAction::make();
        }


        // Initialize bulk actions
        $bulkActions = [
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ExportBulkAction::make(),

            ]),
        ];

        // Conditionally add ExportBulkAction if the user is not a panel_user
       

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
                // Add any filters here if needed
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                ]),
            ])
            ->bulkActions($bulkActions); // Pass the bulk actions array here
    }

    public static function getRelations(): array
    {
        return [
            // Define any relationships here
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
