@php
    $companyName = 'PT. Makna Kreatif Indonesia';
    if (\Illuminate\Support\Facades\Schema::hasTable('companies')) {
        $val = \App\Models\Company::value('company_name');
        if ($val) {
            $companyName = $val;
        }
    }
@endphp

<!-- Simple Copyright Footer -->
<div class="text-center mt-6">
    <p class="text-gray-500 text-sm">
        Copyright © {{ date('Y') }} {{ $companyName }}. All rights reserved.
    </p>
</div>
