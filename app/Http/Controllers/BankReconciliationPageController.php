<?php

namespace App\Http\Controllers;

use App\Models\BankStatement;
use App\Services\ReconciliationService;
use Illuminate\Support\Facades\Log;

class BankReconciliationPageController extends Controller
{
    public function show(BankStatement $bankStatement)
    {
        // Verify that the record has payment method and reconciliation items
        if (! $bankStatement->payment_method_id || ! $bankStatement->paymentMethod) {
            abort(404, 'Bank statement tidak memiliki payment method yang valid.');
        }

        if ($bankStatement->reconciliationItems()->count() === 0) {
            abort(404, 'Bank statement tidak memiliki data reconciliation items.');
        }

        // Load relationships
        $bankStatement->load('paymentMethod', 'reconciliationItems');

        // Initialize reconciliation service
        $reconciliationService = app(ReconciliationService::class);

        // Always run fresh reconciliation for the specific date range and save to database
        $reconciliationResults = $reconciliationService->reconcile(
            $bankStatement->payment_method_id,
            $bankStatement->period_start->format('Y-m-d'),
            $bankStatement->period_end->format('Y-m-d'),
            true  // Save matches to database
        );

        // Then get stored matches to ensure consistency with database
        $storedResults = $reconciliationService->getStoredMatches(
            $bankStatement->payment_method_id,
            $bankStatement->period_start->format('Y-m-d'),
            $bankStatement->period_end->format('Y-m-d')
        );

        // Use stored results for display to ensure unmark functionality works
        $reconciliationResults['matched'] = $storedResults['matched'];

        // DEBUG: Log the results for analysis
        foreach ($reconciliationResults['matched'] as $match) {
            $appTx = $match['app_transaction'];
            $bankItem = $match['bank_item'];

            // Log problematic matches
            if (($appTx->credit_amount == 0 && $bankItem->credit > 0) ||
                ($appTx->debit_amount == 0 && $bankItem->debit > 0)) {
                Log::warning("PROBLEMATIC_MATCH: App(C:{$appTx->credit_amount},D:{$appTx->debit_amount}) vs Bank(C:{$bankItem->credit},D:{$bankItem->debit}) - Confidence: {$match['confidence']}% - Description: {$appTx->description}");
            }
        }

        return view('bank-reconciliation.comparison', [
            'record' => $bankStatement,
            'reconciliationResults' => $reconciliationResults,
            'statistics' => $reconciliationResults['statistics'],
        ]);
    }
}
