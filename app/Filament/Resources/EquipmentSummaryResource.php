<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquipmentSummaryResource\Pages;
use App\Models\EquipmentMonitoring;
use App\Models\Equipment;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

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
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('facility.name')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.description')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->searchable()
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
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('restocking_point')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        $description = optional($record->stockunit)->description;
                        return $record->restocking_point . ($description ? " ($description)" : '');
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
            ->filters([])
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
<<<<<<< HEAD
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
=======
                            return $form->schema([
                                Forms\Components\Select::make('monitored_by')
                                    ->label('Monitored By')
                                    ->options(User::all()->pluck('name', 'id'))
                                    ->default(auth()->user()->id)
                                    ->disabled()
                                    ->required(),
                                Forms\Components\DatePicker::make('monitored_date')
                                    ->label('Monitoring Date')
                                    ->required()
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
                                    ->relationship('facility', 'name')
                                    ->default($record->facility_id)
                                    ->required(),
                                Forms\Components\TextInput::make('remarks')
                                    ->default($record->remarks)
                                    ->formatStateUsing(fn($state) => strip_tags($state))
                                    ->label('Remarks'),
                            ]);
>>>>>>> critical
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
                                'remarks' => $data['remarks'],
                            ]);
    
                            Notification::make()
                                ->success()
                                ->title('Success')
                                ->body('Selected items have been added to your monitoring.')
                                ->send();
                        }),
                    Tables\Actions\Action::make('AdjustStock')
                        ->icon('heroicon-o-minus')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalIcon('heroicon-o-pencil')
                        ->modalHeading('Adjust Stock')
                        ->modalDescription('Enter the quantity to deduct from stocks.')
                        ->form(function (Forms\Form $form, $record) {
                            return $form->schema([
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Quantity to deduct from its stocks')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue($record->no_of_stocks)
                                    ->hint("Available stock: {$record->no_of_stocks}")
                                    ->required(),
                            ]);
                        })
                        ->action(function (array $data, $record) {
                            $newStock = $record->no_of_stocks - $data['quantity'];
                            if ($newStock < 0) {
                                Notification::make()
                                ->danger()
                                ->title('Error')
                                ->body('Insufficient stock. Cannot deduct more than available stock.')
                                ->send();
                        } else {

                            $record->update(['no_of_stocks' => $newStock]);
    
                            Notification::make()
                                ->success()
                                ->title('Stock Adjusted')
                                ->body('Stock has been successfully adjusted.')
                                ->send();
                            }
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEquipmentSummaries::route('/'),
            'create' => Pages\CreateEquipmentSummary::route('/create'),
        ];
    }
}
