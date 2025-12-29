<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Draft Kontrak</title>
    <style>
        @page {
            /* Top margin adjusted to ensure content starts below the fixed header on all pages */
            margin: 110px 50px 30px 70px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1;
            color: #000;
            margin: 0;
        }

        /* Fixed Header */
        header {
            position: fixed;
            top: -90px;
            left: 0;
            right: 0;
            height: 100px;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .logo-img {
            max-height: 60px;
            width: auto;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0px;
            right: 0px;
            text-align: right;
            font-size: 10px;
            color: #000000;
        }

        .pagenum:before {
            content: counter(page);
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.1);
            /* Transparent gray */
            z-index: -1000;
            text-align: center;
            white-space: nowrap;
        }

        /* Typography */
        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            font-size: 14px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .subtitle {
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 20px;
        }

        .section-title {
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-size: 11px;
        }

        /* Content Tables */
        table.content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        table.content-table td {
            vertical-align: top;
            padding: 2px 0;
        }

        .label {
            width: 130px;
            font-weight: bold;
        }

        .separator {
            width: 10px;
            text-align: center;
        }

        .amount {
            text-align: right;
        }

        /* Lists */
        ol,
        ul {
            margin: 0;
            padding-left: 20px;
        }



        li {
            margin-bottom: 3px;
            text-align: justify;
        }

        li p {
            margin: 0;
            display: inline;
        }

        /* Utilities */
        .text-justify {
            text-align: justify;
        }

        .indent {
            margin-left: 20px;
        }

        .page-break {
            page-break-after: always;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 40px;
            width: 100%;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            border: 1px solid #000;
            padding: 10px;
        }

        .sign-space {
            height: 70px;
        }

        /* Invoice Style Header Text */
        .company-name {
            font-size: 14px;
            /* Matches standard invoice size */
            font-weight: bold;
        }

        .company-info {
            font-size: 11px;
        }
    </style>
</head>

<body>
    <div class="watermark">COMING SOON</div>
    <!-- HEADER (Fixed on every page) -->
    <header>
        <table class="header-table">
            <tr>
                <td style="width: 65%;">
                    <div class="company-name">PT. Makna Kreatif Indonesia</div>
                    <div class="company-info">
                        Alamat : Jln. Sintraman Jaya, No. 2148, Sekip Jaya, Palembang<br>
                        No. Tlp : +62 822-9796-2600<br>
                        Email : maknawedding@gmail.com
                    </div>
                </td>
                <td style="text-align: right;">
                    @php
                        $logoPath = public_path(config('invoice.logo', 'images/logo.png'));
                        $logoBase64 = '';
                        if (file_exists($logoPath)) {
                            $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
                            $logoData = file_get_contents($logoPath);
                            $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
                        }
                    @endphp
                    @if ($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo" class="logo-img">
                    @else
                        <b>MAKNA KREATIF</b>
                    @endif
                </td>
            </tr>
        </table>
    </header>

    <!-- FOOTER -->
    <div class="footer">
        <table style="width: 100%; border-collapse: collapse; border: none;">
            <tr>
                <td style="text-align: right; vertical-align: bottom; padding-right: 10px;">
                    www.paketpernikahan.co.id | Hal <span class="pagenum"></span>
                </td>
                <td style="width: 50px; vertical-align: bottom;">
                    <div style="border: 1px solid #000; width: 40px; height: 40px; margin-left: auto; margin-right: 0;">
                    </div>
                    <div style="text-align: center; font-size: 8px; margin-top: 2px;">Paraf</div>
                </td>
                <td style="width: 50px; vertical-align: bottom;">
                    <div style="border: 1px solid #000; width: 40px; height: 40px; margin-left: auto; margin-right: 0;">
                    </div>
                    <div style="text-align: center; font-size: 8px; margin-top: 2px;">Paraf</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- MAIN CONTENT -->

    <!-- Title -->
    <div class="title">KONTRAK KERJASAMA PERNIKAHAN</div>
    <div class="subtitle">Nomor : 0{{ $nomorSurat }}</div>

    <!-- Pihak Pertama -->
    <table class="content-table">
        <tr>
            <td style="width: 20px;">I.</td>
            <td class="label">Nama</td>
            <td class="separator">:</td>
            <td>Ramadhona Utama</td>
        </tr>
        <tr>
            <td></td>
            <td class="label">Jabatan</td>
            <td class="separator">:</td>
            <td>CEO Makna Wedding</td>
        </tr>
    </table>
    <div class="text-justify indent" style="margin-bottom: 10px;">
        Bertindak untuk dan atas nama Makna Wedding Organizer beralamat di Jalan Sintraman Jaya I No. 2148, 20 Ilir D II
        Kec. Kemuning, Kota Palembang, Sumatera Selatan 30137, selanjutnya disebut PIHAK PERTAMA.
    </div>

    <!-- Pihak Kedua -->
    <table class="content-table">
        <tr>
            <td style="width: 20px;">II.</td>
            <td class="label">Nama</td>
            <td class="separator">:</td>
            <td><b>{{ $prospect->name_cpp ?? '....................' }}</b> &
                <b>{{ $prospect->name_cpw ?? '....................' }}</b>
            </td>
        </tr>
        <tr>
            <td></td>
            <td class="label">No. Telp</td>
            <td class="separator">:</td>
            <td>+62{{ $prospect->phone ?? '-' }}</td>
        </tr>
        <tr>
            <td></td>
            <td class="label">Alamat</td>
            <td class="separator">:</td>
            <td>{{ $prospect->address ?? 'Palembang' }}</td>
        </tr>
    </table>
    <div class="text-justify indent" style="margin-bottom: 15px;">
        Bertindak untuk dan atas nama diri sendiri, selanjutnya disebut PIHAK KEDUA.
    </div>

    <div class="text-justify" style="margin-bottom: 15px;">
        Sehubungan dengan akan diadakannya Pernikahan <b>{{ $prospect->name_cpw ?? '...' }} &
            {{ $prospect->name_cpp ?? '...' }}</b> di <b>{{ $prospect->venue ?? '...' }}</b>, berikut adalah rincian
        dan
        ketentuan Paket Pernikahannya :
    </div>

    <!-- Event Details -->
    <div class="section-title">Dream Wedding Packages</div>
    <table class="content-table">
        <tr>
            <td class="label">Nama Acara</td>
            <td class="separator">:</td>
            <td>{{ $prospect->name_event ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Paket WO</td>
            <td class="separator">:</td>
            <td>{{ $record->product->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Lokasi Acara</td>
            <td class="separator">:</td>
            <td>{{ $prospect->venue ?? '-' }}</td>
        </tr>
    </table>

    <div class="section-title">Akad Nikah</div>
    <table class="content-table">
        <tr>
            <td class="label">Hari / Tanggal</td>
            <td class="separator">:</td>
            <td>{{ $prospect->date_akad ? \Carbon\Carbon::parse($prospect->date_akad)->translatedFormat('l, d F Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td class="label">Waktu</td>
            <td class="separator">:</td>
            <td>07:00 / 07:30 s.d Selesai</td>
        </tr>
        <tr>
            <td class="label">Jumlah Undangan</td>
            <td class="separator">:</td>
            <td>50 Undangan atau 100 Pax</td>
        </tr>
    </table>

    <div class="section-title">Resepsi</div>
    <table class="content-table">
        <tr>
            <td class="label">Hari / Tanggal</td>
            <td class="separator">:</td>
            <td>{{ $prospect->date_resepsi ? \Carbon\Carbon::parse($prospect->date_resepsi)->translatedFormat('l, d F Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td class="label">Waktu</td>
            <td class="separator">:</td>
            <td>10:00 s.d Selesai</td>
        </tr>
        <tr>
            <td class="label">Jumlah Undangan</td>
            <td class="separator">:</td>
            <td>{{ $record->product->pax ?? 500 }} Undangan atau {{ ($record->product->pax ?? 500) * 2 }} Pax</td>
        </tr>
    </table>

    <!-- Pricing -->
    <div class="section-title">PERINCIAN BIAYA</div>
    <table class="content-table" style="width: 100%;">
        <tr>
            <td style="padding: 5px 0;"><b>DREAM WEDDING PACKAGE</b></td>
            <td style="width: 1%; white-space: nowrap; padding: 5px 0;"><b>: Rp. </b></td>
            <td style="width: 60%; padding: 5px 0; text-align: left;">
                <b>&nbsp;{{ number_format($record->total_price, 0, ',', '.') }},-</b>
            </td>
        </tr>
        @if ($record->penambahan > 0)
            <tr>
                <td>PENAMBAHAN</td>
                <td style="width: 1%; white-space: nowrap;">: Rp. </td>
                <td style="text-align: left;">&nbsp;{{ number_format($record->penambahan, 0, ',', '.') }},-</td>
            </tr>
        @endif
        @if ($record->pengurangan > 0)
            <tr>
                <td>PENGURANGAN</td>
                <td style="width: 1%; white-space: nowrap;">: Rp. </td>
                <td style="text-align: left;">&nbsp;({{ number_format($record->pengurangan, 0, ',', '.') }},-)</td>
            </tr>
        @endif
        @if ($record->promo > 0)
            <tr>
                <td>PROMO</td>
                <td style="width: 1%; white-space: nowrap;">: Rp. </td>
                <td style="text-align: left;">&nbsp;({{ number_format($record->promo, 0, ',', '.') }},-)</td>
            </tr>
        @endif
        <tr>
            <td style="padding: 5px 0;"><b>TOTAL PEMBAYARAN</b></td>
            <td style="padding: 5px 0; width: 1%; white-space: nowrap;"><b>: Rp.</b></td>
            <td style="padding: 5px 0; text-align: left;">
                <b>&nbsp;{{ number_format($record->grand_total, 0, ',', '.') }},-</b>
            </td>
        </tr>
    </table>

    <!-- Facilities -->
    <div class="section-title">DENGAN RINCIAN FASILITAS SEBAGAI BERIKUT :</div>
    @php
        $groupedItems = $items->groupBy(function ($item) {
            return $item->vendor->category->name ?? 'LAIN-LAIN';
        });
    @endphp

    <ol>
        @foreach ($groupedItems as $categoryName => $categoryItems)
            <li style="font-weight: bold; margin-top: 10px; font-size: 11px;">
                {{ strtoupper($categoryName) }}
                <ol type="a" style="font-weight: normal; margin-top: 5px;">
                    @foreach ($categoryItems as $item)
                        @php
                            $description = $item->description ?? ($item->vendor->name ?? '');
                            $plainContent = trim(strip_tags($description));
                            // Cek apakah konten dimulai dengan angka (misal: "1.", "1)", "1 ")
                            $hideListStyle = preg_match('/^\d+[.)]/', $plainContent);
                        @endphp
                        <li @if ($hideListStyle) style="list-style-type: none;" @endif>
                            {!! $description !!}
                            @if ($item->quantity > 1)
                                <b>({{ $item->quantity }}x)</b>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </li>
        @endforeach
    </ol>

    <!-- Terms & Confirmation -->
    <div class="section-title">KETENTUAN TAMBAHAN</div>
    <div class="section-title">KONFIRMASI</div>
    <ol>
        <li>PIHAK PERTAMA harus menerima konfirmasi dari PIHAK KEDUA tentang acara/event tersebut di atas
            selambat-lambatnya 3 (tiga) hari kerja dari Kontrak Kerjasama Paket Pernikahan ini dibuat.</li>
        <li>Pembatalan secara mendadak setelah Kontrak Kerjasama Paket Pernikahan ini ditandatangani akan dikenakan
            biaya sebesar 50% dari total biaya yang tercantum di Kontrak Kerjasama Paket Pernikahan.</li>
        <li>Kontrak Kerjasama Paket Pernikahan ini juga berlaku sebagai Jaminan atas Pembayaran dari PIHAK KEDUA.
        </li>
        <li>PIHAK PERTAMA akan tetap mengikuti kebijakan pihak Gedung yang menjadi lokasi pernikahan yang dipilih
            oleh
            PIHAK KEDUA.</li>
    </ol>

    <div class="section-title">PEMBAYARAN</div>
    <ol>
        <li>Pembayaran DP (Down Payment) minimal sebesar Rp. 5.000.000,- (Lima Juta Rupiah) atau Booking Date.</li>
        <li>Pembayaran Termin I sebesar 50% (Lima Puluh Persen) dari sisa pembayaran, dibayarkan 2 (dua) bulan
            sebelum
            hari H.</li>
        <li>Pelunasan pembayaran paling lambat H-14 (Empat Belas Hari) sebelum acara dilaksanakan.</li>
        <li>Pembayaran dapat dilakukan melalui transfer ke rekening:
            <div style="text-align: center; margin-top: 5px;">
                <b>Bank Mandiri</b><br>
                No. Rek: <b>113-00-1184732-5</b><br>
                A.n: <b>PT. Makna Kreatif Indonesia</b>
            </div>
        </li>
        <li>Bukti transfer dapat di email ke maknawedding@gmail.com atau datang langsung ke kantor Makna Wedding
            dengan
            menunjukkan bukti ke bagian administrasi.</li>
        <li>Pembayaran secara tunai dilakukan langsung ke bagian administrasi di kantor Makna Wedding Organizer dan
            PIHAK KEDUA akan menerima bukti pembayaran atau pelunasan yang telah ditandatangani oleh bagian keuangan
            atau bisa langsung menghubungi saudari <b>{{ $financeUser->name ?? 'Finance' }} di nomor
                {{ $financeUser->phone_number ?? '-' }}</b>.</li>
        <li>Tidak dibenarkan melakukan pembayaran di luar dengan cara menitipkan kepada pihak lain selain yang
            ditunjuk
            oleh PIHAK PERTAMA.</li>
    </ol>

    <div class="section-title" style="margin-top: 10px;">VENDOR</div>
    <ol>
        <li>Vendor pernikahan yang telah dipilih oleh PIHAK KEDUA, wajib bertanggung jawab terhadap fasilitas yang
            telah
            diberikan sesuai dengan paket yang telah dipilih. PIHAK PERTAMA bersedia membantu sebagai mediator dalam
            berdiskusi dan koordinasi jika terjadi kendala dengan vendor.</li>
        <li>PIHAK PERTAMA akan memberikan daftar rekomendasi vendor yang telah sesuai dengan kriteria sehingga dapat
            dijadikan pilihan oleh PIHAK KEDUA dalam menentukan vendor pernikahan.</li>
        <li>PIHAK KEDUA dapat melakukan perubahan vendor diluar rekomendasi yang telah disampaikan dengan
            menyesuaikan
            perhitungan dari paket sebelumnya.</li>
        <li>Apabila diperlukan, para vendor akan diminta untuk membuat kontrak kerjasama yang isinya mengenai
            pertanggungjawaban para vendor terhadap keberhasilan acara pernikahan sesuai dengan ketentuan yang telah
            disepakati sebelumnya antara vendor dan PIHAK KEDUA.</li>
        <li>Jika vendor yang telah dipilih PIHAK KEDUA tidak mampu mengikuti kesepakatan dari PIHAK KEDUA mengenai
            pertanggung jawaban, maka PIHAK PERTAMA akan memberikan rekomendasi vendor lain yang mampu mengikuti
            kesepakatan PIHAK PERTAMA dan PIHAK KEDUA</li>
    </ol>

    <div class="section-title" style="margin-top: 10px;">PEMBATALAN :</div>
    <ol>
        <li>Apabila terjadi pembatalan sepihak dari konsumen (keluarga/pengantin) PIHAK KEDUA, maka uang yang telah
            disetorkan dapat dikembalikan dengan syarat sebagai berikut :</li>
        <li>Jika pembatalan 3 (tiga) bulan sebelum acara berlangsung maka akan dikenakan biaya 50% dari total biaya
            yang
            telah disepakati.</li>
        <li>Jika pembatalan 1 (satu) bulan sebelum acara berlangsung, maka akan dikenakan biaya 100% dari total
            biaya
            yang telah disepakati.</li>
        <li>Jika pembatalan dilakukan setelah ada pembayaran ke beberapa vendor, maka uang yang telah disetor ke
            vendor
            akan mengikuti kebijakan dari masing - masing vendor dalam hal pengembalian uang.</li>
        <li>Uang muka sebagai tanda jadi atau down payment (DP) yang telah dibayarkan tidak dapat dikembalikan.</li>
    </ol>

    <div class="section-title" style="margin-top: 10px;">FORCE MAJEURE</div>
    <ol>
        <li>Force Majeure yang dimaksud adalah suatu keadaan memaksa diluar batas kemampuan kedua belah pihak yang
            dapat
            menggangu bahkan menggagalkan terlaksananya event, seperti bencana alam, pandemi penyakit berbahaya,
            peperangan, pemogokan, sabotase, pemberontakan masyarakat, blokade, kebijaksanaan pemerintah dan
            khususnya
            yang disebabkan diluar batas kemampuan manusia.</li>
        <li>Terhadap pembatalan akibat dari Force Majeure, PIHAK PERTAMA dan PIHAK KEDUA sepakat untuk menanggung
            kerugiannya masing – masing.</li>
    </ol>

    <p style="text-align: justify; margin-top: 10px;">
        Demikianlah Kontrak Kerjasama Paket Pernikahan ini dibuat dalam 2 (dua) rangkap dan ditandatangani oleh
        kedua
        belah pihak.
    </p>

    <!-- Signatures -->
    <div style="text-align: right; margin-bottom: 5px; margin-top: 20px;">
        Palembang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
    </div>
    <div class="signature-section" style="margin-top: 0;">
        <table class="signature-table">
            <tr>
                <td style="width: 35%;">
                    Menyetujui,<br>
                </td>
                <td colspan="2" style="width: 65%;">
                    Mengetahui,<br>
                    Makna Wedding Organizer
                </td>
            </tr>
            <tr>
                <td style="vertical-align: bottom; height: 100px;">
                    <div style="text-decoration: underline; font-weight: bold;">
                        {{ $record->name_ttd ?? '....................' }}
                    </div>
                    <b>{{ $record->title_ttd ?? 'Calon Pengantin' }}</b>
                </td>
                <td style="vertical-align: bottom; height: 100px;">
                    <div style="text-decoration: underline; font-weight: bold;">
                        Rama Dhona Utama
                    </div>
                    <b>C E O</b>
                </td>
                <td style="vertical-align: bottom; height: 100px;">
                    <div style="text-decoration: underline; font-weight: bold;">
                        Syafira Putri Ramadhania
                    </div>
                    <b>Account Manager</b>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
