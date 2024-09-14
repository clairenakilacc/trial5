<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BorrowListResource\Pages;
use App\Filament\Resources\BorrowListResource\RelationManagers;
use App\Models\BorrowList;
use App\Models\User;
use App\Models\Borrow;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;


class BorrowListResource extends Resource
{
    protected static ?string $model = BorrowList::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Borrowing';

    protected static ?string $navigationLabel = 'Borrow Lists';
    protected static ?int $navigationSort = 3;

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
                    ->label('User')
                    ->options(User::pluck('name', 'id')->toArray())
                    ->disabled()
                    ->required()
                    ->default(fn($record) => $record->user ? $record->user->id : null),
                Forms\Components\Select::make('equipment_id')
                    ->relationship('equipment', 'description')
                    ->required()
                    ->helperText('Leave blank if inapplicable.'),
                Forms\Components\Select::make('facility_id')
                    ->relationship('equipment', 'facility.name')
                    ->required()
                    ->helperText('Leave blank if inapplicable.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user(); // Retrieve the currently authenticated user
        $isPanelUser = $user ? $user->hasRole('panel_user') : false; // Check if the user has the 'panel_user' role safely

        $actions = [];

        // Add EditAction only if the user is not a panel_user
        if (!$isPanelUser) {
            $actions[] = Tables\Actions\EditAction::make();
        }

        // Define actions based on user role
        $actions = [
            Tables\Actions\ActionGroup::make([

                Tables\Actions\Action::make('transferToBorrow')
                    ->label('Transfer to Borrow')
                    ->icon('heroicon-o-arrow-right')
                    ->form([
                        Forms\Components\Grid::make([
                            'default' => 2,
                        ])->schema([
                            Forms\Components\DateTimePicker::make('date')
                                
                                ->native(false)
                                ->format('M d, Y h:i A')
                                ->closeOnDateSelection(false)
                                ->withoutSeconds()
                                ->timezone('Asia/Manila') // Set timezone to Asia/Manila
                                ->default(now('Asia/Manila')) // Set default to current time in Manila
                                ->required()
                                ->disabled()
                                ->extraAttributes([
                                    'data-clock-format' => '12',
                                ]),
                                       
                            Forms\Components\TextInput::make('purpose')
                                ->required()
                                ->default('Course/Class Lecture')
                                ->placeholder('Project Requirements etc.,'),
                            Forms\Components\DateTimePicker::make('date_and_time_of_use')
                                ->native(false)
                                ->format('M d, Y h:i A')
                                ->closeOnDateSelection(false)
                                ->withoutSeconds()
                                ->default(now('Asia/Manila'))                               
                                ->required()
                                ->extraAttributes([
                                    'data-clock-format' => '12', 
                                ]),
                            Forms\Components\TextInput::make('college_department_office')
                                ->required()
                                ->default('CCIS')
                                ->placeholder('CCIS'),
                            Forms\Components\View::make('download_link')
                                ->view('components.download-link'),
                        ]),
                        Forms\Components\FileUpload::make('request_form')
                            ->disk('public')
                            ->required()
                            ->directory('request_forms')
                            ->preserveFilenames()
                    ])
                    ->action(function ($data, $record) {
                        // Ensure the record exists and data is valid
                        if ($record && isset($data['request_form'])) {
                            $borrowList = BorrowList::find($record->id);

                            if ($borrowList) {
                                Borrow::create([
                                    'user_id' => $borrowList->user_id,
                                    'equipment_id' => $borrowList->equipment_id,
                                    'facility_id' => $borrowList->facility_id,
                                    'request_status' => 'Pending',
                                    'request_form' => $data['request_form'],
                                    'date' => now(),
                                    'purpose' => $data['purpose'],
                                    'date_and_time_of_use' => $data['date_and_time_of_use'],
                                    'college_department_office' => $data['college_department_office'],
                                    'borrowed_date' => now(),
                                    'remarks' => 'test',
                                ]);

                                $borrowList->delete();

                                Notification::make()
                                    ->success()
                                    ->title('Success')
                                    ->body('Selected items have been transferred to borrows.')
                                    ->send();
                            } else {
                                Notification::make()
                                    ->error()
                                    ->title('Error')
                                    ->body('Borrow list record not found.')
                                    ->send();
                            }
                        } else {
                            Notification::make()
                                ->error()
                                ->title('Error')
                                ->body('Invalid data or record.')
                                ->send();
                        }
                    })
                    ->color('success'),
            ])->label('Actions') // Optional: You can label the action group
        ];

        // Remove null values
        $actions = array_filter($actions, fn($action) => $action !== null);

        return $table
            // Uncomment and adjust query modification if needed
            ->modifyQueryUsing(function (Builder $query) {
                if (Auth::user() && Auth::user()->hasRole('panel_user')) {
                    $query->where('user_id', Auth::id());
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created By')
                    ->searchable(),
               
                Tables\Columns\TextColumn::make('equipment.description')
                    ->label('Equipment')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('equipment.unit_no')
                    ->label('Unit Number')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('facility.name')
                    ->label('Facility')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->searchable()
                    ->formatStateUsing(fn($state) => $state ?? $state->equipment->facility->name ?? 'N/A'),

                Tables\Columns\TextColumn::make('equipment.category.description')
                    ->label('Category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('equipment.status')
                    ->label('Status')
                    ->badge()
                    ->searchable()
                    ->color(fn(string $state): string => match ($state) {
                        'Working' => 'success',
                        'For Repair' => 'warning',
                        'For Replacement' => 'primary',
                        'Lost' => 'danger',
                        'For Disposal' => 'primary',
                        'Disposed' => 'danger',
                        'Borrowed' => 'indigo',
                    }),
                Tables\Columns\TextColumn::make('equipment.control_no')
                    ->label('Control Number')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('equipment.serial_no')
                    ->label('Serial Number')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('equipment.property_no')
                    ->label('Property Number')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('equipment.remarks')
                    ->label('Remarks')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('equipment.person_liable')
                    ->label('Person_liable')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
            ])
            ->filters([
                // Your filters here
            ])
            ->actions($actions) // Apply the filtered actions
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('transferToBorrow')
                        ->label('Transfer to Borrow')
                        ->icon('heroicon-o-arrow-right')
                        ->form([
                            Forms\Components\Grid::make([
                                'default' => 1,
                            ])
                                ->schema([
                                    Forms\Components\DateTimePicker::make('date')
                                    ->native(false)
                                    ->format('M d, Y h:i A')
                                    ->closeOnDateSelection(false)
                                    ->withoutSeconds()
                                    ->default(now('Asia/Manila'))                               
                                    ->required()
                                    ->disabled()
                                    ->extraAttributes([
                                        'data-clock-format' => '12', 
                                    ]),
                                    Forms\Components\TextInput::make('purpose')
                                        ->required()
                                        ->default('Course/Class Lecture')
                                        ->placeholder('Project Requirements etc.,'),
                                    Forms\Components\DateTimePicker::make('date_and_time_of_use')
                                    ->native(false)
                                    ->format('M d, Y h:i A')
                                    ->closeOnDateSelection(false)
                                    ->withoutSeconds()
                                    ->default(now('Asia/Manila'))                               
                                    ->required()
                                    ->extraAttributes([
                                        'data-clock-format' => '12', 
                                    ]),
                                    Forms\Components\TextInput::make('college_department_office')
                                        ->required()
                                        ->default('CCIS'),
                                    Forms\Components\View::make('download_link')
                                        ->view('components.download-link'),
                                ]),
                            Forms\Components\FileUpload::make('request_form')
                                ->disk('public')
                                ->required()
                                ->directory('request_forms')
                                ->preserveFilenames()
                        ])
                        ->action(function (Collection $records, array $data) {
                            // Ensure data is valid
                            if (isset($data['request_form'])) {
                                foreach ($records as $record) {
                                    // Fetch the record to ensure it exists
                                    $borrowList = BorrowList::find($record->id);

                                    if ($borrowList) {
                                        Borrow::create([
                                            'user_id' => $borrowList->user_id,
                                            'equipment_id' => $borrowList->equipment_id,
                                            'facility_id' => $borrowList->facility_id,
                                            'request_status' => 'Pending',
                                            'request_form' => $data['request_form'],
                                            'date' => now(),
                                            'purpose' => $data['purpose'],
                                            'date_and_time_of_use' => $data['date_and_time_of_use'],
                                            'college_department_office' => $data['college_department_office'],
                                            'borrowed_date' => now(),
                                        ]);

                                        $borrowList->delete();
                                    }
                                }

                                Notification::make()
                                    ->success()
                                    ->title('Success')
                                    ->body('Selected items have been transferred to borrows.')
                                    ->send();
                            } else {
                                Notification::make()
                                    ->error()
                                    ->title('Error')
                                    ->body('Invalid data or request form.')
                                    ->send();
                            }
                        })
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalIcon('heroicon-o-check')
                        ->modalHeading('Add to Borrow List')
                        ->modalDescription('Confirm to add selected items to your borrow list.'),
                ])
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
            'index' => Pages\ListBorrowLists::route('/'),
            'create' => Pages\CreateBorrowList::route('/create'),
            //'edit' => Pages\EditBorrowList::route('/{record}/edit'),
        ];
    }
}
