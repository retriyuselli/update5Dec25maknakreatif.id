# Datacrew CSS - Documentation

## Overview

File CSS ini berisi styling kustom untuk form Data Pribadi dengan desain modern dan responsif.

## File Location

-   **CSS File**: `/public/assets/datacrew/datacrew.css`
-   **Blade Template**: `/resources/views/data-pribadi/create.blade.php`

## Features

1. **Modern Design**: Menggunakan font Noto Sans (sama dengan Filament) dan shadow yang halus
2. **Responsive**: Mendukung tablet dan mobile dengan media queries
3. **Form Styling**: Custom styling untuk form elements dengan fokus states
4. **Button Effects**: Hover effects dengan transform dan shadow
5. **Utility Classes**: Helper classes untuk styling tambahan
6. **Filament Integration**: Menggunakan font stack yang sama dengan Filament admin panel

## CSS Structure

```
1. Base Styles - Body dan typography dasar
2. Container & Layout - Wrapper dan layout styles
3. Form Elements - Input, select, dan form styling
4. Button Styles - Primary button styling dengan hover effects
5. Typography - Page title dan text styling
6. Responsive Design - Media queries untuk tablet dan mobile
7. Utility Classes - Helper classes
8. Special Effects - Hover effects dan image preview
```

## Responsive Breakpoints

-   **Desktop**: Default styles
-   **Tablet**: ≤ 768px
-   **Mobile**: ≤ 576px

## Usage

File CSS ini sudah terintegrasi dengan Blade template dan akan dimuat otomatis ketika halaman form Data Pribadi diakses.

## Files Modified

-   ✅ Moved inline CSS from Blade template to external CSS file
-   ✅ Added responsive design support
-   ✅ Improved organization with comments and sections
-   ✅ Added utility classes for future use
-   ✅ Enhanced form styling with hover effects
-   ✅ Updated font from Poppins to Noto Sans (Filament's font)

## Version History

-   v1.0 - Initial separation from inline styles with responsive design
-   v1.1 - Updated font to Noto Sans to match Filament admin panel

## Custom Success Notification

Setelah user berhasil menyimpan data, akan muncul notifikasi khusus dengan:

-   **Pesan**: "Terima kasih sudah menjadi bagian dari Makna Wedding & Event Planner!"
-   **Sub-pesan**: "Data Anda telah berhasil disimpan dengan baik."
-   **Icon**: Heart icon dengan animasi heartbeat
-   **Design**: Gradient background dengan shadow dan border radius
-   **Duration**: 8 detik dengan animasi slide-in
-   **Auto-features**: Form reset otomatis dan scroll ke atas halaman

### Technical Details

-   Toast notification menggunakan Bootstrap 5
-   Custom CSS dengan gradient background dan animations
-   JavaScript enhancement untuk UX yang lebih baik
-   Responsive design untuk mobile dan desktop
