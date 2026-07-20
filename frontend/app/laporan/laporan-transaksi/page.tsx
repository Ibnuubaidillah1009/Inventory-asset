'use client';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { formatDate, extractData } from '@/lib/utils';
import { Loader2, Printer } from 'lucide-react';

export default function LaporanTransaksiPage() {
  const [data, setData] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  const fetchData = async () => {
    setLoading(true);
    try {
      const [peminjamanRes, pengadaanRes, permintaanRes] = await Promise.all([
        api.get('/peminjaman?per_page=100').catch(() => ({ data: { data: [] } })),
        api.get('/pengadaan?per_page=100').catch(() => ({ data: { data: [] } })),
        api.get('/permintaan?per_page=100').catch(() => ({ data: { data: [] } })),
      ]);
      const items: any[] = [];
      extractData(peminjamanRes.data.data).forEach((p: any) => {
        items.push({ tanggal: p.tanggal_pinjam, jenis: 'Peminjaman', deskripsi: `Peminjaman oleh ${p.peminjam || '-'}`, status: p.status || 'dipinjam' });
      });
      extractData(pengadaanRes.data.data).forEach((p: any) => {
        items.push({ tanggal: p.tanggal_pengadaan, jenis: 'Pengadaan', deskripsi: p.keterangan || `Pengadaan #${p.id_pengadaan || p.id}`, status: p.status || 'Menunggu Proses' });
      });
      extractData(permintaanRes.data.data).forEach((p: any) => {
        items.push({ tanggal: p.tanggal, jenis: 'Permintaan', deskripsi: `Permintaan dari ${p.pengaju || '-'}`, status: p.status || 'pending' });
      });
      items.sort((a, b) => (b.tanggal || '').localeCompare(a.tanggal || ''));
      setData(items);
    } finally { setLoading(false); }
  };

  useEffect(() => { fetchData(); }, []);

  return (
    <div className="space-y-6 max-w-7xl mx-auto">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Laporan Transaksi</h1>
          <p className="text-sm text-gray-500 mt-1">Laporan seluruh transaksi inventaris.</p>
        </div>
        <button onClick={() => window.print()} className="inline-flex items-center justify-center rounded-md text-sm font-medium bg-gray-900 text-white hover:bg-gray-800 h-10 px-4 py-2 cursor-pointer print:hidden">
          <Printer className="mr-2 h-4 w-4" /> Cetak Laporan
        </button>
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm text-left">
            <thead className="text-xs text-gray-500 bg-gray-50 border-b border-gray-200 uppercase">
              <tr>
                <th className="px-4 py-3 font-medium">No.</th>
                <th className="px-4 py-3 font-medium">Tanggal</th>
                <th className="px-4 py-3 font-medium">Jenis Transaksi</th>
                <th className="px-4 py-3 font-medium">Deskripsi</th>
                <th className="px-4 py-3 font-medium">Jumlah</th>
                <th className="px-4 py-3 font-medium">Status</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {loading ? (
                <tr><td colSpan={6} className="px-6 py-12 text-center"><Loader2 className="h-6 w-6 animate-spin mx-auto text-gray-400" /></td></tr>
              ) : data.length === 0 ? (
                <tr><td colSpan={6} className="px-6 py-12 text-center text-gray-500">Tidak ada data transaksi.</td></tr>
              ) : data.map((item, i) => (
                <tr key={item.id || i} className="hover:bg-gray-50 transition-colors">
                  <td className="px-4 py-3 text-gray-500">{i + 1}</td>
                  <td className="px-4 py-3 text-gray-700">{item.tanggal || '-'}</td>
                  <td className="px-4 py-3 text-gray-700">{item.jenis || '-'}</td>
                  <td className="px-4 py-3 font-medium text-gray-900">{item.deskripsi || '-'}</td>
                  <td className="px-4 py-3 text-gray-700">-</td>
                  <td className="px-4 py-3">
                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${item.status === 'berhasil' || item.status === 'Selesai' || item.status === 'dikembalikan' ? 'bg-green-100 text-green-800' : item.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'}`}>
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
