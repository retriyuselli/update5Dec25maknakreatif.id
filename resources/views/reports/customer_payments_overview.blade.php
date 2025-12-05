{{-- resources/views/reports/customer_payments_overview.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Noto Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body class="font-sans bg-gray-100 text-gray-800 leading-relaxed">
    <div class="bg-gray-800 text-white py-4 px-6 sm:px-8 mb-8">
        <h1 class="m-0 text-xl sm:text-2xl font-semibold">Makna Wedding Organizer - Laporan Pembayaran</h1>
    </div>
    <div class="max-w-6xl mx-auto p-4 sm:p-5 bg-white rounded-lg shadow-md">
        <h1 class="text-blue-600 border-b-2 border-gray-200 pb-2.5 mt-0 text-2xl sm:text-3xl font-bold mb-6">{{ $pageTitle }}</h1>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('reports.customer-payments', ['status' => $status]) }}" class="bg-gray-100 p-4 rounded-md mb-6 flex flex-wrap gap-4 items-end">
            <div class="flex flex-col flex-grow sm:flex-grow-0">
                <label for="date_from" class="mb-1 text-xs sm:text-sm font-medium text-gray-700">Tanggal Bayar Dari:</label>
                <input type="date" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="p-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex flex-col flex-grow sm:flex-grow-0">
                <label for="date_to" class="mb-1 text-xs sm:text-sm font-medium text-gray-700">Tanggal Bayar Sampai:</label>
                <input type="date" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="p-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex flex-col flex-grow sm:flex-grow-0">
                <label for="project_name" class="mb-1 text-xs sm:text-sm font-medium text-gray-700">Nama Project:</label>
                <input type="text" id="project_name" name="project_name" value="{{ $filters['project_name'] ?? '' }}" placeholder="Cari nama project..." class="p-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex flex-col flex-grow sm:flex-grow-0">
                <label for="payment_method_id" class="mb-1 text-xs sm:text-sm font-medium text-gray-700">Metode Pembayaran:</label>
                <select id="payment_method_id" name="payment_method_id" class="p-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Metode</option>
                    @foreach($paymentMethods as $pm)
                        <option value="{{ $pm->id }}" {{ (isset($filters['payment_method_id']) && $filters['payment_method_id'] == $pm->id) ? 'selected' : '' }}>
                            {{ $pm->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="py-2 px-4 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Filter</button>
            <a href="{{ route('reports.customer-payments', ['status' => $status]) }}" class="py-2 px-4 bg-gray-600 text-white rounded-md text-sm hover:bg-gray-700 no-underline focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">Reset</a>
        </form>

        @if($payments->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full border-collapse mt-6 text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 p-3 text-left align-middle text-gray-600 font-semibold uppercase tracking-wider">No.</th>
                            <th class="border border-gray-300 p-3 text-left align-middle text-gray-600 font-semibold uppercase tracking-wider">Nama Project</th>
                            <th class="border border-gray-300 p-3 text-left align-middle text-gray-600 font-semibold uppercase tracking-wider">Tanggal Bayar</th>
                            <th class="border border-gray-300 p-3 align-middle text-gray-600 font-semibold uppercase tracking-wider text-right">Nominal</th>
                            <th class="border border-gray-300 p-3 text-left align-middle text-gray-600 font-semibold uppercase tracking-wider">Metode Pembayaran</th>
                            <th class="border border-gray-300 p-3 text-left align-middle text-gray-600 font-semibold uppercase tracking-wider">Keterangan</th>
                            <th class="border border-gray-300 p-3 text-left align-middle text-gray-600 font-semibold uppercase tracking-wider">Bukti Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $index => $payment)
                            <tr class="even:bg-gray-50 hover:bg-gray-200 transition-colors duration-150">
                                <td class="border border-gray-300 p-3 align-middle">{{ $loop->iteration }}</td>
                                <td class="border border-gray-300 p-3 align-middle">
                                    {{ $payment->order?->name ?? 'N/A' }}
                                    {{-- <small class="block text-gray-500 text-xs">({{ $payment->order?->prospect?->name_event ?? 'N/A' }})</small> --}}
                                </td>
                                <td class="border border-gray-300 p-3 align-middle">{{ $payment->tgl_bayar ? \Carbon\Carbon::parse($payment->tgl_bayar)->isoFormat('D MMMM YYYY') : 'N/A' }}</td>
                                <td class="border border-gray-300 p-3 align-middle text-right">Rp {{ number_format($payment->nominal, 0, ',', '.') }}</td>
                                <td class="border border-gray-300 p-3 align-middle">
                                    @if($payment->paymentMethod)
                                        <span class="inline-block px-2 py-1 text-xs font-bold leading-none text-center whitespace-nowrap align-baseline rounded {{ $payment->paymentMethod->is_cash ? 'bg-green-500 text-white' : 'bg-blue-500 text-white' }}">
                                            {{ $payment->paymentMethod->name }}
                                        </span>
                                        @if(!$payment->paymentMethod->is_cash && $payment->paymentMethod->bank_name)
                                            <small class="block text-gray-500 text-xs mt-1">({{ $payment->paymentMethod->bank_name }} - {{ $payment->paymentMethod->no_rekening }})</small>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="border border-gray-300 p-3 align-middle">{{ $payment->keterangan ?? '-' }}</td>
                                <td class="border border-gray-300 p-3 align-middle">
                                    @if($payment->image)
                                        <img src="{{ Storage::url($payment->image) }}" alt="Bukti Bayar" class="max-w-[60px] max-h-[60px] sm:max-w-[80px] sm:max-h-[80px] cursor-pointer rounded-md transition-transform duration-200 ease-in-out hover:scale-110 object-cover" onclick="openModal('{{ Storage::url($payment->image) }}')">
                                    @else
                                        <span class="inline-block px-2 py-1 text-xs font-bold leading-none text-center whitespace-nowrap align-baseline rounded bg-yellow-400 text-gray-800">Tidak Ada</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-md text-base font-medium">
                <strong>Total Pembayaran (Status: {{ Str::ucfirst(str_replace('_', ' ', $status)) }}): Rp {{ number_format($totalPaymentsValue, 0, ',', '.') }}</strong>
            </div>
        @else
            <p class="text-center p-8 italic text-gray-600 text-base">Tidak ada data pembayaran ditemukan untuk status '{{ $status }}'.</p>
        @endif
    </div>

    <!-- Modal untuk menampilkan gambar bukti bayar -->
    <div id="imageModal" class="fixed inset-0 z-50 bg-black bg-opacity-85 items-center justify-center hidden" onclick="closeModal()">
        <span class="absolute top-4 sm:top-6 right-6 sm:right-11 text-white text-4xl sm:text-5xl font-bold cursor-pointer transition-colors duration-200 hover:text-gray-300" onclick="closeModal(event)">&times;</span>
        <img class="block max-w-[90%] max-h-[90%] sm:max-w-[85%] sm:max-h-[85%] rounded-md" id="modalImage" onclick="event.stopPropagation()">
    </div>

    <script>
        function openModal(imageUrl) {
            event.stopPropagation(); // Mencegah modal tertutup oleh event click pada body/container
            const modal = document.getElementById('imageModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.getElementById('modalImage').src = imageUrl;
        }

        function closeModal(e) {
            if (e) {
                e.stopPropagation(); // Mencegah event bubbling jika diklik pada tombol close
            }
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Menutup modal jika user menekan tombol Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                const modal = document.getElementById('imageModal');
                if (!modal.classList.contains('hidden')) {
                    closeModal();
                }
            }
        });
    </script>
</body>
</html>
