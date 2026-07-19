'use client';

import { extractData } from '@/lib/utils';


import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { Loader2, Search, RotateCcw } from 'lucide-react';

import { toast } from 'sonner';

export default function BarangNonAktifPage() {
  const [data, setData] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');
  const [currentPage, setCurrentPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);

  const fetchData = async (page = 1, search = searchQuery) => {
    setLoading(true);
    try {
      const res = await api.get(`/aset?status=non-aktif&kategori=barang&page=${page}&search=${encodeURIComponent(search)}`).catch(() => ({ data: { data: [] } }));
      setData(extractData(res.data.data));
      if (res.data.meta) { setCurrentPage(res.data.meta.current_page); setLastPage(res.data.meta.last_page); }
    } finally { setLoading(false); }
  };

  useEffect(() => { fetchData(); }, []);
  useEffect(() => { const d = setTimeout(() => fetchData(1, searchQuery), 500); return () => clearTimeout(d); }, [searchQuery]);

  const handleAktifkan = async (id: number) => {
    if (!confirm('Yakin ingin mengaktifkan kembali barang ini?')) return;
    try {
      await api.put(`/aset/${id}/status`, { status: 'aktif' });
      toast.success('Data berhasil disimpan');
      fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || 'Gagal mengaktifkan barang.');
    }
  };

  return (
    <div className="space-y-6 max-w-7xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Barang Non-Aktif</h1>
        <p className="text-sm text-gray-500 mt-1">Daftar barang inventaris yang sudah tidak aktif.</p>
      </div>

      <div className="relative w-full sm:max-w-md">
        <Search className="absolute left-3 top-2.5 h-4 w-4 text-gray-400" />
        <input type="text" placeholder="Cari kode atau nama barang..." value={searchQuery} onChange={e => setSearchQuery(e.target.value)} className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md bg-white placeholder-gray-500 focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 sm:text-sm" />
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
                <th className="px-4 py-3 font-medium text-right">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {loading ? (
                <tr><td colSpan={8} className="px-6 py-12 text-center"><Loader2 className="h-6 w-6 animate-spin mx-auto text-gray-400" /></td></tr>
              ) : data.length === 0 ? (
                <tr><td colSpan={8} className="px-6 py-12 text-center text-gray-500">Tidak ada data barang non-aktif.</td></tr>
              ) : data.map((item, i) => (
                <tr key={item.id || i} className="hover:bg-gray-50 transition-colors">
                  <td className="px-4 py-3 text-gray-500">{(currentPage - 1) * 10 + i + 1}</td>
                  <td className="px-4 py-3 font-mono text-xs text-gray-700">{item.kode_barang || '-'}</td>
                  <td className="px-4 py-3 font-medium text-gray-900">{item.nama_barang || '-'}</td>
                  <td className="px-4 py-3 text-gray-700">{item.merek || '-'}</td>
                  <td className="px-4 py-3 text-gray-700">{item.ruang?.nama_ruang || '-'}</td>
                  <td className="px-4 py-3 text-gray-700">{item.kondisi?.nama_kondisi || '-'}</td>
                  <td className="px-4 py-3">
                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                      {item.status || 'Non-Aktif'}
                    </span>
                  </td>
                  <td className="px-4 py-3 text-right">
                    <button onClick={() => handleAktifkan(item.id)} className="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 border border-green-200 rounded-md hover:bg-green-100 transition-colors cursor-pointer" title="Aktifkan">
                      <RotateCcw className="h-3 w-3" />
                      Aktifkan
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
        {lastPage > 1 && (
          <div className="px-4 py-3 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500">
            <span>Halaman {currentPage} dari {lastPage}</span>
            <div className="flex gap-2">
              <button onClick={() => fetchData(currentPage - 1)} disabled={currentPage <= 1} className="px-3 py-1 border rounded-md disabled:opacity-40 hover:bg-gray-50 cursor-pointer">Sebelumnya</button>
              <button onClick={() => fetchData(currentPage + 1)} disabled={currentPage >= lastPage} className="px-3 py-1 border rounded-md disabled:opacity-40 hover:bg-gray-50 cursor-pointer">Berikutnya</button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
