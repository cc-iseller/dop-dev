<?php

namespace App\Filament\Resources\Sellers\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\TextEntry;


class SellerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        TextEntry::make('id')
                            ->label('Store ID'),

                        TextEntry::make('name')
                            ->label('Nama Store'),

                        TextEntry::make('owner.name')
                            ->label('Owner')
                            ->placeholder('-'),

                        TextEntry::make('owner.email')
                            ->label('Email Owner')
                            ->placeholder('-'),
                    ])
                    ->columns(2),

                Group::make()
                    ->schema([
                        TextEntry::make('subscription.plan.name')
                            ->label('Nama Paket')
                            ->placeholder('-'),

                        TextEntry::make('subscription.plan.code')
                            ->label('Kode Paket')
                            ->formatStateUsing(fn ($state) => strtoupper((string) $state))
                            ->placeholder('-'),

                        TextEntry::make('subscription.status')
                            ->label('Status Subscription')
                            ->placeholder('-'),

                        TextEntry::make('subscription.started_at')
                            ->label('Mulai')
                            ->dateTime('d M Y H:i')
                            ->placeholder('-'),

                        TextEntry::make('subscription.ends_at')
                            ->label('Berakhir')
                            ->dateTime('d M Y H:i')
                            ->placeholder('-'),
                    ])
                    ->columns(2),

                Group::make()
                    ->schema([
                        TextEntry::make('subscription.latestPayment.status')
                            ->label('Status Pembayaran Terakhir')
                            ->placeholder('Belum ada'),

                        TextEntry::make('subscription.latestPayment.order_id')
                            ->label('Order ID')
                            ->placeholder('-'),

                        TextEntry::make('subscription.latestPayment.amount')
                            ->label('Amount')
                            ->money('IDR', true)
                            ->placeholder('-'),

                        TextEntry::make('subscription.latestPayment.provider')
                            ->label('Provider')
                            ->placeholder('-'),

                        TextEntry::make('subscription.latestPayment.created_at')
                            ->label('Waktu Pembayaran')
                            ->dateTime('d M Y H:i')
                            ->placeholder('-'),
                    ])
                    ->columns(2),
            ]);
    }
}
