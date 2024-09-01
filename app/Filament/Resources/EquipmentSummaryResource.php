<?php


namespace App\Filament\Resources;

use App\Filament\Resources\EquipmentSummaryResource\Pages;
use App\Filament\Resources\EquipmentSummaryResource\RelationManagers;
use App\Models\EquipmentMonitoring;
use App\Models\Equipment;
use App\Models\Critical;
use App\Models\ForRepair;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;



class EquipmentSummaryResource extends Resource
{
    protected static ?string $model = Equipment::class;

    protected static ?string $navigationGroup = 'Monitoring';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';
    protected static ?string $navigationLabel = 'Equipment Monitoring';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'Working' => 'Working',
                        'For Repair' => 'For Repair',
                        'For Replacement' => 'For Replacement',
                        'Lost' => 'Lost',
                        'For Disposal' => 'For Disposal',
                        'Disposed' => 'Disposed',
                        'Borrowed' => 'Borrowed',
                    ])
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user(); // Retrieve the currently authenticated user
        $isPanelUser = $user->hasRole('panel_user'); // Check if the user has the 'panel_user' role

        // Define common actions
        $commonActions = [
            Tables\Actions\ViewAction::make('view_monitoring')
                ->label('View Records')
                ->icon('heroicon-o-presentation-chart-line')
                ->color('info')
                ->modalHeading('Monitoring Records')
                ->modalContent(function ($record) {
                    $equipmentId = $record->id;

                    $monitorings = EquipmentMonitoring::where('equipment_id', $equipmentId)
                        ->with('user')
                        ->get();

                    return view('filament.resources.equipment-monitoring-modal', [
                        'monitorings' => $monitorings,
                    ]);
                }),

            Tables\Actions\Action::make('update')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Update Status')
                ->modalDescription('Confirm to update the status of the selected equipment.')
                ->form(function (Form $form) {
                    return $form
                        ->schema([
                            Forms\Components\Select::make('status')
                                ->label('New Status')
                                ->options([
                                    'Working' => 'Working',
                                    'For Repair' => 'For Repair',
                                    'For Replacement' => 'For Replacement',
                                    'Lost' => 'Lost',
                                    'For Disposal' => 'For Disposal',
                                    'Disposed' => 'Disposed',
                                    'Borrowed' => 'Borrowed',
                                ]),
                            Forms\Components\Select::make('availability')
                                ->options([
                                    'Returned' => 'Returned',
                                    'Unreturned' => 'Unreturned',
                                ])
                                ->default('Unreturned')
                                ->reactive(),
                            Forms\Components\TextInput::make('remarks')
                                ->visible(fn($get) => $get('availability') === 'Returned')
                                ->label('Remarks'),
                            Forms\Components\Select::make('facility_id')
                                ->relationship('facility', 'name')
                        ]);
                })
                ->action(function (array $data, $record) {
                    $equipment = Equipment::find($record->id);

                    $updateData = [];

                    if (!empty($data['status'])) {
                        $updateData['status'] = $data['status'];
                    }

                    if (!empty($data['facility_id'])) {
                        $updateData['facility_id'] = $data['facility_id'];
                    }

                    if (!empty($data['availability'])) {
                        $updateData['availability'] = $data['availability'];
                    }

                    if (!empty($data['remarks'])) {
                        $updateData['remarks'] = $data['remarks'];
                    }

                    $equipment->update($updateData);

                    Notification::make()
                        ->success()
                        ->title('Updated Successfully')
                        ->body('Equipment has been updated successfully.')
                        ->send();
                }),

            Tables\Actions\Action::make('Monitor')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->requiresConfirmation()
                ->modalIcon('heroicon-o-check')
                ->modalHeading('Add to Monitoring')
                ->modalDescription('Confirm to add selected items to your Monitoring')
                ->form(function (Form $form) {
                    return $form
                        ->schema([
                            Forms\Components\Select::make('monitored_by')
                                ->label('Monitored By')
                                ->relationship('user', 'name')
                                ->disabled()
                                ->default(Auth::user()->id)
                                ->required(),
                            Forms\Components\DatePicker::make('monitored_date')
                                ->label('Monitored Date')
                                ->format('Y-m-d'),
                            Forms\Components\TextInput::make('remarks')
                                ->label('Remarks'),
                            Forms\Components\TextInput::make('monitoring_status')
                                ->label('Monitoring Status'),
                        ]);
                })
                ->action(function (array $data, $record) {
                    $data['equipment_id'] = $record->id;

                    EquipmentMonitoring::create($data);

                    Notification::make()
                        ->success()
                        ->title('Success')
                        ->body('Selected items have been added to your monitoring.')
                        ->send();
                }),

            // New Action: Mark as Critical
            Tables\Actions\Action::make('mark_as_critical')
                ->label('Mark as Critical')
                ->icon('heroicon-o-exclamation-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Mark Equipment as Critical')
                ->modalDescription('Are you sure you want to mark this equipment as critical?')
                ->action(function ($record) {
                    $equipment = Equipment::find($record->id);

                    // Save to the Critical table with status set to "Critical"
                    Critical::create([
                        'user_id' => auth()->id(),               // The ID of the current user
                        'equipment_id' => $equipment->id,        // The ID of the equipment
                        'facility_id' => $equipment->facility_id, // The ID of the facility associated with the equipment
                        'status' => 'Critical',                  // Set the status to "Critical"
                        'remarks' => 'Marked as critical',       // Remarks or reason for marking as critical
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Marked as Critical')
                        ->body('Equipment has been marked as critical and saved to the Critical table.')
                        ->send();
                }),

            Tables\Actions\Action::make('mark_as_for_repair')
                ->label('Mark as For Repair')
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Mark Equipment as For Repair')
                ->modalDescription('Are you sure you want to mark this equipment as for repair?')
                ->action(function ($record) {
                    $equipment = Equipment::find($record->id);

                    // Save to the Critical table with status set to "Critical"
                    ForRepair::create([
                        'user_id' => auth()->id(),               // The ID of the current user
                        'equipment_id' => $equipment->id,        // The ID of the equipment
                        'facility_id' => $equipment->facility_id, // The ID of the facility associated with the equipment
                        'status' => 'Critical',                  // Set the status to "For repair"
                        'remarks' => 'Marked as critical',       // Remarks or reason for marking as for repair
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Marked as For Repair')
                        ->body('Equipment has been marked as for repair.')
                        ->send();
                }),
        ];

        // Determine which actions to display based on user role
        $actions = $isPanelUser ? [] : $commonActions; // Hide all actions if the user is a panel_user

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit_no')
                    ->label('Unit Number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('facility.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('availability')
                    ->searchable(),
            ])
            ->filters([
                // Filters can be added here if needed
            ])
            ->actions([
                Tables\Actions\ActionGroup::make($actions),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Bulk actions can be added here if needed
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
            'index' => Pages\ListEquipmentSummaries::route('/'),
            'edit' => Pages\EditEquipmentSummary::route('/{record}/edit'),
        ];
    }
}
