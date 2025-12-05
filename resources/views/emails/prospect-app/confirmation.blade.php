<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Aplikasi Prospek - {{ config('app.name') }}</title>
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
        }

        .check-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
        }

        .summary-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #374151;
        }

        .info-value {
            color: #6b7280;
        }

        .next-steps {
            background: #fef7ff;
            border: 1px solid #e9d5ff;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }

        .step {
            display: flex;
            align-items: start;
            margin-bottom: 15px;
        }

        .step:last-child {
            margin-bottom: 0;
        }

        .step-number {
            background: #8b5cf6;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .step-text {
            flex: 1;
        }

        .contact-info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }

        .contact-item {
            display: inline-block;
            margin: 5px 15px;
            color: #1d4ed8;
            text-decoration: none;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
        }

        @media (max-width: 600px) {
            .info-row {
                flex-direction: column;
                gap: 4px;
            }

            .contact-item {
                display: block;
                margin: 8px 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="check-icon">✓</div>
            <h1 style="margin: 0; font-size: 24px;">Aplikasi Berhasil Dikirim!</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Terima kasih atas minat Anda</p>
        </div>

        <div style="margin-bottom: 25px;">
            <p style="font-size: 16px; margin-bottom: 20px;">
                Halo <strong>{{ $prospect->full_name }}</strong>,
            </p>

            <p style="margin-bottom: 15px;">
                Terima kasih telah mengirimkan aplikasi prospek untuk <strong>{{ $prospect->company_name }}</strong>.
                Kami telah menerima informasi Anda dan sangat menghargai minat Anda terhadap layanan kami.
            </p>
        </div>

        <div class="summary-box">
            <h3 style="margin: 0 0 15px 0; color: #059669;">📋 Ringkasan Aplikasi Anda</h3>

            <div class="info-row">
                <span class="info-label">Nama Perusahaan:</span>
                <span class="info-value">{{ $prospect->company_name }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Industri:</span>
                <span class="info-value">{{ $prospect->industry->industry_name ?? '-' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $prospect->email }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Telepon:</span>
                <span class="info-value">{{ $prospect->phone }}</span>
            </div>

            @if ($prospect->user_size)
                <div class="info-row">
                    <span class="info-label">Ukuran Perusahaan:</span>
                    <span class="info-value">{{ $prospect->user_size }} karyawan</span>
                </div>
            @endif

            <div class="info-row">
                <span class="info-label">Waktu Pengajuan:</span>
                <span class="info-value">{{ $prospect->submitted_at->format('d M Y, H:i') }} WIB</span>
            </div>

            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">
                    <span
                        style="background: #fef3c7; color: #92400e; padding: 3px 8px; border-radius: 12px; font-size: 11px;">
                        Menunggu Review
                    </span>
                </span>
            </div>
        </div>

        <div class="next-steps">
            <h3 style="margin: 0 0 15px 0; color: #7c3aed;">🚀 Langkah Selanjutnya</h3>

            <div class="step">
                <div class="step-number">1</div>
                <div class="step-text">
                    <strong>Verifikasi Tim (0-24 jam)</strong><br>
                    <span style="color: #6b7280; font-size: 14px;">Tim kami akan memverifikasi dan meninjau aplikasi
                        Anda</span>
                </div>
            </div>

            <div class="step">
                <div class="step-number">2</div>
                <div class="step-text">
                    <strong>Kontak Langsung (1-2 hari)</strong><br>
                    <span style="color: #6b7280; font-size: 14px;">Account Manager kami akan menghubungi Anda untuk
                        konsultasi awal</span>
                </div>
            </div>

            <div class="step">
                <div class="step-number">3</div>
                <div class="step-text">
                    <strong>Proposal & Demo (3-5 hari)</strong><br>
                    <span style="color: #6b7280; font-size: 14px;">Kami akan menyiapkan proposal khusus dan demo sesuai
                        kebutuhan Anda</span>
                </div>
            </div>
        </div>

        <div style="background: #fff7ed; border: 1px solid #fed7aa; border-radius: 8px; padding: 20px; margin: 25px 0;">
            <h4 style="margin: 0 0 10px 0; color: #ea580c;">⏰ Respon Cepat Dijamin</h4>
            <p style="margin: 0; color: #9a3412; font-size: 14px;">
                Tim kami berkomitmen untuk menghubungi Anda dalam <strong>24 jam</strong> setelah aplikasi diterima.
                Kami memahami betapa pentingnya waktu dalam bisnis Anda.
            </p>
        </div>

        <div class="contact-info">
            <h4 style="margin: 0 0 15px 0; color: #1e40af;">📞 Butuh Bantuan Segera?</h4>
            <p style="margin: 0 0 15px 0; color: #374151; font-size: 14px;">
                Jangan ragu untuk menghubungi kami jika ada pertanyaan:
            </p>

            <div>
                <a href="mailto:support@maknaonline.com" class="contact-item">
                    📧 support@maknaonline.com
                </a>
                <a href="tel:+621234567890" class="contact-item">
                    📱 +62 123 456 7890
                </a>
                <a href="https://wa.me/621234567890" class="contact-item">
                    💬 WhatsApp
                </a>
            </div>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <p style="color: #374151; font-style: italic;">
                "Kami senang dapat membantu mengembangkan bisnis Anda!"
            </p>
        </div>

        <div class="footer">
            <p style="margin: 0 0 10px 0;">
                Email ini dikirim otomatis dari sistem {{ config('app.name') }}<br>
                <strong>{{ config('app.url') }}</strong>
            </p>
            <p style="margin: 0; font-size: 11px; color: #9ca3af;">
                Jika Anda tidak merasa mendaftar untuk ini, silakan abaikan email ini.
            </p>
        </div>
    </div>
</body>

</html>
