(function ($) {
    "use strict";
    /*=================================
JS Index Here
==================================*/
    /*
01. Print and Download Button

00. Right Click Disable
00. Inspect Element Disable
*/
    /*=================================
JS Index End
==================================*/

    /*----------- 01. Print and Download Button ----------*/
    // Using html2pdf.js for high-quality text-based PDF output
    $("#download_btn").on("click", function () {
        var downloadSection = document.getElementById("download_section");
        // Select elements to hide for PDF generation - be more specific
        var mainTableVendorCols = $(downloadSection).find(
            ".invoice-table:not(.addition-table):not(.reduction-table) .col-vendor-price"
        );
        var mainTablePublicCols = $(downloadSection).find(
            ".invoice-table:not(.addition-table):not(.reduction-table) .col-public-price"
        );
        var totalTablePublicCols = $(downloadSection).find(
            ".total-table .col-public-price"
        );
        var additionVendorCols = $(downloadSection).find(
            ".addition-table .col-vendor-price"
        );

        // Ambil nama event dari atribut data
        var eventName = downloadSection.dataset.eventName;
        var finalFilename;

        if (eventName && eventName.trim() !== "") {
            // Sanitasi nama event untuk nama file: ganti spasi dengan underscore dan hapus karakter yang tidak valid
            // Memperbolehkan alfanumerik, underscore, tanda hubung, dan titik.
            var sanitizedEventName = eventName
                .trim()
                .replace(/\s+/g, "_")
                .replace(/[^\w.-]/g, "");
            finalFilename = sanitizedEventName + "-penawaran.pdf";
        } else {
            finalFilename = "penawaran-event.pdf"; // Nama file default jika eventName tidak ada
        }

        var opt = {
            margin: [50, 30, 20, 30], // top, left, bottom, right
            filename: finalFilename, // Menggunakan nama file dinamis
            image: { type: "jpeg", quality: 0.98 },
            html2canvas: {
                scale: 2,
                useCORS: true,
                scrollY: 0,
                windowWidth: document.body.scrollWidth,
            },
            pagebreak: { mode: ["avoid-all", "css", "legacy"] },
            jsPDF: { unit: "pt", format: "a4", orientation: "portrait" },
        };

        // Hide columns before generating PDF
        mainTableVendorCols.hide();
        mainTablePublicCols.hide();
        totalTablePublicCols.hide();
        additionVendorCols.hide();

        var style = document.createElement("style");
        style.innerHTML = `
@media print {
    body, * {
        font-family: 'Noto Sans', sans-serif !important;
        font-size: 10px !important;
        line-height: 1.3 !important;
        color: #000 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    .no-print, .invoice-buttons {
        display: none !important;
    }
    
    .col-vendor-price, .col-public-price {
        display: none !important;
    }
    
    .invoice-table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin-bottom: 15px !important;
    }
    
    .invoice-table th, .invoice-table td {
        border: 1px solid #ddd !important;
        padding: 8px !important;
        text-align: left !important;
    }
    
    .invoice-table th {
        background-color: #f8f9fa !important;
        font-weight: bold !important;
        text-transform: uppercase !important;
    }
    
    .total-table th, .total-table td {
        border: 1px solid #ddd !important;
        padding: 6px 8px !important;
    }
    
    .addition-row {
        background-color: #f8f9fa !important;
    }
    
    .addition-amount {
        color: #28a745 !important;
        font-weight: 600 !important;
    }
    
    .reduction-amount {
        color: #dc3545 !important;
        font-weight: 600 !important;
    }
    
    .signature-area {
        page-break-inside: avoid !important;
        margin-top: 40px !important;
    }
}
`;
        downloadSection.appendChild(style);
        html2pdf()
            .set(opt)
            .from(downloadSection)
            .save()
            .then(function () {
                mainTableVendorCols.show();
                mainTablePublicCols.show();
                totalTablePublicCols.show();
                additionVendorCols.show();
                style.remove();
            })
            .catch(function (error) {
                mainTableVendorCols.show();
                mainTablePublicCols.show();
                totalTablePublicCols.show();
                additionVendorCols.show();
                style.remove();
                console.error("Error generating PDF:", error);
            });
    });

    // Print Html Document with enhanced functionality
    $(".print_btn").on("click", function (e) {
        var downloadSection = document.getElementById("download_section");
        // Select elements to hide for printing - be more specific
        var mainTableVendorCols = $(downloadSection).find(
            ".invoice-table:not(.addition-table):not(.reduction-table) .col-vendor-price"
        );
        var mainTablePublicCols = $(downloadSection).find(
            ".invoice-table:not(.addition-table):not(.reduction-table) .col-public-price"
        );
        var totalTablePublicCols = $(downloadSection).find(
            ".total-table .col-public-price"
        );
        var additionVendorCols = $(downloadSection).find(
            ".addition-table .col-vendor-price"
        );

        // Hide columns before printing
        mainTableVendorCols.hide();
        mainTablePublicCols.hide();
        totalTablePublicCols.hide();
        additionVendorCols.hide();

        // Add print-specific styles
        var printStyle = document.createElement("style");
        printStyle.id = "print-specific-style";
        printStyle.innerHTML = `
        @media print {
            body, * {
                font-family: 'Noto Sans', sans-serif !important;
                font-size: 10px !important;
                line-height: 1.3 !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .no-print, .invoice-buttons {
                display: none !important;
            }
            
            /* Hide vendor and public price columns for main table only */
            .invoice-table:not(.addition-table):not(.reduction-table) .col-vendor-price,
            .invoice-table:not(.addition-table):not(.reduction-table) .col-public-price,
            .total-table .col-public-price {
                display: none !important;
            }
            
            /* Show publish price column for addition table, hide vendor */
            .addition-table .col-vendor-price {
                display: none !important;
            }
            
            .addition-table .col-public-price {
                display: table-cell !important;
            }
            
            /* Show amount column for reduction table */
            .reduction-table .col-public-price {
                display: table-cell !important;
            }
            
            .invoice-table {
                width: 100% !important;
                border-collapse: collapse !important;
                margin-bottom: 15px !important;
            }
            
            .invoice-table th, .invoice-table td {
                border: 1px solid #ddd !important;
                padding: 8px !important;
                text-align: left !important;
            }
            
            .invoice-table th {
                background-color: #f8f9fa !important;
                font-weight: bold !important;
                text-transform: uppercase !important;
            }
            
            .total-table th, .total-table td {
                border: 1px solid #ddd !important;
                padding: 6px 8px !important;
            }
            
            .addition-row {
                background-color: #f8f9fa !important;
            }
            
            .addition-amount {
                color: #28a745 !important;
                font-weight: 600 !important;
            }
            
            .reduction-amount {
                color: #dc3545 !important;
                font-weight: 600 !important;
            }
            
            .signature-area {
                page-break-inside: avoid !important;
                margin-top: 40px !important;
            }
            
            .address-box, .booking-info {
                margin-bottom: 10px !important;
            }
            
            @page {
                margin: 0.5in !important;
                size: A4 !important;
            }
        }
    `;
        document.head.appendChild(printStyle);

        // Trigger print
        window.print();

        // Cleanup after print dialog closes
        setTimeout(function () {
            mainTableVendorCols.show();
            mainTablePublicCols.show();
            totalTablePublicCols.show();
            additionVendorCols.show();
            var styleElement = document.getElementById("print-specific-style");
            if (styleElement) {
                styleElement.remove();
            }
        }, 1000);

        // Also handle the afterprint event for better cleanup
        window.addEventListener(
            "afterprint",
            function () {
                mainTableVendorCols.show();
                mainTablePublicCols.show();
                totalTablePublicCols.show();
                additionVendorCols.show();
                var styleElement = document.getElementById(
                    "print-specific-style"
                );
                if (styleElement) {
                    styleElement.remove();
                }
            },
            { once: true }
        );
    });

    // /*----------- 00. Right Click Disable ----------*/
    // window.addEventListener('contextmenu', function (e) {
    // // do something here...
    // e.preventDefault();
    // }, false);

    // /*----------- 00. Inspect Element Disable ----------*/
    // document.onkeydown = function (e) {
    // if (event.keyCode == 123) {
    // return false;
    // }
    // if (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
    // return false;
    // }
    // if (e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
    // return false;
    // }
    // if (e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
    // return false;
    // }
    // if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
    // return false;
    // }
    // }
})(jQuery);
