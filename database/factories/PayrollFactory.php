<?php

namespace Database\Factories;

use App\Models\Payroll;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payroll>
 */
class PayrollFactory extends Factory
{
    protected $model = Payroll::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate realistic salary based on Indonesian standards
        $monthlySalary = $this->faker->numberBetween(4000000, 15000000); // 4-15 juta
        $bonus = $this->faker->numberBetween(500000, 3000000); // 500rb - 3juta

        return [
            'user_id' => User::factory(),
            'monthly_salary' => $monthlySalary,
            'bonus' => $bonus,
            'last_review_date' => $this->faker->dateTimeBetween('-2 years', '-1 month'),
            'next_review_date' => $this->faker->dateTimeBetween('now', '+6 months'),
            'notes' => $this->generateRealisticNotes($monthlySalary),
        ];
    }

    /**
     * Generate realistic notes for payroll
     */
    private function generateRealisticNotes(int $monthlySalary): string
    {
        $notes = [];

        // Initial hire note
        $hireDate = $this->faker->dateTimeBetween('-3 years', '-6 months');
        $initialSalary = $monthlySalary * $this->faker->randomFloat(2, 0.7, 0.9);
        $notes[] = "[{$hireDate->format('d/m/Y')}] Gaji awal: Rp ".number_format($initialSalary, 0, ',', '.').' - Mulai bekerja';

        // Performance review
        $reviewDate = $this->faker->dateTimeBetween('-1 year', '-2 months');
        $performanceNotes = [
            'Review tahunan: Kinerja baik, kenaikan gaji 10%',
            'Promosi jabatan: Peningkatan tanggung jawab',
            'Penyesuaian gaji sesuai market rate',
            'Bonus pencapaian target semester',
            'Kenaikan gaji berdasarkan evaluasi kinerja',
        ];
        $notes[] = "[{$reviewDate->format('d/m/Y')}] ".$this->faker->randomElement($performanceNotes);

        // Current status
        $notes[] = '['.now()->format('d/m/Y').'] Gaji saat ini: Rp '.number_format($monthlySalary, 0, ',', '.').' - Data terupdate sistem payroll';

        return implode("\n\n", $notes);
    }

    /**
     * Indicate that the payroll is for senior level employee.
     */
    public function senior(): static
    {
        return $this->state(fn (array $attributes) => [
            'monthly_salary' => $this->faker->numberBetween(12000000, 25000000),
            'bonus' => $this->faker->numberBetween(3000000, 8000000),
        ]);
    }

    /**
     * Indicate that the payroll is for junior level employee.
     */
    public function junior(): static
    {
        return $this->state(fn (array $attributes) => [
            'monthly_salary' => $this->faker->numberBetween(4000000, 8000000),
            'bonus' => $this->faker->numberBetween(500000, 2000000),
        ]);
    }

    /**
     * Indicate that the payroll is for manager level employee.
     */
    public function manager(): static
    {
        return $this->state(fn (array $attributes) => [
            'monthly_salary' => $this->faker->numberBetween(8000000, 15000000),
            'bonus' => $this->faker->numberBetween(2000000, 5000000),
        ]);
    }

    /**
     * Indicate that the payroll has recent review.
     */
    public function recentReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_review_date' => $this->faker->dateTimeBetween('-3 months', '-1 week'),
            'next_review_date' => $this->faker->dateTimeBetween('+9 months', '+15 months'),
        ]);
    }

    /**
     * Indicate that the payroll needs review soon.
     */
    public function reviewDue(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_review_date' => $this->faker->dateTimeBetween('-15 months', '-12 months'),
            'next_review_date' => $this->faker->dateTimeBetween('-1 week', '+2 weeks'),
        ]);
    }
}
