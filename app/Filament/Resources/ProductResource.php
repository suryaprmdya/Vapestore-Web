<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Product Information')
                    ->schema([

                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn($state, callable $set) =>
                                $set('slug', Str::slug($state))
                            ),

                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        RichEditor::make('description')
                            ->columnSpanFull(),

                        TextInput::make('price')
                            ->numeric()
                            ->required()
                            ->prefix('Rp'),

                        TextInput::make('stock')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Toggle::make('is_active')
                            ->default(true),

                        FileUpload::make('image')
                            ->label('Product Image')
                            ->image()
                            ->disk('public')
                            ->directory('products')
                            ->visibility('public')
                            ->imageEditor()
                            ->downloadable()
                            ->openable()
                            ->previewable(true)
                            ->columnSpanFull(),

                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->square()
                    ->disk('public'),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->badge()
                    ->sortable(),

                TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('stock')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable(),
                // TextColumn::make('image')
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
