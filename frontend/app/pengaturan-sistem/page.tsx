'use client';
import Link from 'next/link';
import { Settings, Database } from 'lucide-react';

export default function Page() {
  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Pengaturan Sistem</h1>
        <p className="text-sm text-gray-500 mt-1">Kelola pengaturan sistem inventaris.</p>
      </div>
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <Link href="/pengaturan-sistem/pengaturan" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <Settings className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Pengaturan</h3>
              <p className="text-xs text-gray-500">Pengaturan umum sistem</p>
            </div>
          </div>
        </Link>
        <Link href="/pengaturan-sistem/database" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <Database className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Basis Data</h3>
              <p className="text-xs text-gray-500">Pengaturan basis data</p>
            </div>
          </div>
        </Link>
      </div>
    </div>
  );
}
