<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPengurangan;
use App\Models\ProductVendor;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get existing data
        $categories = Category::all();
        $vendors = Vendor::all();

        if ($categories->isEmpty()) {
            $this->command->error('No categories found. Please run CategorySeeder first.');

            return;
        }

        if ($vendors->isEmpty()) {
            $this->command->error('No vendors found. Please run VendorSeeder first.');

            return;
        }

        $products = [
            [
                'name' => 'Paket Wedding Mewah Jakarta 300 Pax',
                'slug' => 'paket-wedding-mewah-jakarta-300-pax',
                'category_id' => $categories->random()->id,
                'pax' => 300,
                'stock' => 10,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Dekorasi Mewah Jakarta',
                        'quantity' => 1,
                        'description' => '<p>Paket dekorasi lengkap meliputi:</p><ul><li>Pelaminan mewah dengan backdrop bunga segar</li><li>Dekorasi meja tamu VIP</li><li>Lighting romantic</li><li>Red carpet</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Catering Premium Bogor',
                        'quantity' => 1,
                        'description' => '<p>Paket catering 300 pax meliputi:</p><ul><li>Menu prasmanan lengkap 15 macam</li><li>Welcome drink</li><li>Ice cream corner</li><li>Live cooking station</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Foto & Video Cinematic',
                        'quantity' => 1,
                        'description' => '<p>Dokumentasi lengkap meliputi:</p><ul><li>Pre-wedding photoshoot</li><li>Wedding day coverage</li><li>Same day edit video</li><li>Album premium 50 foto</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Diskon Early Bird',
                        'amount' => 2000000,
                        'notes' => '<p>Diskon khusus untuk pemesanan 6 bulan sebelum acara</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Garden Party Outdoor 200 Pax',
                'slug' => 'paket-wedding-garden-party-outdoor-200-pax',
                'category_id' => $categories->random()->id,
                'pax' => 200,
                'stock' => 10,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Dekorasi Mewah Jakarta',
                        'quantity' => 1,
                        'description' => '<p>Dekorasi outdoor garden theme:</p><ul><li>Gazebo dekorasi dengan bunga segar</li><li>Fairy lights</li><li>Wooden arch</li><li>Rustic decoration</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Catering Premium Bogor',
                        'quantity' => 1,
                        'description' => '<p>Paket catering outdoor 200 pax:</p><ul><li>BBQ corner</li><li>Salad bar</li><li>Refreshing drinks</li><li>Mini desserts</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Soundsystem Pro Jakarta',
                        'quantity' => 1,
                        'description' => '<p>Sound system outdoor:</p><ul><li>Weather proof speakers</li><li>Wireless microphone</li><li>Background music</li><li>MC sound support</li></ul>',
                    ],
                ],
                'pengurangans' => [],
            ],
            [
                'name' => 'Paket Wedding Intimate 150 Pax',
                'slug' => 'paket-wedding-intimate-150-pax',
                'category_id' => $categories->random()->id,
                'pax' => 150,
                'stock' => 10,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Make Up Artist Professional',
                        'quantity' => 1,
                        'description' => '<p>Paket make up lengkap:</p><ul><li>Make up pengantin akad & resepsi</li><li>Hair styling 2 gaya</li><li>Make up orang tua</li><li>Touch up during event</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Car Rental Luxury',
                        'quantity' => 1,
                        'description' => '<p>Transportasi mewah:</p><ul><li>Mercedes Benz S-Class untuk pengantin</li><li>Dekorasi mobil dengan bunga</li><li>Driver professional</li><li>Duration 8 jam</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Entertainment Wedding Organizer',
                        'quantity' => 1,
                        'description' => '<p>Entertainment package:</p><ul><li>MC professional bilingual</li><li>Acoustic duo performance</li><li>Traditional dance 2 songs</li><li>Lighting show</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Paket Hemat Intimate',
                        'amount' => 1500000,
                        'notes' => '<p>Diskon khusus untuk wedding intimate dengan guest di bawah 200 pax</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Traditional Jawa 400 Pax',
                'slug' => 'paket-wedding-traditional-jawa-400-pax',
                'category_id' => $categories->random()->id,
                'pax' => 400,
                'stock' => 10,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Dekorasi Mewah Jakarta',
                        'quantity' => 1,
                        'description' => '<p>Dekorasi adat Jawa lengkap:</p><ul><li>Pelaminan Jawa dengan bleketepe</li><li>Janur kuning</li><li>Gamelan decoration</li><li>Traditional umbul-umbul</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Entertainment Wedding Organizer',
                        'quantity' => 1,
                        'description' => '<p>Entertainment traditional:</p><ul><li>Gamelan performance</li><li>Traditional Javanese dance</li><li>MC adat Jawa</li><li>Tari Bedhaya</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Catering Premium Bogor',
                        'quantity' => 1,
                        'description' => '<p>Menu tradisional Jawa 400 pax:</p><ul><li>Gudeg Yogya</li><li>Sate ayam & kambing</li><li>Nasi liwet</li><li>Jajanan pasar traditional</li></ul>',
                    ],
                ],
                'pengurangans' => [],
            ],
            [
                'name' => 'Paket Wedding Modern Minimalist 250 Pax',
                'slug' => 'paket-wedding-modern-minimalist-250-pax',
                'category_id' => $categories->random()->id,
                'pax' => 250,
                'stock' => 10,
                'is_active' => true,
                'is_approved' => false, // Belum di-approve
                'vendors' => [
                    [
                        'vendor_name' => 'Foto & Video Cinematic',
                        'quantity' => 1,
                        'description' => '<p>Dokumentasi modern style:</p><ul><li>Drone aerial photography</li><li>Cinematic wedding film</li><li>Instagram-ready photos</li><li>Live streaming setup</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Soundsystem Pro Jakarta',
                        'quantity' => 1,
                        'description' => '<p>Audio visual modern:</p><ul><li>LED screen for presentation</li><li>High-end sound system</li><li>Wireless mic premium</li><li>DJ equipment</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Promo New Package',
                        'amount' => 3000000,
                        'notes' => '<p>Promo launching paket baru modern minimalist</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Beach Resort 180 Pax',
                'slug' => 'paket-wedding-beach-resort-180-pax',
                'category_id' => $categories->random()->id,
                'pax' => 180,
                'stock' => 8,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Dekorasi Mewah Jakarta',
                        'quantity' => 1,
                        'description' => '<p>Dekorasi beach theme:</p><ul><li>White gazebo with ocean view</li><li>Tropical flower arrangements</li><li>Sandy aisle decoration</li><li>Sunset lighting setup</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Catering Premium Bogor',
                        'quantity' => 1,
                        'description' => '<p>Seafood buffet 180 pax:</p><ul><li>Fresh grilled fish</li><li>Lobster thermidor</li><li>Tropical fruit bar</li><li>Beach BBQ station</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Foto & Video Cinematic',
                        'quantity' => 1,
                        'description' => '<p>Beach wedding documentation:</p><ul><li>Sunset couple photoshoot</li><li>Aerial drone shots</li><li>Underwater pre-wedding</li><li>Beach wedding video</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Weekend Off-Season Discount',
                        'amount' => 1800000,
                        'notes' => '<p>Diskon khusus untuk weekend di luar musim liburan</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Grand Ballroom 500 Pax',
                'slug' => 'paket-wedding-grand-ballroom-500-pax',
                'category_id' => $categories->random()->id,
                'pax' => 500,
                'stock' => 5,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Entertainment Wedding Organizer',
                        'quantity' => 1,
                        'description' => '<p>Grand entertainment package:</p><ul><li>Live band performance 3 hours</li><li>Professional MC bilingual</li><li>LED wall backdrop</li><li>Fireworks display</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Catering Premium Bogor',
                        'quantity' => 1,
                        'description' => '<p>International buffet 500 pax:</p><ul><li>Western & Asian cuisine</li><li>Premium wine selection</li><li>Chocolate fountain</li><li>Chef live cooking</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Make Up Artist Professional',
                        'quantity' => 1,
                        'description' => '<p>Bridal beauty grand package:</p><ul><li>Pre-wedding makeup trial</li><li>Wedding day glam makeup</li><li>Hair & accessories styling</li><li>Family makeup service</li></ul>',
                    ],
                ],
                'pengurangans' => [],
            ],
            [
                'name' => 'Paket Wedding Rustic Vintage 220 Pax',
                'slug' => 'paket-wedding-rustic-vintage-220-pax',
                'category_id' => $categories->random()->id,
                'pax' => 220,
                'stock' => 12,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Dekorasi Mewah Jakarta',
                        'quantity' => 1,
                        'description' => '<p>Rustic vintage decoration:</p><ul><li>Wooden arch with vintage lace</li><li>Mason jar centerpieces</li><li>Vintage furniture setup</li><li>String lights ambiance</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Soundsystem Pro Jakarta',
                        'quantity' => 1,
                        'description' => '<p>Vintage audio setup:</p><ul><li>Retro style speakers</li><li>Vintage microphone collection</li><li>Acoustic music system</li><li>Background jazz playlist</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Car Rental Luxury',
                        'quantity' => 1,
                        'description' => '<p>Classic vintage car:</p><ul><li>Classic Volkswagen Beetle</li><li>Vintage car decoration</li><li>Professional chauffeur</li><li>Photo session included</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Vintage Style Discount',
                        'amount' => 2200000,
                        'notes' => '<p>Diskon khusus untuk tema vintage dengan dekorasi klasik</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Corporate Style 350 Pax',
                'slug' => 'paket-wedding-corporate-style-350-pax',
                'category_id' => $categories->random()->id,
                'pax' => 350,
                'stock' => 7,
                'is_active' => true,
                'is_approved' => false,
                'vendors' => [
                    [
                        'vendor_name' => 'Soundsystem Pro Jakarta',
                        'quantity' => 1,
                        'description' => '<p>Corporate audio visual:</p><ul><li>Professional presentation setup</li><li>High-definition LED screens</li><li>Conference microphone system</li><li>Live streaming capability</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Catering Premium Bogor',
                        'quantity' => 1,
                        'description' => '<p>Corporate dining 350 pax:</p><ul><li>Business lunch menu</li><li>Coffee break station</li><li>International cuisine</li><li>Formal table setting</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Entertainment Wedding Organizer',
                        'quantity' => 1,
                        'description' => '<p>Corporate entertainment:</p><ul><li>Professional MC corporate</li><li>Classical music ensemble</li><li>Award ceremony setup</li><li>Business networking session</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Corporate Package Discount',
                        'amount' => 2500000,
                        'notes' => '<p>Diskon untuk acara wedding dengan format corporate</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Bohemian Chic 160 Pax',
                'slug' => 'paket-wedding-bohemian-chic-160-pax',
                'category_id' => $categories->random()->id,
                'pax' => 160,
                'stock' => 15,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Dekorasi Mewah Jakarta',
                        'quantity' => 1,
                        'description' => '<p>Bohemian style decoration:</p><ul><li>Macrame wall hangings</li><li>Dried flower arrangements</li><li>Moroccan rugs & cushions</li><li>Dream catcher installations</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Make Up Artist Professional',
                        'quantity' => 1,
                        'description' => '<p>Boho bridal makeup:</p><ul><li>Natural glowing makeup</li><li>Braided hairstyle</li><li>Flower crown styling</li><li>Henna art application</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Foto & Video Cinematic',
                        'quantity' => 1,
                        'description' => '<p>Bohemian photoshoot:</p><ul><li>Golden hour photography</li><li>Natural outdoor setting</li><li>Lifestyle wedding video</li><li>Instagram story templates</li></ul>',
                    ],
                ],
                'pengurangans' => [],
            ],
            [
                'name' => 'Paket Wedding Royal Palace 600 Pax',
                'slug' => 'paket-wedding-royal-palace-600-pax',
                'category_id' => $categories->random()->id,
                'pax' => 600,
                'stock' => 3,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Dekorasi Mewah Jakarta',
                        'quantity' => 1,
                        'description' => '<p>Royal palace decoration:</p><ul><li>Gold baroque backdrop</li><li>Crystal chandeliers</li><li>Red carpet entrance</li><li>Royal throne setup</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Entertainment Wedding Organizer',
                        'quantity' => 1,
                        'description' => '<p>Royal entertainment:</p><ul><li>Chamber orchestra</li><li>Opera singer performance</li><li>Royal protocol MC</li><li>Classical dance troupe</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Catering Premium Bogor',
                        'quantity' => 1,
                        'description' => '<p>Royal feast 600 pax:</p><ul><li>Gourmet international menu</li><li>Premium wine & champagne</li><li>Royal dessert table</li><li>Butler service</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Royal Early Bird',
                        'amount' => 5000000,
                        'notes' => '<p>Super early bird discount untuk paket royal palace</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Garden Tea Party 120 Pax',
                'slug' => 'paket-wedding-garden-tea-party-120-pax',
                'category_id' => $categories->random()->id,
                'pax' => 120,
                'stock' => 20,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Catering Premium Bogor',
                        'quantity' => 1,
                        'description' => '<p>English tea party menu:</p><ul><li>Afternoon tea set</li><li>Scones & finger sandwiches</li><li>Premium tea selection</li><li>Vintage china service</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Dekorasi Mewah Jakarta',
                        'quantity' => 1,
                        'description' => '<p>Garden tea party setup:</p><ul><li>English garden decoration</li><li>Vintage tea tables</li><li>Floral arch entrance</li><li>Garden umbrella setup</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Entertainment Wedding Organizer',
                        'quantity' => 1,
                        'description' => '<p>Tea party entertainment:</p><ul><li>Acoustic guitar duo</li><li>Garden party games</li><li>Tea ceremony demonstration</li><li>English style MC</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Intimate Tea Party Discount',
                        'amount' => 1200000,
                        'notes' => '<p>Diskon untuk paket intimate tea party wedding</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Industrial Loft 280 Pax',
                'slug' => 'paket-wedding-industrial-loft-280-pax',
                'category_id' => $categories->random()->id,
                'pax' => 280,
                'stock' => 9,
                'is_active' => true,
                'is_approved' => false,
                'vendors' => [
                    [
                        'vendor_name' => 'Soundsystem Pro Jakarta',
                        'quantity' => 1,
                        'description' => '<p>Industrial sound system:</p><ul><li>Warehouse-style speakers</li><li>DJ booth setup</li><li>Electronic music system</li><li>LED strip lighting</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Dekorasi Mewah Jakarta',
                        'quantity' => 1,
                        'description' => '<p>Industrial loft decoration:</p><ul><li>Exposed brick backdrop</li><li>Metal arch installation</li><li>Edison bulb lighting</li><li>Concrete table settings</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Catering Premium Bogor',
                        'quantity' => 1,
                        'description' => '<p>Urban street food 280 pax:</p><ul><li>Food truck style stations</li><li>Craft beer selection</li><li>Gourmet burger bar</li><li>Artisan coffee corner</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Urban Style Discount',
                        'amount' => 2800000,
                        'notes' => '<p>Diskon untuk konsep wedding industrial yang unik</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Cultural Fusion 380 Pax',
                'slug' => 'paket-wedding-cultural-fusion-380-pax',
                'category_id' => $categories->random()->id,
                'pax' => 380,
                'stock' => 6,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Entertainment Wedding Organizer',
                        'quantity' => 1,
                        'description' => '<p>Cultural fusion entertainment:</p><ul><li>Traditional & modern dance</li><li>Multi-cultural music</li><li>Bilingual MC service</li><li>Cultural ceremony integration</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Catering Premium Bogor',
                        'quantity' => 1,
                        'description' => '<p>Fusion cuisine 380 pax:</p><ul><li>Asian-Western fusion menu</li><li>Cultural food stations</li><li>International dessert bar</li><li>Traditional drink corner</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Dekorasi Mewah Jakarta',
                        'quantity' => 1,
                        'description' => '<p>Cultural fusion decoration:</p><ul><li>Mix traditional & modern elements</li><li>Cultural motif backdrop</li><li>Fusion flower arrangements</li><li>Heritage color scheme</li></ul>',
                    ],
                ],
                'pengurangans' => [],
            ],
            [
                'name' => 'Paket Wedding Eco Green 190 Pax',
                'slug' => 'paket-wedding-eco-green-190-pax',
                'category_id' => $categories->random()->id,
                'pax' => 190,
                'stock' => 14,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Dekorasi Mewah Jakarta',
                        'quantity' => 1,
                        'description' => '<p>Eco-friendly decoration:</p><ul><li>Potted plant centerpieces</li><li>Bamboo arch installation</li><li>Recycled material decor</li><li>Living wall backdrop</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Catering Premium Bogor',
                        'quantity' => 1,
                        'description' => '<p>Organic menu 190 pax:</p><ul><li>Farm-to-table cuisine</li><li>Organic vegetarian options</li><li>Eco-friendly packaging</li><li>Local ingredient focus</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Foto & Video Cinematic',
                        'quantity' => 1,
                        'description' => '<p>Nature wedding documentation:</p><ul><li>Natural light photography</li><li>Outdoor nature shots</li><li>Eco-themed video</li><li>Digital album only</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Green Wedding Discount',
                        'amount' => 1900000,
                        'notes' => '<p>Diskon untuk memilih konsep wedding ramah lingkungan</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Luxury Yacht 80 Pax',
                'slug' => 'paket-wedding-luxury-yacht-80-pax',
                'category_id' => $categories->random()->id,
                'pax' => 80,
                'stock' => 4,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Venue Ballroom Grand',
                        'quantity' => 1,
                        'description' => '<p>Luxury yacht venue:</p><ul><li>Private yacht charter</li><li>Ocean view ceremony</li><li>Upper deck reception</li><li>Professional crew service</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Catering Premium Bogor',
                        'quantity' => 1,
                        'description' => '<p>Maritime dining 80 pax:</p><ul><li>Fresh seafood platter</li><li>Ocean breeze cocktails</li><li>Sunset dinner service</li><li>Gourmet yacht catering</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Drone Wedding Video',
                        'quantity' => 1,
                        'description' => '<p>Yacht aerial documentation:</p><ul><li>Drone over ocean shots</li><li>Yacht ceremony coverage</li><li>Sunset sailing video</li><li>Ocean wedding film</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Exclusive Yacht Discount',
                        'amount' => 3500000,
                        'notes' => '<p>Diskon untuk paket eksklusif yacht wedding</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Mountain Resort 130 Pax',
                'slug' => 'paket-wedding-mountain-resort-130-pax',
                'category_id' => $categories->random()->id,
                'pax' => 130,
                'stock' => 8,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Wedding Venue Garden',
                        'quantity' => 1,
                        'description' => '<p>Mountain resort venue:</p><ul><li>Scenic mountain backdrop</li><li>Pine forest ceremony</li><li>Fresh mountain air</li><li>Resort accommodation</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Outdoor Wedding Specialist',
                        'quantity' => 1,
                        'description' => '<p>Mountain adventure setup:</p><ul><li>Weather-proof planning</li><li>Mountain hiking pre-wedding</li><li>Campfire reception</li><li>Rustic mountain decor</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Traditional Dance Performance',
                        'quantity' => 1,
                        'description' => '<p>Highland cultural show:</p><ul><li>Traditional mountain dance</li><li>Local cultural performance</li><li>Highland music ensemble</li><li>Regional costume display</li></ul>',
                    ],
                ],
                'pengurangans' => [],
            ],
            [
                'name' => 'Paket Wedding Art Gallery 240 Pax',
                'slug' => 'paket-wedding-art-gallery-240-pax',
                'category_id' => $categories->random()->id,
                'pax' => 240,
                'stock' => 6,
                'is_active' => true,
                'is_approved' => false,
                'vendors' => [
                    [
                        'vendor_name' => 'Lighting Design Pro',
                        'quantity' => 1,
                        'description' => '<p>Gallery lighting design:</p><ul><li>Art-focused lighting</li><li>Dramatic spotlighting</li><li>Gallery ambiance</li><li>Modern LED installation</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Calligraphy Service',
                        'quantity' => 1,
                        'description' => '<p>Artistic stationery:</p><ul><li>Hand-lettered invitations</li><li>Gallery-style signage</li><li>Artistic place cards</li><li>Custom art quotes</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Photo Booth Rental',
                        'quantity' => 1,
                        'description' => '<p>Art gallery photo experience:</p><ul><li>Artistic photo backdrop</li><li>Gallery-themed props</li><li>Instant art prints</li><li>Digital gallery sharing</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Art Lover Discount',
                        'amount' => 2400000,
                        'notes' => '<p>Diskon untuk wedding dengan tema seni dan galeri</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Historical Heritage 320 Pax',
                'slug' => 'paket-wedding-historical-heritage-320-pax',
                'category_id' => $categories->random()->id,
                'pax' => 320,
                'stock' => 5,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Traditional Kebaya Designer',
                        'quantity' => 1,
                        'description' => '<p>Heritage bridal attire:</p><ul><li>Traditional kebaya collection</li><li>Historical pattern design</li><li>Authentic fabric selection</li><li>Cultural styling service</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Traditional Gamelan Group',
                        'quantity' => 1,
                        'description' => '<p>Heritage music performance:</p><ul><li>Historical gamelan music</li><li>Traditional wedding songs</li><li>Cultural ceremony music</li><li>Heritage instrument display</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Favor Specialist',
                        'quantity' => 1,
                        'description' => '<p>Heritage wedding favors:</p><ul><li>Traditional craft souvenirs</li><li>Cultural gift items</li><li>Heritage packaging</li><li>Historical themed favors</li></ul>',
                    ],
                ],
                'pengurangans' => [],
            ],
            [
                'name' => 'Paket Wedding Winter Wonderland 200 Pax',
                'slug' => 'paket-wedding-winter-wonderland-200-pax',
                'category_id' => $categories->random()->id,
                'pax' => 200,
                'stock' => 10,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Ice Sculpture Art',
                        'quantity' => 1,
                        'description' => '<p>Winter ice decorations:</p><ul><li>Wedding ice sculpture</li><li>Frozen centerpieces</li><li>Ice bar setup</li><li>Winter themed ice art</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Balloon Decorator',
                        'quantity' => 1,
                        'description' => '<p>Winter balloon installation:</p><ul><li>Silver & white balloon arch</li><li>Snowflake balloon designs</li><li>Winter wonderland setup</li><li>Frozen-themed decorations</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Bridal Spa Treatment',
                        'quantity' => 1,
                        'description' => '<p>Winter bridal preparation:</p><ul><li>Winter skin care treatment</li><li>Moisturizing facial</li><li>Winter glow makeup prep</li><li>Warming spa therapy</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Winter Theme Discount',
                        'amount' => 2000000,
                        'notes' => '<p>Diskon untuk tema winter wonderland yang unik</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Tropical Paradise 180 Pax',
                'slug' => 'paket-wedding-tropical-paradise-180-pax',
                'category_id' => $categories->random()->id,
                'pax' => 180,
                'stock' => 12,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Florist Wedding Specialist',
                        'quantity' => 1,
                        'description' => '<p>Tropical flower arrangements:</p><ul><li>Orchid bridal bouquet</li><li>Tropical centerpieces</li><li>Palm leaf decorations</li><li>Exotic flower arch</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Live Music Band Wedding',
                        'quantity' => 1,
                        'description' => '<p>Tropical music entertainment:</p><ul><li>Steel drum performance</li><li>Tropical jazz ensemble</li><li>Island music repertoire</li><li>Beach party soundtrack</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Dessert Table',
                        'quantity' => 1,
                        'description' => '<p>Tropical dessert paradise:</p><ul><li>Tropical fruit tarts</li><li>Coconut dessert bar</li><li>Exotic fruit display</li><li>Paradise-themed sweets</li></ul>',
                    ],
                ],
                'pengurangans' => [],
            ],
            [
                'name' => 'Paket Wedding Gatsby Glamour 300 Pax',
                'slug' => 'paket-wedding-gatsby-glamour-300-pax',
                'category_id' => $categories->random()->id,
                'pax' => 300,
                'stock' => 7,
                'is_active' => true,
                'is_approved' => false,
                'vendors' => [
                    [
                        'vendor_name' => 'Bridal Hair Accessories',
                        'quantity' => 1,
                        'description' => '<p>Gatsby bridal accessories:</p><ul><li>Art deco headpieces</li><li>Pearl hair accessories</li><li>Vintage-style tiara</li><li>1920s hair styling</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Furniture Rental',
                        'quantity' => 1,
                        'description' => '<p>Gatsby era furniture:</p><ul><li>Art deco lounge sets</li><li>Vintage cocktail tables</li><li>1920s style seating</li><li>Gold accent furniture</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Magician Entertainment',
                        'quantity' => 1,
                        'description' => '<p>Gatsby entertainment magic:</p><ul><li>1920s style magic show</li><li>Period-appropriate illusions</li><li>Vintage magic tricks</li><li>Art deco stage setup</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Gatsby Era Discount',
                        'amount' => 3000000,
                        'notes' => '<p>Diskon untuk tema glamour era 1920s</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Destination Villa 150 Pax',
                'slug' => 'paket-wedding-destination-villa-150-pax',
                'category_id' => $categories->random()->id,
                'pax' => 150,
                'stock' => 8,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Honeymoon Travel Agent',
                        'quantity' => 1,
                        'description' => '<p>Destination villa package:</p><ul><li>Private villa rental</li><li>Guest accommodation</li><li>Transportation coordination</li><li>Destination planning</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Transportation',
                        'quantity' => 1,
                        'description' => '<p>Destination transport service:</p><ul><li>Guest shuttle service</li><li>Airport transfers</li><li>Local transportation</li><li>Luxury transport options</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Live Streaming',
                        'quantity' => 1,
                        'description' => '<p>Remote wedding streaming:</p><ul><li>Live stream for distant family</li><li>Multi-platform broadcasting</li><li>International connectivity</li><li>Professional streaming setup</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Destination Wedding Discount',
                        'amount' => 4000000,
                        'notes' => '<p>Diskon untuk destination wedding package</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Fairy Tale Castle 450 Pax',
                'slug' => 'paket-wedding-fairy-tale-castle-450-pax',
                'category_id' => $categories->random()->id,
                'pax' => 450,
                'stock' => 4,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Wedding Gown Designer Couture',
                        'quantity' => 1,
                        'description' => '<p>Princess wedding gown:</p><ul><li>Ball gown style dress</li><li>Royal train design</li><li>Crystal embellishments</li><li>Princess accessories</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Fireworks Display',
                        'quantity' => 1,
                        'description' => '<p>Fairy tale fireworks show:</p><ul><li>Castle fireworks display</li><li>Magical light effects</li><li>Princess-themed pyrotechnics</li><li>Grand finale castle lighting</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Car Rental Luxury',
                        'quantity' => 1,
                        'description' => '<p>Princess carriage transport:</p><ul><li>Luxury Rolls Royce</li><li>Royal decoration</li><li>Red carpet service</li><li>Princess arrival experience</li></ul>',
                    ],
                ],
                'pengurangans' => [],
            ],
            [
                'name' => 'Paket Wedding Urban Rooftop 220 Pax',
                'slug' => 'paket-wedding-urban-rooftop-220-pax',
                'category_id' => $categories->random()->id,
                'pax' => 220,
                'stock' => 9,
                'is_active' => true,
                'is_approved' => false,
                'vendors' => [
                    [
                        'vendor_name' => 'Wedding Tent Rental',
                        'quantity' => 1,
                        'description' => '<p>Rooftop tent installation:</p><ul><li>Weather-resistant tenting</li><li>City view preservation</li><li>Wind-proof setup</li><li>Urban skyline backdrop</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Soundsystem Pro Jakarta',
                        'quantity' => 1,
                        'description' => '<p>Urban rooftop audio:</p><ul><li>Wind-resistant speakers</li><li>City noise cancellation</li><li>Rooftop acoustics</li><li>Skyline sound design</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Security Wedding Service',
                        'quantity' => 1,
                        'description' => '<p>Rooftop security service:</p><ul><li>Height safety protocols</li><li>Guest safety management</li><li>Weather emergency plans</li><li>Urban venue security</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Urban Rooftop Discount',
                        'amount' => 2200000,
                        'notes' => '<p>Diskon untuk venue rooftop dengan pemandangan kota</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Midnight Starlight 160 Pax',
                'slug' => 'paket-wedding-midnight-starlight-160-pax',
                'category_id' => $categories->random()->id,
                'pax' => 160,
                'stock' => 11,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Lighting Design Pro',
                        'quantity' => 1,
                        'description' => '<p>Starlight lighting design:</p><ul><li>LED star ceiling</li><li>Moonlight spotlighting</li><li>Constellation backdrop</li><li>Midnight ambiance lighting</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Perfume Signature',
                        'quantity' => 1,
                        'description' => '<p>Midnight scent experience:</p><ul><li>Evening fragrance blend</li><li>Starlight signature scent</li><li>Romantic night perfume</li><li>Midnight memories fragrance</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Live Music Band Wedding',
                        'quantity' => 1,
                        'description' => '<p>Midnight jazz performance:</p><ul><li>Late night jazz ensemble</li><li>Romantic midnight songs</li><li>Starlight serenade</li><li>Evening acoustic set</li></ul>',
                    ],
                ],
                'pengurangans' => [],
            ],
            [
                'name' => 'Paket Wedding Sports Club 270 Pax',
                'slug' => 'paket-wedding-sports-club-270-pax',
                'category_id' => $categories->random()->id,
                'pax' => 270,
                'stock' => 8,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Venue Ballroom Grand',
                        'quantity' => 1,
                        'description' => '<p>Sports club venue rental:</p><ul><li>Golf course ceremony</li><li>Clubhouse reception</li><li>Sports field backdrop</li><li>Athletic club facilities</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Catering Premium Bogor',
                        'quantity' => 1,
                        'description' => '<p>Sports club dining 270 pax:</p><ul><li>Athletic club menu</li><li>Sports bar setup</li><li>Healthy gourmet options</li><li>Club-style service</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Entertainment Wedding Organizer',
                        'quantity' => 1,
                        'description' => '<p>Sports themed entertainment:</p><ul><li>Sports trivia games</li><li>Athletic competition activities</li><li>Sports commentary MC</li><li>Victory celebration music</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Athletic Club Discount',
                        'amount' => 2700000,
                        'notes' => '<p>Diskon untuk member club olahraga</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Vintage Library 140 Pax',
                'slug' => 'paket-wedding-vintage-library-140-pax',
                'category_id' => $categories->random()->id,
                'pax' => 140,
                'stock' => 15,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Wedding Stationery Design',
                        'quantity' => 1,
                        'description' => '<p>Literary wedding stationery:</p><ul><li>Book-themed invitations</li><li>Library card save-the-dates</li><li>Literary quote decorations</li><li>Vintage book styling</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Calligraphy Service',
                        'quantity' => 1,
                        'description' => '<p>Literary calligraphy art:</p><ul><li>Poetry quote lettering</li><li>Book-style signage</li><li>Vintage manuscript style</li><li>Library-themed place cards</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Favor Specialist',
                        'quantity' => 1,
                        'description' => '<p>Literary wedding favors:</p><ul><li>Mini book souvenirs</li><li>Bookmark wedding favors</li><li>Literary quote cards</li><li>Vintage book-themed gifts</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Book Lover Discount',
                        'amount' => 1400000,
                        'notes' => '<p>Diskon untuk pasangan pecinta buku dan sastra</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Masquerade Ball 330 Pax',
                'slug' => 'paket-wedding-masquerade-ball-330-pax',
                'category_id' => $categories->random()->id,
                'pax' => 330,
                'stock' => 6,
                'is_active' => true,
                'is_approved' => false,
                'vendors' => [
                    [
                        'vendor_name' => 'Bridal Hair Accessories',
                        'quantity' => 1,
                        'description' => '<p>Masquerade bridal accessories:</p><ul><li>Venetian mask collection</li><li>Feathered hair pieces</li><li>Mystery-themed accessories</li><li>Elegant mask styling</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Magician Entertainment',
                        'quantity' => 1,
                        'description' => '<p>Masquerade magic show:</p><ul><li>Mystery magic performance</li><li>Venetian-style illusions</li><li>Mask-themed tricks</li><li>Elegant mystery entertainment</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Furniture Rental',
                        'quantity' => 1,
                        'description' => '<p>Masquerade ballroom setup:</p><ul><li>Venetian-style furniture</li><li>Elegant ballroom seating</li><li>Mystery-themed lounge</li><li>Masquerade ball ambiance</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Mystery Ball Discount',
                        'amount' => 3300000,
                        'notes' => '<p>Diskon untuk tema masquerade ball yang misterius</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Autumn Harvest 210 Pax',
                'slug' => 'paket-wedding-autumn-harvest-210-pax',
                'category_id' => $categories->random()->id,
                'pax' => 210,
                'stock' => 10,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Florist Wedding Specialist',
                        'quantity' => 1,
                        'description' => '<p>Autumn flower arrangements:</p><ul><li>Fall color palette</li><li>Harvest-themed bouquets</li><li>Autumn leaf decorations</li><li>Seasonal flower displays</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Dessert Table',
                        'quantity' => 1,
                        'description' => '<p>Autumn harvest desserts:</p><ul><li>Pumpkin spice treats</li><li>Apple cider desserts</li><li>Autumn fruit tarts</li><li>Harvest-themed sweets</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Balloon Decorator',
                        'quantity' => 1,
                        'description' => '<p>Autumn balloon decorations:</p><ul><li>Fall color balloons</li><li>Harvest-themed arrangements</li><li>Orange & gold balloon arch</li><li>Seasonal decoration setup</li></ul>',
                    ],
                ],
                'pengurangans' => [],
            ],
            [
                'name' => 'Paket Wedding Underwater Theme 100 Pax',
                'slug' => 'paket-wedding-underwater-theme-100-pax',
                'category_id' => $categories->random()->id,
                'pax' => 100,
                'stock' => 18,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Lighting Design Pro',
                        'quantity' => 1,
                        'description' => '<p>Underwater lighting effects:</p><ul><li>Blue ocean lighting</li><li>Underwater bubble effects</li><li>Sea-themed projections</li><li>Aquatic ambiance lighting</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Drone Wedding Video',
                        'quantity' => 1,
                        'description' => '<p>Underwater videography:</p><ul><li>Underwater camera shots</li><li>Ocean-themed video</li><li>Aquatic wedding film</li><li>Sea-themed documentation</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Favor Specialist',
                        'quantity' => 1,
                        'description' => '<p>Ocean-themed wedding favors:</p><ul><li>Seashell souvenirs</li><li>Ocean-themed gifts</li><li>Marine life favors</li><li>Underwater-themed keepsakes</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Ocean Theme Discount',
                        'amount' => 1500000,
                        'notes' => '<p>Diskon untuk tema underwater yang unik</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Space Galaxy 290 Pax',
                'slug' => 'paket-wedding-space-galaxy-290-pax',
                'category_id' => $categories->random()->id,
                'pax' => 290,
                'stock' => 7,
                'is_active' => true,
                'is_approved' => false,
                'vendors' => [
                    [
                        'vendor_name' => 'Lighting Design Pro',
                        'quantity' => 1,
                        'description' => '<p>Galaxy lighting design:</p><ul><li>Starfield ceiling projection</li><li>Cosmic lighting effects</li><li>Galaxy backdrop lighting</li><li>Space-themed ambiance</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Fireworks Display',
                        'quantity' => 1,
                        'description' => '<p>Cosmic fireworks show:</p><ul><li>Galaxy-themed pyrotechnics</li><li>Star burst effects</li><li>Cosmic light display</li><li>Space-themed finale</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Magician Entertainment',
                        'quantity' => 1,
                        'description' => '<p>Space magic entertainment:</p><ul><li>Cosmic illusion show</li><li>Space-themed magic tricks</li><li>Galaxy mystery performance</li><li>Stellar magic effects</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Galactic Wedding Discount',
                        'amount' => 2900000,
                        'notes' => '<p>Diskon untuk tema space galaxy yang futuristik</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Music Festival 400 Pax',
                'slug' => 'paket-wedding-music-festival-400-pax',
                'category_id' => $categories->random()->id,
                'pax' => 400,
                'stock' => 5,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Live Music Band Wedding',
                        'quantity' => 2,
                        'description' => '<p>Festival music lineup:</p><ul><li>Multiple band performances</li><li>Festival stage setup</li><li>Diverse music genres</li><li>Concert-style entertainment</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Soundsystem Pro Jakarta',
                        'quantity' => 1,
                        'description' => '<p>Festival sound system:</p><ul><li>Concert-grade speakers</li><li>Multi-stage audio</li><li>Professional mixing</li><li>Festival sound experience</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Tent Rental',
                        'quantity' => 1,
                        'description' => '<p>Festival venue setup:</p><ul><li>Large festival tents</li><li>Multiple venue areas</li><li>Stage canopy setup</li><li>Festival ground layout</li></ul>',
                    ],
                ],
                'pengurangans' => [],
            ],
            [
                'name' => 'Paket Wedding Zen Garden 170 Pax',
                'slug' => 'paket-wedding-zen-garden-170-pax',
                'category_id' => $categories->random()->id,
                'pax' => 170,
                'stock' => 13,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Wedding Venue Garden',
                        'quantity' => 1,
                        'description' => '<p>Zen garden ceremony space:</p><ul><li>Peaceful garden setting</li><li>Meditation area setup</li><li>Tranquil water features</li><li>Zen garden landscaping</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Bridal Spa Treatment',
                        'quantity' => 1,
                        'description' => '<p>Zen bridal wellness:</p><ul><li>Meditation spa treatment</li><li>Zen relaxation therapy</li><li>Mindfulness preparation</li><li>Peaceful bridal experience</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Traditional Dance Performance',
                        'quantity' => 1,
                        'description' => '<p>Zen cultural performance:</p><ul><li>Meditative dance</li><li>Peaceful music ensemble</li><li>Traditional zen ceremony</li><li>Cultural mindfulness display</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Zen Mindfulness Discount',
                        'amount' => 1700000,
                        'notes' => '<p>Diskon untuk wedding zen yang menenangkan</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Renaissance Fair 260 Pax',
                'slug' => 'paket-wedding-renaissance-fair-260-pax',
                'category_id' => $categories->random()->id,
                'pax' => 260,
                'stock' => 8,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Wedding Gown Designer Couture',
                        'quantity' => 1,
                        'description' => '<p>Renaissance period gown:</p><ul><li>Medieval-style wedding dress</li><li>Period-accurate design</li><li>Renaissance accessories</li><li>Historical bridal wear</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Traditional Dance Performance',
                        'quantity' => 1,
                        'description' => '<p>Renaissance entertainment:</p><ul><li>Medieval court dancing</li><li>Renaissance music ensemble</li><li>Period costume performance</li><li>Historical entertainment</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Favor Specialist',
                        'quantity' => 1,
                        'description' => '<p>Renaissance wedding favors:</p><ul><li>Medieval-themed souvenirs</li><li>Renaissance craft gifts</li><li>Period-style keepsakes</li><li>Historical wedding favors</li></ul>',
                    ],
                ],
                'pengurangans' => [],
            ],
            [
                'name' => 'Paket Wedding Circus Carnival 230 Pax',
                'slug' => 'paket-wedding-circus-carnival-230-pax',
                'category_id' => $categories->random()->id,
                'pax' => 230,
                'stock' => 9,
                'is_active' => true,
                'is_approved' => false,
                'vendors' => [
                    [
                        'vendor_name' => 'Wedding Magician Entertainment',
                        'quantity' => 1,
                        'description' => '<p>Circus magic show:</p><ul><li>Carnival magic performance</li><li>Circus-themed illusions</li><li>Fun interactive magic</li><li>Carnival entertainment</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Balloon Decorator',
                        'quantity' => 1,
                        'description' => '<p>Carnival balloon decorations:</p><ul><li>Circus balloon arch</li><li>Carnival color schemes</li><li>Fun balloon animals</li><li>Festive carnival setup</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Dessert Table',
                        'quantity' => 1,
                        'description' => '<p>Carnival dessert experience:</p><ul><li>Cotton candy station</li><li>Carnival popcorn bar</li><li>Fun fair treats</li><li>Circus-themed sweets</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Carnival Fun Discount',
                        'amount' => 2300000,
                        'notes' => '<p>Diskon untuk tema carnival yang menyenangkan</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Safari Adventure 180 Pax',
                'slug' => 'paket-wedding-safari-adventure-180-pax',
                'category_id' => $categories->random()->id,
                'pax' => 180,
                'stock' => 11,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Outdoor Wedding Specialist',
                        'quantity' => 1,
                        'description' => '<p>Safari adventure setup:</p><ul><li>Wildlife venue setting</li><li>Safari-themed decorations</li><li>Adventure ceremony space</li><li>Nature expedition experience</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Transportation',
                        'quantity' => 1,
                        'description' => '<p>Safari transport experience:</p><ul><li>Safari vehicle transport</li><li>Adventure tour coordination</li><li>Wildlife viewing transport</li><li>Nature expedition service</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Drone Wedding Video',
                        'quantity' => 1,
                        'description' => '<p>Safari aerial documentation:</p><ul><li>Wildlife drone footage</li><li>Safari adventure video</li><li>Nature expedition film</li><li>Adventure wedding story</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Adventure Safari Discount',
                        'amount' => 1800000,
                        'notes' => '<p>Diskon untuk tema safari yang adventurous</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Steampunk Victorian 250 Pax',
                'slug' => 'paket-wedding-steampunk-victorian-250-pax',
                'category_id' => $categories->random()->id,
                'pax' => 250,
                'stock' => 8,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Wedding Gown Designer Couture',
                        'quantity' => 1,
                        'description' => '<p>Steampunk Victorian gown:</p><ul><li>Victorian-era inspired dress</li><li>Steampunk accessories</li><li>Industrial design elements</li><li>Vintage-modern fusion</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Furniture Rental',
                        'quantity' => 1,
                        'description' => '<p>Steampunk furniture setup:</p><ul><li>Industrial Victorian furniture</li><li>Copper and brass accents</li><li>Vintage machinery decor</li><li>Steampunk lounge areas</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Magician Entertainment',
                        'quantity' => 1,
                        'description' => '<p>Steampunk magic show:</p><ul><li>Industrial illusion performance</li><li>Victorian magic tricks</li><li>Steampunk-themed entertainment</li><li>Mechanical magic effects</li></ul>',
                    ],
                ],
                'pengurangans' => [],
            ],
            [
                'name' => 'Paket Wedding Japanese Zen 190 Pax',
                'slug' => 'paket-wedding-japanese-zen-190-pax',
                'category_id' => $categories->random()->id,
                'pax' => 190,
                'stock' => 10,
                'is_active' => true,
                'is_approved' => false,
                'vendors' => [
                    [
                        'vendor_name' => 'Traditional Kebaya Designer',
                        'quantity' => 1,
                        'description' => '<p>Japanese kimono styling:</p><ul><li>Traditional kimono rental</li><li>Japanese hair styling</li><li>Authentic accessories</li><li>Cultural costume coordination</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Traditional Dance Performance',
                        'quantity' => 1,
                        'description' => '<p>Japanese cultural performance:</p><ul><li>Traditional Japanese dance</li><li>Tea ceremony demonstration</li><li>Cultural music ensemble</li><li>Zen meditation session</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Calligraphy Service',
                        'quantity' => 1,
                        'description' => '<p>Japanese calligraphy art:</p><ul><li>Shodo wedding signage</li><li>Japanese character art</li><li>Cultural quote lettering</li><li>Traditional brush calligraphy</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Japanese Cultural Discount',
                        'amount' => 1900000,
                        'notes' => '<p>Diskon untuk tema Japanese zen yang autentik</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Desert Oasis 140 Pax',
                'slug' => 'paket-wedding-desert-oasis-140-pax',
                'category_id' => $categories->random()->id,
                'pax' => 140,
                'stock' => 12,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Outdoor Wedding Specialist',
                        'quantity' => 1,
                        'description' => '<p>Desert oasis ceremony:</p><ul><li>Desert landscape venue</li><li>Oasis-themed decorations</li><li>Sand dune ceremony space</li><li>Desert sunset experience</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Henna Artist Traditional',
                        'quantity' => 1,
                        'description' => '<p>Desert henna art:</p><ul><li>Arabic henna designs</li><li>Desert-inspired patterns</li><li>Traditional Middle Eastern art</li><li>Bridal henna ceremony</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Catering Premium Bogor',
                        'quantity' => 1,
                        'description' => '<p>Desert oasis dining:</p><ul><li>Middle Eastern cuisine</li><li>Desert-themed menu</li><li>Bedouin-style service</li><li>Oasis dining experience</li></ul>',
                    ],
                ],
                'pengurangans' => [],
            ],
            [
                'name' => 'Paket Wedding Movie Premier 310 Pax',
                'slug' => 'paket-wedding-movie-premier-310-pax',
                'category_id' => $categories->random()->id,
                'pax' => 310,
                'stock' => 6,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Wedding Videographer Cinematic',
                        'quantity' => 1,
                        'description' => '<p>Movie premier documentation:</p><ul><li>Red carpet videography</li><li>Hollywood-style filming</li><li>Premier event coverage</li><li>Cinematic wedding movie</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Photo Booth Rental',
                        'quantity' => 1,
                        'description' => '<p>Hollywood photo experience:</p><ul><li>Red carpet photo booth</li><li>Movie-themed props</li><li>Premier photo backdrop</li><li>Celebrity-style photos</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Car Rental Luxury',
                        'quantity' => 1,
                        'description' => '<p>Hollywood arrival experience:</p><ul><li>Luxury limousine service</li><li>Red carpet arrival</li><li>Paparazzi-style entrance</li><li>Movie star treatment</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Hollywood Premier Discount',
                        'amount' => 3100000,
                        'notes' => '<p>Diskon untuk tema movie premier yang glamour</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Arctic Winter 160 Pax',
                'slug' => 'paket-wedding-arctic-winter-160-pax',
                'category_id' => $categories->random()->id,
                'pax' => 160,
                'stock' => 12,
                'is_active' => true,
                'is_approved' => true,
                'vendors' => [
                    [
                        'vendor_name' => 'Ice Sculpture Art',
                        'quantity' => 2,
                        'description' => '<p>Arctic ice installations:</p><ul><li>Large ice sculpture displays</li><li>Arctic-themed ice art</li><li>Frozen wedding decorations</li><li>Winter wonderland ice setup</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Lighting Design Pro',
                        'quantity' => 1,
                        'description' => '<p>Arctic lighting effects:</p><ul><li>Aurora borealis lighting</li><li>Ice blue illumination</li><li>Winter crystal effects</li><li>Frozen palace ambiance</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Perfume Signature',
                        'quantity' => 1,
                        'description' => '<p>Winter signature scent:</p><ul><li>Cool winter fragrance</li><li>Arctic-inspired perfume</li><li>Crisp mountain air scent</li><li>Frozen memory fragrance</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Arctic Theme Discount',
                        'amount' => 1600000,
                        'notes' => '<p>Diskon untuk tema arctic winter yang unik</p>',
                    ],
                ],
            ],
            [
                'name' => 'Paket Wedding Cosmic Constellation 280 Pax',
                'slug' => 'paket-wedding-cosmic-constellation-280-pax',
                'category_id' => $categories->random()->id,
                'pax' => 280,
                'stock' => 7,
                'is_active' => true,
                'is_approved' => false,
                'vendors' => [
                    [
                        'vendor_name' => 'Lighting Design Pro',
                        'quantity' => 1,
                        'description' => '<p>Constellation lighting design:</p><ul><li>Star map ceiling projection</li><li>Constellation pattern lighting</li><li>Cosmic dance floor</li><li>Galaxy wedding ambiance</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Live Streaming',
                        'quantity' => 1,
                        'description' => '<p>Cosmic live streaming:</p><ul><li>Multi-planet broadcasting</li><li>Space-themed stream overlay</li><li>Cosmic wedding coverage</li><li>Universal love streaming</li></ul>',
                    ],
                    [
                        'vendor_name' => 'Wedding Favor Specialist',
                        'quantity' => 1,
                        'description' => '<p>Cosmic wedding favors:</p><ul><li>Star-themed souvenirs</li><li>Constellation gift items</li><li>Space-themed keepsakes</li><li>Cosmic memory gifts</li></ul>',
                    ],
                ],
                'pengurangans' => [
                    [
                        'description' => 'Cosmic Love Discount',
                        'amount' => 2800000,
                        'notes' => '<p>Diskon untuk cinta yang melampaui galaksi</p>',
                    ],
                ],
            ],
        ];

        foreach ($products as $productData) {
            // Create main product
            $product = Product::firstOrCreate(
                [
                    'name' => $productData['name'],
                    'slug' => $productData['slug'],
                ],
                [
                    'category_id' => $productData['category_id'],
                    'pax' => $productData['pax'],
                    'stock' => $productData['stock'],
                    'is_active' => $productData['is_active'],
                    'is_approved' => $productData['is_approved'],
                    'product_price' => 0, // Will be calculated
                    'pengurangan' => 0,   // Will be calculated
                    'price' => 0,         // Will be calculated
                ]
            );

            // Add vendors to product
            $totalProductPrice = 0;
            foreach ($productData['vendors'] as $vendorData) {
                $vendor = Vendor::where('name', $vendorData['vendor_name'])->first();
                if ($vendor) {
                    $pricePublic = $vendor->harga_publish * $vendorData['quantity'];
                    $totalProductPrice += $pricePublic;

                    ProductVendor::firstOrCreate(
                        [
                            'product_id' => $product->id,
                            'vendor_id' => $vendor->id,
                        ],
                        [
                            'harga_publish' => $vendor->harga_publish,
                            'quantity' => $vendorData['quantity'],
                            'price_public' => $pricePublic,
                            'total_price' => $pricePublic, // Individual item total
                            'harga_vendor' => $vendor->harga_vendor,
                            'description' => $vendorData['description'],
                        ]
                    );
                }
            }

            // Add pengurangan (discounts) to product
            $totalPengurangan = 0;
            foreach ($productData['pengurangans'] as $penguranganData) {
                $totalPengurangan += $penguranganData['amount'];

                ProductPengurangan::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'description' => $penguranganData['description'],
                    ],
                    [
                        'amount' => $penguranganData['amount'],
                        'notes' => $penguranganData['notes'],
                    ]
                );
            }

            // Update product totals
            $finalPrice = $totalProductPrice - $totalPengurangan;
            $product->update([
                'product_price' => $totalProductPrice,
                'pengurangan' => $totalPengurangan,
                'price' => $finalPrice,
            ]);
        }

        $this->command->info('50 products with vendors and discounts created successfully!');
    }
}
