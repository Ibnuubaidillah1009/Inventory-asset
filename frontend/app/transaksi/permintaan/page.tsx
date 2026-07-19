'use client';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { formatDate } from '@/lib/utils';
import { Plus, Trash2, X, Loader2, Search, Eye, XCircle } from 'lucide-react';

import { toast } from 'sonner';
import DropdownMenu from '@/app/components/DropdownMenu';

const statusBadge: Record<string, string> = {
  Menunggu: 'bg-yellow-100 text-yellow-800',
  Disetujui: 'bg-green-100 text-green-800',
  Ditolak: 'bg-red-100 text-red-800' };

const statusLabel: Record<string, string> = {
  Menunggu: 'Menunggu',
  Disetujui: 'Disetujui',
  Ditolak: 'Ditolak' };

export default function PermintaanPage() {
  const [data, setData] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [isDetailModalOpen, setIsDetailModalOpen] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [selectedItem, setSelectedItem] = useState<any>(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [currentPage, setCurrentPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);

  const inputClass = "w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white";

  const fetchData = async (page = 1, search = searchQuery) => {
    setLoading(true);
    try {
      const res = await api.get(`/permintaan?page=${page}&search=${encodeURIComponent(search)}`).catch(() => ({ data: { data: [] } }));
      const items = Array.isArray(res.data.data) ? res.data.data : (res.data.data?.data || []);
      setData(items);
      const meta = res.data.meta || res.data.data?.meta;
      if (meta) { setCurrentPage(meta.current_page); setLastPage(meta.last_page); }
    } finally { setLoading(false); }
  };

  useEffect(() => { fetchData(); }, []);
  useEffect(() => { const d = setTimeout(() => fetchData(1, searchQuery), 500); return () => clearTimeout(d); }, [searchQuery]);

  const openDetailModal = (item: any) => { setSelectedItem(item); setIsDetailModalOpen(true); };
  const closeDetailModal = () => { setIsDetailModalOpen(false); setSelectedItem(null); };

  const handleKeputusan = async (kode: string, status: string) => {
    const label = status === 'disetujui' ? 'Menyetujui' : 'Menolak';
    if (!window.confirm(`Yakin ingin ${label} permintaan ini?`)) return;
    setIsSubmitting(true);
    try {
      await api.put(`/permintaan/${encodeURIComponent(kode)}/keputusan`, { status_persetujuan: status === 'disetujui' ? 'Disetujui' : 'Ditolak' });
      toast.success('Data berhasil disimpan');
      fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || 'Gagal memproses permintaan.');
    } finally { setIsSubmitting(false); }
  };

  const handleDelete = async (kode: string) => {
    if (!window.confirm('Apakah Anda yakin ingin menghapus permintaan ini?')) return;
    try {
      await api.delete(`/permintaan/${encodeURIComponent(kode)}`);
      toast.success('Data berhasil dihapus');
      fetchData();
    } catch (error: any) {
      toast.error(error?.response?.data?.message || 'Gagal menghapus data.');
    }
  };

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Permintaan Barang</h1>
        <p className="text-sm text-gray-500 mt-1">Kelola permintaan pengadaan barang dari berbagai unit.</p>
      </div>

      <div className="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div className="relative w-full sm:max-w-md">
          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search className="h-4 w-4 text-gray-400" />
          </div>
          <input type="text" placeholder="Cari kode permintaan, keterangan..." value={searchQuery} onChange={e => setSearchQuery(e.target.value)} className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 sm:text-sm transition-colors" />
        </div>
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm text-left">
            <thead className="text-xs text-gray-500 bg-gray-50 border-b border-gray-200 uppercase">
              <tr>
                <th className="px-6 py-4 font-medium">No.</th>
                <th className="px-6 py-4 font-medium">Kode Permintaan</th>
                <th className="px-6 py-4 font-medium">Tanggal</th>
                <th className="px-6 py-4 font-medium">Pengaju</th>
                <th className="px-6 py-4 font-medium">Keterangan</th>
                <th className="px-6 py-4 font-medium">Status</th>
                <th className="px-6 py-4 font-medium text-right">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {loading ? (
                <tr><td colSpan={7} className="px-6 py-12 text-center"><Loader2 className="h-6 w-6 animate-spin mx-auto text-gray-400" /></td></tr>
              ) : data.length === 0 ? (
                <tr><td colSpan={7} className="px-6 py-12 text-center text-gray-500">{searchQuery ? 'Tidak ada data yang cocok dengan pencarian Anda.' : 'Belum ada data permintaan.'}</td></tr>
              ) : data.map((item, i) => (
                <tr key={item.kode_permintaan || i} className="hover:bg-gray-50 transition-colors">
                  <td className="px-6 py-4 text-gray-500">{(currentPage - 1) * 15 + i + 1}</td>
                  <td className="px-6 py-4 font-mono text-xs font-medium text-gray-900">{item.kode_permintaan || '-'}</td>
                  <td className="px-6 py-4 text-gray-500">{formatDate(item.tanggal_permintaan)}</td>
                  <td className="px-6 py-4 text-gray-900">{item.pengguna?.username || '-'}</td>
                  <td className="px-6 py-4 text-gray-500 max-w-[200px] truncate">{item.keterangan_keperluan || '-'}</td>
                  <td className="px-6 py-4">
                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusBadge[item.status_persetujuan] || 'bg-gray-100 text-gray-800'}`}>
                      {statusLabel[item.status_persetujuan] || item.status_persetujuan || '-'}
                    </span>
                  </td>
                  <td className="px-6 py-4 text-right">
                                      <DropdownMenu actions={[
                    { label: 'Lihat Detail', icon: Eye, onClick: () => openDetailModal(item), variant: 'default' },
                    { label: 'Hapus', icon: Trash2, onClick: () => handleDelete(item.kode_permintaan), variant: 'danger' },
                  ]} />
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
        <div className="px-6 py-4 border-t border-gray-200 flex items-center justify-between bg-gray-50">
          <span className="text-sm text-gray-500">Halaman {currentPage} dari {lastPage}</span>
          <div className="flex gap-2">
            <button onClick={() => fetchData(currentPage - 1)} disabled={currentPage === 1} className="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">Sebelumnya</button>
            <button onClick={() => fetchData(currentPage + 1)} disabled={currentPage === lastPage} className="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">Selanjutnya &rarr;</button>
          </div>
        </div>
      </div>

      {isDetailModalOpen && selectedItem && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100">
              <h3 className="text-lg font-semibold text-gray-900">Detail Permintaan</h3>
              <button onClick={closeDetailModal} className="text-gray-400 hover:text-gray-500"><X className="h-5 w-5" /></button>
            </div>
            <div className="p-6 space-y-4 text-sm">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Kode Permintaan</label>
                  <div className="text-gray-900 font-mono font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.kode_permintaan || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Tanggal</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{formatDate(selectedItem.tanggal_permintaan)}</div>
                </div>
              </div>
              <div>
                <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Pengaju</label>
                <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.pengguna?.username || '-'}</div>
              </div>
              <div>
                <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Keterangan Keperluan</label>
                <div className="text-gray-900 bg-gray-50 p-2 rounded-md border border-gray-100 whitespace-pre-wrap">{selectedItem.keterangan_keperluan || '-'}</div>
              </div>
              <div>
                <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Status</label>
                <div className="bg-gray-50 p-2 rounded-md border border-gray-100">
                  <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusBadge[selectedItem.status_persetujuan] || 'bg-gray-100 text-gray-800'}`}>
                    {statusLabel[selectedItem.status_persetujuan] || selectedItem.status_persetujuan || '-'}
                  </span>
                </div>
              </div>
              {selectedItem.detail_permintaan && selectedItem.detail_permintaan.length > 0 && (
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Detail Barang Diminta</label>
                  <div className="bg-gray-50 rounded-md border border-gray-100 divide-y divide-gray-200">
                    {selectedItem.detail_permintaan.map((d: any, idx: number) => (
                      <div key={idx} className="px-3 py-2 text-sm">
                        <span className="font-medium text-gray-900">{d.master_barang?.nama_barang || `Barang #${d.id_master_barang}`}</span>
                        <span className="text-gray-500 ml-2">× {d.jumlah_diminta}</span>
                        {d.alasan_kebutuhan && <span className="text-gray-400 ml-2">— {d.alasan_kebutuhan}</span>}
                      </div>
                    ))}
                  </div>
                </div>
              )}
              <div className="pt-4 flex justify-end">
                <button type="button" onClick={closeDetailModal} className="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 font-medium transition-colors cursor-pointer">Tutup</button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
