'use client';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { Loader2, Search, RotateCcw, Eye, X } from 'lucide-react';
import { formatDate } from '@/lib/utils';

import { toast } from 'sonner';

export default function PengembalianPage() {
  const [data, setData] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [isProcessing, setIsProcessing] = useState<string | null>(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedItem, setSelectedItem] = useState<any>(null);
  const [isDetailOpen, setIsDetailOpen] = useState(false);

  const fetchData = async () => {
    setLoading(true);
    try {
      const res = await api.get('/peminjaman?per_page=100').catch(() => ({ data: { data: { data: [] } } }));
      const items = Array.isArray(res.data.data) ? res.data.data : (res.data.data?.data || []);
      setData(items);
    } catch (error) {
      console.error('Gagal mengambil data peminjaman', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchData(); }, []);

  const handleKembalikan = async (nomor: string) => {
    if (!window.confirm('Konfirmasi pengembalian peminjaman ini?')) return;
    setIsProcessing(nomor);
    try {
      await api.put(`/peminjaman/${encodeURIComponent(nomor)}/kembalikan`);
      toast.success('Data berhasil disimpan');
      fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || 'Gagal mengembalikan.');
    } finally {
      setIsProcessing(null);
    }
  };

  const filtered = data.filter((item) =>
    !searchQuery ||
    item.nomor_peminjaman?.toLowerCase().includes(searchQuery.toLowerCase()) ||
    item.nama_peminjam?.toLowerCase().includes(searchQuery.toLowerCase()) ||
    item.status_peminjaman?.toLowerCase().includes(searchQuery.toLowerCase())
  );

  const activeLoans = filtered.filter((item) => item.status_peminjaman === 'Sedang Dipinjam');
  const returnedLoans = filtered.filter((item) => item.status_peminjaman === 'Dikembalikan');

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight flex items-center gap-2"><RotateCcw className="h-6 w-6" /> Pengembalian</h1>
        <p className="text-sm text-gray-500 mt-1">Proses pengembalian barang yang sedang dipinjam.</p>
      </div>

      <div className="relative w-full sm:max-w-md">
        <Search className="absolute left-3 top-2.5 h-4 w-4 text-gray-400" />
        <input type="text" placeholder="Cari berdasarkan nomor, nama peminjam..." value={searchQuery} onChange={(e) => setSearchQuery(e.target.value)} className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 sm:text-sm" />
      </div>

      {/* Aktif (Sedang Dipinjam) */}
      <div>
        <h2 className="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Sedang Dipinjam ({activeLoans.length})</h2>
        <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full text-sm text-left">
              <thead className="text-xs text-gray-500 bg-gray-50 border-b border-gray-200 uppercase">
                <tr>
                  <th className="px-6 py-4 font-medium">No.</th>
                  <th className="px-6 py-4 font-medium">Nomor Peminjaman</th>
                  <th className="px-6 py-4 font-medium">Peminjam</th>
                  <th className="px-6 py-4 font-medium">Tgl Pinjam</th>
                  <th className="px-6 py-4 font-medium">Lama (Hari)</th>
                  <th className="px-6 py-4 font-medium">Status</th>
                  <th className="px-6 py-4 font-medium text-right">Aksi</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {loading ? (
                  <tr><td colSpan={7} className="px-6 py-12 text-center"><Loader2 className="h-6 w-6 animate-spin mx-auto text-gray-400" /></td></tr>
                ) : activeLoans.length === 0 ? (
                  <tr><td colSpan={7} className="px-6 py-12 text-center text-gray-500">Tidak ada peminjaman aktif.</td></tr>
                ) : activeLoans.map((item, i) => (
                  <tr key={item.nomor_peminjaman || i} className="hover:bg-gray-50 transition-colors">
                    <td className="px-6 py-4 text-gray-500">{i + 1}</td>
                    <td className="px-6 py-4 font-medium text-gray-900">{item.nomor_peminjaman}</td>
                    <td className="px-6 py-4 text-gray-900">{item.nama_peminjam}</td>
                    <td className="px-6 py-4 text-gray-500">{formatDate(item.tanggal_pinjam)}</td>
                    <td className="px-6 py-4 text-gray-500">{item.lama_pinjam_hari} hari</td>
                    <td className="px-6 py-4">
                      <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{item.status_peminjaman}</span>
                    </td>
                    <td className="px-6 py-4 text-right">
                      <button onClick={() => { setSelectedItem(item); setIsDetailOpen(true); }} className="text-gray-400 hover:text-blue-600 mr-3 cursor-pointer" title="Rincian"><Eye className="h-4 w-4" /></button>
                      <button onClick={() => handleKembalikan(item.nomor_peminjaman)} disabled={isProcessing === item.nomor_peminjaman} className="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-md hover:bg-green-700 disabled:opacity-50 cursor-pointer">
                        {isProcessing === item.nomor_peminjaman ? <Loader2 className="h-3 w-3 animate-spin mr-1" /> : <RotateCcw className="h-3 w-3 mr-1" />}
                        Kembalikan
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {/* Sudah Dikembalikan */}
      {returnedLoans.length > 0 && (
        <div>
          <h2 className="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Sudah Dikembalikan ({returnedLoans.length})</h2>
          <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div className="overflow-x-auto">
              <table className="w-full text-sm text-left">
                <thead className="text-xs text-gray-500 bg-gray-50 border-b border-gray-200 uppercase">
                  <tr>
                    <th className="px-6 py-4 font-medium">No.</th>
                    <th className="px-6 py-4 font-medium">Nomor Peminjaman</th>
                    <th className="px-6 py-4 font-medium">Peminjam</th>
                    <th className="px-6 py-4 font-medium">Tgl Pinjam</th>
                    <th className="px-6 py-4 font-medium">Status</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200">
                  {returnedLoans.map((item, i) => (
                    <tr key={item.nomor_peminjaman || i} className="hover:bg-gray-50 transition-colors">
                      <td className="px-6 py-4 text-gray-500">{i + 1}</td>
                      <td className="px-6 py-4 font-medium text-gray-900">{item.nomor_peminjaman}</td>
                      <td className="px-6 py-4 text-gray-900">{item.nama_peminjam}</td>
                      <td className="px-6 py-4 text-gray-500">{formatDate(item.tanggal_pinjam)}</td>
                      <td className="px-6 py-4">
                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{item.status_peminjaman}</span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}

      {/* Detail Modal */}
      {isDetailOpen && selectedItem && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100">
              <h3 className="text-lg font-semibold text-gray-900">Detail Peminjaman</h3>
              <button onClick={() => { setIsDetailOpen(false); setSelectedItem(null); }} className="text-gray-400 hover:text-gray-500"><X className="h-5 w-5" /></button>
            </div>
            <div className="p-6 space-y-3 text-sm">
              {[
                ['Nomor Peminjaman', selectedItem.nomor_peminjaman],
                ['Nama Peminjam', selectedItem.nama_peminjam],
                ['No. Telepon', selectedItem.nomor_telepon],
                ['Tanggal Pinjam', formatDate(selectedItem.tanggal_pinjam)],
                ['Lama Pinjam', `${selectedItem.lama_pinjam_hari} hari`],
                ['Status', selectedItem.status_peminjaman],
                ['Keterangan', selectedItem.keterangan],
              ].map(([label, value]) => (
                <div key={label} className="flex justify-between">
                  <span className="text-gray-500">{label}</span>
                  <span className="text-gray-900 font-medium text-right">{value || '-'}</span>
                </div>
              ))}
              {selectedItem.detail_peminjaman?.length > 0 && (
                <div className="pt-2">
                  <span className="text-gray-500 text-xs uppercase tracking-wider">Barang Dipinjam:</span>
                  <ul className="mt-2 space-y-1">
                    {selectedItem.detail_peminjaman.map((d: any, idx: number) => (
                      <li key={idx} className="text-gray-900 bg-gray-50 p-2 rounded-md border border-gray-100 text-xs">
                        {d.aset?.kode_inventaris || d.kode_barang} - {d.aset?.master_barang?.nama_barang || 'Barang'}
                        {d.aset?.kondisi?.nama_kondisi ? ` (${d.aset.kondisi.nama_kondisi})` : ''}
                      </li>
                    ))}
                  </ul>
                </div>
              )}
            </div>
            <div className="px-6 py-4 border-t border-gray-100 flex justify-end bg-gray-50">
              <button onClick={() => { setIsDetailOpen(false); setSelectedItem(null); }} className="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 font-medium cursor-pointer">Tutup</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
