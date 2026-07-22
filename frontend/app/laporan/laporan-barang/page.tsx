'use client';

import { extractData } from '@/lib/utils';


import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { Loader2, Printer } from 'lucide-react';

export default function LaporanBarangPage() {
  const [data, setData] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  const fetchData = async () => {
    setLoading(true);
    try {
      const res = await api.get('/aset?per_page=100').catch(() => ({ data: { data: [] } }));
      setData(extractData(res.data.data));
    } finally { setLoading(false); }
  };

  useEffect(() => { fetchData(); }, []);

  const totalBarang = data.length;
  const barangAktif = data.filter((item: any) => item.status === 'Aktif' || item.status === 'aktif').length;
  const barangNonAktif = data.filter((item: any) => item.status === 'Nonaktif' || item.status === 'non-aktif').length;

  return (
    <div className="space-y-6 max-w-7xl mx-auto">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Laporan Barang</h1>
          <p className="text-sm text-gray-500 mt-1">Laporan data seluruh barang inventaris.</p>
        </div>
        <button onClick={() => window.print()} className="inline-flex items-center justify-center rounded-md text-sm font-medium bg-gray-900 text-white hover:bg-gray-800 h-10 px-4 py-2 cursor-pointer print:hidden">
          <Printer className="mr-2 h-4 w-4" /> Cetak Laporan
        </button>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 print:hidden">
        <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
          <p className="text-sm text-gray-500">Total Barang</p>
          <p className="text-2xl font-semibold text-gray-900">{totalBarang}</p>
        </div>
        <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
          <p className="text-sm text-gray-500">Aktif</p>
          <p className="text-2xl font-semibold text-green-600">{barangAktif}</p>
        </div>
        <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
          <p className="text-sm text-gray-500">Non-Aktif / Di Gudang / Dipinjam</p>
          <p className="text-2xl font-semibold text-amber-600">{totalBarang - barangAktif}</p>
        </div>
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm text-left">
            <thead className="text-xs text-gray-500 bg-gray-50 border-b border-gray-200 uppercase">
              <tr>
                <th className="px-4 py-3 font-medium">No.</th>
                <th className="px-4 py-3 font-medium">Kode Barang</th>
                <th className="px-4 py-3 font-medium">Nama Barang</th>
                <th className="px-4 py-3 font-medium">Merek</th>
                <th className="px-4 py-3 font-medium">Ruang</th>
                <th className="px-4 py-3 font-medium">Kondisi</th>
                <th className="px-4 py-3 font-medium">Status</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {loading ? (
                <tr><td colSpan={7} className="px-6 py-12 text-center"><Loader2 className="h-6 w-6 animate-spin mx-auto text-gray-400" /></td></tr>
              ) : data.length === 0 ? (
                <tr><td colSpan={7} className="px-6 py-12 text-center text-gray-500">Tidak ada data barang.</td></tr>
              ) : data.map((item, i) => (
                <tr key={item.kode_barang || item.id || i} className="hover:bg-gray-50 transition-colors">
                  <td className="px-4 py-3 text-gray-500">{i + 1}</td>
                  <td className="px-4 py-3 font-mono text-xs text-gray-700">{item.kode_inventaris || item.kode_barang || '-'}</td>
                  <td className="px-4 py-3 font-medium text-gray-900">{item.master_barang?.nama_barang || item.nama_barang || '-'}</td>
                  <td className="px-4 py-3 text-gray-700">{item.master_barang?.merek?.nama_merek || item.merek || '-'}</td>
                  <td className="px-4 py-3 text-gray-700">{item.ruang?.nama_ruang || '-'}</td>
                  <td className="px-4 py-3 text-gray-700">{item.kondisi?.nama_kondisi || '-'}</td>
                  <td className="px-4 py-3">
                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                      item.status === 'Aktif' || item.status === 'aktif'
                        ? 'bg-green-100 text-green-800'
                        : item.status === 'Di Gudang'
                        ? 'bg-blue-100 text-blue-800'
                        : item.status === 'Dipinjam'
                        ? 'bg-yellow-100 text-yellow-800'
                        : 'bg-red-100 text-red-800'
                    }`}>
                      {item.status || '-'}
                    </span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
