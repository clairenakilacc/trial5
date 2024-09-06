<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BorrowResource\Pages;
use App\Filament\Resources\BorrowResource\RelationManagers;
use App\Models\Borrow;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class BorrowResource extends Resource
{
    protected static ?string $model = Borrow::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $navigationGroup = 'Borrowing';
    protected static ?string $navigationLabel = 'Transferred to Borrows';
    protected static ?int $navigationSort = 3;

    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }

    public static function getNavigationBadge(): ?string
    {
        // Check if the user is authenticated and has the 'panel_user' role
        if (Auth::check() && Auth::user()->hasRole('panel_user')) {
            // Count only the records where 'user_id' matches the logged-in user's ID
            return static::getModel()::where('user_id', Auth::id())->count();
        }

        // If the user is not a 'panel_user', return the total count
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->default(auth()->id()),
                Forms\Components\Select::make('equipment_id')
                    ->relationship('equipment', 'name')
                    ->nullable(),
                Forms\Components\Select::make('facility_id')
                    ->relationship('facility', 'name')
                    ->nullable(),
                Forms\Components\TextInput::make('request_status')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('request_form')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('borrowed_date')
                    ->required()
                    ->disabled()
                    ->default(fn() => now()->format('Y-m-d'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('returned_date')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {

        return $table
            ->modifyQueryUsing(function (Builder $query) {
                // Check if the authenticated user is a 'panel_user'
                if (Auth::user() && Auth::user()->hasRole('panel_user')) {
                    // Filter the records to only show those that belong to the current logged-in user
                    $query->where('user_id', Auth::id());
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric(),
                Tables\Columns\TextColumn::make('equipment.description'),
                Tables\Columns\TextColumn::make('facility.name'),

                /*Tables\Columns\TextColumn::make('equipment.unit_no')
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('equipment.category'),
                Tables\Columns\TextColumn::make('equipment.serial_no'),
                Tables\Columns\TextColumn::make('equipment.property_no')
                ->toggleable(isToggledHiddenByDefault: true),    
                Tables\Columns\TextColumn::make('equipment.serial_no')
                ->toggleable(isToggledHiddenByDefault: true),   
                Tables\Columns\TextColumn::make('equipment.facility')
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('equipment.status')
                ->toggleable(isToggledHiddenByDefault: true),   
                Tables\Columns\TextColumn::make('equipment.person_liable')
                ->toggleable(isToggledHiddenByDefault: true),     
                Tables\Columns\TextColumn::make('remarks')
                ->toggleable(isToggledHiddenByDefault: true),*/

                Tables\Columns\TextColumn::make('purpose'),
                Tables\Columns\TextColumn::make('date_and_time_of_use'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Availability')
                    ->searchable(),
                Tables\Columns\TextColumn::make('request_status'),
                Tables\Columns\TextColumn::make('remarks'),
                Tables\Columns\TextColumn::make('returned_date'),
                Tables\Columns\TextColumn::make('request_form'),
                // \EightyNine\Approvals\Tables\Columns\ApprovalStatusColumn::make("approvalStatus.status"),
            ])
            ->filters([
                //
            ])
            ->actions([
                // ...\EightyNine\Approvals\Tables\Actions\ApprovalActions::make(
                //     // define your action here that will appear once approval is completed
                //     Action::make("Done"),
                //     [
                //         Tables\Actions\EditAction::make(),
                //         Tables\Actions\ViewAction::make()
                //     ]
                // ),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('viewPdf')
                        ->label('Download Form')
                        ->icon('heroicon-o-document-text')
                        ->action(function ($record) {
                            // Generate the download URL
                            $url = asset('storage/' . $record->request_form);

                            // Redirect to the PDF URL to trigger the download
                            return redirect()->away($url);
                        })
                        ->color('info'),
                    Tables\Actions\Action::make('updateStatus')
                        ->label('Update')
                        ->color('danger')
                        ->icon('heroicon-o-pencil')
                        ->form([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Select::make('status')
                                        ->label('Availability')
                                        ->options([
                                            'Returned' => 'Returned',
                                            'Unreturned' => 'Unreturned',
                                        ])
                                        ->reactive()
                                        ->required(),
                                    Forms\Components\Select::make('request_status')
                                        ->label('Request Status')
                                        ->options([
                                            'Pending' => 'Pending',
                                            'Approved' => 'Approved',
                                            'Rejected' => 'Rejected',
                                            // Add more options if needed
                                        ])
                                        ->reactive()
                                        ->required()
                                        ->default(fn($record) => $record->request_status),
                                    Forms\Components\DatePicker::make('returned_date')
                                        ->label('Returned Date')
                                        ->visible(fn(callable $get) => $get('status') === 'Returned')
                                        ->required(fn(callable $get) => $get('status') === 'Returned')
                                        ->placeholder('Select return date'),
                                    Forms\Components\TextInput::make('remarks')
                                        ->visible(fn(callable $get) => $get('status') === 'Returned')
                                        ->required(fn(callable $get) => $get('status') === 'Returned')
                                        ->columnSpanFull()
                                ]),
                        ])
                        ->action(function ($record, $data) {
                            // Log the data for debugging purposes
                            Log::info('Updating record', [
                                'record_id' => $record->id,
                                'status' => $data['status'],
                                'returned_date' => $data['returned_date'],
                                'remarks' => $data['remarks'],
                            ]);

                            // Update the record with the new status, returned date, and remarks
                            $record->update([
                                'status' => $data['status'],
                                'returned_date' => $data['status'] === 'Returned' ? $data['returned_date'] : null,
                                'remarks' => $data['status'] === 'Returned' ? $data['remarks'] : null,
                            ]);

                            // Check if the record was updated successfully
                            if ($record->wasChanged()) {
                                Notification::make()
                                    ->title('Status Updated')
                                    ->success()
                                    ->body('The status has been updated successfully.')
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('No Changes Detected')
                                    ->warning()
                                    ->body('The status did not change.')
                                    ->send();
                            }
                        })
                        ->modalHeading('Update Equipment Status')
                        ->color('success'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]); // Filter borrows for panel users;
    }

    // protected static function getTableQuery(): Builder
    // {
    //     $query = parent::getTableQuery();
    //     $user = Auth::user();

    //     // Only show borrows associated with the logged-in user
    //     if ($user->hasRole('panel_user')) { // Replace 'panel_user' with the actual role
    //         $query->where('user_id', $user->id);
    //     }

    //     return $query;
    // }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBorrows::route('/'),
            'create' => Pages\CreateBorrow::route('/create'),
            'edit' => Pages\EditBorrow::route('/{record}/edit'),
        ];
    }
}
