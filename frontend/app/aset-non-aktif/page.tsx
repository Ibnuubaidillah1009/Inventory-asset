'use client';
import Link from 'next/link';
import { PackageX, Mountain, Building, Trash2 } from 'lucide-react';

export default function Page() {
  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Aset Non-Aktif</h1>
        <p className="text-sm text-gray-500 mt-1">Kelola aset yang sudah non-aktif.</p>
      </div>
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <Link href="/aset-non-aktif/barang-non-aktif" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <PackageX className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Barang Non-Aktif</h3>
              <p className="text-xs text-gray-500">Daftar barang non-aktif</p>
            </div>
          </div>
        </Link>
        <Link href="/aset-non-aktif/tanah-non-aktif" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <Mountain className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Tanah Non-Aktif</h3>
              <p className="text-xs text-gray-500">Daftar tanah non-aktif</p>
            </div>
          </div>
        </Link>
        <Link href="/aset-non-aktif/bangunan-non-aktif" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <Building className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Bangunan Non-Aktif</h3>
              <p className="text-xs text-gray-500">Daftar bangunan non-aktif</p>
            </div>
          </div>
        </Link>
        <Link href="/aset-non-aktif/penghapusan-aset" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <Trash2 className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Penghapusan Aset</h3>
              <p className="text-xs text-gray-500">Proses penghapusan aset</p>
            </div>
          </div>
        </Link>
      </div>
    </div>
  );
}
