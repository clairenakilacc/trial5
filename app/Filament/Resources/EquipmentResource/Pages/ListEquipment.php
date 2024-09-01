<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use App\Filament\Resources\EquipmentResource;
use Filament\Actions;
use Filament\Actions\Action;
use App\Imports\EquipmentImport;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Illuminate\Support\Facades\Storage;

class ListEquipment extends ListRecords
{
    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        $isPanelUser = $user->hasRole('panel_user');

        $actions = [
            Actions\CreateAction::make()
                ->label('Create'),
        ];

        if (!$isPanelUser) {

            $actions[] = Action::make('importEquipment')
                ->label('Import')
                ->color('success')
                ->button()
                // ->form([
                //     FileUpload::make('attachment'),
                // ])
                ->form([
                    FileUpload::make('attachment')
                        ->disk('local')
                        ->directory('imports') // Store in 'imports' folder within storage/app
                        ->preserveFilenames(),
                ])
                // ->action(function (array $data) {
                //     $file = public_path('storage/' . $data['attachment']);

                //     // dd($file);

                //     Excel::import(new EquipmentImport, $file);

                //     Notification::make()
                //         ->title('Equipment Imported')
                //         ->success()
                //         ->send();
                // });
                ->action(function (array $data) {
                    // Get the correct file path from storage
                    $filePath = $data['attachment'];

                    // Full path to file
                    $file = Storage::disk('local')->path($filePath);

                    // Perform the Excel import
                    Excel::import(new EquipmentImport, $file);

                    Notification::make()
                        ->title('Equipment Imported')
                        ->success()
                        ->send();
                });
        }

        return $actions;
    }


    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            Tab::make('All')
                ->modifyQueryUsing(function ($query) {
                    return $query; // No filtering, display all records
                }),
            Tab::make('Working')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', 'Working');
                }),
            Tab::make('For Repair')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', 'For Repair');
                }),
            Tab::make('For Replacement')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', 'For Replacement');
                }),
            Tab::make('Lost')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', 'Lost');
                }),
            Tab::make('For Disposal')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', 'For Disposal');
                }),
            Tab::make('Disposed')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', 'Disposed');
                }),
            Tab::make('Borrowed')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', 'Borrowed');
                }),
        ];
    }
}
