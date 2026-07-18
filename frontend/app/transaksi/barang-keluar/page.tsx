'use client';

import { extractData } from '@/lib/utils';


import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { Trash2, X, Loader2, Search, Eye } from 'lucide-react';

export default function BarangKeluarPage() {
  const [data, setData] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDetailModalOpen, setIsDetailModalOpen] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [selectedItem, setSelectedItem] = useState<any>(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [currentPage, setCurrentPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);

  const [formData, setFormData] = useState({
    kode_barang: '',
    jumlah: '',
    tanggal_keluar: '',
    penerima: '',
    keterangan: '',
  });

  const inputClass = "w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white";

  const fetchData = async (page = 1, search = searchQuery) => {
    setLoading(true);
    try {
      const res = await api.get(`/barang-keluar?page=${page}&search=${encodeURIComponent(search)}`).catch(() => ({ data: { data: [] } }));
      setData(extractData(res.data.data));
      if (res.data.meta) { setCurrentPage(res.data.meta.current_page); setLastPage(res.data.meta.last_page); }
    } finally { setLoading(false); }
  };

  useEffect(() => { fetchData(); }, []);
  useEffect(() => { const d = setTimeout(() => fetchData(1, searchQuery), 500); return () => clearTimeout(d); }, [searchQuery]);

  const openModal = () => {
    setFormData({ kode_barang: '', jumlah: '', tanggal_keluar: new Date().toISOString().split('T')[0], penerima: '', keterangan: '' });
    setIsModalOpen(true);
  };

  const closeModal = () => { setIsModalOpen(false); };

  const openDetailModal = (item: any) => { setSelectedItem(item); setIsDetailModalOpen(true); };
  const closeDetailModal = () => { setIsDetailModalOpen(false); setSelectedItem(null); };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    try {
      await api.post('/barang-keluar', { ...formData, jumlah: Number(formData.jumlah) });
      closeModal();
      fetchData();
    } catch (error: any) {
      alert(error?.response?.data?.message || 'Gagal menyimpan data barang keluar.');
    } finally { setIsSubmitting(false); }
  };

  const handleDelete = async (id: number) => {
    if (!window.confirm('Apakah Anda yakin ingin menghapus data barang keluar ini?')) return;
    try {
      await api.delete(`/barang-keluar/${id}`);
      fetchData();
    } catch (error: any) {
      alert(error?.response?.data?.message || 'Gagal menghapus data.');
    }
  };

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Barang Keluar</h1>
          <p className="text-sm text-gray-500 mt-1">Catat barang yang keluar dari inventaris.</p>
        </div>
        <button onClick={() => openModal()} className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-gray-900 text-white hover:bg-gray-800 h-10 px-4 py-2 cursor-pointer">
          <span className="mr-2 h-4 w-4" /> Tambah Barang Keluar
        </button>
      </div>

      <div className="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div className="relative w-full sm:max-w-md">
          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search className="h-4 w-4 text-gray-400" />
          </div>
          <input type="text" placeholder="Cari kode barang, penerima..." value={searchQuery} onChange={e => setSearchQuery(e.target.value)} className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 sm:text-sm transition-colors" />
        </div>
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm text-left">
            <thead className="text-xs text-gray-500 bg-gray-50 border-b border-gray-200 uppercase">
              <tr>
                <th className="px-6 py-4 font-medium">No.</th>
                <th className="px-6 py-4 font-medium">ID</th>
                <th className="px-6 py-4 font-medium">Kode Barang</th>
                <th className="px-6 py-4 font-medium">Jumlah</th>
                <th className="px-6 py-4 font-medium">Tanggal Keluar</th>
                <th className="px-6 py-4 font-medium">Penerima</th>
                <th className="px-6 py-4 font-medium">Keterangan</th>
                <th className="px-6 py-4 font-medium text-right">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {loading ? (
                <tr><td colSpan={8} className="px-6 py-12 text-center"><Loader2 className="h-6 w-6 animate-spin mx-auto text-gray-400" /></td></tr>
              ) : data.length === 0 ? (
                <tr><td colSpan={8} className="px-6 py-12 text-center text-gray-500">{searchQuery ? 'Tidak ada data yang cocok dengan pencarian Anda.' : 'Belum ada data barang keluar.'}</td></tr>
              ) : data.map((item, i) => (
                <tr key={item.id || i} className="hover:bg-gray-50 transition-colors">
                  <td className="px-6 py-4 text-gray-500">{(currentPage - 1) * 10 + i + 1}</td>
                  <td className="px-6 py-4 font-medium text-gray-900">{item.id || '-'}</td>
                  <td className="px-6 py-4 font-mono text-xs text-gray-700">{item.kode_barang || '-'}</td>
                  <td className="px-6 py-4 text-gray-700">{item.jumlah || '-'}</td>
                  <td className="px-6 py-4 text-gray-500">{item.tanggal_keluar || '-'}</td>
                  <td className="px-6 py-4 text-gray-900">{item.penerima?.username || item.nama_penerima || '-'}</td>
                  <td className="px-6 py-4 text-gray-500 max-w-[150px] truncate">{item.keterangan || '-'}</td>
                  <td className="px-6 py-4 text-right">
                    <button onClick={() => openDetailModal(item)} className="text-gray-400 hover:text-blue-600 mr-3 transition-colors cursor-pointer" title="Lihat Detail"><Eye className="h-4 w-4" /></button>
                    <button onClick={() => handleDelete(item.id)} className="text-gray-400 hover:text-red-600 transition-colors cursor-pointer" title="Hapus"><Trash2 className="h-4 w-4" /></button>
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

      {/* Detail Modal */}
      {isDetailModalOpen && selectedItem && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100">
              <h3 className="text-lg font-semibold text-gray-900">Detail Barang Keluar</h3>
              <button onClick={closeDetailModal} className="text-gray-400 hover:text-gray-500"><X className="h-5 w-5" /></button>
            </div>
            <div className="p-6 space-y-4 text-sm">
              {[
                ['ID', selectedItem.id],
                ['Kode Barang', selectedItem.kode_barang],
                ['Jumlah', selectedItem.jumlah],
                ['Tanggal Keluar', selectedItem.tanggal_keluar],
                ['Penerima', selectedItem.penerima?.username || selectedItem.nama_penerima],
                ['Keterangan', selectedItem.keterangan],
              ].map(([label, value]) => (
                <div key={label}>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">{label}</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100 whitespace-pre-wrap">{value || '-'}</div>
                </div>
              ))}
              <div className="pt-4 flex justify-end">
                <button type="button" onClick={closeDetailModal} className="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 font-medium transition-colors cursor-pointer">Tutup</button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Form Modal */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100">
              <h3 className="text-lg font-semibold text-gray-900">Tambah Barang Keluar</h3>
              <button onClick={closeModal} className="text-gray-400 hover:text-gray-500"><X className="h-5 w-5" /></button>
            </div>
            <form onSubmit={handleSubmit} className="p-6 space-y-4 text-sm">
              <div>
                <label className="block font-medium text-gray-700 mb-1">Kode Barang *</label>
                <input type="text" required value={formData.kode_barang} onChange={e => setFormData({ ...formData, kode_barang: e.target.value })} className={inputClass} placeholder="Masukkan kode barang" />
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Jumlah *</label>
                <input type="number" min="1" required value={formData.jumlah} onChange={e => setFormData({ ...formData, jumlah: e.target.value })} className={inputClass} placeholder="0" />
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Tanggal Keluar *</label>
                <input type="date" required value={formData.tanggal_keluar} onChange={e => setFormData({ ...formData, tanggal_keluar: e.target.value })} className={inputClass} />
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Penerima *</label>
                <input type="text" required value={formData.penerima} onChange={e => setFormData({ ...formData, penerima: e.target.value })} className={inputClass} placeholder="Nama penerima" />
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea rows={3} value={formData.keterangan} onChange={e => setFormData({ ...formData, keterangan: e.target.value })} className={inputClass} placeholder="Opsional" />
              </div>
              <div className="pt-4 flex justify-end gap-3">
                <button type="button" onClick={closeModal} className="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 font-medium transition-colors cursor-pointer">Batal</button>
                <button type="submit" disabled={isSubmitting} className="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 font-medium transition-colors flex items-center disabled:opacity-50 cursor-pointer">
                  {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />} Simpan
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
