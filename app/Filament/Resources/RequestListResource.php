<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RequestListResource\Pages;
use App\Filament\Resources\RequestListtResource\RelationManagers;
use App\Models\RequestList;
use App\Models\User;
use App\Models\BorrowedItems;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;


class RequestListResource extends Resource
{
    protected static ?string $model = RequestList::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Borrowing';

    protected static ?string $navigationLabel = 'Request List';
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

    /*public static function form(Form $form): Form
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
                Forms\Components\Select::make('borrowed_by')
                    ->required(),
                Forms\Components\Select::make('facility_id')
                    ->relationship('equipment', 'facility.name')
                    ->required()
                    ->helperText('Leave blank if inapplicable.'),
            ]);
    }*/

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

                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-arrow-right')
                    ->form([
                       /* Forms\Components\View::make('download_link')
                                ->view('components.download-link'),*/
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
                            Forms\Components\TextInput::make('borrowed_by')
                                ->required(),
                                //->default(fn () => Auth::user() ? Auth::user()->name : ''),                                       
                            Forms\Components\TextInput::make('purpose')
                                ->required()
                                ->default('Course/Class Lecture')
                                ->placeholder('Project Requirements etc.,'),
                            Forms\Components\DateTimePicker::make('start_date_and_time_of_use')
                                ->native(false)
                                ->format('M d, Y h:i A')
                                ->closeOnDateSelection(false)
                                ->withoutSeconds()
                                ->default(now('Asia/Manila'))                               
                                ->required()
                                ->extraAttributes([
                                    'data-clock-format' => '12', 
                                ]),
                            Forms\Components\DateTimePicker::make('end_date_and_time_of_use')
                                ->native(false)
                                ->format('M d, Y h:i A')
                                ->closeOnDateSelection(false)
                                ->withoutSeconds()
                                ->default(now('Asia/Manila'))                               
                                ->required()
                                ->extraAttributes([
                                    'data-clock-format' => '12', 
                                ]),
                            Forms\Components\DateTimePicker::make('expected_return_date')
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
                            /*Forms\Components\View::make('download_link')
                                ->view('components.download-link'),*/
                        ]),
                        Forms\Components\FileUpload::make('request_form')
                            ->label('Signed Request Form')
                            ->disk('public')
                            ->required()
                            ->directory('request_forms')
                            ->preserveFilenames()
                    ])
                    ->action(function ($data, $record) {
                        // Ensure the record exists and data is valid
                        if ($record && isset($data['request_form'])) {
                            $requestlist = RequestList::find($record->id);

                            if ($requestlist) {
                                BorrowedItems::create([
                                    'user_id' => $requestlist->user_id,
                                    'equipment_id' => $requestlist->equipment_id,
                                    'facility_id' => $requestlist->facility_id,
                                    'request_status' => 'Pending',
                                    'borrowed_by' => $data['borrowed_by'],
                                    'request_form' => $data['request_form'],
                                    'date' => now(),
                                    'purpose' => $data['purpose'],
                                    'start_date_and_time_of_use' => $data['start_date_and_time_of_use'],
                                    'end_date_and_time_of_use' => $data['end_date_and_time_of_use'],
                                    'expected_return_date' => $data['expected_return_date'],
                                    'received_by' => $data['received_by'],
                                    'college_department_office' => $data['college_department_office'],
                                    'borrowed_date' => now(),
                                    'remarks' => '',
                                ]);

                                $requestlist->delete();

                                Notification::make()
                                    ->success()
                                    ->title('Success')
                                    ->body('Selected item/s have been transferred to borrowed items.')
                                    ->send();
                            } else {
                                Notification::make()
                                    ->error()
                                    ->title('Error')
                                    ->body('Request list record not found.')
                                    ->send();
                            }
                        } else {
                            Notification::make()
                                ->danger()
                                //->error()
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
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
               
                Tables\Columns\TextColumn::make('equipment.description')
                    ->label('Equipment')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('equipment.unit_no')
                    ->label('Unit Number')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('facility.name')
                    ->label('Facility')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(fn($state) => $state ?? $state->equipment->facility->name ?? 'N/A'),

                Tables\Columns\TextColumn::make('equipment.category.description')
                    ->label('Category')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('equipment.status')
                    ->label('Status')
                    ->badge()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
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
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Approve')
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
                                    Forms\Components\TextInput::make('borrowed_by')
                                        //->default(fn () => Auth::user() ? Auth::user()->name : '')                                      
                                        ->required(),
                                    Forms\Components\TextInput::make('purpose')
                                        ->required()
                                        ->default('Course/Class Lecture')
                                        ->placeholder('Project Requirements etc.,'),
                                    Forms\Components\DateTimePicker::make('start_date_and_time_of_use')
                                    ->native(false)
                                    ->format('M d, Y h:i A')
                                    ->closeOnDateSelection(false)
                                    ->withoutSeconds()
                                    ->default(now('Asia/Manila'))                               
                                    ->required()
                                    ->extraAttributes([
                                        'data-clock-format' => '12', 
                                    ]),
                                    Forms\Components\DateTimePicker::make('end_date_and_time_of_use')
                                    ->native(false)
                                    ->format('M d, Y h:i A')
                                    ->closeOnDateSelection(false)
                                    ->withoutSeconds()
                                    ->default(now('Asia/Manila'))                               
                                    ->required()
                                    ->extraAttributes([
                                        'data-clock-format' => '12', 
                                    ]),
                                    Forms\Components\DateTimePicker::make('expected_return_date')
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
                                    /*Forms\Components\View::make('download_link')
                                        ->view('components.download-link'),*/
                                ]),
                            Forms\Components\FileUpload::make('request_form')
                                ->label('Signed Request Form')
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
                                    $requestlist = RequestList::find($record->id);

                                    if ($requestlist) {
                                        BorrowedItems::create([
                                            'user_id' => $requestlist->user_id,
                                            'equipment_id' => $requestlist->equipment_id,
                                            'facility_id' => $requestlist->facility_id,
                                            'request_status' => 'Pending',
                                            'request_form' => $data['request_form'],
                                            'date' => now(),
                                            'purpose' => $data['purpose'],
                                            'borrowed_by' => $data['borrowed_by'],
                                            'start_date_and_time_of_use' => $data['start_date_and_time_of_use'],
                                            'end_date_and_time_of_use' => $data['end_date_and_time_of_use'],
                                            'expected_return_date' => $data['expected_return_date'],
                                            'college_department_office' => $data['college_department_office'],
                                            'borrowed_date' => now()->format('Y-m-d h:i A'),
                                        ]);

                                        $requestlist->delete();
                                    }
                                }

                                Notification::make()
                                    ->success()
                                    ->title('Success')
                                    ->body('Selected item/s have been transferred to borrowed items.')
                                    ->send();
                            } else {
                                Notification::make()
                                    ->danger()
                                //->error()
                                    ->title('Error')
                                    ->body('Invalid data or request form.')
                                    ->send();
                            }
                        })
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalIcon('heroicon-o-check')
                        ->modalHeading('Add to Borrowed Items')
                        ->modalDescription('Confirm to add selected item/s to your borrowed items.'),
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
            'index' => Pages\ListRequestList::route('/'),
            'create' => Pages\CreateRequestList::route('/create'),
            //'edit' => Pages\EditBorrowList::route('/{record}/edit'),
        ];
    }
}
