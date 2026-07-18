'use client';

import Link from 'next/link';
import { Database, ArrowRight } from 'lucide-react';

export default function DataMasterPage() {
  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Data Master</h1>
        <p className="text-sm text-gray-500 mt-1">Kelola data master referensi sistem.</p>
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="p-8 text-center">
          <Database className="h-12 w-12 mx-auto text-gray-300 mb-4" />
          <p className="text-gray-600 text-lg font-medium mb-2">Halaman ini untuk mengelola data master referensi sistem.</p>
          <p className="text-gray-500 text-sm mb-6">Pilih kategori data master yang ingin dikelola di bawah ini.</p>

          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link
              href="/manajemen-aset/master-barang"
              className="inline-flex items-center justify-center gap-2 rounded-md text-sm font-medium transition-colors bg-gray-900 text-white hover:bg-gray-800 h-10 px-6 py-2"
            >
              Master Barang
              <ArrowRight className="h-4 w-4" />
            </Link>
            <Link
              href="/manajemen-aset/master-sekolah"
              className="inline-flex items-center justify-center gap-2 rounded-md text-sm font-medium transition-colors bg-gray-900 text-white hover:bg-gray-800 h-10 px-6 py-2"
            >
              Master Sekolah
              <ArrowRight className="h-4 w-4" />
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}
