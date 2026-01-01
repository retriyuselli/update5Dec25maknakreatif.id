<?php

namespace App\Observers;

use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DocumentObserver
{
    public function creating(Document $document): void
    {
        if (empty($document->created_by) && Auth::check()) {
            $document->created_by = Auth::id();
        }

        if (empty($document->document_number) && $document->category) {
            $document->document_number = $this->generateDocumentNumber($document);
        }
    }

    protected function generateDocumentNumber(Document $document): string
    {
        $category = $document->category;
        $format = $category->format_number ?? '{SEQ}/{CAT}/MKI/{ROMAN_MONTH}/{Y}';

        // Replacements
        $replacements = [
            '{Y}' => now()->year,
            '{M}' => now()->format('m'),
            '{ROMAN_MONTH}' => $this->getRomanMonth(now()->month),
            '{CAT}' => $category->code ?? 'DOC',
            '{DEPT}' => 'GEN',
        ];

        $number = str_replace(array_keys($replacements), array_values($replacements), $format);

        // Sequence Handling
        if (str_contains($number, '{SEQ}')) {
            $latestDocument = Document::where('category_id', $category->id)
                ->whereYear('created_at', now()->year)
                ->where('id', '!=', $document->id) // Exclude self if updating (though this is creating)
                ->latest()
                ->first();

            $lastNumber = 0;
            if ($latestDocument && $latestDocument->document_number) {
                // Extract sequence assuming it's at the end or we parse it?
                // Simple approach: Count documents in this category this year + 1
                // Better approach: Regex to find the sequence part if possible, or just Count.
                // For now, let's use Count + 1 as a fallback if we can't parse.
                // Ideally we should store sequence separately, but for now:
                $count = Document::where('category_id', $category->id)
                    ->whereYear('created_at', now()->year)
                    ->count();
                $lastNumber = $count;
            }
            
            $sequence = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            $number = str_replace('{SEQ}', $sequence, $number);
        }

        return $number;
    }

    protected function getRomanMonth(int $month): string
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $map[$month] ?? '';
    }
}
