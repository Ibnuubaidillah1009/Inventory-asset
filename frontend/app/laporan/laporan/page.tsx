'use client';

import { FileText } from 'lucide-react';

export default function LaporanUmumPage() {
  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Laporan Umum</h1>
        <p className="text-sm text-gray-500 mt-1">Laporan umum inventaris sekolah.</p>
      </div>
      <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-16 text-center">
        <FileText className="h-16 w-16 text-gray-300 mx-auto mb-4" />
        <h2 className="text-lg font-medium text-gray-900 mb-2">Laporan Umum</h2>
        <p className="text-sm text-gray-500 max-w-md mx-auto">
          Halaman laporan umum akan segera hadir. Fitur cetak dan ekspor PDF akan tersedia.
        </p>
      </div>
    </div>
  );
}
