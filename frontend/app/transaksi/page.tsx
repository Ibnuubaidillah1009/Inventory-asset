'use client';
import Link from 'next/link';
import { HandCoins, ClipboardList, ArrowLeftRight, AlertTriangle, Wrench, PackageMinus } from 'lucide-react';

export default function Page() {
  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Transaksi</h1>
        <p className="text-sm text-gray-500 mt-1">Kelola transaksi inventaris.</p>
      </div>
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <Link href="/transaksi/peminjaman" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <HandCoins className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Peminjaman</h3>
              <p className="text-xs text-gray-500">Kelola peminjaman barang</p>
            </div>
          </div>
        </Link>
        <Link href="/transaksi/permintaan" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <ClipboardList className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Permintaan</h3>
              <p className="text-xs text-gray-500">Kelola permintaan barang</p>
            </div>
          </div>
        </Link>
        <Link href="/transaksi/mutasi" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <ArrowLeftRight className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Mutasi</h3>
              <p className="text-xs text-gray-500">Kelola mutasi barang</p>
            </div>
          </div>
        </Link>
        <Link href="/transaksi/kerusakan" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <AlertTriangle className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Kerusakan</h3>
              <p className="text-xs text-gray-500">Laporkan kerusakan barang</p>
            </div>
          </div>
        </Link>
        <Link href="/transaksi/perbaikan" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <Wrench className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Perbaikan</h3>
              <p className="text-xs text-gray-500">Kelola perbaikan barang</p>
            </div>
          </div>
        </Link>
        <Link href="/transaksi/barang-keluar" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <PackageMinus className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Barang Keluar</h3>
              <p className="text-xs text-gray-500">Kelola barang keluar</p>
            </div>
          </div>
        </Link>
      </div>
    </div>
  );
}
