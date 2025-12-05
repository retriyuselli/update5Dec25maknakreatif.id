<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\ProductItem;
use App\Models\ProductVendor; // Pastikan model ini sesuai dengan item produk Anda (mis: ProductVendorItem)
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected array $productIds;

    protected bool $isSingleProductExport;

    protected int $itemCounter = 0;

    public function __construct(array $productIds)
    {
        $this->productIds = $productIds;
        $this->isSingleProductExport = count($productIds) === 1;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        if ($this->isSingleProductExport) {
            $productId = $this->productIds[0];

            // Ambil item untuk produk tunggal, pastikan relasi 'vendor' di-load
            return ProductVendor::with('vendor')
                ->where('product_id', $productId)
                ->orderBy('id') // Atau urutan lain yang konsisten
                ->get();
        } else {
            // Ambil beberapa produk untuk ekspor massal
            // Eager load relasi yang dibutuhkan untuk ringkasan produk
            return Product::with(['category', 'items', 'pengurangans'])
                ->withCount('items') // Untuk jumlah vendor/item
                ->withCount(['orderItems as unique_orders_count' => function ($query) {
                    $query->select(DB::raw('count(distinct order_id)'));
                }])
                ->withSum('orderItems as total_quantity_sold', 'quantity')
                ->whereIn('id', $this->productIds)
                ->get();
        }
    }

    public function headings(): array
    {
        if ($this->isSingleProductExport) {
            return [
                'No.',
                'Vendor Name',
                'Item Description',
                'Vendor Price (Item Total)', // Kolom "Vendor" dari preview (harga_vendor * qty)
                'Public Price (Item Total)', // Kolom "Publish" dari preview (harga_publish * qty)
            ];
        } else {
            // Headings untuk ekspor massal produk (ringkasan)
            return [
                'Product ID',
                'Product Name',
                'SKU/Slug',
                'Category',
                'Pax',
                'Status',
                'Approved',
                'Harga Paket (Total Publish Items)', // Product->product_price
                'Total Pengurangan', // Product->pengurangan
                'Harga Jual Final', // Product->price
                'Jumlah Vendor Items',
                'Jumlah Order Unik',
                'Total Kuantitas Terjual',
            ];
        }
    }

    /**
     * @param  mixed  $record  ProductItem jika isSingleProductExport true, Product jika false.
     */
    public function map($record): array
    {
        if ($this->isSingleProductExport) {
            /** @var ProductItem $item */
            $item = $record;
            $this->itemCounter++;

            $itemDescription = $item->description ? strip_tags($item->description) : '';
            $itemDescription = trim(preg_replace('/\s+/', ' ', $itemDescription));

            return [
                $this->itemCounter,
                $item->vendor->name ?? 'N/A',
                $itemDescription,
                // Menggunakan price_vendor dan price_public dari ProductItem, yang merupakan total per item (harga * qty)
                // Sesuai dengan tampilan di details-preview.blade.php
                $item->price_vendor ?? ($item->harga_vendor ?? 0),
                $item->price_public ?? ($item->harga_publish ?? 0),
            ];
        } else {
            /** @var Product $product */
            $product = $record;

            return [
                $product->id,
                $product->name,
                $product->slug,
                $product->category->name ?? 'N/A',
                $product->pax,
                $product->is_active ? 'Active' : 'Inactive',
                $product->is_approved ? 'Approved' : 'Not Approved',
                $product->product_price,
                $product->pengurangan,
                $product->price,
                $product->items_count, // Dari withCount('items')
                $product->unique_orders_count ?? 0, // Dari withCount
                $product->total_quantity_sold ?? 0, // Dari withSum
            ];
        }
    }
}
