<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('orders.my-orders', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        $order->load([
            'items.product',
            'items.shade',
            'payment',
        ]);

        return view('orders.detail', compact('order'));
    }

    public function uploadProof(Order $order, Request $request)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        $request->validate([
            'payment_proof' => 'required|image|max:2048',
        ], [
            'payment_proof.required' => 'Bukti transfer wajib diunggah.',
            'payment_proof.image'    => 'Bukti transfer harus berupa gambar (JPG/PNG).',
            'payment_proof.max'      => 'Ukuran gambar maksimal 2MB.',
        ]);

        $payment = $order->payment;
        if (!$payment) {
            return back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        if ($payment->method !== 'transfer') {
            return back()->with('error', 'Metode pembayaran ini tidak memerlukan bukti transfer.');
        }

        // Hapus file bukti transfer lama jika ada
        if ($payment->payment_proof) {
            Storage::disk('public')->delete($payment->payment_proof);
        }

        $proofPath = $request->file('payment_proof')->store('payment_proofs', 'public');

        $payment->update([
            'payment_proof' => $proofPath,
            'status'        => 'waiting_verification', // reset status ke waiting_verification
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah! Menunggu verifikasi admin.');
    }
}