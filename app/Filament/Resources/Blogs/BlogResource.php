<?php

namespace App\Filament\Resources\Blogs;

use App\Filament\Resources\Blogs\Pages\CreateBlog;
use App\Filament\Resources\Blogs\Pages\EditBlog;
use App\Filament\Resources\Blogs\Pages\ListBlogs;
use App\Filament\Resources\Blogs\Widgets\BlogStatsWidget;
use App\Models\Blog;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    protected static string|\UnitEnum|null $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Blog';

    protected static ?string $modelLabel = 'Artikel Blog';

    protected static ?string $pluralModelLabel = 'Artikel Blog';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->description('Informasi utama artikel')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $context, $state, callable $set) => $context === 'create' ? $set('slug', Str::slug($state)) : null)
                                    ->columnSpan(2),

                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Blog::class, 'slug', ignoreRecord: true)
                                    ->helperText('Versi judul yang ramah URL')
                                    ->columnSpan(2),
                            ]),

                        Textarea::make('excerpt')
                            ->required()
                            ->maxLength(500)
                            ->helperText('Deskripsi singkat artikel (maks 500 karakter)')
                            ->columnSpanFull(),
                    ]),

                Section::make('Konten')
                    ->description('Konten artikel dan media')
                    ->schema([
                        RichEditor::make('content')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('featured_image')
                            ->url()
                            ->placeholder('https://example.com/image.jpg')
                            ->helperText('URL gambar utama (gunakan Unsplash atau sumber gambar lainnya)')
                            ->columnSpanFull(),
                    ]),

                Section::make('Kategorisasi')
                    ->description('Kategori dan tag')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('category')
                                    ->required()
                                    ->options([
                                        'Featured' => 'Featured',
                                        'Tutorial' => 'Tutorial',
                                        'Business' => 'Business',
                                        'Tips' => 'Tips',
                                        'Keuangan' => 'Keuangan',
                                    ])
                                    ->searchable()
                                    ->preload(),

                                TagsInput::make('tags')
                                    ->placeholder('Tambahkan tag')
                                    ->helperText('Tekan Enter untuk menambahkan setiap tag'),
                            ]),
                    ]),

                Section::make('Author Information')
                    ->description('Author details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('author_name')
                                    ->required()
                                    ->maxLength(255)
                                    ->default(fn () => Auth::user()?->name ?? 'Admin WOFINS')
                                    ->helperText('Author name (defaults to current user)'),

                                Select::make('author_title')
                                    ->required()
                                    ->options([
                                        'Financial Expert' => 'Financial Expert',
                                        'Wedding Consultant' => 'Wedding Consultant',
                                        'Business Analyst' => 'Business Analyst',
                                        'Content Manager' => 'Content Manager',
                                        'Technical Expert' => 'Technical Expert',
                                        'Marketing Expert' => 'Marketing Expert',
                                        'SEO Specialist' => 'SEO Specialist',
                                        'Admin WOFINS' => 'Admin WOFINS',
                                    ])
                                    ->default(function () {
                                        $user = Auth::user();
                                        if (! $user) {
                                            return 'Financial Expert';
                                        }

                                        // Smart default based on user email
                                        $email = strtolower($user->email);
                                        if (str_contains($email, 'admin') || str_contains($email, 'manager')) {
                                            return 'Admin WOFINS';
                                        } elseif (str_contains($email, 'tech') || str_contains($email, 'dev')) {
                                            return 'Technical Expert';
                                        } elseif (str_contains($email, 'marketing')) {
                                            return 'Marketing Expert';
                                        } else {
                                            return 'Financial Expert';
                                        }
                                    })
                                    ->searchable()
                                    ->helperText('Select appropriate author title'),
                            ]),
                    ]),

                Section::make('Publishing Settings')
                    ->description('Publication and visibility settings')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('read_time')
                                    ->required()
                                    ->numeric()
                                    ->default(5)
                                    ->suffix('minutes')
                                    ->minValue(1)
                                    ->maxValue(60),

                                Toggle::make('is_featured')
                                    ->helperText('Show in featured articles section'),

                                Toggle::make('is_published')
                                    ->helperText('Make article visible to public')
                                    ->default(true),
                            ]),

                        DateTimePicker::make('published_at')
                            ->default(now())
                            ->helperText('When should this article be published?'),
                    ]),

                Section::make('SEO Settings')
                    ->description('Search engine optimization')
                    ->collapsible()
                    ->schema([
                        TextInput::make('meta_title')
                            ->maxLength(255)
                            ->helperText('SEO title (leave empty to use article title)'),

                        Textarea::make('meta_description')
                            ->maxLength(160)
                            ->helperText('SEO description (max 160 characters)')
                            ->columnSpanFull(),

                        TextInput::make('views_count')
                            ->numeric()
                            ->default(0)
                            ->helperText('Article view count (for display purposes)'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')
                    ->label('Image')
                    ->square()
                    ->size(60),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->wrap(),

                BadgeColumn::make('category')
                    ->searchable()
                    ->colors([
                        'primary' => 'Featured',
                        'success' => 'Tutorial',
                        'warning' => 'Business',
                        'info' => 'Tips',
                        'danger' => 'Keuangan',
                    ]),

                TextColumn::make('author_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('read_time')
                    ->label('Read Time')
                    ->suffix(' min')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->alignCenter(),

                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('M j, Y')
                    ->sortable(),

                TextColumn::make('views_count')
                    ->label('Views')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'Featured' => 'Featured',
                        'Tutorial' => 'Tutorial',
                        'Business' => 'Business',
                        'Tips' => 'Tips',
                        'Keuangan' => 'Keuangan',
                    ]),

                Filter::make('is_featured')
                    ->label('Featured Articles')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),

                Filter::make('is_published')
                    ->label('Published Articles')
                    ->query(fn (Builder $query): Builder => $query->where('is_published', true)),

                Filter::make('published_this_month')
                    ->label('Published This Month')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('published_at', now()->month)),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('toggle_featured')
                        ->label('Toggle Featured')
                        ->icon('heroicon-o-star')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_featured' => ! $record->is_featured]);
                            }
                        }),
                    BulkAction::make('toggle_published')
                        ->label('Toggle Published')
                        ->icon('heroicon-o-eye')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_published' => ! $record->is_published]);
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            BlogStatsWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlogs::route('/'),
            'create' => CreateBlog::route('/create'),
            'edit' => EditBlog::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'excerpt', 'author_name', 'category'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Category' => $record->category,
            'Author' => $record->author_name,
            'Published' => $record->published_at?->format('M j, Y'),
        ];
    }
}
