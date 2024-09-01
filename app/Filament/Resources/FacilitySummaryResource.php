<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FacilitySummaryResource\Pages;
use App\Filament\Resources\FacilitySummaryResource\RelationManagers;
use App\Models\Facility;
use App\Models\User;
use App\Models\Critical;
use App\Models\ForRepair;
use App\Models\Equipment;
use App\Models\FacilityMonitoring;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\Action;

class FacilitySummaryResource extends Resource
{
    protected static ?string $model = Facility::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Monitoring';
    protected static ?string $navigationLabel = 'Facility Monitoring';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {

        $user = auth()->user(); // Retrieve the currently authenticated user
        $isPanelUser = $user->hasRole('panel_user');

        $commonActions = [
            Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make('view_monitoring')
                    ->label('View Records')
                    ->icon('heroicon-o-presentation-chart-line')
                    ->color('info')
                    ->modalHeading('Monitoring Records')
                    ->modalContent(function ($record) {
                        $facilityId = $record->id;

                        $monitorings = FacilityMonitoring::where('facility_id', $facilityId)
                            ->with('user')
                            ->get();
                        // dd($monitorings);                      
                        return view('filament.resources.facility-monitoring-modal', [
                            'monitorings' => $monitorings,
                        ]);
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
                                    ->options(User::all()->pluck('name', 'id'))
                                    ->required(),
                                Forms\Components\DatePicker::make('monitored_date')
                                    ->label('Monitored Date')
                                    ->format('Y-m-d'),
                                Forms\Components\TextInput::make('remarks')
                                    ->label('Remarks'),
                            ]);
                    })
                    ->action(function (array $data, $record) {
                        $data['facility_id'] = $record->id;

                        FacilityMonitoring::create($data);

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
            ]),
        ];

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('connection_type')
                    ->label('Connection Type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('facility_type')
                    ->label('Facility Type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cooling_tools')
                    ->label('Cooling Tools')
                    ->searchable(),
                Tables\Columns\TextColumn::make('floor_level')
                    ->label('Floor Level')
                    ->searchable(),
                Tables\Columns\TextColumn::make('building')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions($commonActions)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListFacilitySummaries::route('/'),
            'create' => Pages\CreateFacilitySummary::route('/create'),
            'edit' => Pages\EditFacilitySummary::route('/{record}/edit'),
        ];
    }
}
