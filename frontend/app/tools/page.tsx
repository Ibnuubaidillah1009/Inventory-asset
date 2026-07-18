'use client';
import Link from 'next/link';
import { Download, Upload, RotateCcw, Database } from 'lucide-react';

export default function Page() {
  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Peralatan</h1>
        <p className="text-sm text-gray-500 mt-1">Peralatan dan utilitas sistem.</p>
      </div>
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <Link href="/tools/backup" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <Download className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Cadangan</h3>
              <p className="text-xs text-gray-500">Cadangkan data sistem</p>
            </div>
          </div>
        </Link>
        <Link href="/tools/restore" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <Upload className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Pemulihan</h3>
              <p className="text-xs text-gray-500">Pulihkan data dari cadangan</p>
            </div>
          </div>
        </Link>
        <Link href="/tools/reset" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <RotateCcw className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Atur Ulang</h3>
              <p className="text-xs text-gray-500">Atur ulang data sistem</p>
            </div>
          </div>
        </Link>
        <Link href="/tools/koneksi" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <Database className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Koneksi Database</h3>
              <p className="text-xs text-gray-500">Pengaturan koneksi database</p>
            </div>
          </div>
        </Link>
      </div>
    </div>
  );
}
