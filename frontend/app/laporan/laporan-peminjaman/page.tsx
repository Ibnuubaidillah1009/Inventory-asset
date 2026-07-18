'use client';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { formatDate } from '@/lib/utils';
import { Loader2, Printer, Search } from 'lucide-react';

export default function LaporanPeminjamanPage() {
  const [data, setData] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [startDate, setStartDate] = useState('');
  const [endDate, setEndDate] = useState('');

  const fetchData = async () => {
    setLoading(true);
    try {
      let url = '/peminjaman?per_page=100';
      if (startDate) url += `&start_date=${startDate}`;
      if (endDate) url += `&end_date=${endDate}`;
      const res = await api.get(url).catch(() => ({ data: { data: [] } }));
      const raw = res.data.data;
      setData(Array.isArray(raw) ? raw : raw?.data || []);
    } finally { setLoading(false); }
  };

  useEffect(() => { fetchData(); }, []);

  const handleFilter = () => { fetchData(); };

  return (
    <div className="space-y-6 max-w-7xl mx-auto">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Laporan Peminjaman</h1>
          <p className="text-sm text-gray-500 mt-1">Laporan seluruh data peminjaman barang inventaris.</p>
        </div>
        <button onClick={() => window.print()} className="inline-flex items-center justify-center rounded-md text-sm font-medium bg-gray-900 text-white hover:bg-gray-800 h-10 px-4 py-2 cursor-pointer print:hidden">
          <Printer className="mr-2 h-4 w-4" /> Cetak Laporan
        </button>
      </div>

      <div className="flex flex-col sm:flex-row gap-3 items-end print:hidden">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
          <input type="date" value={startDate} onChange={e => setStartDate(e.target.value)} className="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900" />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
          <input type="date" value={endDate} onChange={e => setEndDate(e.target.value)} className="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900" />
        </div>
        <button onClick={handleFilter} className="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 font-medium text-sm transition-colors cursor-pointer">
          <Search className="h-4 w-4" /> Filter
        </button>
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm text-left">
            <thead className="text-xs text-gray-500 bg-gray-50 border-b border-gray-200 uppercase">
              <tr>
                <th className="px-4 py-3 font-medium">No.</th>
                <th className="px-4 py-3 font-medium">Nama Peminjam</th>
                <th className="px-4 py-3 font-medium">Barang</th>
                <th className="px-4 py-3 font-medium">Tgl Pinjam</th>
                <th className="px-4 py-3 font-medium">Tgl Kembali</th>
                <th className="px-4 py-3 font-medium">Status</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {loading ? (
                <tr><td colSpan={6} className="px-6 py-12 text-center"><Loader2 className="h-6 w-6 animate-spin mx-auto text-gray-400" /></td></tr>
              ) : data.length === 0 ? (
                <tr><td colSpan={6} className="px-6 py-12 text-center text-gray-500">Tidak ada data peminjaman.</td></tr>
              ) : data.map((item, i) => (
                <tr key={item.id || i} className="hover:bg-gray-50 transition-colors">
                  <td className="px-4 py-3 text-gray-500">{i + 1}</td>
                  <td className="px-4 py-3 font-medium text-gray-900">{item.nama_peminjam || item.user?.name || '-'}</td>
                  <td className="px-4 py-3 text-gray-700">{item.barang?.nama_barang || item.nama_barang || '-'}</td>
                  <td className="px-4 py-3 text-gray-700">{formatDate(item.tanggal_pinjam)}</td>
                  <td className="px-4 py-3 text-gray-700">{formatDate(item.tanggal_kembali)}</td>
                  <td className="px-4 py-3">
                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${item.status === 'dikembalikan' ? 'bg-green-100 text-green-800' : item.status === 'dipinjam' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'}`}>
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
