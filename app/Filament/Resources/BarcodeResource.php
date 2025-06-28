<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarcodeResource\Pages;
use App\Filament\Resources\BarcodeResource\RelationManagers;
use App\Models\Barcode;
use App\Services\GenerateQrcodeService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BarcodeResource extends Resource
{
    protected static ?string $model = Barcode::class;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $navigationGroup = 'Master';

    protected static ?int $navigationSort = 1;

    public static function canEdit(Model $record): bool
    {
        return false;   
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('table_number')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('table_number')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('Image-url')
                    ->label('Image QrCode')
                    ->extraImgAttributes(['class' => 'w-16 h-16 object-cover']),
                Tables\Columns\TextColumn::make('qr_code')
                    ->label('QR Code Link'),
                Tables\Columns\TextColumn::make('user.name')
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
                self::generateQrcode(),
                self::downloadQrcode(),
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
            'index' => Pages\ManageBarcodes::route('/'),
        ];
    }

    public static function generateQrcode()
    {
        return Action::make('generate-qrcode')
            ->label('Generate Qrcode')
            ->color('success')
            ->icon('heroicon-o-qr-code')
            ->action(function(Barcode $barcode){
                $generateQrcode = app(GenerateQrcodeService::class)->generateQr($barcode->id);

                $qrValue = 'qrcode/' . $barcode->table_number . '.svg';

                Storage::disk('public')->put($qrValue, $generateQrcode);

                $barcode->update([
                    'images'  => $qrValue,
                    'qr_code' => $_SERVER['HTTP_HOST'] .'/'.$barcode->table_number,
                    'users_id' => Auth::user()->id,
                ]);

                Notification::make()
                    ->title('Generate Qrcode Success')
                    ->success()
                    ->send();
            })
            ->visible(fn(Barcode $barcode) => !$barcode->images)
            ->button();
    }

    public static function downloadQrcode()
    {
        return Action::make('download-qrcode')
            ->label('Download QrCode')
            ->color('warning')
            ->button()
            ->visible(fn(Barcode $barcode) => $barcode->images)
            ->icon('heroicon-o-arrow-down-circle');
    }
}
