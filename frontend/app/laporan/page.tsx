'use client';
import Link from 'next/link';
import { FileText, Package, HandCoins, Receipt } from 'lucide-react';

export default function Page() {
  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Laporan</h1>
        <p className="text-sm text-gray-500 mt-1">Lihat laporan inventaris sekolah.</p>
      </div>
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <Link href="/laporan/laporan" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <FileText className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Laporan Umum</h3>
              <p className="text-xs text-gray-500">Laporan umum inventaris</p>
            </div>
          </div>
        </Link>
        <Link href="/laporan/laporan-barang" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <Package className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Laporan Barang</h3>
              <p className="text-xs text-gray-500">Laporan data barang</p>
            </div>
          </div>
        </Link>
        <Link href="/laporan/laporan-peminjaman" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <HandCoins className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Laporan Peminjaman</h3>
              <p className="text-xs text-gray-500">Laporan peminjaman barang</p>
            </div>
          </div>
        </Link>
        <Link href="/laporan/laporan-transaksi" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <Receipt className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Laporan Transaksi</h3>
              <p className="text-xs text-gray-500">Laporan transaksi inventaris</p>
            </div>
          </div>
        </Link>
      </div>
    </div>
  );
}
