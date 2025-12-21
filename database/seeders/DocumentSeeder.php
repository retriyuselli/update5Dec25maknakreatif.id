<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        
        if (! $user) {
            // Ensure at least one user exists to assign as creator
            $user = User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
            ]);
        }

        $categories = DocumentCategory::all();

        if ($categories->isEmpty()) {
            $this->call(DocumentCategorySeeder::class);
            $categories = DocumentCategory::all();
        }

        $statuses = ['draft', 'pending', 'approved', 'published', 'archived'];
        $confidentialities = ['public', 'internal', 'confidential', 'secret'];

        foreach (range(1, 25) as $i) {
            $category = $categories->random();
            $status = $statuses[array_rand($statuses)];
            $confidentiality = $confidentialities[array_rand($confidentialities)];
            
            // Simple format replacement logic for seed data
            $romanMonth = $this->getRomanMonth(date('n'));
            $year = date('Y');
            $seq = str_pad($i, 3, '0', STR_PAD_LEFT);
            
            $docNumber = $category->format_number ?? 'DOC/{Y}/{SEQ}';
            $docNumber = str_replace(
                ['{Y}', '{SEQ}', '{DEPT}', '{ROMAN_MONTH}'],
                [$year, $seq, 'GEN', $romanMonth],
                $docNumber
            );

            if (Document::where('document_number', $docNumber)->exists()) {
                continue;
            }

            Document::create([
                'category_id' => $category->id,
                'document_number' => $docNumber,
                'title' => fake()->sentence(rand(4, 8)),
                'summary' => fake()->paragraph(),
                'content' => $this->generateHtmlContent(),
                'date_effective' => Carbon::now()->subDays(rand(0, 60)),
                'date_expired' => rand(0, 1) ? Carbon::now()->addYears(rand(1, 5)) : null,
                'status' => $status,
                'confidentiality' => $confidentiality,
                'created_by' => $user->id,
                'metadata' => [
                    'keywords' => fake()->words(5),
                    'version' => '1.' . rand(0, 5),
                    'priority' => rand(0, 1) ? 'high' : 'normal',
                ],
            ]);
        }
    }

    private function getRomanMonth($month)
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $map[$month] ?? 'X';
    }

    private function generateHtmlContent()
    {
        return '
            <h2>' . fake()->sentence() . '</h2>
            <p>' . fake()->paragraph(4) . '</p>
            <ul>
                <li>' . fake()->sentence() . '</li>
                <li>' . fake()->sentence() . '</li>
                <li>' . fake()->sentence() . '</li>
            </ul>
            <p>' . fake()->paragraph(3) . '</p>
            <h3>Conclusion</h3>
            <p>' . fake()->paragraph(2) . '</p>
        ';
    }
}
