'use client';

import { extractData } from '@/lib/utils';


import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { Plus, Pencil, Trash2, X, Loader2, Search, Eye } from 'lucide-react';

import { toast } from 'sonner';

export default function AsetHabisPakaiPage() {
  const [data, setData] = useState<any[]>([]);
  const [kondisiList, setKondisiList] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDetailModalOpen, setIsDetailModalOpen] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedItem, setSelectedItem] = useState<any>(null);
  const [currentPage, setCurrentPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);

  const [formData, setFormData] = useState({
    kode_barang: '',
    id_master_barang: '',
    stok: '',
    id_kondisi: '',
    status: '',
    keterangan: '',
  });

  const fetchData = async (page = 1, search = searchQuery) => {
    setLoading(true);
    try {
      const response = await api.get(`/aset-habis-pakai?page=${page}&search=${encodeURIComponent(search)}`);
      setData(extractData(response.data.data));
      if (response.data.meta) {
        setCurrentPage(response.data.meta.current_page);
        setLastPage(response.data.meta.last_page);
      }
    } catch (error) {
      console.error('Gagal mengambil data aset habis pakai', error);
    } finally {
      setLoading(false);
    }
  };

  const fetchKondisi = async () => {
    try {
      const response = await api.get('/aset-kondisi?per_page=100');
      setKondisiList(extractData(response.data.data));
    } catch (error) {
      console.error('Gagal mengambil data kondisi', error);
    }
  };

  useEffect(() => {
    fetchKondisi();
  }, []);

  useEffect(() => {
    const delayDebounceFn = setTimeout(() => {
      fetchData(1, searchQuery);
    }, 500);
    return () => clearTimeout(delayDebounceFn);
  }, [searchQuery]);

  const openModal = (item: any = null) => {
    if (item) {
      setEditingId(item.kode_barang);
      setFormData({
        kode_barang: item.kode_barang || '',
        id_master_barang: item.id_master_barang || '',
        stok: item.stok || '',
        id_kondisi: item.id_kondisi || '',
        status: item.status || '',
        keterangan: item.keterangan || '',
      });
    } else {
      setEditingId(null);
      setFormData({ kode_barang: '', id_master_barang: '', stok: '', id_kondisi: '', status: '', keterangan: '' });
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
      const payload: any = { ...formData, stok: Number(formData.stok) };
      if (!editingId) {
        payload.tanggal_registrasi = new Date().toISOString().split('T')[0];
      }
      if (editingId) {
        await api.put(`/aset-habis-pakai/${editingId}`, payload);
      } else {
        await api.post('/aset-habis-pakai', payload);
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
    if (window.confirm('Apakah Anda yakin ingin menghapus aset habis pakai ini?')) {
      try {
        await api.delete(`/aset-habis-pakai/${id}`);
        toast.success('Data berhasil dihapus');
        fetchData();
      } catch (error) {
        console.error('Gagal menghapus data', error);
        toast.error('Gagal menghapus data.');
      }
    }
  };

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Aset Habis Pakai</h1>
          <p className="text-sm text-gray-500 mt-1">Kelola daftar aset habis pakai (barang yang habis digunakan).</p>
        </div>
        <button
          onClick={() => openModal()}
          className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-gray-900 text-white hover:bg-gray-800 h-10 px-4 py-2 cursor-pointer"
        >
          <Plus className="mr-2 h-4 w-4" />
          Tambah Aset Habis Pakai
        </button>
      </div>

      <div className="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div className="relative w-full sm:max-w-md">
          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search className="h-4 w-4 text-gray-400" />
          </div>
          <input
            type="text"
            placeholder="Cari kode/nama barang..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 sm:text-sm transition-colors"
          />
        </div>
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm text-left">
            <thead className="text-xs text-gray-500 bg-gray-50 border-b border-gray-200 uppercase">
              <tr>
                <th className="px-6 py-4 font-medium">No.</th>
                <th className="px-6 py-4 font-medium">Kode</th>
                <th className="px-6 py-4 font-medium">Nama Barang</th>
                <th className="px-6 py-4 font-medium">Stok</th>
                <th className="px-6 py-4 font-medium">Kondisi</th>
                <th className="px-6 py-4 font-medium">Status</th>
                <th className="px-6 py-4 font-medium text-right">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {loading ? (
                <tr>
                  <td colSpan={7} className="px-6 py-12 text-center">
                    <Loader2 className="h-6 w-6 animate-spin mx-auto text-gray-400" />
                  </td>
                </tr>
              ) : data.length === 0 ? (
                <tr>
                  <td colSpan={7} className="px-6 py-12 text-center text-gray-500">
                    {searchQuery ? 'Tidak ada data yang cocok dengan pencarian Anda.' : 'Belum ada data aset habis pakai.'}
                  </td>
                </tr>
              ) : (
                data.map((item, index) => (
                  <tr key={index} className="hover:bg-gray-50 transition-colors">
                    <td className="px-6 py-4 text-gray-500">{index + 1}</td>
                    <td className="px-6 py-4 font-medium text-gray-900">{item.kode_barang || '-'}</td>
                    <td className="px-6 py-4 text-gray-900">{item.nama_barang || '-'}</td>
                    <td className="px-6 py-4 text-gray-900">{item.stok}</td>
                    <td className="px-6 py-4 text-gray-500">{item.nama_kondisi || '-'}</td>
                    <td className="px-6 py-4 text-gray-500">{item.status || '-'}</td>
                    <td className="px-6 py-4 text-right">
                      <button onClick={() => openDetailModal(item)} className="text-gray-400 hover:text-blue-600 mr-3 transition-colors cursor-pointer" title="Rincian">
                        <Eye className="h-4 w-4" />
                      </button>
                      <button onClick={() => openModal(item)} className="text-gray-400 hover:text-gray-900 mr-3 transition-colors cursor-pointer" title="Ubah">
                        <Pencil className="h-4 w-4" />
                      </button>
                      <button onClick={() => handleDelete(item.kode_barang)} className="text-gray-400 hover:text-red-600 transition-colors cursor-pointer" title="Hapus">
                        <Trash2 className="h-4 w-4" />
                      </button>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
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

      {isDetailModalOpen && selectedItem && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100">
              <h3 className="text-lg font-semibold text-gray-900">Detail Aset Habis Pakai</h3>
              <button onClick={closeDetailModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>
            <div className="p-6 space-y-4 text-sm">
              <div>
                <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Kode Barang</label>
                <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.kode_barang || '-'}</div>
              </div>
              <div>
                <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Nama Barang</label>
                <div className="text-gray-900 bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.nama_barang || '-'}</div>
              </div>
              <div>
                <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Stok</label>
                <div className="text-gray-900 bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.stok ?? '-'}</div>
              </div>
              <div>
                <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Kondisi</label>
                <div className="text-gray-900 bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.nama_kondisi || '-'}</div>
              </div>
              <div>
                <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Status</label>
                <div className="text-gray-900 bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.status || '-'}</div>
              </div>
              <div>
                <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Keterangan</label>
                <div className="text-gray-900 bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.keterangan || '-'}</div>
              </div>
              <div className="pt-4 flex justify-end">
                <button type="button" onClick={closeDetailModal} className="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 font-medium transition-colors cursor-pointer">
                  Tutup
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100">
              <h3 className="text-lg font-semibold text-gray-900">
                {editingId ? 'Edit Aset Habis Pakai' : 'Tambah Aset Habis Pakai'}
              </h3>
              <button onClick={closeModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>
            <form onSubmit={handleSubmit} className="p-6 space-y-4 text-sm">
              <div>
                <label className="block font-medium text-gray-700 mb-1">Kode Barang</label>
                <input
                  type="text"
                  required
                  disabled={!!editingId}
                  value={formData.kode_barang}
                  onChange={(e) => setFormData({ ...formData, kode_barang: e.target.value })}
                  className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 disabled:bg-gray-100"
                  placeholder="Masukkan kode barang"
                />
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">ID Master Barang</label>
                <input
                  type="number"
                  required
                  value={formData.id_master_barang}
                  onChange={(e) => setFormData({ ...formData, id_master_barang: e.target.value })}
                  className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                  placeholder="Masukkan ID master barang"
                />
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Stok</label>
                <input
                  type="number"
                  required
                  min="0"
                  value={formData.stok}
                  onChange={(e) => setFormData({ ...formData, stok: e.target.value })}
                  className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                  placeholder="0"
                />
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Kondisi</label>
                <select
                  required
                  value={formData.id_kondisi}
                  onChange={(e) => setFormData({ ...formData, id_kondisi: e.target.value })}
                  className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                >
                  <option value="">Pilih Kondisi</option>
                  {kondisiList.map((k) => (
                    <option key={k.id_kondisi || k.id} value={k.id_kondisi || k.id}>
                      {k.nama_kondisi}
                    </option>
                  ))}
                </select>
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Status</label>
                <select
                  required
                  value={formData.status}
                  onChange={(e) => setFormData({ ...formData, status: e.target.value })}
                  className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                >
                  <option value="">Pilih Status</option>
                  <option value="Di Gudang">Di Gudang</option>
                  <option value="Aktif">Aktif</option>
                  <option value="Dipinjam">Dipinjam</option>
                  <option value="Nonaktif">Nonaktif</option>
                  <option value="Keluar">Keluar</option>
                  <option value="Tersedia">Tersedia</option>
                </select>
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea
                  value={formData.keterangan}
                  onChange={(e) => setFormData({ ...formData, keterangan: e.target.value })}
                  className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                  placeholder="Keterangan (opsional)"
                  rows={2}
                />
              </div>
              <div className="pt-4 flex justify-end gap-3">
                <button type="button" onClick={closeModal} className="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 font-medium transition-colors cursor-pointer">
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
