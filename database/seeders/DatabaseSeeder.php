<?php

namespace Database\Seeders;

use App\Models\Studio;
use App\Models\User;
use App\Models\Booking;
use App\Models\InventoryItem;
use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // === Create Users ===
        $owner = User::create([
            'name' => 'Admin Rockstar',
            'email' => 'admin@rockstarstudio.com',
            'phone' => '081234567890',
            'role' => 'owner',
            'password' => Hash::make('password'),
            'locale' => 'id',
            'theme' => 'light',
        ]);

        $renter1 = User::create([
            'name' => 'Ihsan Penyewa',
            'email' => 'ihsan@gmail.com',
            'phone' => '082345678901',
            'role' => 'renter',
            'password' => Hash::make('password'),
            'locale' => 'id',
            'theme' => 'light',
        ]);

        $renter2 = User::create([
            'name' => 'Budi Musisi',
            'email' => 'budi@gmail.com',
            'phone' => '083456789012',
            'role' => 'renter',
            'password' => Hash::make('password'),
        ]);

        // === Create Studios ===
        $studioA = Studio::create([
            'name' => 'Studio A',
            'description' => 'Ruang studio utama dengan akustik premium, cocok untuk band penuh dan sesi rekaman. Dilengkapi dengan sound system berkualitas tinggi dan ruangan kedap suara.',
            'address' => 'Jl. Sendang Sari No.33, Gempol, Condongcatur, Kec. Depok, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55281',
            'latitude' => -7.7500754,
            'longitude' => 110.4067722,
            'images' => [],
            'facilities' => ['Drum Set', 'Bass Amplifier', 'Guitar Amplifier', 'Keyboard', 'Microphone', 'Sound System', 'AC', 'Ruang Kedap Suara'],
            'price_per_session' => 85000,
            'dp_amount' => 40000,
        ]);

        $studioB = Studio::create([
            'name' => 'Studio B',
            'description' => 'Ruang studio compact yang nyaman untuk latihan band kecil atau duo akustik. Cocok untuk jamming session santai dengan suasana yang intimate.',
            'address' => 'Jl. Sendang Sari No.33, Gempol, Condongcatur, Kec. Depok, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55281',
            'latitude' => -7.7500754,
            'longitude' => 110.4067722,
            'images' => [],
            'facilities' => ['Drum Set', 'Guitar Amplifier', 'Microphone', 'Sound System', 'AC'],
            'price_per_session' => 85000,
            'dp_amount' => 40000,
        ]);

        // === Create Demo Bookings (this week) ===
        $monday = Carbon::now()->startOfWeek(Carbon::MONDAY);

        $demoBookings = [
            ['studio_id' => $studioA->id, 'user_id' => $renter1->id, 'date' => $monday->copy(), 'session_number' => 4, 'band_name' => 'GAD2K', 'booker_name' => 'Galih', 'booker_phone' => '081111111111'],
            ['studio_id' => $studioA->id, 'user_id' => $renter1->id, 'date' => $monday->copy(), 'session_number' => 5, 'band_name' => 'Rancvu', 'booker_name' => 'Raka', 'booker_phone' => '081222222222'],
            ['studio_id' => $studioA->id, 'user_id' => $renter2->id, 'date' => $monday->copy(), 'session_number' => 6, 'band_name' => 'Steady Steps', 'booker_name' => 'Budi', 'booker_phone' => '083456789012'],
            ['studio_id' => $studioA->id, 'user_id' => $renter2->id, 'date' => $monday->copy()->addDay(), 'session_number' => 5, 'band_name' => 'Pulung & Friends', 'booker_name' => 'Pulung', 'booker_phone' => '081333333333'],
            ['studio_id' => $studioA->id, 'user_id' => $renter1->id, 'date' => $monday->copy()->addDay(), 'session_number' => 6, 'band_name' => 'Medal Band', 'booker_name' => 'Medal', 'booker_phone' => '081444444444'],
            ['studio_id' => $studioA->id, 'user_id' => $renter2->id, 'date' => $monday->copy()->addDays(2), 'session_number' => 4, 'band_name' => 'Suaranta', 'booker_name' => 'Anto', 'booker_phone' => '081555555555'],
            ['studio_id' => $studioA->id, 'user_id' => $renter1->id, 'date' => $monday->copy()->addDays(2), 'session_number' => 5, 'band_name' => 'daniel caesar', 'booker_name' => 'Daniel', 'booker_phone' => '081666666666'],
            ['studio_id' => $studioA->id, 'user_id' => $renter2->id, 'date' => $monday->copy()->addDays(2), 'session_number' => 6, 'band_name' => 'Medal Band', 'booker_name' => 'Medal', 'booker_phone' => '081444444444'],
        ];

        $sessions = $studioA->getSessionTemplates();

        foreach ($demoBookings as $data) {
            $session = $sessions[$data['session_number'] - 1];
            Booking::create([
                ...$data,
                'start_time' => $session['start_time'],
                'end_time' => $session['end_time'],
                'status' => 'confirmed',
                'amount' => 85000,
                'dp_amount' => 40000,
                'locked_at' => now()->subHours(2),
                'confirmed_at' => now()->subHours(2),
            ]);
        }

        // === Create Demo Inventory ===
        $inventoryData = [
            ['name' => 'Drum Set Yamaha Stage Custom', 'category' => 'alat_musik', 'quantity' => 1, 'condition' => 'baik', 'purchase_price' => 12000000],
            ['name' => 'Gitar Elektrik Fender', 'category' => 'alat_musik', 'quantity' => 2, 'condition' => 'baik', 'purchase_price' => 8000000],
            ['name' => 'Bass Elektrik Ibanez', 'category' => 'alat_musik', 'quantity' => 1, 'condition' => 'cukup', 'purchase_price' => 5000000],
            ['name' => 'Keyboard Yamaha PSR', 'category' => 'alat_musik', 'quantity' => 1, 'condition' => 'baik', 'purchase_price' => 4000000],
            ['name' => 'Microphone Shure SM58', 'category' => 'alat_rekaman', 'quantity' => 4, 'condition' => 'baik', 'purchase_price' => 1500000],
            ['name' => 'Audio Interface Focusrite', 'category' => 'alat_rekaman', 'quantity' => 1, 'condition' => 'baik', 'purchase_price' => 3000000],
            ['name' => 'Mixer Behringer X32', 'category' => 'alat_rekaman', 'quantity' => 1, 'condition' => 'cukup', 'purchase_price' => 7000000],
            ['name' => 'Amplifier Marshall MG100', 'category' => 'alat_elektronik', 'quantity' => 2, 'condition' => 'baik', 'purchase_price' => 6000000],
            ['name' => 'Speaker Monitor JBL', 'category' => 'alat_elektronik', 'quantity' => 2, 'condition' => 'baik', 'purchase_price' => 4000000],
            ['name' => 'AC Daikin 2 PK', 'category' => 'alat_elektronik', 'quantity' => 2, 'condition' => 'baik', 'purchase_price' => 8000000],
        ];

        foreach ($inventoryData as $item) {
            InventoryItem::create([
                'studio_id' => $studioA->id,
                ...$item,
            ]);
        }

        // === Create Demo Event ===
        Event::create([
            'studio_id' => $studioA->id,
            'title' => 'Open Mic Night - Rockstar Studio',
            'description' => 'Tunjukkan bakat musik kamu di Open Mic Night! Gratis untuk semua musisi. Daftar sekarang di Rockstar Studio.',
            'start_date' => now(),
            'end_date' => now()->addDays(14),
            'is_active' => true,
        ]);

        Event::create([
            'studio_id' => $studioA->id,
            'title' => 'Promo Sesi Malam - Diskon 20%',
            'description' => 'Dapatkan diskon 20% untuk booking sesi 7 & 8 (20:00-24:00) selama bulan ini!',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'is_active' => true,
        ]);
    }
}
