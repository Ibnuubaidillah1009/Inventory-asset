'use client';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { Plus, Pencil, Trash2, X, Loader2, Search, Eye } from 'lucide-react';

export default function InputBangunanPage() {
  const [data, setData] = useState<any[]>([]);
  const [kondisiList, setKondisiList] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDetailModalOpen, setIsDetailModalOpen] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [editingId, setEditingId] = useState<string | null>(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedItem, setSelectedItem] = useState<any>(null);
  const [currentPage, setCurrentPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);

  const [formData, setFormData] = useState({
    kode_inventaris: '',
    nama_bangunan: '',
    luas_bangunan: '',
    kondisi_bangunan: '',
    nilai_aset: '',
    keterangan: '',
    tanggal_bangunan: '',
    id_kondisi: '',
    ukuran_p: '',
    ukuran_l: '',
    konstruksi: '',
  });

  const fetchData = async (page = 1, search = searchQuery) => {
    setLoading(true);
    try {
      const [resAset, resKondisi] = await Promise.all([
        api.get(`/aset?page=${page}&search=${encodeURIComponent(search)}`).catch(() => ({ data: { data: [] } })),
        api.get('/aset-kondisi').catch(() => ({ data: { data: [] } })),
      ]);

      const asetData = (resAset.data.data || []).filter((a: any) => a.aset_bangunan || a.nama_bangunan);
      setData(asetData);
      setKondisiList(resKondisi.data.data || []);

      if (resAset.data.meta) {
        setCurrentPage(resAset.data.meta.current_page);
        setLastPage(resAset.data.meta.last_page);
      }
    } catch (error) {
      console.error('Gagal mengambil data aset bangunan', error);
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
      setEditingId(item.kode_barang || item.kode_inventaris);
      setFormData({
        kode_inventaris: item.kode_barang || item.kode_inventaris || '',
        nama_bangunan: item.aset_bangunan?.nama_bangunan || item.nama_bangunan || '',
        luas_bangunan: item.aset_bangunan?.luas_bangunan || item.luas_bangunan || '',
        kondisi_bangunan: item.aset_bangunan?.kondisi_bangunan || item.kondisi_bangunan || '',
        nilai_aset: item.aset_bangunan?.nilai_aset || item.nilai_aset || '',
        keterangan: item.aset_bangunan?.keterangan || item.keterangan || '',
        tanggal_bangunan: item.aset_bangunan?.tanggal_bangunan || item.tanggal_bangunan || '',
        id_kondisi: item.aset_bangunan?.id_kondisi || item.id_kondisi || '',
        ukuran_p: item.aset_bangunan?.ukuran_p || item.ukuran_p || '',
        ukuran_l: item.aset_bangunan?.ukuran_l || item.ukuran_l || '',
        konstruksi: item.aset_bangunan?.konstruksi || item.konstruksi || '',
      });
    } else {
      setEditingId(null);
      setFormData({
        kode_inventaris: '',
        nama_bangunan: '',
        luas_bangunan: '',
        kondisi_bangunan: '',
        nilai_aset: '',
        keterangan: '',
        tanggal_bangunan: '',
        id_kondisi: '',
        ukuran_p: '',
        ukuran_l: '',
        konstruksi: '',
      });
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
      const payload: any = {
        nama_bangunan: formData.nama_bangunan,
        luas_bangunan: formData.luas_bangunan ? Number(formData.luas_bangunan) : null,
        kondisi_bangunan: formData.kondisi_bangunan,
        nilai_aset: formData.nilai_aset ? Number(formData.nilai_aset) : null,
        keterangan: formData.keterangan || null,
        tanggal_bangunan: formData.tanggal_bangunan || null,
        id_kondisi: formData.id_kondisi ? Number(formData.id_kondisi) : null,
        ukuran_p: formData.ukuran_p ? Number(formData.ukuran_p) : null,
        ukuran_l: formData.ukuran_l ? Number(formData.ukuran_l) : null,
        konstruksi: formData.konstruksi || null,
      };

      if (editingId) {
        await api.put(`/aset/${editingId}`, { keterangan: JSON.stringify(payload) });
      } else {
        await api.post('/aset', {
          kode_inventaris: formData.kode_inventaris,
          id_master_barang: 1,
          status: 'Aktif',
          keterangan: JSON.stringify(payload),
        });
      }
      closeModal();
      fetchData();
    } catch (error) {
      console.error('Gagal menyimpan data', error);
      alert('Gagal menyimpan data. Periksa kembali input Anda.');
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleDelete = async (id: string) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus aset bangunan ini?')) {
      try {
        await api.delete(`/aset/${id}`);
        fetchData();
      } catch (error) {
        console.error('Gagal menghapus data', error);
        alert('Gagal menghapus data. Aset mungkin sedang digunakan.');
      }
    }
  };

  const formatRupiah = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
    }).format(value);
  };

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Input Bangunan</h1>
          <p className="text-sm text-gray-500 mt-1">Input data aset bangunan.</p>
        </div>
        <button
          onClick={() => openModal()}
          className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-gray-900 text-white hover:bg-gray-800 h-10 px-4 py-2 cursor-pointer"
        >
          <Plus className="mr-2 h-4 w-4" />
          Tambah Bangunan
        </button>
      </div>

      <div className="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div className="relative w-full sm:max-w-md">
          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search className="h-4 w-4 text-gray-400" />
          </div>
          <input
            type="text"
            placeholder="Cari bangunan berdasarkan kode, nama..."
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
                <th className="px-6 py-4 font-medium">No</th>
                <th className="px-6 py-4 font-medium">Kode Inventaris</th>
                <th className="px-6 py-4 font-medium">Nama Bangunan</th>
                <th className="px-6 py-4 font-medium">Luas (m²)</th>
                <th className="px-6 py-4 font-medium">Kondisi</th>
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
                    {searchQuery ? 'Tidak ada data yang cocok dengan pencarian Anda.' : 'Belum ada data aset bangunan.'}
                  </td>
                </tr>
              ) : (
                data.map((item: any, index: number) => (
                  <tr key={item.kode_barang || index} className="hover:bg-gray-50 transition-colors">
                    <td className="px-6 py-4 text-gray-500">{index + 1}</td>
                    <td className="px-6 py-4 font-medium text-gray-900">{item.kode_barang || '-'}</td>
                    <td className="px-6 py-4 text-gray-900 font-medium">
                      {item.aset_bangunan?.nama_bangunan || item.nama_bangunan || '-'}
                    </td>
                    <td className="px-6 py-4 text-gray-500">
                      {item.aset_bangunan?.luas_bangunan || item.luas_bangunan || '-'} m²
                    </td>
                    <td className="px-6 py-4 text-gray-500">
                      {item.aset_bangunan?.kondisi_bangunan || item.kondisi_bangunan || '-'}
                    </td>
                    <td className="px-6 py-4 text-right">
                      <button onClick={() => openDetailModal(item)} className="text-gray-400 hover:text-blue-600 mr-3 transition-colors cursor-pointer" title="Lihat Detail">
                        <Eye className="h-4 w-4" />
                      </button>
                      <button onClick={() => openModal(item)} className="text-gray-400 hover:text-gray-900 mr-3 transition-colors cursor-pointer" title="Edit">
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
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4 py-8">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
              <h3 className="text-lg font-semibold text-gray-900">Detail Aset Bangunan</h3>
              <button onClick={closeDetailModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>
            <div className="p-6 space-y-4 text-sm overflow-y-auto">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Kode Inventaris</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.kode_barang || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Nama Bangunan</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.aset_bangunan?.nama_bangunan || selectedItem.nama_bangunan || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Luas Bangunan</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.aset_bangunan?.luas_bangunan || selectedItem.luas_bangunan || '-'} m²</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Kondisi</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.aset_bangunan?.kondisi_bangunan || selectedItem.kondisi_bangunan || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Nilai Aset</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{formatRupiah(selectedItem.aset_bangunan?.nilai_aset || selectedItem.nilai_aset || 0)}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Tanggal Bangunan</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.aset_bangunan?.tanggal_bangunan || selectedItem.tanggal_bangunan || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Ukuran P x L</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">
                    {selectedItem.aset_bangunan?.ukuran_p || selectedItem.ukuran_p || '-'} x {selectedItem.aset_bangunan?.ukuran_l || selectedItem.ukuran_l || '-'} m
                  </div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Konstruksi</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.aset_bangunan?.konstruksi || selectedItem.konstruksi || '-'}</div>
                </div>
                <div className="sm:col-span-2">
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Keterangan</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100 whitespace-pre-wrap">{selectedItem.aset_bangunan?.keterangan || selectedItem.keterangan || '-'}</div>
                </div>
              </div>
            </div>
            <div className="px-6 py-4 border-t border-gray-100 flex justify-end shrink-0 bg-gray-50">
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
      )}

      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4 py-8">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-full flex flex-col">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
              <h3 className="text-lg font-semibold text-gray-900">
                {editingId ? 'Edit Aset Bangunan' : 'Tambah Aset Bangunan'}
              </h3>
              <button onClick={closeModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>
            <div className="overflow-y-auto p-6">
              <form id="bangunanForm" onSubmit={handleSubmit} className="space-y-4 text-sm">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Kode Inventaris</label>
                    <input
                      type="text"
                      required
                      disabled={!!editingId}
                      value={formData.kode_inventaris}
                      onChange={(e) => setFormData({ ...formData, kode_inventaris: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 disabled:bg-gray-100 disabled:text-gray-500"
                      placeholder="Misal: BNG-2026-001"
                    />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Nama Bangunan</label>
                    <input
                      type="text"
                      required
                      value={formData.nama_bangunan}
                      onChange={(e) => setFormData({ ...formData, nama_bangunan: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                      placeholder="Misal: Gedung Utama"
                    />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Luas Bangunan (m²)</label>
                    <input
                      type="number"
                      min="0"
                      step="0.01"
                      value={formData.luas_bangunan}
                      onChange={(e) => setFormData({ ...formData, luas_bangunan: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                      placeholder="Misal: 500"
                    />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Kondisi Bangunan</label>
                    <input
                      type="text"
                      value={formData.kondisi_bangunan}
                      onChange={(e) => setFormData({ ...formData, kondisi_bangunan: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                      placeholder="Misal: Baik"
                    />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Nilai Aset (Rp)</label>
                    <input
                      type="number"
                      min="0"
                      value={formData.nilai_aset}
                      onChange={(e) => setFormData({ ...formData, nilai_aset: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                      placeholder="Misal: 500000000"
                    />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Tanggal Bangunan</label>
                    <input
                      type="date"
                      value={formData.tanggal_bangunan}
                      onChange={(e) => setFormData({ ...formData, tanggal_bangunan: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                    />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Ukuran Panjang (m)</label>
                    <input
                      type="number"
                      min="0"
                      step="0.01"
                      value={formData.ukuran_p}
                      onChange={(e) => setFormData({ ...formData, ukuran_p: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                      placeholder="Misal: 20"
                    />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Ukuran Lebar (m)</label>
                    <input
                      type="number"
                      min="0"
                      step="0.01"
                      value={formData.ukuran_l}
                      onChange={(e) => setFormData({ ...formData, ukuran_l: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                      placeholder="Misal: 15"
                    />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Konstruksi</label>
                    <input
                      type="text"
                      value={formData.konstruksi}
                      onChange={(e) => setFormData({ ...formData, konstruksi: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                      placeholder="Misal: Beton"
                    />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Kondisi (Referensi)</label>
                    <select
                      value={formData.id_kondisi}
                      onChange={(e) => setFormData({ ...formData, id_kondisi: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                    >
                      <option value="">Pilih Kondisi</option>
                      {kondisiList.map((k: any) => (
                        <option key={k.id_kondisi || k.id} value={k.id_kondisi || k.id}>
                          {k.nama_kondisi || k.nama}
                        </option>
                      ))}
                    </select>
                  </div>
                  <div className="sm:col-span-2">
                    <label className="block font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea
                      rows={3}
                      value={formData.keterangan}
                      onChange={(e) => setFormData({ ...formData, keterangan: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                      placeholder="Catatan tambahan (opsional)"
                    />
                  </div>
                </div>
              </form>
            </div>
            <div className="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50 shrink-0 rounded-b-xl">
              <button
                type="button"
                onClick={closeModal}
                className="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 font-medium transition-colors cursor-pointer"
              >
                Batal
              </button>
              <button
                form="bangunanForm"
                type="submit"
                disabled={isSubmitting}
                className="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 font-medium transition-colors flex items-center disabled:opacity-50 cursor-pointer"
              >
                {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                Simpan
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
