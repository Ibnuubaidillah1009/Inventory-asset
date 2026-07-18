'use client';

import Link from 'next/link';
import { ArrowLeft, Package } from 'lucide-react';

export default function AboutPage() {
  return (
    <div className="space-y-6 max-w-4xl mx-auto">
      <div>
        <Link href="/help" className="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-900 transition-colors mb-4">
          <ArrowLeft className="h-4 w-4" />
          Kembali ke Bantuan
        </Link>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Tentang</h1>
        <p className="text-sm text-gray-500 mt-1">Informasi tentang aplikasi inventaris.</p>
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="px-6 py-4 border-b border-gray-100">
          <h3 className="text-sm font-semibold text-gray-900 uppercase tracking-wider">Aplikasi</h3>
        </div>
        <div className="p-6">
          <div className="flex items-center gap-4 mb-6">
            <div className="p-3 rounded-lg bg-gray-100">
              <Package className="h-8 w-8 text-gray-600" />
            </div>
            <div>
              <h2 className="text-xl font-bold text-gray-900">Manajemen Inventaris & Aset</h2>
              <p className="text-sm text-gray-500">Sistem Pengelolaan Inventaris & Aset</p>
            </div>
          </div>
          <p className="text-sm text-gray-700 leading-relaxed">
            Aplikasi ini dirancang untuk membantu institusi dalam mengelola inventaris dan aset secara digital.
            Mulai dari pencatatan pengadaan, pelabelan barcode, hingga transaksi peminjaman dan pengembalian barang,
            semuanya dapat dikelola dalam satu sistem terpadu.
          </p>
        </div>
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="px-6 py-4 border-b border-gray-100">
          <h3 className="text-sm font-semibold text-gray-900 uppercase tracking-wider">Teknologi</h3>
        </div>
        <div className="p-6">
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div className="rounded-lg border border-gray-200 p-4">
              <h4 className="font-medium text-gray-900">Frontend</h4>
              <p className="text-sm text-gray-500 mt-1">Next.js & React</p>
              <p className="text-xs text-gray-400 mt-1">Tailwind CSS, TypeScript</p>
            </div>
            <div className="rounded-lg border border-gray-200 p-4">
              <h4 className="font-medium text-gray-900">Backend</h4>
              <p className="text-sm text-gray-500 mt-1">Laravel (PHP)</p>
              <p className="text-xs text-gray-400 mt-1">RESTful API, Spatie Permision</p>
            </div>
            <div className="rounded-lg border border-gray-200 p-4">
              <h4 className="font-medium text-gray-900">Database</h4>
              <p className="text-sm text-gray-500 mt-1">MySQL</p>
              <p className="text-xs text-gray-400 mt-1">Laravel Migration & Seeder</p>
            </div>
          </div>
        </div>
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="px-6 py-4 border-b border-gray-100">
          <h3 className="text-sm font-semibold text-gray-900 uppercase tracking-wider">Versi</h3>
        </div>
        <div className="p-6">
          <div className="grid grid-cols-2 gap-4 text-sm">
            <div>
              <span className="text-gray-500">Versi Aplikasi</span>
              <p className="font-medium text-gray-900 mt-0.5">1.0.0</p>
            </div>
            <div>
              <span className="text-gray-500">Tanggal Rilis</span>
              <p className="font-medium text-gray-900 mt-0.5">2026</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
