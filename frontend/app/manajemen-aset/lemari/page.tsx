'use client';

import { extractData } from '@/lib/utils';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { Plus, Pencil, Trash2, X, Loader2, Search, Eye } from 'lucide-react';

import { toast } from 'sonner';

export default function LemariPage() {
  const [data, setData] = useState<any[]>([]);
  const [ruangList, setRuangList] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDetailModalOpen, setIsDetailModalOpen] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedItem, setSelectedItem] = useState<any>(null);
  const [currentPage, setCurrentPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);

  // Form State
  const [formData, setFormData] = useState({
    id_ruang: '',
    nama_lemari: '',
    keterangan: '',
  });

  const fetchData = async (page = 1, search = searchQuery) => {
    setLoading(true);
    try {
      const [resLemari, resRuang] = await Promise.all([
        api.get(`/lemari?page=${page}&search=${encodeURIComponent(search)}`),
        api.get('/ruang').catch(() => ({ data: { data: [] } }))
      ]);

      setData(extractData(resLemari.data.data));
      if (resLemari.data.meta) {
        setCurrentPage(resLemari.data.meta.current_page);
        setLastPage(resLemari.data.meta.last_page);
      }
      setRuangList(extractData(resRuang.data.data));
    } catch (error) {
      console.error('Gagal mengambil data lemari', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    const delayDebounceFn = setTimeout(() => {
      fetchData(1, searchQuery);
    }, 500);

    return () => clearTimeout(delayDebounceFn);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [searchQuery]);

  const openModal = (item: any = null) => {
    if (item) {
      setEditingId(item.id_lemari || item.id);
      setFormData({
        id_ruang: item.id_ruang || '',
        nama_lemari: item.nama_lemari || '',
        keterangan: item.keterangan || '',
      });
    } else {
      setEditingId(null);
      setFormData({ id_ruang: '', nama_lemari: '', keterangan: '' });
    }
    setIsModalOpen(true);
  };

  const closeModal = () => {
    setIsModalOpen(false);
    setEditingId(null);
  };

  const openDetailModal = (item: any) => {
    setSelectedItem(item);
    setIsDetailModalOpen(true);
  };

  const closeDetailModal = () => {
    setIsDetailModalOpen(false);
    setSelectedItem(null);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    try {
      if (editingId) {
        await api.put(`/lemari/${editingId}`, formData);
      } else {
        await api.post('/lemari', formData);
      }
      toast.success('Data berhasil disimpan');
      closeModal();
      fetchData();
    } catch (error) {
      console.error('Gagal menyimpan data', error);
      toast.error('Gagal menyimpan data. Periksa kembali input Anda.');
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleDelete = async (id: number) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus lemari ini?')) {
      try {
        await api.delete(`/lemari/${id}`);
        toast.success('Data berhasil dihapus');
        fetchData();
      } catch (error) {
        console.error('Gagal menghapus data', error);
        toast.error('Gagal menghapus data. Lemari mungkin sedang digunakan.');
      }
    }
  };

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      {/* Header Halaman */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Master Lemari</h1>
          <p className="text-sm text-gray-500 mt-1">Kelola daftar lemari penyimpanan di ruang kelas atau lab.</p>
        </div>
        <button
          onClick={() => openModal()}
          className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-gray-900 text-white hover:bg-gray-800 h-10 px-4 py-2 cursor-pointer"
        >
          <Plus className="mr-2 h-4 w-4" />
          Tambah Lemari
        </button>
      </div>

      {/* Filter dan Pencarian */}
      <div className="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div className="relative w-full sm:max-w-md">
          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search className="h-4 w-4 text-gray-400" />
          </div>
          <input
            type="text"
            placeholder="Cari lemari..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 sm:text-sm transition-colors"
          />
        </div>
      </div>

      {/* Tabel Data */}
      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm text-left">
            <thead className="text-xs text-gray-500 bg-gray-50 border-b border-gray-200 uppercase">
              <tr>
                <th className="px-6 py-4 font-medium">No.</th>
                <th className="px-6 py-4 font-medium">ID Lemari</th>
                <th className="px-6 py-4 font-medium">Nama Lemari</th>
                <th className="px-6 py-4 font-medium">Ruang</th>
                <th className="px-6 py-4 font-medium">Keterangan</th>
                <th className="px-6 py-4 font-medium text-right">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {loading ? (
                <tr>
                  <td colSpan={6} className="px-6 py-12 text-center">
                    <Loader2 className="h-6 w-6 animate-spin mx-auto text-gray-400" />
                  </td>
                </tr>
              ) : data.length === 0 ? (
                <tr>
                  <td colSpan={6} className="px-6 py-12 text-center text-gray-500">
                    {searchQuery ? 'Tidak ada data yang cocok dengan pencarian Anda.' : 'Belum ada data lemari.'}
                  </td>
                </tr>
              ) : (
                data.map((item, index) => (
                  <tr key={index} className="hover:bg-gray-50 transition-colors">
                    <td className="px-6 py-4 text-gray-500">{index + 1}</td>
                    <td className="px-6 py-4 font-medium text-gray-900">{item.id_lemari || '-'}</td>
                    <td className="px-6 py-4 text-gray-900">{item.nama_lemari}</td>
                    <td className="px-6 py-4 text-gray-500">{item.ruang?.nama_ruang || '-'}</td>
                    <td className="px-6 py-4 text-gray-500">{item.keterangan || '-'}</td>
                    <td className="px-6 py-4 text-right">
                      <button onClick={() => openDetailModal(item)} className="text-gray-400 hover:text-blue-600 mr-3 transition-colors cursor-pointer" title="Lihat Detail">
                        <Eye className="h-4 w-4" />
                      </button>
                      <button onClick={() => openModal(item)} className="text-gray-400 hover:text-gray-900 mr-3 transition-colors cursor-pointer" title="Ubah">
                        <Pencil className="h-4 w-4" />
                      </button>
                      <button onClick={() => handleDelete(item.id_lemari || item.id)} className="text-gray-400 hover:text-red-600 transition-colors cursor-pointer" title="Hapus">
                        <Trash2 className="h-4 w-4" />
                      </button>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
        {/* Pagination */}
        <div className="px-6 py-4 border-t border-gray-200 flex items-center justify-between bg-gray-50">
          <span className="text-sm text-gray-500">
            Halaman {currentPage} dari {lastPage}
          </span>
          <div className="flex gap-2">
            <button
              onClick={() => fetchData(currentPage - 1)}
              disabled={currentPage === 1}
              className="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
            >
              Sebelumnya
            </button>
            <button
              onClick={() => fetchData(currentPage + 1)}
              disabled={currentPage === lastPage}
              className="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
            >
              Selanjutnya &rarr;
            </button>
          </div>
        </div>
      </div>

      {/* Pop Up Modal Detail */}
      {isDetailModalOpen && selectedItem && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100">
              <h3 className="text-lg font-semibold text-gray-900">Detail Lemari</h3>
              <button onClick={closeDetailModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>
            
            <div className="p-6 space-y-4 text-sm">
              <div>
                <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">ID Lemari</label>
                <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.id_lemari || '-'}</div>
              </div>
              <div>
                <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Nama Lemari</label>
                <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.nama_lemari || '-'}</div>
              </div>
              <div>
                <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Ruang</label>
                <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.ruang?.nama_ruang || '-'}</div>
              </div>
              <div>
                <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Keterangan</label>
                <div className="text-gray-900 bg-gray-50 p-2 rounded-md border border-gray-100 whitespace-pre-wrap">{selectedItem.keterangan || '-'}</div>
              </div>
              
              <div className="pt-4 flex justify-end">
                <button
                  type="button"
                  onClick={closeDetailModal}
                  className="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 font-medium transition-colors cursor-pointer"
                >
                  Tutup
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Pop Up Modal Form */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100">
              <h3 className="text-lg font-semibold text-gray-900">
                {editingId ? 'Edit Lemari' : 'Tambah Lemari'}
              </h3>
              <button onClick={closeModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>
            
            <form onSubmit={handleSubmit} className="p-6 space-y-4 text-sm">
              <div>
                <label className="block font-medium text-gray-700 mb-1">Nama Lemari</label>
                <input
                  type="text"
                  required
                  value={formData.nama_lemari}
                  onChange={(e) => setFormData({ ...formData, nama_lemari: e.target.value })}
                  className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                  placeholder="Misal: Lemari A1"
                />
              </div>

              <div>
                <label className="block font-medium text-gray-700 mb-1">Ruang</label>
                <select
                  required
                  value={formData.id_ruang}
                  onChange={(e) => setFormData({ ...formData, id_ruang: e.target.value })}
                  className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                >
                  <option value="" disabled>Pilih Ruang</option>
                  {ruangList.map((ruang) => (
                    <option key={ruang.id_ruang || ruang.id} value={ruang.id_ruang || ruang.id}>
                      {ruang.nama_ruang}
                    </option>
                  ))}
                </select>
              </div>

              <div>
                <label className="block font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea
                  rows={3}
                  value={formData.keterangan}
                  onChange={(e) => setFormData({ ...formData, keterangan: e.target.value })}
                  className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                  placeholder="Opsional"
                />
              </div>
              
              <div className="pt-4 flex justify-end gap-3">
                <button
                  type="button"
                  onClick={closeModal}
                  className="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 font-medium transition-colors cursor-pointer"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  disabled={isSubmitting}
                  className="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 font-medium transition-colors flex items-center disabled:opacity-50 cursor-pointer"
                >
                  {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                  Simpan
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
