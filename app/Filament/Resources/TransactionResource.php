<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionItemResource\Pages\ManageTransactionItems;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Transaction';

    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('external_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('checkout_link')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('barcodes_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('payment_method')
                    ->maxLength(255),
                Forms\Components\TextInput::make('payment_status')
                    ->maxLength(255),
                Forms\Components\TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('ppn')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('total')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Transaction Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('external_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('checkout_link')
                    ->searchable(),
                Tables\Columns\TextColumn::make('barcode.qr_code')
                    ->label('Barcode'),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Peymanet Method')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Peymanet Status')
                    ->badge()
                    ->colors([
                        'success' => fn($state) : bool => in_array($state,['SUCCESS', 'PAID', 'SETTLED']),
                        'warnig'  => fn($state) : bool => $state === 'PENDING',
                        'danger'  => fn($state) : bool => in_array($state, ['FAILED', 'EXPIRED']),
                    ])
                    ->searchable(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Sub Total')
                    ->numeric()
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('ppn')
                    ->label('PPN')
                    ->numeric()
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->numeric()
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                self::seeDetailTransaction(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTransactions::route('/'),

            // menentukan route bisa dari sini
            // tombol detailTransaction bagian url
            'transaction-items.index' => ManageTransactionItems::route('{parent}/transaction')
        ];
    }

    public static function seeDetailTransaction()
    {
        return Action::make('detailTransaction')
            ->button()
            ->color('success')
            ->url(fn(Transaction $record) : string => static::getUrl('transaction-items.index', [
                'parent' => $record->id,
            ]))
            ->label('Detail Transaction');
    }
}
