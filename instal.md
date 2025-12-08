php artisan migrate --path=database/migrations/2025_08_30_094406_create_company_logos_table.php
php artisan shield:generate --all
lanjut blogs php artisan migrate --path=database/migrations/2025_09_01_100318_create_blogs_table.php
php artisan migrate --path=database/migrations/
cd /home/u380354370/domains/update.maknakreatif.id/public_html
https://console.cloud.google.com/

# Server Deployment Commands (Run setelah git pull)

git pull origin main
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
php artisan optimize

Berisi file sudah dioptimize menggunakan filament v4
Update 29 Nov 2025

## Urutan Widget Dashboard (Filament v4)

-   Prinsip urutan: gunakan `protected static ?int $sort` pada setiap widget. Angka lebih kecil tampil lebih atas; nilai negatif akan ditempatkan paling atas.

-   Finance

    -   `app/Filament/Widgets/AccountManagerWidget.php` → `8` (Account Manager Performance Dashboard)
    -   `app/Filament/Widgets/EventManager.php` → `9` (Event Manager Performance Dashboard)
    -   `app/Filament/Widgets/DashboardKeuangan.php` → `10`
    -   `app/Filament/Widgets/ChartCombinedFinancialWidget.php` → `11`
    -   `app/Filament/Widgets/StatsOverviewWidget.php` → `12`

-   Sales/Revenue

    -   `app/Filament/Widgets/OmsetTableWidget.php` → `20`
    -   `app/Filament/Widgets/RevenueBulananWeddingWidget.php` → `21`
    -   `app/Filament/Widgets/UserRolesChartWidget.php` → `22`
    -   `app/Filament/Widgets/AccountManagerMonthlyRevenueChart.php` → `23`

-   Prospects

    -   `app/Filament/Widgets/ProspectStatsWidget.php` → `30`

-   Events

    -   `app/Filament/Widgets/ComingSoonAkadWidget.php` → `40`
    -   `app/Filament/Widgets/ComingSoonResepsiWidget.php` → `41`

-   HR

    -   `app/Filament/Widgets/LeaveBalanceWidget.php` → `50`
    -   `app/Filament/Widgets/LeaveUsageChartWidget.php` → `51`
    -   `app/Filament/Widgets/RecentLeaveRequestsWidget.php` → `52`

-   Cara mengubah urutan:
    -   Edit nilai `protected static ?int $sort` di file widget terkait.
    -   Contoh: untuk menaruh widget di paling atas, set `sort` ke nilai negatif yang lebih kecil dari widget lain (mis. `-101`).
