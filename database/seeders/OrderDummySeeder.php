<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductShade;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class OrderDummySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $users = User::where('role', 'user')->get();

        if ($users->isEmpty()) {
            $this->command?->warn('User dummy belum ada. Jalankan UserDummySeeder dulu.');
            return;
        }

        $products = Product::with('shades')->get()->filter(function ($product) {
            return $product->shades->isNotEmpty();
        })->values();

        if ($products->isEmpty()) {
            $this->command?->warn('Produk / shade belum ada. Jalankan ProductAndShadeSeeder dulu.');
            return;
        }

        // =========================
        // PERIODE DUMMY ORDER
        // =========================
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd   = Carbon::now()->subMonth()->endOfMonth();

        $thisMonthStart = Carbon::now()->startOfMonth();
        $thisMonthEnd   = Carbon::now();

        foreach ($users as $user) {
            // Bulan lalu: 2 - 4 order
            $lastMonthOrders = rand(2, 4);

            // Bulan ini: 4 - 7 order (lebih ramai)
            $thisMonthOrders = rand(4, 7);

            for ($i = 0; $i < $lastMonthOrders; $i++) {
                $createdAt = $this->randomDateBetween($lastMonthStart, $lastMonthEnd);
                $this->createRealisticOrder($user, $admin, $products, $createdAt);
            }

            for ($i = 0; $i < $thisMonthOrders; $i++) {
                $createdAt = $this->randomDateBetween($thisMonthStart, $thisMonthEnd);
                $this->createRealisticOrder($user, $admin, $products, $createdAt);
            }
        }
    }

    private function createRealisticOrder(User $user, ?User $admin, Collection $products, Carbon $createdAt): void
    {
        $statusPack = $this->randomStatusPack();

        $order = Order::create([
            'user_id'          => $user->id,
            'order_code'       => $this->generateOrderCode(),
            'total_price'      => 0,
            'status'           => $statusPack['order_status'],
            'receiver_name'    => $user->name,
            'receiver_email'   => $user->email,
            'receiver_phone'   => $user->phone,
            'shipping_address' => $statusPack['delivery_method'] === 'courier' ? $user->address : null,
            'delivery_method'  => $statusPack['delivery_method'],
            'shipping_note'    => $statusPack['delivery_method'] === 'pickup'
                ? 'Ambil langsung di toko.'
                : 'Pesanan dikirim ke alamat utama.',
            'created_at'       => $createdAt,
            'updated_at'       => $createdAt,
        ]);

        $usedProductIds = [];
        $itemCount = rand(1, 3);
        $total = 0;

        for ($j = 0; $j < $itemCount; $j++) {
            $product = $this->pickWeightedProduct($products, $usedProductIds);

            if (!$product) {
                continue;
            }

            $shade = $this->pickWeightedShade($product->shades);

            if (!$shade) {
                continue;
            }

            $qty = $this->weightedQty();
            $price = (int) $product->price;
            $subtotal = $price * $qty;

            OrderItem::create([
                'order_id'         => $order->id,
                'product_id'       => $product->id,
                'product_shade_id' => $shade->id,
                'price'            => $price,
                'qty'              => $qty,
                'subtotal'         => $subtotal,
                'created_at'       => $createdAt->copy()->addMinutes(rand(1, 15)),
                'updated_at'       => $createdAt->copy()->addMinutes(rand(1, 15)),
            ]);

            $usedProductIds[] = $product->id;
            $total += $subtotal;
        }

        if ($total <= 0) {
            $order->delete();
            return;
        }

        $order->update([
            'total_price' => $total,
            'updated_at'  => $createdAt->copy()->addMinutes(rand(10, 30)),
        ]);

        Payment::create([
            'order_id'      => $order->id,
            'method'        => $statusPack['payment_method'],
            'status'        => $statusPack['payment_status'],
            'payment_proof' => $statusPack['payment_method'] === 'transfer'
                ? 'payment_proofs/dummy-proof-' . rand(1, 9) . '.jpg'
                : null,
            'verified_at'   => $statusPack['payment_status'] === 'verified'
                ? $createdAt->copy()->addHours(rand(1, 24))
                : null,
            'verified_by'   => in_array($statusPack['payment_status'], ['verified', 'rejected'], true)
                ? optional($admin)->id
                : null,
            'note'          => $this->buildPaymentNote(
                $statusPack['payment_method'],
                $statusPack['payment_status']
            ),
            'created_at'    => $createdAt->copy()->addMinutes(rand(5, 20)),
            'updated_at'    => $createdAt->copy()->addMinutes(rand(10, 40)),
        ]);
    }

    private function generateOrderCode(): string
    {
        do {
            $code = 'ORD-' . strtoupper(Str::random(8));
        } while (Order::where('order_code', $code)->exists());

        return $code;
    }

    private function randomDateBetween(Carbon $start, Carbon $end): Carbon
    {
        return Carbon::createFromTimestamp(rand($start->timestamp, $end->timestamp));
    }

    private function weightedQty(): int
    {
        $pool = [1, 1, 1, 2, 2, 3];
        return $pool[array_rand($pool)];
    }

    private function pickWeightedProduct(Collection $products, array $usedProductIds): ?Product
    {
        $available = $products->filter(function ($product) use ($usedProductIds) {
            return !in_array($product->id, $usedProductIds, true);
        })->values();

        if ($available->isEmpty()) {
            return null;
        }

        $weighted = [];

        foreach ($available as $product) {
            $weight = $this->productWeight($product);

            for ($i = 0; $i < $weight; $i++) {
                $weighted[] = $product;
            }
        }

        if (empty($weighted)) {
            return $available->random();
        }

        return $weighted[array_rand($weighted)];
    }

    private function productWeight(Product $product): int
    {
        $weight = 1;

        $popularBrands = [
            'Wardah'      => 5,
            'Make Over'   => 5,
            'Emina'       => 4,
            'Implora'     => 4,
            'Somethinc'   => 4,
            'Skintific'   => 4,
            'Hanasui'     => 3,
            'Y.O.U'       => 3,
            'Luxcrime'    => 3,
            'ESQA'        => 3,
            'BLP Beauty'  => 2,
            'Dear Me Beauty' => 2,
            'Azarine'     => 2,
            'Purbasari'   => 2,
            'Madam Gie'   => 2,
        ];

        $popularCategories = [
            'Cushion'        => 6,
            'Foundation'     => 5,
            'Concealer'      => 4,
            'BB Cream'       => 3,
            'Skin Tint'      => 3,
            'Compact Powder' => 2,
            'Loose Powder'   => 2,
        ];

        $weight += $popularBrands[$product->brand] ?? 1;
        $weight += $popularCategories[$product->category] ?? 1;

        return max($weight, 1);
    }

    private function pickWeightedShade(Collection $shades): ?ProductShade
    {
        if ($shades->isEmpty()) {
            return null;
        }

        $weighted = [];

        foreach ($shades as $shade) {
            $weight = $this->shadeWeight($shade);

            for ($i = 0; $i < $weight; $i++) {
                $weighted[] = $shade;
            }
        }

        if (empty($weighted)) {
            return $shades->random();
        }

        return $weighted[array_rand($weighted)];
    }

    private function shadeWeight(ProductShade $shade): int
    {
        $weight = 1;

        // tone medium/light/fair lebih umum dibeli
        $toneWeights = [
            'fair'   => 4,
            'light'  => 5,
            'medium' => 6,
            'tan'    => 3,
            'deep'   => 2,
            'dark'   => 1,
        ];

        // undertone neutral/warm lebih umum
        $undertoneWeights = [
            'neutral' => 4,
            'warm'    => 4,
            'cool'    => 2,
        ];

        $weight += $toneWeights[strtolower($shade->tone)] ?? 1;
        $weight += $undertoneWeights[strtolower($shade->undertone)] ?? 1;

        return max($weight, 1);
    }

    private function randomStatusPack(): array
    {
        $rand = rand(1, 100);

        if ($rand <= 42) {
            return [
                'order_status'    => 'selesai',
                'payment_method'  => $this->randomPaymentMethod(['transfer', 'cod', 'store']),
                'payment_status'  => 'verified',
                'delivery_method' => $this->randomDeliveryMethod(),
            ];
        }

        if ($rand <= 62) {
            return [
                'order_status'    => 'dikirim',
                'payment_method'  => $this->randomPaymentMethod(['transfer', 'cod', 'store']),
                'payment_status'  => 'verified',
                'delivery_method' => 'courier',
            ];
        }

        if ($rand <= 80) {
            return [
                'order_status'    => 'diproses',
                'payment_method'  => $this->randomPaymentMethod(['transfer', 'cod', 'store']),
                'payment_status'  => 'verified',
                'delivery_method' => $this->randomDeliveryMethod(),
            ];
        }

        if ($rand <= 92) {
            return [
                'order_status'    => 'menunggu_verifikasi',
                'payment_method'  => 'transfer',
                'payment_status'  => 'waiting_verification',
                'delivery_method' => $this->randomDeliveryMethod(),
            ];
        }

        return [
            'order_status'    => 'dibatalkan',
            'payment_method'  => 'transfer',
            'payment_status'  => 'rejected',
            'delivery_method' => $this->randomDeliveryMethod(),
        ];
    }

    private function randomPaymentMethod(array $allowed = ['transfer', 'cod', 'store']): string
    {
        $weighted = [];

        foreach ($allowed as $method) {
            $weight = match ($method) {
                'transfer' => 5,
                'cod'      => 3,
                'store'    => 2,
                default    => 1,
            };

            for ($i = 0; $i < $weight; $i++) {
                $weighted[] = $method;
            }
        }

        return $weighted[array_rand($weighted)];
    }

    private function randomDeliveryMethod(): string
    {
        return rand(1, 100) <= 78 ? 'courier' : 'pickup';
    }

    private function buildPaymentNote(string $method, string $status): ?string
    {
        if ($method === 'transfer' && $status === 'waiting_verification') {
            return 'Menunggu verifikasi pembayaran transfer.';
        }

        if ($method === 'transfer' && $status === 'verified') {
            return 'Pembayaran transfer telah diverifikasi admin.';
        }

        if ($method === 'transfer' && $status === 'rejected') {
            return 'Pembayaran ditolak karena bukti transfer tidak valid.';
        }

        if ($method === 'cod') {
            return 'Bayar di tempat.';
        }

        if ($method === 'store') {
            return 'Bayar langsung di toko.';
        }

        return null;
    }
}