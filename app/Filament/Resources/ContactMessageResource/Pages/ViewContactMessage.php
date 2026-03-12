<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('markRead')
                ->label('Mark as read')
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(function (): bool {
                    return $this->record instanceof ContactMessage && ! $this->record->is_read;
                })
                ->action(function (): void {
                    if (! $this->record instanceof ContactMessage) {
                        return;
                    }

                    $this->record->markAsRead();

                    Notification::make()
                        ->title('Message marked as read')
                        ->success()
                        ->send();

                    $this->refreshFormData(['is_read']);
                }),

            DeleteAction::make()
                ->successRedirectUrl(ContactMessageResource::getUrl('index')),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (! $this->record instanceof ContactMessage) {
            return $data;
        }

        if (! $this->record->is_read) {
            $this->record->markAsRead();
            $data['is_read'] = true;
        }

        return $data;
    }
}
