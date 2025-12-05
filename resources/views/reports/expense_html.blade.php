<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['Poppins', 'sans-serif'],
            },
            flexGrow: {
              '2': '2',
            }
          }
        }
      }
    </script>
    <title>Laporan Pengeluaran</title>
    <style>
        /* Custom toast visibility for JS */
        .toast-visible {
            opacity: 1;
            pointer-events: auto;
        }
    </style>
</head>
<body class="font-sans bg-gray-100 text-gray-800 leading-relaxed">
    <div class="max-w-6xl mx-auto my-10 bg-white p-7 md:p-10 rounded-xl shadow-lg">
        <div class="text-center mb-8 pb-6 border-b border-gray-300" role="banner">
            @php
                $logoPath = public_path('images/logomki.png');
                $logoSrc = '';
                if (file_exists($logoPath)) {
                    $logoMime = mime_content_type($logoPath);
                    if ($logoMime) {
                        $logoSrc = 'data:' . $logoMime . ';base64,' . base64_encode(file_get_contents($logoPath));
                    }
                }
            @endphp
            @if($logoSrc)
                <img
                    src="{{ $logoSrc }}"
                    alt="Nama Perusahaan Anda"
                    class="max-h-10 mx-auto mb-4 block"
                />
            @endif

            <h1 class="text-2xl font-bold uppercase tracking-wider text-gray-700 mt-0 mb-2">
                Laporan Pengeluaran Klien Makna Wedding
                @isset($selectedMonth, $selectedYear)
                    @if($selectedMonth && $selectedYear)
                        <br />
                        <small class="text-[0.7em] font-normal text-gray-500 normal-case">(Periode: {{ $months[$selectedMonth] }} {{ $selectedYear }})</small>
                    @elseif($selectedYear && (!$selectedMonth || $selectedMonth == ''))
                        <br />
                        <small class="text-[0.7em] font-normal text-gray-500 normal-case">(Periode: Tahun {{ $selectedYear }})</small>
                    @else
                        <br />
                        <small class="text-[0.65em] font-normal text-gray-600 normal-case">(Semua Periode)</small>
                    @endif
                @else
                    <br />
                    <small class="text-[0.65em] font-normal text-gray-600 normal-case">(Semua Periode)</small>
                @endisset
            </h1>
            <p class="text-xs text-gray-500 mt-1">
                Jl. Sintraman Jaya I No. 2148, 20 Ilir D II, <br />
                Kecamatan Kemuning, Kota Palembang, Sumatera Selatan 30137
            </p>
            <p class="text-xs text-gray-500 mt-0">
                PT. Makna Kreatif Indonesia | maknawedding@gmail.com | +62 822-9796-2600
            </p>
        </div>

        <form
            action="{{ route('expense.html-report') }}"
            method="GET"
            class="mb-6 p-5 bg-blue-50 rounded-md flex flex-wrap gap-4 items-center"
            role="search"
            aria-label="Filter laporan pengeluaran"
        >
            <div class="grow min-w-[150px]">
                <label for="month" class="font-medium text-gray-700 text-sm block mb-1">Bulan:</label>
                <select name="month" id="month" class="py-2 px-3 border border-gray-300 rounded-md text-base w-full focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 focus:outline-none transition-all duration-200 ease-in-out">
                    <option value="">-- Semua Bulan --</option>
                    @foreach($months as $monthNum => $monthName)
                        <option
                            value="{{ $monthNum }}"
                            {{ (isset($selectedMonth) && $selectedMonth == $monthNum) ? 'selected' : '' }}
                        >
                            {{ $monthName }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="grow min-w-[150px]">
                <label for="year" class="font-medium text-gray-700 text-sm block mb-1">Tahun:</label>
                <select name="year" id="year" class="py-2 px-3 border border-gray-300 rounded-md text-base w-full focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 focus:outline-none transition-all duration-200 ease-in-out">
                    <option value="">-- Semua Tahun --</option>
                    @foreach($years as $year)
                        <option
                            value="{{ $year }}"
                            {{ (isset($selectedYear) && $selectedYear == $year) ? 'selected' : '' }}
                        >
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="grow min-w-[150px]">
                <label for="order_status" class="font-medium text-gray-700 text-sm block mb-1">Status Order:</label>
                <select name="order_status" id="order_status" class="py-2 px-3 border border-gray-300 rounded-md text-base w-full focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 focus:outline-none transition-all duration-200 ease-in-out">
                    <option value="">-- Semua Status Order --</option>
                    {{-- Pastikan $orderStatuses dan $selectedOrderStatus di-pass dari controller --}}
                    @if(isset($orderStatuses) && !empty($orderStatuses))
                        @foreach($orderStatuses as $statusEnum)
                            <option value="{{ $statusEnum->value }}"
                                {{ (isset($selectedOrderStatus) && $selectedOrderStatus == $statusEnum->value) ? 'selected' : '' }}>
                                {{ $statusEnum->getLabel() }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="grow-[2] min-w-[150px]">
                <label for="search_name" class="font-medium text-gray-700 text-sm block mb-1">Nama Pengeluaran:</label>
                <input
                    type="text"
                    name="search_name"
                    id="search_name"
                    value="{{ $searchName ?? '' }}"
                    placeholder="Cari nama..."
                    class="py-2 px-3 border border-gray-300 rounded-md text-base w-full focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 focus:outline-none transition-all duration-200 ease-in-out"
                />
            </div>
            <div class="grow-[2] min-w-[150px]">
                <label for="search_note" class="font-medium text-gray-700 text-sm block mb-1">Catatan:</label>
                <input
                    type="text"
                    name="search_note"
                    id="search_note"
                    value="{{ $searchNote ?? '' }}"
                    placeholder="Cari catatan..."
                    class="py-2 px-3 border border-gray-300 rounded-md text-base w-full focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 focus:outline-none transition-all duration-200 ease-in-out"
                />
            </div>
            <div class="grow min-w-[150px] self-end">
                <button type="submit" class="w-full py-2.5 px-4 rounded-md text-base cursor-pointer transition-colors duration-200 ease-in-out text-white bg-blue-600 hover:bg-blue-700 focus:bg-blue-700">Filter</button>
            </div>
            <div class="grow min-w-[150px] self-end">
                <button
                    type="button"
                    class="w-full py-2.5 px-4 rounded-md text-base cursor-pointer transition-colors duration-200 ease-in-out text-white bg-green-500 hover:bg-green-600 focus:bg-green-600"
                    id="btnDownloadPDF"
                    aria-label="Download laporan pengeluaran dalam format PDF"
                >
                    Download PDF
                </button>
            </div>
            <div class="grow min-w-[150px] self-end">
                <button
                    type="button"
                    class="w-full py-2.5 px-4 rounded-md text-base cursor-pointer transition-colors duration-200 ease-in-out text-black bg-yellow-500 hover:bg-yellow-600 focus:bg-yellow-600"
                    onclick="window.location.href='{{ route('expense.html-report') }}'"
                    aria-label="Reset semua filter"
                >
                    Reset
                </button>
            </div>
            <div class="grow min-w-[150px] self-end">
                <button
                    type="button"
                    class="w-full py-2.5 px-4 rounded-md text-base cursor-pointer transition-colors duration-200 ease-in-out text-white bg-gray-500 hover:bg-gray-600 focus:bg-gray-600"
                    onclick="window.history.back()"
                    aria-label="Kembali ke halaman sebelumnya"
                >
                    Kembali
                </button>
            </div>
        </form>

        <table role="table" aria-label="Tabel laporan pengeluaran" class="w-full border-collapse mt-5 text-sm">
            <thead>
                <tr>
                    <th scope="col" class="border border-gray-200 py-3 px-4 text-left align-middle bg-blue-600 text-white uppercase tracking-wide font-semibold">ID</th>
                    <th scope="col" class="border border-gray-200 py-3 px-4 text-left align-middle bg-blue-600 text-white uppercase tracking-wide font-semibold">Nama Pengeluaran</th>
                    <th scope="col" class="border border-gray-200 py-3 px-4 align-middle bg-blue-600 text-white uppercase tracking-wide font-semibold text-right">Jumlah</th>
                    <th scope="col" class="border border-gray-200 py-3 px-4 text-left align-middle bg-blue-600 text-white uppercase tracking-wide font-semibold">Tanggal</th>
                    <th scope="col" class="border border-gray-200 py-3 px-4 text-left align-middle bg-blue-600 text-white uppercase tracking-wide font-semibold">No. ND</th>
                    <th scope="col" class="border border-gray-200 py-3 px-4 text-left align-middle bg-blue-600 text-white uppercase tracking-wide font-semibold">Vendor</th>
                    <th scope="col" class="border border-gray-200 py-3 px-4 text-left align-middle bg-blue-600 text-white uppercase tracking-wide font-semibold">Catatan</th>
                    <th scope="col" class="border border-gray-200 py-3 px-4 text-left align-middle bg-blue-600 text-white uppercase tracking-wide font-semibold">Bukti</th>
                </tr>
            </thead>
            <tbody>
                @php $totalPengeluaran = 0; @endphp
                @forelse($expenses as $expense)
                    <tr>
                        <td class="border border-gray-200 py-3 px-4 text-left align-middle">{{ $expense->id }}</td>
                        <td class="border border-gray-200 py-3 px-4 text-left align-middle copyable-cell"
                            tabindex="0"
                            role="cell"
                            aria-label="Nama pengeluaran: {{ $expense->order->name ?? ($expense->name ?? 'N/A') }}"
                            title="Tekan Enter untuk menyalin nama pengeluaran"
                        >
                            {{ $expense->order->name ?? ($expense->name ?? 'N/A') }}
                        </td>
                        <td class="border border-gray-200 py-3 px-4 align-middle text-right">
                            {{ number_format($expense->amount ?? 0, 0, ",", ".") }}
                        </td>
                        <td class="border border-gray-200 py-3 px-4 text-left align-middle">
                            {{ $expense->date_expense
                                ? \Carbon\Carbon::parse($expense->date_expense)
                                    ->locale("id")
                                    ->isoFormat("D MMMM YYYY")
                                : "N/A" }}
                        </td>
                        <td class="border border-gray-200 py-3 px-4 text-left align-middle">{{ $expense->no_nd ?? "-" }}</td>
                        <td class="border border-gray-200 py-3 px-4 text-left align-middle copyable-cell"
                            tabindex="0"
                            role="cell"
                            aria-label="Vendor: {{ $expense->vendor->name ?? 'N/A' }}"
                            title="Tekan Enter untuk menyalin vendor"
                        >
                            {{ $expense->vendor->name ?? "N/A" }}
                        </td>
                        <td class="border border-gray-200 py-3 px-4 text-left align-middle">{{ $expense->note ?? "-" }}</td>
                        <td class="border border-gray-200 py-3 px-4 text-left align-middle">
                            @if($expense->image)
                                <a href="{{ Storage::url($expense->image) }}" target="_blank" rel="noopener noreferrer">
                                    <img
                                        src="{{ Storage::url($expense->image) }}"
                                        alt="Bukti pengeluaran"
                                        class="max-w-[100px] max-h-[100px] cursor-pointer"
                                        loading="lazy"
                                    />
                                </a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @php $totalPengeluaran += $expense->amount ?? 0; @endphp
                @empty
                    <tr>
                        <td colspan="8" class="border border-gray-200 py-3 px-4 text-left align-middle text-center text-gray-500 p-5 italic">
                            Tidak ada data pengeluaran yang ditemukan.
                        </td>
                    </tr>
                @endforelse
                @if($expenses->isNotEmpty())
                    <tr class="font-bold bg-blue-100">
                        <td colspan="2" class="border border-gray-200 py-3 px-4 align-middle text-right">
                            <strong>Total Keseluruhan:</strong>
                        </td>
                        <td class="border border-gray-200 py-3 px-4 align-middle text-right">
                            <strong>{{ number_format($totalPengeluaran, 0, ",", ".") }}</strong>
                        </td>
                        <td colspan="5"></td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="text-center mt-8 text-sm text-gray-600 py-4" role="contentinfo" aria-live="polite" aria-atomic="true">
            Laporan ini dihasilkan pada:
            {{ \Carbon\Carbon::now()->locale("id")->isoFormat("D MMMM YYYY, HH:mm:ss") }}
        </div>
    </div>

    <div id="toast" class="fixed bottom-5 right-5 bg-gray-800 text-white py-2 px-5 rounded-md opacity-0 pointer-events-none transition-opacity duration-300 ease-in-out z-50" role="alert" aria-live="assertive" aria-atomic="true"></div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const toast = document.getElementById("toast");

            function showToast(message) {
                toast.textContent = message;
                toast.classList.add("toast-visible"); // Use custom class for visibility
                setTimeout(() => {
                    toast.classList.remove("toast-visible");
                }, 2000);
            }

            function copyText(text) {
                navigator.clipboard
                    .writeText(text)
                    .then(() => {
                        showToast(`Teks "${text}" telah disalin ke clipboard.`);
                    })
                    .catch((err) => {
                        console.error("Tidak dapat menyalin teks: ", err);
                        showToast("Gagal menyalin teks.");
                    });
            }

            // Event delegation for copyable cells
            document.querySelectorAll(".copyable-cell").forEach((cell) => {
                cell.addEventListener("click", () => {
                    copyText(cell.textContent.trim());
                });
                cell.addEventListener("keydown", (e) => {
                    if (e.key === "Enter" || e.key === " ") {
                        e.preventDefault();
                        copyText(cell.textContent.trim());
                    }
                });
            });

            // Download PDF button handler
            const btnDownloadPDF = document.getElementById("btnDownloadPDF");
            btnDownloadPDF.addEventListener("click", () => {
                const currentParams = new URLSearchParams(window.location.search);
                const pdfUrl = `{{ route("expense.pdf-report") }}?${currentParams.toString()}&is_pdf=1`;
                window.open(pdfUrl, "_blank");
            });
        });
    </script>
</body>
</html>
