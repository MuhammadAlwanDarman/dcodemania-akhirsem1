<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\AddressRelationManager;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Enums\Alignment;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Order Information')->schema([
                        Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('payment_method')
                            ->options([
                                'stripe' => 'Stripe',
                                'cod'    => 'Cash on Delivery',
                            ])
                            ->default('stripe')
                            ->required(),

                        Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'paid'    => 'Paid',
                                'failed'  => 'Failed',
                            ])
                            ->default('pending')
                            ->required(),

                        ToggleButtons::make('status')
                            ->options([
                                'new'        => 'New',
                                'processing' => 'Processing',
                                'shipped'    => 'Shipped',
                                'delivered'  => 'Delivered',
                                'cancelled'  => 'Cancelled',
                            ])
                            ->inline()
                            ->default('new')
                            ->required()
                            ->colors([
                                'new'        => 'info',
                                'processing' => 'warning',
                                'shipped'    => 'success',
                                'delivered'  => 'success',
                                'cancelled'  => 'danger',
                            ])
                            ->icons([
                                'new'        => 'heroicon-m-sparkles',
                                'processing' => 'heroicon-m-arrow-path',
                                'shipped'    => 'heroicon-m-truck',
                                'delivered'  => 'heroicon-m-check-badge',
                                'cancelled'  => 'heroicon-m-x-circle',
                            ]),

                        Select::make('currency')
                            ->options([
                                'inr' => 'INR',
                                'usd' => 'USD',
                                'eur' => 'EUR',
                                'gbp' => 'GBP',
                            ])
                            ->default('inr')
                            ->required(),

                        Select::make('shipping_method')
                            ->options([
                                'fedex' => 'FedEx',
                                'ups'   => 'UPS',
                                'dhl'   => 'DHL',
                                'usps'  => 'USPS',
                            ]),

                        Textarea::make('notes')
                            ->columnSpanFull(),
                    ])->columns(2),

                    Section::make('Order Items')->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->columnSpan(4)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $price = \App\Models\Product::find($state)?->price ?? 0;
                                        $quantity = $get('quantity') ?? 1;

                                        $set('unit_amount', $price);
                                        $set('total_amount', $quantity * $price);

                                        // Update grand_total
                                        $items = $get('../../items') ?? [];
                                        $grandTotal = collect($items)->sum(fn ($item) => ($item['total_amount'] ?? 0));
                                        $set('../../grand_total', $grandTotal);
                                    }),

                                TextInput::make('quantity')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1)
                                    ->columnSpan(2)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $set('total_amount', $state * $get('unit_amount'));

                                        // Update grand_total
                                        $items = $get('../../items') ?? [];
                                        $grandTotal = collect($items)->sum(fn ($item) => ($item['quantity'] ?? 1) * ($item['unit_amount'] ?? 0));
                                        $set('../../grand_total', $grandTotal);
                                    }),

                                TextInput::make('unit_amount')
                                    ->numeric()
                                    ->required()
                                    ->disabled()
                                    ->columnSpan(3),

                                TextInput::make('total_amount')
                                    ->numeric()
                                    ->required()
                                    ->columnSpan(3),
                            ])->columns(12),

                        Placeholder::make('grand_total_placeholder')
                            ->label('Grand Total')
                            ->content(function (Get $get) {
                                $total = 0;

                                if ($repeaters = $get('items')) {
                                    foreach ($repeaters as $repeater) {
                                        $total += $repeater['total_amount'] ?? 0;
                                    }
                                }

                                return (new \NumberFormatter('en_IN', \NumberFormatter::CURRENCY))
                                    ->formatCurrency($total, 'INR');
                            }),

                        Hidden::make('grand_total')
                            ->default(0)
                            ->dehydrated(), // penting biar ke-save
                    ]),
                ])->columnSpanFull(),
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('grand_total')
                    ->label('Grand Total')
                    ->money(fn ($record) => $record->currency ?? 'INR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->badge()
                    ->colors([
                        'stripe' => 'info',
                        'cod'    => 'warning',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment Status')
                    ->badge()
                    ->colors([
                        'pending' => 'warning',
                        'paid'    => 'success',
                        'failed'  => 'danger',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('currency')
                    ->label('Currency')
                    ->sortable(),

                Tables\Columns\TextColumn::make('shipping_method')
                    ->label('Shipping Method')
                    ->sortable(),

                Tables\Columns\SelectColumn::make('status')
                ->label('Status')
                ->options([
                    'new'        => 'New',
                    'processing' => 'Processing',
                    'shipped'    => 'Shipped',
                    'delivered'  => 'Delivered',
                    'cancelled'  => 'Cancelled',
                ])
                ->sortable()
                ->searchable()
                ->selectablePlaceholder(false)
                ->rules(['required']),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'stripe' => 'Stripe',
                        'cod'    => 'Cash on Delivery',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid'    => 'Paid',
                        'failed'  => 'Failed',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new'        => 'New',
                        'processing' => 'Processing',
                        'shipped'    => 'Shipped',
                        'delivered'  => 'Delivered',
                        'cancelled'  => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }



    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    
}
