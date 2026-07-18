'use client';

import Link from 'next/link';
import { ArrowLeft, ShieldCheck } from 'lucide-react';

export default function AktivasiPage() {
  return (
    <div className="space-y-6 max-w-4xl mx-auto">
      <div>
        <Link href="/help" className="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-900 transition-colors mb-4">
          <ArrowLeft className="h-4 w-4" />
          Kembali ke Bantuan
        </Link>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Aktivasi</h1>
        <p className="text-sm text-gray-500 mt-1">Status dan informasi aktivasi lisensi sistem.</p>
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="px-6 py-4 border-b border-gray-100">
          <h3 className="text-sm font-semibold text-gray-900 uppercase tracking-wider">Status Aktivasi</h3>
        </div>
        <div className="p-6">
          <div className="flex items-center gap-4 p-4 rounded-lg border border-green-200 bg-green-50">
            <div className="p-2 rounded-lg bg-green-100">
              <ShieldCheck className="h-6 w-6 text-green-600" />
            </div>
            <div>
              <h4 className="font-medium text-green-800">Aktif</h4>
              <p className="text-sm text-green-700">Sistem dalam status aktif dan berfungsi normal.</p>
            </div>
          </div>
        </div>
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="px-6 py-4 border-b border-gray-100">
          <h3 className="text-sm font-semibold text-gray-900 uppercase tracking-wider">Informasi Lisensi</h3>
        </div>
        <div className="p-6 space-y-4">
          <p className="text-sm text-gray-700 leading-relaxed">
            Aplikasi Inventory Asset Management menggunakan sistem aktivasi berbasis lisensi untuk mengatur akses fitur.
            Pastikan lisensi Anda valid dan tidak kedaluwarsa agar seluruh fitur dapat berfungsi dengan baik.
          </p>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div className="rounded-lg border border-gray-200 p-4">
              <span className="text-gray-500">Tipe Lisensi</span>
              <p className="font-medium text-gray-900 mt-0.5">Kelembagaan</p>
            </div>
            <div className="rounded-lg border border-gray-200 p-4">
              <span className="text-gray-500">Batas Pengguna</span>
              <p className="font-medium text-gray-900 mt-0.5">Tidak Terbatas</p>
            </div>
          </div>
          <div className="bg-blue-50 border border-blue-200 rounded-md p-4 text-sm text-blue-800">
            Untuk perpanjangan atau aktivasi lisensi baru, hubungi administrator sistem atau tim pengembang.
          </div>
        </div>
      </div>
    </div>
  );
}
