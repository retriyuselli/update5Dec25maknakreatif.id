<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\KeyValue;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Toggle;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Document')
                    ->tabs([
                        Tab::make('General Info')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextInput::make('title')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                        Select::make('recipientsList')
                                            ->label('Kepada')
                                            ->placeholder('Pilih Tujuan Pengiriman')
                                            ->relationship('recipientsList', 'name')
                                            ->multiple()
                                            ->searchable()
                                            ->preload()
                                            ->columnSpanFull(),
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('category_id')
                                                    ->relationship('category', 'name')
                                                    ->required()
                                                    ->searchable()
                                                    ->preload(),
                                                Select::make('confidentiality')
                                                    ->options([
                                                        'public' => 'Public',
                                                        'internal' => 'Internal',
                                                        'confidential' => 'Confidential',
                                                        'secret' => 'Secret',
                                                    ])
                                                    ->required()
                                                    ->default('internal'),
                                                TextInput::make('document_number')
                                                    ->label('Document Number')
                                                    ->placeholder('Auto-generated')
                                                    ->disabled()
                                                    ->dehydrated(false),
                                                Toggle::make('use_digital_signature')
                                                    ->label('Gunakan Tanda Tangan Digital')
                                                    ->default(true)
                                                    ->inline(false),
                                                Toggle::make('show_confidentiality_warning')
                                                    ->label('Tampilkan Peringatan Kerahasiaan')
                                                    ->default(false)
                                                    ->inline(false),
                                            ]),
                                        Textarea::make('summary')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        Tab::make('Content & Dates')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        RichEditor::make('content')
                                            ->columnSpanFull(),
                                        Grid::make(2)
                                            ->schema([
                                                DatePicker::make('date_effective')
                                                    ->label('Effective Date'),
                                                DatePicker::make('date_expired')
                                                    ->label('Expiration Date')
                                                    ->live()
                                                    ->afterStateUpdated(function ($get, $state) {
                                                        $effectiveDate = $get('date_effective');
                                                        if ($effectiveDate && $state && $state < $effectiveDate) {
                                                            Notification::make()
                                                                ->danger()
                                                                ->title('Tanggal Tidak Valid')
                                                                ->body('Tanggal kedaluwarsa tidak boleh sebelum tanggal efektif.')
                                                                ->send();
                                                        }
                                                    }),
                                            ]),
                                    ]),
                            ]),
                        Tab::make('Attachments & Metadata')
                            ->schema([
                                Section::make('Attachments')
                                    ->schema([
                                        Repeater::make('attachments')
                                            ->relationship()
                                            ->schema([
                                                FileUpload::make('file_path')
                                                    ->label('File')
                                                    ->directory('documents')
                                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'])
                                                    ->maxSize(10240) // 10MB
                                                    ->downloadable()
                                                    ->openable(),
                                                TextInput::make('file_name')
                                                    ->required(),
                                            ])
                                            ->columns(2),
                                    ]),
                                Section::make('Metadata')
                                    ->schema([
                                        KeyValue::make('metadata')
                                            ->label('Additional Data')
                                            ->keyLabel('Field Name')
                                            ->valueLabel('Value'),
                                    ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
