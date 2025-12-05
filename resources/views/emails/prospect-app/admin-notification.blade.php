<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Prospek Baru - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .info-value {
            color: #333;
            font-size: 14px;
        }

        .full-width {
            grid-column: span 2;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 12px;
        }

        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }

        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
            }

            .full-width {
                grid-column: span 1;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0; font-size: 24px;">🎯 Aplikasi Prospek Baru</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Ada prospek baru yang perlu ditindaklanjuti</p>
        </div>

        <div style="margin-bottom: 25px;">
            <h2 style="color: #333; margin-bottom: 15px;">📝 Detail Prospek</h2>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nama Lengkap</div>
                    <div class="info-value">{{ $prospect->full_name }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $prospect->email }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Telepon</div>
                    <div class="info-value">{{ $prospect->phone }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Posisi</div>
                    <div class="info-value">{{ $prospect->position ?: '-' }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Nama Perusahaan</div>
                    <div class="info-value">{{ $prospect->company_name }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Industri</div>
                    <div class="info-value">{{ $prospect->industry->industry_name ?? '-' }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Website</div>
                    <div class="info-value">
                        @if ($prospect->name_of_website)
                            <a href="{{ $prospect->name_of_website }}" target="_blank"
                                style="color: #667eea;">{{ $prospect->name_of_website }}</a>
                        @else
                            -
                        @endif
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">Ukuran Perusahaan</div>
                    <div class="info-value">{{ $prospect->user_size ?: '-' }}</div>
                </div>

                @if ($prospect->reason_for_interest)
                    <div class="info-item full-width">
                        <div class="info-label">Alasan Minat</div>
                        <div class="info-value">{{ $prospect->reason_for_interest }}</div>
                    </div>
                @endif
            </div>
        </div>

        <div style="margin-bottom: 25px;">
            <h3 style="color: #333; margin-bottom: 10px;">📅 Informasi Tambahan</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <span
                            style="background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                            {{ ucfirst($prospect->status) }}
                        </span>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">Waktu Pengajuan</div>
                    <div class="info-value">{{ $prospect->submitted_at->format('d M Y, H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ config('app.url') }}/admin/prospect-apps/{{ $prospect->id }}" class="button">
                📊 Lihat Detail di Admin Panel
            </a>
        </div>

        <div style="background: #e7f3ff; padding: 20px; border-radius: 8px; border-left: 4px solid #2196f3;">
            <h4 style="margin: 0 0 10px 0; color: #1976d2;">💡 Action Required</h4>
            <ul style="margin: 0; padding-left: 20px; color: #555;">
                <li>Hubungi prospek dalam 24 jam</li>
                <li>Verifikasi informasi perusahaan</li>
                <li>Siapkan proposal awal</li>
                <li>Update status di admin panel</li>
            </ul>
        </div>

        <div class="footer">
            <p style="margin: 0;">
                Email ini dikirim otomatis dari sistem {{ config('app.name') }}<br>
                <strong>{{ config('app.url') }}</strong>
            </p>
            <p style="margin: 10px 0 0 0; font-size: 11px; color: #999;">
                Dikirim pada {{ now()->format('d M Y, H:i') }} WIB
            </p>
        </div>
    </div>
</body>

</html>
