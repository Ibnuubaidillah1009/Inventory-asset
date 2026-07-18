'use client';

import { extractData } from '@/lib/utils';


import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { Loader2, Search } from 'lucide-react';

export default function TanahNonAktifPage() {
  const [data, setData] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');
  const [currentPage, setCurrentPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);

  const fetchData = async (page = 1, search = searchQuery) => {
    setLoading(true);
    try {
      const res = await api.get(`/tanah-non-aktif?page=${page}&search=${encodeURIComponent(search)}`).catch(() => ({ data: { data: [] } }));
      setData(extractData(res.data.data));
      if (res.data.meta) { setCurrentPage(res.data.meta.current_page); setLastPage(res.data.meta.last_page); }
    } finally { setLoading(false); }
  };

  useEffect(() => { fetchData(); }, []);
  useEffect(() => { const d = setTimeout(() => fetchData(1, searchQuery), 500); return () => clearTimeout(d); }, [searchQuery]);

  return (
    <div className="space-y-6 max-w-7xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Tanah Non-Aktif</h1>
        <p className="text-sm text-gray-500 mt-1">Daftar aset tanah yang sudah tidak aktif.</p>
      </div>

      <div className="relative w-full sm:max-w-md">
        <Search className="absolute left-3 top-2.5 h-4 w-4 text-gray-400" />
        <input type="text" placeholder="Cari kode atau nama tanah..." value={searchQuery} onChange={e => setSearchQuery(e.target.value)} className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md bg-white placeholder-gray-500 focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 sm:text-sm" />
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm text-left">
            <thead className="text-xs text-gray-500 bg-gray-50 border-b border-gray-200 uppercase">
              <tr>
                <th className="px-4 py-3 font-medium">No.</th>
                <th className="px-4 py-3 font-medium">Kode Tanah</th>
                <th className="px-4 py-3 font-medium">Nama Tanah</th>
                <th className="px-4 py-3 font-medium">Luas</th>
                <th className="px-4 py-3 font-medium">Lokasi</th>
                <th className="px-4 py-3 font-medium">Status</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {loading ? (
                <tr><td colSpan={6} className="px-6 py-12 text-center"><Loader2 className="h-6 w-6 animate-spin mx-auto text-gray-400" /></td></tr>
              ) : data.length === 0 ? (
                <tr><td colSpan={6} className="px-6 py-12 text-center text-gray-500">Tidak ada data tanah non-aktif.</td></tr>
              ) : data.map((item, i) => (
                <tr key={item.id || i} className="hover:bg-gray-50 transition-colors">
                  <td className="px-4 py-3 text-gray-500">{(currentPage - 1) * 10 + i + 1}</td>
                  <td className="px-4 py-3 font-mono text-xs text-gray-700">{item.kode_tanah || '-'}</td>
                  <td className="px-4 py-3 font-medium text-gray-900">{item.nama_tanah || '-'}</td>
                  <td className="px-4 py-3 text-gray-700">{item.luas ? `${item.luas} m²` : '-'}</td>
                  <td className="px-4 py-3 text-gray-700">{item.lokasi || '-'}</td>
                  <td className="px-4 py-3">
                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                      {item.status || 'Non-Aktif'}
                    </span>
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
