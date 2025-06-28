<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FoodResource\Pages;
use App\Filament\Resources\FoodResource\RelationManagers;
use App\Models\Food;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class FoodResource extends Resource
{
    protected static ?string $model = Food::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationGroup = 'Master';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('images')
                    ->required()
                    ->image()
                    ->directory('foods')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->columnSpanFull()
                    ->reactive()
                    ->prefix('Rp'),
                Forms\Components\Toggle::make('is_promo')
                    ->reactive(),
                Forms\Components\Select::make('discount')
                    ->options([
                        10 => '10%',
                        25 => '25%',
                        35 => '35%',
                        50 => '50%',
                    ])
                    ->reactive()
                    ->columnSpanFull()
                    ->hidden(fn($get) => !$get('is_promo'))
                    ->afterStateUpdated(function($set, $get, $state){
                        if($get('is_promo') && $get('price') && $get('discount')){
                            $discount = ($get('price') * (int)$get('discount')) / 100;
                            $set('price_afterdiscount', $get('price') - $discount);
                        }
                        else {
                            $set('price_afterdiscount', $get('price'));
                        }
                   }),
                Forms\Components\TextInput::make('price_afterdiscount')
                    ->label('Price After Discount')
                    ->prefix('Rp')
                    ->columnSpanFull()
                    ->readOnly()
                    ->hidden(fn($get) => !$get('is_promo')),
                Forms\Components\Select::make('categories_id')   
                    ->required()
                    ->columnSpanFull()
                    ->relationship('categories','name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Images')
                    ->extraImgAttributes(['class' => 'w-16 h-16 object-cover']),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_afterdiscount')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_promo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount')
                    ->formatStateUsing(fn($state) => $state !==null ? $state . '%' : '-')
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
            'index' => Pages\ManageFood::route('/'),
        ];
    }
}
