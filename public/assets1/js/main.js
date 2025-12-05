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
    $('#download_btn').on('click', function () {
      var downloadSection = document.getElementById('download_section');
      // Select elements to hide for PDF generation
      var vendorPriceCols = $(downloadSection).find('.col-vendor-price');
      var publicPriceCols = $(downloadSection).find('.col-public-price');
      var totalVendorPriceRow = $(downloadSection).find('.hide-on-pdf'); // Tambahkan selector untuk baris yang ingin disembunyikan

      // Ambil nama event dari atribut data
      var eventName = downloadSection.dataset.eventName;
      var finalFilename;

      if (eventName && eventName.trim() !== '') {
        // Sanitasi nama event untuk nama file: ganti spasi dengan underscore dan hapus karakter yang tidak valid
        // Memperbolehkan alfanumerik, underscore, tanda hubung, dan titik.
        var sanitizedEventName = eventName.trim().replace(/\s+/g, '_').replace(/[^\w.-]/g, '');
        finalFilename = sanitizedEventName + '-invoice.pdf';
      } else {
        finalFilename = 'themeholy-invoice.pdf'; // Nama file default jika eventName tidak ada
      }

      var opt = {
        margin:       [50, 30, 20, 30], // top, left, bottom, right
        filename:     finalFilename, // Menggunakan nama file dinamis
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  {
          scale: 2,
          useCORS: true,
          scrollY: 0,
          windowWidth: document.body.scrollWidth
        },
        pagebreak: { mode: ['avoid-all', 'css', 'legacy'] },
        jsPDF:        { unit: 'pt', format: 'a4', orientation: 'portrait' }
      };

      // Hide columns before generating PDF
      vendorPriceCols.hide();
      publicPriceCols.hide();
      totalVendorPriceRow.hide(); // Sembunyikan baris "Total Vendor Price"

      html2pdf().set(opt).from(downloadSection).save().then(function() {
        // Show columns again after PDF is saved (or generation is complete)
        vendorPriceCols.show();
        publicPriceCols.show();
        totalVendorPriceRow.show(); // Tampilkan kembali baris "Total Vendor Price"
      }).catch(function(error) {
        // Ensure columns are shown even if there's an error
        vendorPriceCols.show();
        publicPriceCols.show();
        totalVendorPriceRow.show(); // Tampilkan kembali baris "Total Vendor Price" jika ada error
        console.error('Error generating PDF:', error);
      });
    });

    // Print Html Document
    $('.print_btn').on('click', function (e) {
      window.print();
    });

    // /*----------- 00. Right Click Disable ----------*/
    //   window.addEventListener('contextmenu', function (e) {
    //     // do something here...
    //     e.preventDefault();
    //   }, false);

    // /*----------- 00. Inspect Element Disable ----------*/
    //   document.onkeydown = function (e) {
    //     if (event.keyCode == 123) {
    //       return false;
    //     }
    //     if (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
    //       return false;
    //     }
    //     if (e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
    //       return false;
    //     }
    //     if (e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
    //       return false;
    //     }
    //     if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
    //       return false;
    //     }
    //   }
})(jQuery);