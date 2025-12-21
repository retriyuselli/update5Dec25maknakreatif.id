<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $record->title }}</title>
    <style>
        @page {
            margin: 20px 50px 20px 60px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            /* Support for UTF-8 */
            font-size: 12px;
            line-height: 1.2;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header p {
            margin: 0;
            font-size: 12px;
        }

        .meta {
            margin-bottom: 5px;
        }

        .meta table {
            width: 100%;
        }

        .meta td {
            padding: 2px;
        }

        .content {
            text-align: justify;
            margin-bottom: 40px;
            margin-top: 40px;
        }

        .signature {
            float: right;
            width: 200px;
            text-align: center;
        }

        .signature .name {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            text-align: center;
            font-size: 9px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 5px;
            font-style: italic;
            background-color: #fff;
        }
    </style>
</head>

<body>
    <div class="footer">
        Dokumen ini diterbitkan secara otomatis oleh sistem komputer dan sah tanpa tanda tangan basah.
    </div>
    <div class="header">
        <table style="width: 100%; margin-bottom: 1px; padding-bottom: 3px;">
            <tr>
                <td style="line-height: 1; text-align: left;">
                    <div style="font-size: 14px; font-weight: bold; text-transform: uppercase;">PT. Makna Kreatif
                        Indonesia</div>
                    <div style="font-size: 12px;">
                        Alamat : Jln. Sintraman Jaya, No. 2148, Sekip Jaya, Palembang<br>
                        No. Tlp : +62 822-9796-2600<br>
                        Email : maknawedding@gmail.com
                    </div>
                </td>
                <td style="width: 40%; text-align: right; vertical-align: middle;">
                    @php
                        $logoPath = public_path(config('invoice.logo', 'images/logo.png'));
                        if (file_exists($logoPath)) {
                            $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
                            $logoData = file_get_contents($logoPath);
                            $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
                        } else {
                            $logoBase64 = '';
                        }
                    @endphp
                    @if ($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Company Logo" style="max-height: 50px; width: auto;">
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="meta">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td width="15%"><strong>Nomor</strong></td>
                <td width="2%">:</td>
                <td width="50%">{{ $record->document_number }}</td>
                <td width="33%" align="right">Palembang, {{ $record->created_at->format('d F Y') }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;"><strong>Kepada</strong></td>
                <td style="vertical-align: top;">:</td>
                <td>{{ $record->recipientsList->count() > 0 ? $record->recipientsList->pluck('name')->join(', ') : '-' }}
                </td>
                <td></td>
            </tr>
            <tr>
                <td><strong>Kategori</strong></td>
                <td>:</td>
                <td>{{ $record->category->name ?? '-' }}</td>
                <td></td>
            </tr>
            <tr>
                <td><strong>Sifat</strong></td>
                <td>:</td>
                <td>{{ ucfirst($record->confidentiality) }}</td>
                <td></td>
            </tr>
            @if ($record->date_effective)
                <tr>
                    <td><strong>Efektif</strong></td>
                    <td>:</td>
                    <td>{{ $record->date_effective->format('d F Y') }}</td>
                    <td></td>
                </tr>
            @endif
            @if ($record->date_expired)
                <tr>
                    <td><strong>Berlaku s.d.</strong></td>
                    <td>:</td>
                    <td>{{ $record->date_expired->format('d F Y') }}</td>
                    <td></td>
                </tr>
            @endif
            <tr>
                <td style="padding-top: 10px;"><strong>Perihal</strong></td>
                <td style="padding-top: 10px;">:</td>
                <td style="padding-top: 10px;" colspan="2"><strong>{{ $record->title }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="content">
        @if (!empty($record->content) && $record->content !== '<p></p>')
            {!! $record->content !!}
        @else
            <p><em>Tidak ada konten detail.</em></p>
        @endif
    </div>

    {{-- @if ($record->attachments && $record->attachments->count() > 0)
        <div style="margin-top: 30px; border-top: 1px dashed #ccc; padding-top: 10px;">
            <strong>Lampiran:</strong>
            <ul style="margin-top: 5px;">
                @foreach ($record->attachments as $attachment)
                    <li>{{ $attachment->file_name ?? basename($attachment->file_path) }}</li>
                @endforeach
            </ul>
        </div>
    @endif --}}

    <div class="signature">
        <p>Hormat Kami,</p>

        @php
            $signatureBase64 = '';
            if ($record->creator && $record->creator->signature_url) {
                $signaturePath = public_path('storage/' . $record->creator->signature_url);
                if (file_exists($signaturePath)) {
                    $sigType = pathinfo($signaturePath, PATHINFO_EXTENSION);
                    $sigData = file_get_contents($signaturePath);
                    $signatureBase64 = 'data:image/' . $sigType . ';base64,' . base64_encode($sigData);
                }
            }
        @endphp

        @if ($signatureBase64)
            <div style="margin-bottom: 5px;">
                <img src="{{ $signatureBase64 }}" alt="Signature" style="height: 60px; width: auto;">
            </div>
            <div class="name" style="margin-bottom: 0px;">{{ $record->creator->name ?? 'Admin' }}</div>
        @else
            <div class="name" style="margin-top: 60px; margin-bottom: 0px;">{{ $record->creator->name ?? 'Admin' }}
            </div>
        @endif

        <div>{{ $record->creator->activeEmployee->position ?? 'Direktur Utama' }}</div>
    </div>
</body>

</html>
