'use client';

import Link from 'next/link';
import { ArrowLeft, CheckCircle } from 'lucide-react';

export default function TutorialPage() {
  const steps = [
    {
      number: 1,
      title: 'Login ke Sistem',
      description: 'Akses halaman login dan masukkan akun administrator atau pengguna yang telah didaftarkan. Pastikan kredensial yang digunakan benar untuk mengakses dashboard sistem inventaris.',
    },
    {
      number: 2,
      title: 'Mengatur Lembaga',
      description: 'Lengkapi data lembaga melalui menu Pengaturan > Set Lembaga. Isi nama instansi, alamat, kontak, serta data kepala sekolah dan format kode inventaris yang akan digunakan.',
    },
    {
      number: 3,
      title: 'Mengelola Data Master',
      description: 'Atur data master seperti ruang/lokasi, kategori barang, merek, dan satuan. Data master ini akan menjadi acuan saat proses pengadaan dan pencatatan aset.',
    },
    {
      number: 4,
      title: 'Pengadaan Barang',
      description: 'Buat pengadaan baru untuk mencatat masuknya barang/aset. Setiap pengadaan akan menghasilkan kode inventaris unik yang otomatis digenerate berdasarkan format yang telah diatur.',
    },
    {
      number: 5,
      title: 'Transaksi Peminjaman',
      description: 'Kelola peminjaman dan pengembalian barang. Pilih barang yang akan dipinjam, tentukan peminjam, serta lacak status pengembalian melalui menu transaksi.',
    },
  ];

  return (
    <div className="space-y-6 max-w-4xl mx-auto">
      <div>
        <Link href="/help" className="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-900 transition-colors mb-4">
          <ArrowLeft className="h-4 w-4" />
          Kembali ke Bantuan
        </Link>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Panduan</h1>
        <p className="text-sm text-gray-500 mt-1">Panduan langkah demi langkah menggunakan sistem inventaris.</p>
      </div>

      <div className="space-y-4">
        {steps.map((step) => (
          <div key={step.number} className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div className="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
              <div className="flex items-center justify-center w-8 h-8 rounded-full bg-gray-900 text-white text-sm font-bold shrink-0">
                {step.number}
              </div>
              <h3 className="text-sm font-semibold text-gray-900 uppercase tracking-wider">{step.title}</h3>
            </div>
            <div className="p-6">
              <div className="flex items-start gap-3">
                <CheckCircle className="h-5 w-5 text-gray-400 mt-0.5 shrink-0" />
                <p className="text-sm text-gray-700 leading-relaxed">{step.description}</p>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
