<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Prospect;
use App\Models\SimulasiProduk;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SimulasiProdukSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get existing data
        $prospects = Prospect::all();
        $products = Product::all();
        $users = User::all();

        if ($prospects->isEmpty()) {
            $this->command->error('No prospects found. Please run ProspectSeeder first.');

            return;
        }

        if ($products->isEmpty()) {
            $this->command->error('No products found. Please run ProductSeeder first.');

            return;
        }

        if ($users->isEmpty()) {
            $this->command->error('No users found. Please run DatabaseSeeder first.');

            return;
        }

        $this->command->info("Found {$prospects->count()} prospects and {$products->count()} products.");

        // If we have fewer prospects/products than simulations, we'll create fallback simulations
        $maxSimulations = min(50, $prospects->count() * $products->count());
        $this->command->info("Will create up to {$maxSimulations} simulations based on available data.");

        $simulasiData = [
            [
                'prospect_name' => 'Wedding Andi & Sari',
                'product_name' => 'Paket Wedding Mewah Jakarta 300 Pax',
                'notes' => '<p>Simulasi untuk wedding mewah dengan tema elegant classic. Client menginginkan dekorasi yang sangat mewah dengan budget yang memadai.</p><p><strong>Catatan khusus:</strong></p><ul><li>Venue di hotel 5 bintang</li><li>Guest VIP sekitar 50 orang</li><li>Preferensi warna gold dan putih</li><li>Budget fleksibel untuk upgrade</li></ul>',
                'promo' => 1000000,
                'penambahan' => 2500000,
                'pengurangan' => 500000,
            ],
            [
                'prospect_name' => 'Wedding Budi & Maya',
                'product_name' => 'Paket Wedding Garden Party Outdoor 200 Pax',
                'notes' => '<p>Simulasi untuk wedding outdoor dengan konsep garden party yang fresh dan natural.</p><p><strong>Request khusus:</strong></p><ul><li>Venue outdoor dengan backup indoor</li><li>Dekorasi natural dengan bunga segar</li><li>Menu BBQ dan healthy food</li><li>Aktivitas outdoor games</li></ul>',
                'promo' => 0,
                'penambahan' => 1500000,
                'pengurangan' => 0,
            ],
            [
                'prospect_name' => 'Wedding Dedi & Rina',
                'product_name' => 'Paket Wedding Traditional Jawa 400 Pax',
                'notes' => '<p>Simulasi untuk wedding adat Jawa lengkap dengan semua tradisi dan ritual yang diperlukan.</p><p><strong>Kebutuhan adat:</strong></p><ul><li>Upacara siraman dan midodareni</li><li>Gamelan lengkap untuk prosesi</li><li>Tata rias pengantin adat Jawa</li><li>Catering menu tradisional Jawa</li></ul>',
                'promo' => 1500000,
                'penambahan' => 3000000,
                'pengurangan' => 1000000,
            ],
            [
                'prospect_name' => 'Wedding Eko & Fitri',
                'product_name' => 'Paket Wedding Intimate 150 Pax',
                'notes' => '<p>Simulasi untuk intimate wedding dengan konsep cozy dan personal. Lebih fokus pada kualitas daripada kuantitas.</p><p><strong>Konsep intimate:</strong></p><ul><li>Guest terbatas hanya keluarga dan sahabat dekat</li><li>Venue yang cozy dan private</li><li>Personalized decoration</li><li>Interactive entertainment</li></ul>',
                'promo' => 500000,
                'penambahan' => 1000000,
                'pengurangan' => 200000,
            ],
            [
                'prospect_name' => 'Wedding Fajar & Indira',
                'product_name' => 'Paket Wedding Modern Minimalist 250 Pax',
                'notes' => '<p>Simulasi untuk wedding modern dengan konsep minimalist yang sophisticated dan trendy.</p><p><strong>Style modern:</strong></p><ul><li>Clean design dengan line yang tegas</li><li>Color palette monochrome</li><li>Technology integration (LED, projection mapping)</li><li>Modern cuisine dengan presentation menarik</li></ul>',
                'promo' => 2000000,
                'penambahan' => 4000000,
                'pengurangan' => 800000,
            ],
            [
                'prospect_name' => 'Wedding Gita & Reza',
                'product_name' => 'Paket Wedding Beach Resort 180 Pax',
                'notes' => '<p>Simulasi beach wedding dengan sunset ceremony dan ocean view reception.</p><p><strong>Beach concept:</strong></p><ul><li>Sunset ceremony timing</li><li>Barefoot beach walking</li><li>Seafood buffet dinner</li><li>Beach bonfire after party</li></ul>',
                'promo' => 800000,
                'penambahan' => 2200000,
                'pengurangan' => 400000,
            ],
            [
                'prospect_name' => 'Wedding Hendra & Lina',
                'product_name' => 'Paket Wedding Grand Ballroom 500 Pax',
                'notes' => '<p>Simulasi grand wedding dengan scale besar dan kemewahan maksimal.</p><p><strong>Grand celebration:</strong></p><ul><li>International guest list</li><li>Multi-course fine dining</li><li>Celebrity entertainment</li><li>Luxury transportation fleet</li></ul>',
                'promo' => 3000000,
                'penambahan' => 8000000,
                'pengurangan' => 1500000,
            ],
            [
                'prospect_name' => 'Wedding Irfan & Dewi',
                'product_name' => 'Paket Wedding Rustic Vintage 220 Pax',
                'notes' => '<p>Simulasi vintage wedding dengan dekorasi antique dan atmosphere nostalgic.</p><p><strong>Vintage elements:</strong></p><ul><li>Antique furniture collection</li><li>Vintage photo booth props</li><li>Classic car rental</li><li>Retro music playlist</li></ul>',
                'promo' => 1200000,
                'penambahan' => 1800000,
                'pengurangan' => 600000,
            ],
            [
                'prospect_name' => 'Wedding Joko & Nina',
                'product_name' => 'Paket Wedding Corporate Style 350 Pax',
                'notes' => '<p>Simulasi corporate wedding dengan format business elegant dan professional touch.</p><p><strong>Corporate style:</strong></p><ul><li>Business networking session</li><li>Professional presentation setup</li><li>Corporate gift exchange</li><li>Formal dining protocol</li></ul>',
                'promo' => 2500000,
                'penambahan' => 5000000,
                'pengurangan' => 1200000,
            ],
            [
                'prospect_name' => 'Wedding Kevin & Olivia',
                'product_name' => 'Paket Wedding Bohemian Chic 160 Pax',
                'notes' => '<p>Simulasi boho wedding dengan free spirit vibe dan natural beauty.</p><p><strong>Boho elements:</strong></p><ul><li>Macrame decoration wall</li><li>Flower crown for bridesmaids</li><li>Acoustic live music</li><li>Organic food menu</li></ul>',
                'promo' => 600000,
                'penambahan' => 1400000,
                'pengurangan' => 300000,
            ],
            [
                'prospect_name' => 'Wedding Lucky & Putri',
                'product_name' => 'Paket Wedding Royal Palace 600 Pax',
                'notes' => '<p>Simulasi royal wedding dengan kemewahan palace dan royal treatment.</p><p><strong>Royal experience:</strong></p><ul><li>Royal protocol ceremony</li><li>Palace-style decoration</li><li>Royal feast dining</li><li>Crown and scepter accessories</li></ul>',
                'promo' => 5000000,
                'penambahan' => 12000000,
                'pengurangan' => 2500000,
            ],
            [
                'prospect_name' => 'Wedding Mario & Sinta',
                'product_name' => 'Paket Wedding Garden Tea Party 120 Pax',
                'notes' => '<p>Simulasi tea party wedding dengan English garden atmosphere dan afternoon tea tradition.</p><p><strong>Tea party concept:</strong></p><ul><li>English garden setting</li><li>Traditional afternoon tea service</li><li>Garden party games</li><li>Vintage china collection</li></ul>',
                'promo' => 400000,
                'penambahan' => 800000,
                'pengurangan' => 200000,
            ],
            [
                'prospect_name' => 'Wedding Nanda & Tari',
                'product_name' => 'Paket Wedding Industrial Loft 280 Pax',
                'notes' => '<p>Simulasi industrial wedding dengan urban vibe dan modern industrial decoration.</p><p><strong>Industrial concept:</strong></p><ul><li>Exposed brick wall backdrop</li><li>Edison bulb lighting</li><li>Urban street food menu</li><li>DJ electronic music</li></ul>',
                'promo' => 1800000,
                'penambahan' => 3500000,
                'pengurangan' => 900000,
            ],
            [
                'prospect_name' => 'Wedding Omar & Wulan',
                'product_name' => 'Paket Wedding Cultural Fusion 380 Pax',
                'notes' => '<p>Simulasi fusion wedding menggabungkan budaya Indonesia dan internasional.</p><p><strong>Cultural fusion:</strong></p><ul><li>Multi-cultural ceremony</li><li>International cuisine fusion</li><li>Traditional and modern dance</li><li>Bilingual wedding program</li></ul>',
                'promo' => 2200000,
                'penambahan' => 4500000,
                'pengurangan' => 1100000,
            ],
            [
                'prospect_name' => 'Wedding Panji & Qori',
                'product_name' => 'Paket Wedding Eco Green 190 Pax',
                'notes' => '<p>Simulasi eco-friendly wedding dengan sustainable concept dan green living theme.</p><p><strong>Eco concept:</strong></p><ul><li>Zero waste wedding planning</li><li>Organic local food menu</li><li>Potted plants as centerpieces</li><li>Digital invitation only</li></ul>',
                'promo' => 700000,
                'penambahan' => 1600000,
                'pengurangan' => 350000,
            ],
            [
                'prospect_name' => 'Wedding Raffi & Sarah',
                'product_name' => 'Paket Wedding Luxury Yacht 80 Pax',
                'notes' => '<p>Simulasi exclusive yacht wedding dengan ocean ceremony dan luxury maritime experience.</p><p><strong>Yacht luxury:</strong></p><ul><li>Private yacht charter</li><li>Ocean sunset ceremony</li><li>Luxury seafood dining</li><li>Professional yacht crew</li></ul>',
                'promo' => 1500000,
                'penambahan' => 6000000,
                'pengurangan' => 800000,
            ],
            [
                'prospect_name' => 'Wedding Tama & Umi',
                'product_name' => 'Paket Wedding Mountain Resort 130 Pax',
                'notes' => '<p>Simulasi mountain wedding dengan fresh air ceremony dan scenic mountain view.</p><p><strong>Mountain experience:</strong></p><ul><li>Pine forest ceremony</li><li>Mountain hiking pre-wedding</li><li>Campfire evening reception</li><li>Fresh mountain air ambiance</li></ul>',
                'promo' => 600000,
                'penambahan' => 1800000,
                'pengurangan' => 300000,
            ],
            [
                'prospect_name' => 'Wedding Vino & Yuli',
                'product_name' => 'Paket Wedding Art Gallery 240 Pax',
                'notes' => '<p>Simulasi art gallery wedding dengan artistic decoration dan cultural appreciation.</p><p><strong>Art gallery concept:</strong></p><ul><li>Contemporary art exhibition</li><li>Artistic photo installation</li><li>Creative catering presentation</li><li>Artist performance showcase</li></ul>',
                'promo' => 1000000,
                'penambahan' => 2800000,
                'pengurangan' => 500000,
            ],
            [
                'prospect_name' => 'Wedding Wahyu & Zara',
                'product_name' => 'Paket Wedding Historical Heritage 320 Pax',
                'notes' => '<p>Simulasi heritage wedding dengan historical venue dan cultural preservation theme.</p><p><strong>Heritage concept:</strong></p><ul><li>Historical venue setting</li><li>Traditional costume ceremony</li><li>Cultural music performance</li><li>Heritage food presentation</li></ul>',
                'promo' => 1800000,
                'penambahan' => 4200000,
                'pengurangan' => 900000,
            ],
            [
                'prospect_name' => 'Wedding Alex & Bella',
                'product_name' => 'Paket Wedding Winter Wonderland 200 Pax',
                'notes' => '<p>Simulasi winter theme wedding dengan ice decoration dan snow-white atmosphere.</p><p><strong>Winter wonderland:</strong></p><ul><li>Ice sculpture centerpieces</li><li>Winter white decoration</li><li>Warm comfort food menu</li><li>Snowflake lighting effects</li></ul>',
                'promo' => 900000,
                'penambahan' => 2400000,
                'pengurangan' => 450000,
            ],
            [
                'prospect_name' => 'Wedding Charlie & Diana',
                'product_name' => 'Paket Wedding Tropical Paradise 180 Pax',
                'notes' => '<p>Simulasi tropical wedding dengan exotic flowers dan island paradise vibe.</p><p><strong>Tropical paradise:</strong></p><ul><li>Exotic tropical flowers</li><li>Island-style decoration</li><li>Tropical fruit buffet</li><li>Steel drum music entertainment</li></ul>',
                'promo' => 800000,
                'penambahan' => 2000000,
                'pengurangan' => 400000,
            ],
            [
                'prospect_name' => 'Wedding Edward & Fiona',
                'product_name' => 'Paket Wedding Gatsby Glamour 300 Pax',
                'notes' => '<p>Simulasi 1920s Gatsby wedding dengan art deco style dan glamorous atmosphere.</p><p><strong>Gatsby glamour:</strong></p><ul><li>Art deco decoration</li><li>1920s costume theme</li><li>Jazz music entertainment</li><li>Champagne tower ceremony</li></ul>',
                'promo' => 1500000,
                'penambahan' => 3800000,
                'pengurangan' => 750000,
            ],
            [
                'prospect_name' => 'Wedding George & Helen',
                'product_name' => 'Paket Wedding Destination Villa 150 Pax',
                'notes' => '<p>Simulasi destination wedding dengan private villa dan exclusive guest experience.</p><p><strong>Destination experience:</strong></p><ul><li>Private villa rental</li><li>Guest accommodation package</li><li>Local cultural experience</li><li>Destination adventure tour</li></ul>',
                'promo' => 2000000,
                'penambahan' => 5500000,
                'pengurangan' => 1000000,
            ],
            [
                'prospect_name' => 'Wedding Ivan & Julia',
                'product_name' => 'Paket Wedding Fairy Tale Castle 450 Pax',
                'notes' => '<p>Simulasi fairy tale wedding dengan princess theme dan magical castle atmosphere.</p><p><strong>Fairy tale magic:</strong></p><ul><li>Princess ball gown styling</li><li>Castle decoration setup</li><li>Magical fireworks display</li><li>Royal carriage entrance</li></ul>',
                'promo' => 2500000,
                'penambahan' => 7000000,
                'pengurangan' => 1250000,
            ],
            [
                'prospect_name' => 'Wedding Kelvin & Luna',
                'product_name' => 'Paket Wedding Urban Rooftop 220 Pax',
                'notes' => '<p>Simulasi urban rooftop wedding dengan city skyline view dan metropolitan atmosphere.</p><p><strong>Urban rooftop:</strong></p><ul><li>City skyline backdrop</li><li>Rooftop garden decoration</li><li>Urban cuisine menu</li><li>Sunset city view ceremony</li></ul>',
                'promo' => 1100000,
                'penambahan' => 2600000,
                'pengurangan' => 550000,
            ],
            [
                'prospect_name' => 'Wedding Michael & Nova',
                'product_name' => 'Paket Wedding Midnight Starlight 160 Pax',
                'notes' => '<p>Simulasi midnight wedding dengan starlight decoration dan romantic evening atmosphere.</p><p><strong>Midnight starlight:</strong></p><ul><li>LED star ceiling installation</li><li>Midnight jazz performance</li><li>Romantic evening menu</li><li>Starlight photography session</li></ul>',
                'promo' => 700000,
                'penambahan' => 1900000,
                'pengurangan' => 350000,
            ],
            [
                'prospect_name' => 'Wedding Nathan & Olivia',
                'product_name' => 'Paket Wedding Sports Club 270 Pax',
                'notes' => '<p>Simulasi sports club wedding dengan athletic theme dan sports facility venue.</p><p><strong>Sports club theme:</strong></p><ul><li>Golf course ceremony</li><li>Sports club dining</li><li>Athletic competition games</li><li>Sports memorabilia decoration</li></ul>',
                'promo' => 1300000,
                'penambahan' => 3200000,
                'pengurangan' => 650000,
            ],
            [
                'prospect_name' => 'Wedding Paul & Queen',
                'product_name' => 'Paket Wedding Vintage Library 140 Pax',
                'notes' => '<p>Simulasi library wedding dengan literary theme dan vintage book decoration.</p><p><strong>Literary vintage:</strong></p><ul><li>Vintage book decoration</li><li>Library venue setting</li><li>Literary quote signage</li><li>Book-themed wedding favors</li></ul>',
                'promo' => 500000,
                'penambahan' => 1300000,
                'pengurangan' => 250000,
            ],
            [
                'prospect_name' => 'Wedding Rio & Stella',
                'product_name' => 'Paket Wedding Masquerade Ball 330 Pax',
                'notes' => '<p>Simulasi masquerade wedding dengan venetian masks dan mystery ball atmosphere.</p><p><strong>Masquerade mystery:</strong></p><ul><li>Venetian mask collection</li><li>Mystery ball decoration</li><li>Elegant ballroom setup</li><li>Masquerade entertainment</li></ul>',
                'promo' => 1600000,
                'penambahan' => 4000000,
                'pengurangan' => 800000,
            ],
            [
                'prospect_name' => 'Wedding Samuel & Tina',
                'product_name' => 'Paket Wedding Autumn Harvest 210 Pax',
                'notes' => '<p>Simulasi autumn wedding dengan harvest theme dan fall season decoration.</p><p><strong>Autumn harvest:</strong></p><ul><li>Fall color decoration</li><li>Harvest food menu</li><li>Autumn leaf arrangements</li><li>Seasonal fruit display</li></ul>',
                'promo' => 800000,
                'penambahan' => 2100000,
                'pengurangan' => 400000,
            ],
            [
                'prospect_name' => 'Wedding Tommy & Vina',
                'product_name' => 'Paket Wedding Underwater Theme 100 Pax',
                'notes' => '<p>Simulasi underwater theme wedding dengan aquatic decoration dan ocean-inspired atmosphere.</p><p><strong>Underwater theme:</strong></p><ul><li>Aquatic blue lighting</li><li>Ocean-themed decoration</li><li>Seafood specialty menu</li><li>Underwater photography</li></ul>',
                'promo' => 600000,
                'penambahan' => 1500000,
                'pengurangan' => 300000,
            ],
            [
                'prospect_name' => 'Wedding Umar & Winda',
                'product_name' => 'Paket Wedding Space Galaxy 290 Pax',
                'notes' => '<p>Simulasi space theme wedding dengan galaxy decoration dan cosmic atmosphere.</p><p><strong>Space galaxy:</strong></p><ul><li>Galaxy ceiling projection</li><li>Cosmic lighting effects</li><li>Space-themed entertainment</li><li>Galactic fireworks finale</li></ul>',
                'promo' => 1400000,
                'penambahan' => 3600000,
                'pengurangan' => 700000,
            ],
            [
                'prospect_name' => 'Wedding Victor & Yolanda',
                'product_name' => 'Paket Wedding Music Festival 400 Pax',
                'notes' => '<p>Simulasi music festival wedding dengan concert-style setup dan multiple band performance.</p><p><strong>Music festival:</strong></p><ul><li>Multiple band lineup</li><li>Festival stage setup</li><li>Concert-grade sound system</li><li>Festival ground atmosphere</li></ul>',
                'promo' => 2000000,
                'penambahan' => 5000000,
                'pengurangan' => 1000000,
            ],
            [
                'prospect_name' => 'Wedding William & Zelda',
                'product_name' => 'Paket Wedding Zen Garden 170 Pax',
                'notes' => '<p>Simulasi zen garden wedding dengan peaceful atmosphere dan mindfulness concept.</p><p><strong>Zen tranquility:</strong></p><ul><li>Zen garden venue</li><li>Meditation ceremony</li><li>Peaceful music ensemble</li><li>Mindfulness wedding experience</li></ul>',
                'promo' => 700000,
                'penambahan' => 1800000,
                'pengurangan' => 350000,
            ],
            [
                'prospect_name' => 'Wedding Adrian & Bella',
                'product_name' => 'Paket Wedding Renaissance Fair 260 Pax',
                'notes' => '<p>Simulasi renaissance wedding dengan medieval theme dan historical period atmosphere.</p><p><strong>Renaissance period:</strong></p><ul><li>Medieval costume theme</li><li>Renaissance music ensemble</li><li>Historical venue decoration</li><li>Period-accurate ceremony</li></ul>',
                'promo' => 1200000,
                'penambahan' => 3000000,
                'pengurangan' => 600000,
            ],
            [
                'prospect_name' => 'Wedding Bryan & Cinta',
                'product_name' => 'Paket Wedding Circus Carnival 230 Pax',
                'notes' => '<p>Simulasi circus carnival wedding dengan fun atmosphere dan colorful entertainment.</p><p><strong>Circus carnival:</strong></p><ul><li>Colorful carnival decoration</li><li>Circus entertainment show</li><li>Carnival food stations</li><li>Fun fair activities</li></ul>',
                'promo' => 1000000,
                'penambahan' => 2500000,
                'pengurangan' => 500000,
            ],
            [
                'prospect_name' => 'Wedding David & Eva',
                'product_name' => 'Paket Wedding Safari Adventure 180 Pax',
                'notes' => '<p>Simulasi safari wedding dengan wildlife theme dan adventure experience.</p><p><strong>Safari adventure:</strong></p><ul><li>Wildlife venue setting</li><li>Safari vehicle transport</li><li>Adventure tour experience</li><li>Nature documentation</li></ul>',
                'promo' => 800000,
                'penambahan' => 2200000,
                'pengurangan' => 400000,
            ],
            [
                'prospect_name' => 'Wedding Felix & Grace',
                'product_name' => 'Paket Wedding Steampunk Victorian 250 Pax',
                'notes' => '<p>Simulasi steampunk wedding dengan industrial Victorian theme dan unique atmosphere.</p><p><strong>Steampunk Victorian:</strong></p><ul><li>Industrial Victorian decoration</li><li>Steampunk costume theme</li><li>Mechanical entertainment</li><li>Vintage-modern fusion</li></ul>',
                'promo' => 1200000,
                'penambahan' => 3100000,
                'pengurangan' => 600000,
            ],
            [
                'prospect_name' => 'Wedding Harry & Isabella',
                'product_name' => 'Paket Wedding Japanese Zen 190 Pax',
                'notes' => '<p>Simulasi Japanese zen wedding dengan authentic cultural ceremony dan traditional atmosphere.</p><p><strong>Japanese zen:</strong></p><ul><li>Traditional kimono ceremony</li><li>Japanese tea ceremony</li><li>Zen meditation session</li><li>Authentic cultural performance</li></ul>',
                'promo' => 900000,
                'penambahan' => 2300000,
                'pengurangan' => 450000,
            ],
            [
                'prospect_name' => 'Wedding Jacob & Karina',
                'product_name' => 'Paket Wedding Desert Oasis 140 Pax',
                'notes' => '<p>Simulasi desert oasis wedding dengan Middle Eastern theme dan exotic atmosphere.</p><p><strong>Desert oasis:</strong></p><ul><li>Desert landscape venue</li><li>Middle Eastern cuisine</li><li>Traditional henna ceremony</li><li>Bedouin-style decoration</li></ul>',
                'promo' => 600000,
                'penambahan' => 1700000,
                'pengurangan' => 300000,
            ],
            [
                'prospect_name' => 'Wedding Leonardo & Maria',
                'product_name' => 'Paket Wedding Movie Premier 310 Pax',
                'notes' => '<p>Simulasi movie premier wedding dengan Hollywood theme dan red carpet experience.</p><p><strong>Hollywood premier:</strong></p><ul><li>Red carpet entrance</li><li>Celebrity-style treatment</li><li>Hollywood photo experience</li><li>Movie premier atmosphere</li></ul>',
                'promo' => 1500000,
                'penambahan' => 3800000,
                'pengurangan' => 750000,
            ],
            [
                'prospect_name' => 'Wedding Nicolas & Ophelia',
                'product_name' => 'Paket Wedding Arctic Winter 160 Pax',
                'notes' => '<p>Simulasi arctic winter wedding dengan ice theme dan frozen wonderland atmosphere.</p><p><strong>Arctic winter:</strong></p><ul><li>Ice sculpture installations</li><li>Aurora lighting effects</li><li>Winter wonderland decoration</li><li>Frozen-themed entertainment</li></ul>',
                'promo' => 700000,
                'penambahan' => 1900000,
                'pengurangan' => 350000,
            ],
            [
                'prospect_name' => 'Wedding Oscar & Priscilla',
                'product_name' => 'Paket Wedding Cosmic Constellation 280 Pax',
                'notes' => '<p>Simulasi cosmic wedding dengan constellation theme dan galactic atmosphere.</p><p><strong>Cosmic constellation:</strong></p><ul><li>Star map ceiling projection</li><li>Cosmic lighting design</li><li>Galaxy-themed decoration</li><li>Universal love concept</li></ul>',
                'promo' => 1300000,
                'penambahan' => 3400000,
                'pengurangan' => 650000,
            ],
            [
                'prospect_name' => 'Wedding Ryan & Sophia',
                'product_name' => 'Paket Wedding Luxury Yacht 80 Pax',
                'notes' => '<p>Simulasi second yacht wedding dengan exclusive maritime luxury dan intimate ocean ceremony.</p><p><strong>Exclusive yacht:</strong></p><ul><li>Premium yacht charter</li><li>Intimate ocean ceremony</li><li>Luxury maritime dining</li><li>Exclusive yacht crew service</li></ul>',
                'promo' => 1600000,
                'penambahan' => 6200000,
                'pengurangan' => 800000,
            ],
            [
                'prospect_name' => 'Wedding Sebastian & Tiffany',
                'product_name' => 'Paket Wedding Mountain Resort 130 Pax',
                'notes' => '<p>Simulasi second mountain wedding dengan highland adventure dan scenic mountain experience.</p><p><strong>Highland adventure:</strong></p><ul><li>Mountain peak ceremony</li><li>Highland adventure tour</li><li>Scenic mountain dining</li><li>Fresh alpine air experience</li></ul>',
                'promo' => 650000,
                'penambahan' => 1850000,
                'pengurangan' => 325000,
            ],
            [
                'prospect_name' => 'Wedding Theodore & Ursula',
                'product_name' => 'Paket Wedding Art Gallery 240 Pax',
                'notes' => '<p>Simulasi second art gallery wedding dengan contemporary art exhibition dan cultural appreciation.</p><p><strong>Contemporary art:</strong></p><ul><li>Modern art exhibition</li><li>Contemporary decoration</li><li>Artistic performance</li><li>Cultural appreciation ceremony</li></ul>',
                'promo' => 1050000,
                'penambahan' => 2850000,
                'pengurangan' => 525000,
            ],
            [
                'prospect_name' => 'Wedding Vincent & Wendy',
                'product_name' => 'Paket Wedding Winter Wonderland 200 Pax',
                'notes' => '<p>Simulasi second winter wedding dengan enhanced ice decoration dan snow-white magical atmosphere.</p><p><strong>Winter magic:</strong></p><ul><li>Enhanced ice sculptures</li><li>Snow-white decoration</li><li>Winter magical entertainment</li><li>Frozen fairy tale experience</li></ul>',
                'promo' => 950000,
                'penambahan' => 2450000,
                'pengurangan' => 475000,
            ],
            [
                'prospect_name' => 'Wedding Xavier & Yasmine',
                'product_name' => 'Paket Wedding Tropical Paradise 180 Pax',
                'notes' => '<p>Simulasi second tropical wedding dengan enhanced exotic atmosphere dan island paradise luxury.</p><p><strong>Paradise luxury:</strong></p><ul><li>Luxury tropical resort</li><li>Enhanced exotic decoration</li><li>Paradise island experience</li><li>Tropical luxury dining</li></ul>',
                'promo' => 850000,
                'penambahan' => 2050000,
                'pengurangan' => 425000,
            ],
            [
                'prospect_name' => 'Wedding Yusuf & Zelia',
                'product_name' => 'Paket Wedding Gatsby Glamour 300 Pax',
                'notes' => '<p>Simulasi second Gatsby wedding dengan enhanced 1920s glamour dan art deco luxury.</p><p><strong>Enhanced glamour:</strong></p><ul><li>Luxury art deco decoration</li><li>Enhanced 1920s theme</li><li>Premium jazz entertainment</li><li>Champagne luxury experience</li></ul>',
                'promo' => 1550000,
                'penambahan' => 3850000,
                'pengurangan' => 775000,
            ],
            [
                'prospect_name' => 'Wedding Antonio & Bianca',
                'product_name' => 'Paket Wedding Destination Villa 150 Pax',
                'notes' => '<p>Simulasi second destination wedding dengan luxury villa dan exclusive international experience.</p><p><strong>International luxury:</strong></p><ul><li>Luxury private villa</li><li>International guest experience</li><li>Exclusive destination tour</li><li>Luxury accommodation package</li></ul>',
                'promo' => 2100000,
                'penambahan' => 5600000,
                'pengurangan' => 1050000,
            ],
            [
                'prospect_name' => 'Wedding Christian & Daniela',
                'product_name' => 'Paket Wedding Fairy Tale Castle 450 Pax',
                'notes' => '<p>Simulasi second fairy tale wedding dengan enhanced princess theme dan magical royal experience.</p><p><strong>Royal magic:</strong></p><ul><li>Enhanced princess styling</li><li>Royal castle decoration</li><li>Magical royal entertainment</li><li>Princess dream experience</li></ul>',
                'promo' => 2600000,
                'penambahan' => 7200000,
                'pengurangan' => 1300000,
            ],
            [
                'prospect_name' => 'Wedding Eduardo & Francesca',
                'product_name' => 'Paket Wedding Urban Rooftop 220 Pax',
                'notes' => '<p>Simulasi second urban rooftop wedding dengan enhanced city view dan metropolitan luxury.</p><p><strong>Metropolitan luxury:</strong></p><ul><li>Premium city skyline view</li><li>Enhanced rooftop decoration</li><li>Metropolitan luxury dining</li><li>Urban luxury experience</li></ul>',
                'promo' => 1150000,
                'penambahan' => 2650000,
                'pengurangan' => 575000,
            ],
        ];

        $createdCount = 0;

        foreach ($simulasiData as $data) {
            // Find prospect - try different matching strategies
            $prospect = $prospects->where('name_event', $data['prospect_name'])->first();

            // If not found by exact match, try to find by partial name match
            if (! $prospect) {
                // Extract couple names from the wedding name (e.g., "Wedding Andi & Sari" -> "Andi & Sari")
                $coupleName = str_replace('Wedding ', '', $data['prospect_name']);
                $prospect = $prospects->filter(function ($p) use ($coupleName) {
                    return stripos($p->name_event, $coupleName) !== false;
                })->first();
            }

            // If still not found, use random prospect
            if (! $prospect) {
                $prospect = $prospects->random();
                $this->command->warn("Prospect '{$data['prospect_name']}' not found, using random prospect: {$prospect->name_event}");
            }

            // Find product - try exact match first
            $product = $products->where('name', $data['product_name'])->first();

            // If not found by exact match, try partial match
            if (! $product) {
                $product = $products->filter(function ($p) use ($data) {
                    return stripos($p->name, $data['product_name']) !== false;
                })->first();
            }

            // If still not found, use random product
            if (! $product) {
                $product = $products->random();
                $this->command->warn("Product '{$data['product_name']}' not found, using random product: {$product->name}");
            }

            // Calculate grand total
            $total_price = $product->price ?? 0;
            $grand_total = $total_price + $data['penambahan'] - $data['promo'] - $data['pengurangan'];

            $simulation = SimulasiProduk::firstOrCreate(
                [
                    'prospect_id' => $prospect->id,
                    'product_id' => $product->id,
                ],
                [
                    'slug' => Str::slug($prospect->name_event.'-'.$product->name),
                    'user_id' => $users->random()->id,
                    'total_price' => $total_price,
                    'promo' => $data['promo'],
                    'penambahan' => $data['penambahan'],
                    'pengurangan' => $data['pengurangan'],
                    'notes' => $data['notes'],
                ]
            );

            if ($simulation->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        // If we haven't reached 50 simulations, create additional random ones
        $targetCount = 50;
        while ($createdCount < $targetCount && $createdCount < $maxSimulations) {
            $randomProspect = $prospects->random();
            $randomProduct = $products->random();

            // Check if this combination already exists
            $existing = SimulasiProduk::where('prospect_id', $randomProspect->id)
                ->where('product_id', $randomProduct->id)
                ->first();

            if (! $existing) {
                $randomPromo = collect([0, 500000, 1000000, 1500000, 2000000])->random();
                $randomPenambahan = collect([1000000, 1500000, 2000000, 2500000, 3000000])->random();
                $randomPengurangan = collect([0, 250000, 500000, 750000, 1000000])->random();

                $total_price = $randomProduct->price ?? 0;

                $simulation = SimulasiProduk::create([
                    'prospect_id' => $randomProspect->id,
                    'product_id' => $randomProduct->id,
                    'slug' => Str::slug($randomProspect->name_event.'-'.$randomProduct->name.'-'.$createdCount),
                    'user_id' => $users->random()->id,
                    'total_price' => $total_price,
                    'promo' => $randomPromo,
                    'penambahan' => $randomPenambahan,
                    'pengurangan' => $randomPengurangan,
                    'notes' => "<p>Simulasi otomatis untuk {$randomProspect->name_event} dengan {$randomProduct->name}.</p><p><strong>Catatan:</strong></p><ul><li>Simulasi dibuat otomatis oleh sistem</li><li>Budget disesuaikan dengan kebutuhan</li><li>Dapat dikustomisasi sesuai permintaan client</li><li>Konsultasi lebih lanjut tersedia</li></ul>",
                ]);

                $createdCount++;
            }
        }

        $this->command->info("{$createdCount} simulasi produk created successfully!");
    }
}
