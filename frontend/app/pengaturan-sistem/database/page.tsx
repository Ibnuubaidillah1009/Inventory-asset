'use client';

import { Database, Download, Upload, RefreshCw, Wifi } from 'lucide-react';
import Link from 'next/link';

export default function DatabasePage() {
  const tools = [
    {
      title: 'Cadangan Basis Data',
      description: 'Ekspor basis data ke berkas SQL untuk keperluan cadangan.',
      href: '/pengaturan-sistem/database/tools/backup',
      icon: Download,
    },
    {
      title: 'Pemulihan Basis Data',
      description: 'Impor berkas SQL untuk memulihkan basis data.',
      href: '/pengaturan-sistem/database/tools/restore',
      icon: Upload,
    },
    {
      title: 'Atur Ulang Basis Data',
      description: 'Atur ulang basis data ke kondisi awal.',
      href: '/pengaturan-sistem/database/tools/reset',
      icon: RefreshCw,
    },
    {
      title: 'Koneksi Basis Data',
      description: 'Pengaturan koneksi ke server basis data.',
      href: '/pengaturan-sistem/database/tools/koneksi',
      icon: Wifi,
    },
  ];

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Basis Data</h1>
        <p className="text-sm text-gray-500 mt-1">Informasi dan pengelolaan basis data.</p>
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="px-6 py-4 border-b border-gray-100">
          <h3 className="text-sm font-semibold text-gray-900 uppercase tracking-wider">Informasi Koneksi</h3>
        </div>
        <div className="p-6">
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label className="block text-xs text-gray-500 mb-1">Tipe Database</label>
              <div className="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-gray-900 text-sm">MySQL</div>
            </div>
            <div>
              <label className="block text-xs text-gray-500 mb-1">Host</label>
              <div className="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-gray-900 text-sm font-mono">localhost</div>
            </div>
            <div>
              <label className="block text-xs text-gray-500 mb-1">Port</label>
              <div className="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-gray-900 text-sm font-mono">3306</div>
            </div>
            <div>
              <label className="block text-xs text-gray-500 mb-1">Status</label>
              <div className="w-full rounded-md border border-green-200 bg-green-50 px-3 py-2 text-green-700 text-sm font-medium flex items-center gap-2">
                <Database className="h-4 w-4" />
                Terhubung
              </div>
            </div>
          </div>
        </div>
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="px-6 py-4 border-b border-gray-100">
          <h3 className="text-sm font-semibold text-gray-900 uppercase tracking-wider">Alat Database</h3>
        </div>
        <div className="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
          {tools.map((tool) => (
            <Link
              key={tool.href}
              href={tool.href}
              className="group flex items-start gap-4 rounded-lg border border-gray-200 p-4 hover:border-gray-900 hover:shadow-sm transition-all"
            >
              <div className="p-2 rounded-lg bg-gray-100 group-hover:bg-gray-900 group-hover:text-white text-gray-600 transition-colors">
                <tool.icon className="h-5 w-5" />
              </div>
              <div>
                <h4 className="font-medium text-gray-900 group-hover:text-gray-900">{tool.title}</h4>
                <p className="text-sm text-gray-500 mt-0.5">{tool.description}</p>
              </div>
            </Link>
          ))}
        </div>
      </div>
    </div>
  );
}
