@extends('layouts.app')

@section('title', 'Project     .running-text-container::after {
        right: 0;
        background: linear-gradient(to left, rgba(37, 99, 235, 0.6), transparent);
    }
    
    /* Rich Text Formatting Styles */
    .rich-text-content {
        line-height: 1.6;
    }
    
    .rich-text-content ol {
        counter-reset: list-counter;
        padding-left: 0;
        margin: 1rem 0;
    }
    
    .rich-text-content ol li {
        counter-increment: list-counter;
        position: relative;
        padding-left: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .rich-text-content ol li::before {
        content: counter(list-counter) ".";
        position: absolute;
        left: 0;
        top: 0;
        font-weight: bold;
        color: #1e40af;
        min-width: 1.5rem;
    }
    
    .rich-text-content ul {
        padding-left: 1.5rem;
        margin: 1rem 0;
    }
    
    .rich-text-content ul li {
        position: relative;
        margin-bottom: 0.5rem;
        list-style-type: disc;
    }
    
    .rich-text-content ul li::marker {
        color: #1e40af;
    }
    
    .rich-text-content p {
        margin-bottom: 1rem;
    }
    
    .rich-text-content h3 {
        font-size: 1.125rem;
        font-weight: 600;
        margin: 1.5rem 0 0.5rem 0;
        color: #1f2937;
    }
    
    .rich-text-content h4 {
        font-size: 1rem;
        font-weight: 600;
        margin: 1rem 0 0.5rem 0;
        color: #374151;
    }
    
    .rich-text-content strong {
        font-weight: 600;
        color: #1f2937;
    }
    
    .rich-text-content em {
        font-style: italic;
    }
    
    /* Nested lists */
    .rich-text-content ol ol {
        counter-reset: sublist-counter;
        margin: 0.5rem 0;
    }
    
    .rich-text-content ol ol li::before {
        content: counter(list-counter) "." counter(sublist-counter) ".";
        counter-increment: sublist-counter;
    }
</style>
@endpush - ' . $order->name)

@push('styles')
<style>
    .running-text-container {
        overflow: hidden;
        white-space: nowrap;
    }
    
    .running-text {
        display: inline-block;
        animation: scroll-left 40s linear infinite;
        /* Start animation immediately */
        animation-delay: 0s;
    }
    
    @keyframes scroll-left {
        0% {
            transform: translateX(50%);
        }
        100% {
            transform: translateX(-100%);
        }
    }
    
    .running-text:hover {
        animation-play-state: paused;
    }
    
    /* Smooth fade effects for better visibility */
    .running-text-container {
        position: relative;
    }
    
    .running-text-container::before,
    .running-text-container::after {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        width: 30px;
        z-index: 2;
        pointer-events: none;
    }
    
    .running-text-container::before {
        left: 0;
        background: linear-gradient(to right, rgba(37, 100, 235, 0), transparent);
    }
    
    .running-text-container::after {
        right: 0;
        background: linear-gradient(to left, rgba(37, 100, 235, 0), transparent);
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    @include('front.header')

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-gray-900 via-blue-900 to-black text-white py-20">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto">
                <div class="text-center">
                    {{-- <div class="flex items-center justify-center mb-4">
                        <span class="
                            @if($order->status === \App\Enums\OrderStatus::Processing) bg-blue-600 text-blue-100
                            @elseif($order->status === \App\Enums\OrderStatus::Done) bg-gray-800 text-gray-100  
                            @elseif($order->status === \App\Enums\OrderStatus::Pending) bg-blue-500 text-blue-100
                            @elseif($order->status === \App\Enums\OrderStatus::Cancelled) bg-gray-600 text-gray-100
                            @else bg-blue-400 text-blue-100
                            @endif px-4 py-2 rounded-full text-sm font-semibold">
                            {{ $order->status ? $order->status->getLabel() : 'N/A' }}
                        </span>
                    </div> --}}
                    <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">
                        <span class="bg-gradient-to-r from-blue-400 to-purple-600 bg-clip-text text-transparent">
                            {{ $order->name }}
                        </span>
                    </h1>
                    <p class="text-xl text-gray-300 mb-2">{{ $order->number }}</p>
                    @if($order->no_kontrak)
                        <p class="text-blue-300 mb-6">Kontrak: {{ $order->no_kontrak }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Project Detail Content -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="max-w-6xl mx-auto">
                
                <!-- Running Text Section -->
                <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-blue-800 rounded-lg mb-8 overflow-hidden shadow-xl">
                    <div class="relative">
                        <div class="bg-black/20 backdrop-blur-sm">
                            <div class="flex items-center py-4 px-6">
                                <div class="flex-shrink-0 mr-4">
                                    <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center animate-pulse">
                                        <i class="fas fa-bullhorn text-black text-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-1 relative">
                                    <div class="running-text-container relative">
                                        <div class="running-text text-white text-lg font-semibold whitespace-nowrap">
                                            🎉 Welcome to Project Detail - {{ $order->name }} &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; Status: {{ $order->status ? $order->status->getLabel() : 'N/A' }} &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; 
                                            @if($order->no_kontrak) Contract: {{ $order->no_kontrak }} &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; @endif
                                            Total Value: Rp {{ number_format($order->total_price, 0, ',', '.') }} &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; 
                                            💼 Managed by Professional Wedding Organizer Team &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; 
                                            📞 Contact us for more information &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; 
                                            ✨ Creating Beautiful Moments Together ✨ &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-8">
                        
                        <!-- Project Information Card -->
                        <div class="bg-white rounded-xl shadow-lg p-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                                Project Information
                            </h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Project Name</label>
                                        <p class="text-lg font-semibold text-gray-900">{{ $order->name }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Project Number</label>
                                        <p class="text-lg font-semibold text-gray-900">{{ $order->number }}</p>
                                    </div>
                                    
                                    @if($order->no_kontrak)
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Contract Number</label>
                                        <p class="text-lg font-semibold text-gray-900">{{ $order->no_kontrak }}</p>
                                    </div>
                                    @endif
                                    
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Status</label>
                                        <div class="mt-1">
                                            <span class="
                                                @if($order->status === \App\Enums\OrderStatus::Processing) bg-blue-100 text-blue-800
                                                @elseif($order->status === \App\Enums\OrderStatus::Done) bg-gray-100 text-gray-800  
                                                @elseif($order->status === \App\Enums\OrderStatus::Pending) bg-blue-50 text-blue-700
                                                @elseif($order->status === \App\Enums\OrderStatus::Cancelled) bg-gray-200 text-gray-700
                                                @else bg-blue-50 text-blue-600
                                                @endif px-3 py-1 rounded-full text-sm font-semibold">
                                                {{ $order->status ? $order->status->getLabel() : 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Client</label>
                                        <p class="text-lg font-semibold text-gray-900">{{ $order->user?->name ?? 'N/A' }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Wedding Planner</label>
                                        <p class="text-lg font-semibold text-gray-900">{{ $order->employee?->name ?? 'N/A' }}</p>
                                    </div>
                                    
                                    @if($order->pax)
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Number of Guests</label>
                                        <p class="text-lg font-semibold text-gray-900">{{ $order->pax }} Tamu</p>
                                    </div>
                                    @endif
                                    
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Created Date</label>
                                        <p class="text-lg font-semibold text-gray-900">{{ $order->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            @if($order->note)
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <label class="text-sm font-medium text-gray-600">Notes</label>
                                <div class="mt-2 text-gray-700 leading-relaxed rich-text-content">
                                    @php
                                        // Handle different content formats
                                        $content = $order->note;
                                        
                                        // If content looks like JSON (TipTap format), try to parse
                                        if (is_string($content) && (str_starts_with(trim($content), '{') || str_starts_with(trim($content), '['))) {
                                            try {
                                                $decoded = json_decode($content, true);
                                                if (json_last_error() === JSON_ERROR_NONE) {
                                                    // Simple conversion for basic TipTap structure
                                                    if (isset($decoded['content'])) {
                                                        $htmlContent = '';
                                                        foreach ($decoded['content'] as $block) {
                                                            if ($block['type'] === 'paragraph') {
                                                                $htmlContent .= '<p>';
                                                                if (isset($block['content'])) {
                                                                    foreach ($block['content'] as $text) {
                                                                        if (isset($text['text'])) {
                                                                            $htmlContent .= htmlspecialchars($text['text']);
                                                                        }
                                                                    }
                                                                }
                                                                $htmlContent .= '</p>';
                                                            } elseif ($block['type'] === 'orderedList') {
                                                                $htmlContent .= '<ol class="numbered-list">';
                                                                if (isset($block['content'])) {
                                                                    foreach ($block['content'] as $listItem) {
                                                                        if ($listItem['type'] === 'listItem') {
                                                                            $htmlContent .= '<li>';
                                                                            if (isset($listItem['content'])) {
                                                                                foreach ($listItem['content'] as $para) {
                                                                                    if (isset($para['content'])) {
                                                                                        foreach ($para['content'] as $text) {
                                                                                            if (isset($text['text'])) {
                                                                                                $htmlContent .= htmlspecialchars($text['text']);
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                            $htmlContent .= '</li>';
                                                                        }
                                                                    }
                                                                }
                                                                $htmlContent .= '</ol>';
                                                            }
                                                        }
                                                        $content = $htmlContent;
                                                    }
                                                }
                                            } catch (Exception $e) {
                                                // If parsing fails, use original content
                                            }
                                        }
                                        
                                        // Ensure list elements have proper classes
                                        $content = preg_replace('/<ol(?![^>]*class)/', '<ol class="numbered-list"', $content);
                                        $content = preg_replace('/<ul(?![^>]*class)/', '<ul class="bulleted-list"', $content);
                                    @endphp
                                    {!! $content !!}
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Payment History Card (if any payments exist) -->
                        <div class="bg-white rounded-xl shadow-lg p-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-history text-blue-600 mr-3"></i>
                                Payment History
                            </h2>
                            
                            <div class="space-y-4">
                                <!-- Payment timeline placeholder -->
                                <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-calendar text-white text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">Project Created</p>
                                        <p class="text-sm text-gray-600">{{ $order->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>
                                
                                @if($order->paid_amount > 0)
                                <div class="flex items-center space-x-4 p-4 bg-green-50 rounded-lg">
                                    <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-money-bill text-white text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">Payment Received</p>
                                        <p class="text-sm text-gray-600">Rp {{ number_format($order->paid_amount, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                @endif
                                
                                @if($order->status === \App\Enums\OrderStatus::Done)
                                <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center">
                                        <i class="fas fa-check text-white text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">Project Completed</p>
                                        <p class="text-sm text-gray-600">{{ $order->updated_at->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sidebar -->
                    <div class="space-y-8">
                        
                        <!-- Payment Progress Card -->
                        @if($order->total_price > 0)
                        @php
                            $paidAmount = $order->paid_amount ?? 0;
                            $totalPrice = $order->total_price ?? 1;
                            $progressPercentage = $totalPrice > 0 ? ($paidAmount / $totalPrice) * 100 : 0;
                            $progressPercentage = min($progressPercentage, 100);
                            
                            // Simulation for better demo
                            if ($paidAmount == 0 && $order->status === \App\Enums\OrderStatus::Processing) {
                                $paidAmount = $totalPrice * 0.3;
                                $progressPercentage = 30;
                            } elseif ($paidAmount == 0 && $order->status === \App\Enums\OrderStatus::Done) {
                                $paidAmount = $totalPrice;
                                $progressPercentage = 100;
                            }
                        @endphp
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-chart-pie text-blue-600 mr-3"></i>
                                Payment Overview
                            </h3>
                            
                            <!-- Circular Progress -->
                            <div class="relative w-32 h-32 mx-auto mb-6">
                                <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 36 36">
                                    <path class="text-gray-300" stroke="currentColor" stroke-width="3" fill="none" 
                                          d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                    <path class="
                                        @if($order->status === \App\Enums\OrderStatus::Processing) text-blue-600
                                        @elseif($order->status === \App\Enums\OrderStatus::Done) text-gray-800
                                        @else text-blue-500
                                        @endif transition-all duration-1000 ease-out" 
                                        stroke="currentColor" stroke-width="3" stroke-linecap="round" fill="none"
                                        stroke-dasharray="{{ $progressPercentage }}, 100"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-2xl font-bold 
                                        @if($order->status === \App\Enums\OrderStatus::Processing) text-blue-600
                                        @elseif($order->status === \App\Enums\OrderStatus::Done) text-gray-800
                                        @else text-blue-500
                                        @endif">{{ number_format($progressPercentage, 0) }}%</span>
                                </div>
                            </div>
                            
                            <!-- Payment Details -->
                            <div class="space-y-4">
                                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-600">Total Price</span>
                                    <span class="font-bold text-gray-900">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-600">Paid Amount</span>
                                    <span class="font-bold text-green-700">Rp {{ number_format($paidAmount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-600">Outstanding</span>
                                    <span class="font-bold text-gray-700">Rp {{ number_format($totalPrice - $paidAmount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            <!-- Payment Status Badge -->
                            <div class="mt-6 text-center">
                                @if($order->is_paid || $progressPercentage >= 100)
                                    <span class="bg-green-100 text-green-800 text-sm px-4 py-2 rounded-full font-semibold">
                                        <i class="fas fa-check-circle mr-2"></i>FULLY PAID
                                    </span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 text-sm px-4 py-2 rounded-full font-semibold">
                                        <i class="fas fa-clock mr-2"></i>PENDING PAYMENT
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        <!-- Action Buttons Card -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">Quick Actions</h3>
                            
                            <div class="space-y-3">
                                <a href="/admin/orders/{{ $order->id }}/edit"
                                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-center block">
                                    <i class="fas fa-edit mr-2"></i>Edit Project
                                </a>
                                
                                <button onclick="addPayment({{ $order->id }})"
                                    class="w-full bg-green-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                                    <i class="fas fa-plus-circle mr-2"></i>Add Payment
                                </button>
                                
                                <button onclick="generateInvoice({{ $order->id }})"
                                    class="w-full bg-gray-800 text-white py-3 px-4 rounded-lg font-semibold hover:bg-black transition-colors">
                                    <i class="fas fa-file-invoice mr-2"></i>Generate Invoice
                                </button>
                                
                                <button onclick="downloadContract({{ $order->id }})"
                                    class="w-full bg-purple-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-purple-700 transition-colors">
                                    <i class="fas fa-download mr-2"></i>Download Contract
                                </button>
                                
                                <a href="{{ route('project') }}"
                                    class="w-full bg-gray-200 text-gray-700 py-3 px-4 rounded-lg font-semibold hover:bg-gray-300 transition-colors text-center block">
                                    <i class="fas fa-arrow-left mr-2"></i>Back to Projects
                                </a>
                            </div>
                        </div>
                        
                        <!-- Contact Information Card -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">Contact Information</h3>
                            
                            @if($order->user)
                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Client</label>
                                    <p class="text-lg font-semibold text-gray-900">{{ $order->user->name }}</p>
                                    @if($order->user->email)
                                    <p class="text-sm text-gray-600">{{ $order->user->email }}</p>
                                    @endif
                                </div>
                                
                                @if($order->employee)
                                <div class="pt-4 border-t border-gray-200">
                                    <label class="text-sm font-medium text-gray-600">Wedding Planner</label>
                                    <p class="text-lg font-semibold text-gray-900">{{ $order->employee->name }}</p>
                                    @if($order->employee->email)
                                    <p class="text-sm text-gray-600">{{ $order->employee->email }}</p>
                                    @endif
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- JavaScript for Actions -->
    <script>        
        function addPayment(projectId) {
            // Open payment form
            alert('Add Payment for Project #' + projectId + ' - This would open payment form');
        }
        
        function generateInvoice(projectId) {
            // Generate and download invoice
            alert('Generate Invoice for Project #' + projectId + ' - This would generate PDF invoice');
        }
        
        function downloadContract(projectId) {
            // Download contract document
            alert('Download Contract for Project #' + projectId + ' - This would download contract PDF');
        }
    </script>
@endsection
