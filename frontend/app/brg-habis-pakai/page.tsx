'use client';
import Link from 'next/link';
import { Package, ShoppingCart, PackageMinus, Database } from 'lucide-react';

export default function Page() {
  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Barang Habis Pakai</h1>
        <p className="text-sm text-gray-500 mt-1">Manajemen barang habis pakai.</p>
      </div>
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <Link href="/brg-habis-pakai/data-barang-habis-pakai" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <Package className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Data Barang</h3>
              <p className="text-xs text-gray-500">Daftar barang habis pakai</p>
            </div>
          </div>
        </Link>
        <Link href="/brg-habis-pakai/pengadaan-brg-habis-pakai" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <ShoppingCart className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Pengadaan</h3>
              <p className="text-xs text-gray-500">Pengadaan barang habis pakai</p>
            </div>
          </div>
        </Link>
        <Link href="/brg-habis-pakai/barang-keluar" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
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
        <Link href="/brg-habis-pakai/master-data-brg-habis-pakai" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <Database className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Master Data</h3>
              <p className="text-xs text-gray-500">Master data barang habis pakai</p>
            </div>
          </div>
        </Link>
      </div>
    </div>
  );
}
