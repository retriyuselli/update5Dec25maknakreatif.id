<?php

namespace App\Filament\Resources\DocumentCategories\Schemas;

use App\Enums\DocumentCategoryType;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

class DocumentCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Details')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('code')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Select::make('type')
                            ->options(DocumentCategoryType::class)
                            ->required(),
                        TextInput::make('format_number')
                            ->label('Numbering Format')
                            ->placeholder('e.g., {SEQ}/{CAT}/MKI/{ROMAN_MONTH}/{Y}'),
                        Select::make('parent_id')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Parent Category'),
                        Toggle::make('is_approval_required')
                            ->label('Requires Approval Workflow')
                            ->default(true),
                    ])->columns(2),
            ]);
    }
}
