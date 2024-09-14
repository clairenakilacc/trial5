<?php


namespace App\Filament\Resources;

use App\Filament\Resources\EquipmentSummaryResource\Pages;
use App\Filament\Resources\EquipmentSummaryResource\RelationManagers;
use App\Models\EquipmentMonitoring;
use App\Models\Equipment;
use App\Models\Critical;
use App\Models\ForRepair;
use App\Models\User;
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
    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
                Tables\Columns\TextColumn::make('unit_no')
                    ->label('Unit no.')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('facility.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.description')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                    Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable()
                    ->color(fn(string $state): string => match ($state) {
                        'Working' => 'success',
                        'For Repair' => 'warning',
                        'For Replacement' => 'primary',
                        'Lost' => 'danger',
                        'For Disposal' => 'primary',
                        'Disposed' => 'danger',
                        'Borrowed' => 'indigo',
                    }),
                Tables\Columns\TextColumn::make('date_acquired')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('supplier')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('amount')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('estimated_life')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('item_no')
                ->label('Item no.')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('property_no')
                ->label('Property no.')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('control_no')
                ->label('Control no.')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('serial_no')
                ->label('Serial no.')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_of_stocks')
                ->label('No. of Stocks')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        $description = optional($record->stockunit)->description;
                        return $record->no_of_stocks . ($description ? " ($description)" : '');
                    })
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
               
                Tables\Columns\TextColumn::make('restocking_point')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(function ($record) {
                        $description = optional($record->stockunit)->description;
                        return $record->no_of_stocks . ($description ? " ($description)" : '');
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('person_liable')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('remarks')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make('view_monitoring')
                        ->label('View Records')
                        ->icon('heroicon-o-presentation-chart-line')
                        ->color('info')
                        ->modalHeading('Monitoring Records')
                        ->modalContent(function ($record) {
                            $equipmentId = $record->id;
                            $monitorings = EquipmentMonitoring::with('equipment.facility', 'user')
                                ->where('equipment_id', $equipmentId)
                                ->get();

                            // dd($monitorings);                      
                            return view('filament.resources.equipment-monitoring-modal', [
                                'monitorings' => $monitorings,
                            ]);
                        }),
                    Tables\Actions\Action::make('Update Status')
                        ->icon('heroicon-o-plus')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->modalIcon('heroicon-o-check')
                        ->modalHeading('Add to Monitoring')
                        ->modalDescription('Confirm to add selected items to your Monitoring')
                        ->form(function (Forms\Form $form, $record) {
                            return $form
                                ->schema([
                                    Forms\Components\Select::make('monitored_by')
                                        ->label('Monitored By')
                                        ->options(User::all()->pluck('name', 'id'))
                                        ->default(auth()->user()->id)
                                        ->disabled()
                                        ->required(),
                                    Forms\Components\DatePicker::make('monitored_date')
                                        ->label('Monitoring Date')
                                        ->required()
                                        ->disabled()
                                        ->default(now())
                                        ->format('Y-m-d'),

                                    Forms\Components\Select::make('status')
                                        ->required()
                                        ->options([
                                            'Working' => 'Working',
                                            'For Repair' => 'For Repair',
                                            'For Replacement' => 'For Replacement',
                                            'Lost' => 'Lost',
                                            'For Disposal' => 'For Disposal',
                                            'Disposed' => 'Disposed',
                                            'Borrowed' => 'Borrowed',
                                        ])
                                        ->default($record->status)
                                        ->native(false),
                                    Forms\Components\Select::make('facility_id')
                                        ->label ('New Assigned Facility')
                                        ->relationship('facility', 'name')
                                        ->default($record->facility_id)
                                        ->required(),
                                    
                                    Forms\Components\TextInput::make('remarks')
                                        ->default($record->remarks)
                                        ->formatStateUsing(fn($state) => strip_tags($state))
                                        ->label('Remarks'),
                                ]);
                        })
                        ->action(function (array $data, $record) {
                            $data['equipment_id'] = $record->id;

                            if (empty($data['monitored_by'])) {
                                $data['monitored_by'] = auth()->user()->id;
                            }

                            if (empty($data['monitored_date'])) {
                                $data['monitored_date'] = now()->format('Y-m-d');
                            }

                            EquipmentMonitoring::create($data);

                            $record->update([
                                'status' => $data['status'],
                                'facility_id' => $data['facility_id'],
                                'remarks' => $data['remarks']
                            ]);
                            Notification::make()
                                ->success()
                                ->title('Success')
                                ->body('Selected items have been added to your monitoring.')
                                ->send();
                        }),
                ])
            ])
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
            'index' => Pages\ListEquipmentSummaries::route('/'),
            'create' => Pages\CreateEquipmentSummary::route('/create'),
            //'edit' => Pages\EditEquipmentSummary::route('/{record}/edit'),
        ];
    }
}