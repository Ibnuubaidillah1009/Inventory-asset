'use client';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { Plus, Pencil, Trash2, X, Loader2, Search, Eye } from 'lucide-react';

interface Kategori { id_kategori?: number; id?: number; kode_kategori?: string; nama_kategori: string; }
interface Merek { id_merek?: number; id?: number; nama_merek: string; }
interface Satuan { id_satuan?: number; id?: number; nama_satuan: string; }
interface MasterBarang {
  id_master_barang: number;
  nama_barang: string;
  id_kategori?: string | number;
  id_merek?: string | number;
  id_satuan?: string | number;
  jenis_barang?: string;
  keterangan?: string;
  kategori?: Kategori;
  merek?: Merek;
  satuan?: Satuan;
}

export default function MasterBarangPage() {
  const [data, setData] = useState<MasterBarang[]>([]);
  const [kategoriList, setKategoriList] = useState<Kategori[]>([]);
  const [merekList, setMerekList] = useState<Merek[]>([]);
  const [satuanList, setSatuanList] = useState<Satuan[]>([]);
  
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDetailModalOpen, setIsDetailModalOpen] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedItem, setSelectedItem] = useState<MasterBarang | null>(null);
  const [currentPage, setCurrentPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);

  // Form State menyesuaikan dengan schema master_barang di database
  const [formData, setFormData] = useState<{
    nama_barang: string;
    id_kategori: string | number;
    id_merek: string | number;
    id_satuan: string | number;
    jenis_barang: string;
    keterangan: string;
  }>({
    nama_barang: '',
    id_kategori: '',
    id_merek: '',
    id_satuan: '',
    jenis_barang: 'Aset',
    keterangan: '',
  });

  const fetchApiData = async (page = 1, search = searchQuery) => {
    try {
      // Fetch Master Barang, Kategori, Merek, dan Satuan secara paralel
      const [resMaster, resKategori, resMerek, resSatuan] = await Promise.all([
        api.get(`/master-barang?page=${page}&search=${encodeURIComponent(search)}`),
        api.get('/kategori'),
        api.get('/merek'),
        api.get('/satuan')
      ]);
      
      setData(resMaster.data.data || []);
      if (resMaster.data.meta) {
        setCurrentPage(resMaster.data.meta.current_page);
        setLastPage(resMaster.data.meta.last_page);
      }
      setKategoriList(resKategori.data.data || []);
      setMerekList(resMerek.data.data || []);
      setSatuanList(resSatuan.data.data || []);
    } catch (error) {
      console.error('Gagal mengambil data master barang', error);
    } finally {
      setLoading(false);
    }
  };

  const fetchData = async (page = 1, search = searchQuery) => {
    setLoading(true);
    await fetchApiData(page, search);
  };

  useEffect(() => {
    const delayDebounceFn = setTimeout(() => {
      fetchData(1, searchQuery);
    }, 500);

    return () => clearTimeout(delayDebounceFn);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [searchQuery]);

  const openModal = (item: MasterBarang | null = null) => {
    if (item) {
      setEditingId(item.id_master_barang);
      setFormData({
        nama_barang: item.nama_barang || '',
        id_kategori: item.id_kategori || '',
        id_merek: item.id_merek || '',
        id_satuan: item.id_satuan || '',
        jenis_barang: item.jenis_barang || 'Aset',
        keterangan: item.keterangan || '',
      });
    } else {
      setEditingId(null);
      setFormData({ 
        nama_barang: '', 
        id_kategori: '', 
        id_merek: '', 
        id_satuan: '', 
        jenis_barang: 'Aset',
        keterangan: '' 
      });
    }
    setIsModalOpen(true);
  };

  const closeModal = () => {
    setIsModalOpen(false);
    setEditingId(null);
  };

  const openDetailModal = (item: MasterBarang) => {
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
        await api.put(`/master-barang/${editingId}`, formData);
      } else {
        await api.post('/master-barang', formData);
      }
      closeModal();
      fetchData();
    } catch (error) {
      console.error('Gagal menyimpan data', error);
      alert('Gagal menyimpan data. Pastikan semua field yang wajib sudah terisi dengan benar.');
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleDelete = async (id: number) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus barang ini?')) {
      try {
        await api.delete(`/master-barang/${id}`);
        fetchData();
      } catch (error) {
        console.error('Gagal menghapus data', error);
        alert('Gagal menghapus data. Barang mungkin sedang digunakan pada tabel aset.');
      }
    }
  };

  // FilteredData dihapus karena pencarian dihandle oleh server-side

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      {/* Header Halaman */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Master Barang</h1>
          <p className="text-sm text-gray-500 mt-1">Kelola katalog utama barang inventaris.</p>
        </div>
        <button
          onClick={() => openModal()}
          className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-gray-900 text-white hover:bg-gray-800 h-10 px-4 py-2 cursor-pointer"
        >
          <Plus className="mr-2 h-4 w-4" />
          Tambah Barang
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
            placeholder="Cari barang berdasarkan nama, kategori, merek, jenis..."
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
                <th className="px-6 py-4 font-medium">No</th>
                <th className="px-6 py-4 font-medium">Kode Barang</th>
                <th className="px-6 py-4 font-medium">Nama Barang</th>
                <th className="px-6 py-4 font-medium">Kategori</th>
                <th className="px-6 py-4 font-medium">Merek</th>
                <th className="px-6 py-4 font-medium text-right">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {loading ? (
                <tr>
                  <td colSpan={8} className="px-6 py-12 text-center">
                    <Loader2 className="h-6 w-6 animate-spin mx-auto text-gray-400" />
                  </td>
                </tr>
              ) : data.length === 0 ? (
                <tr>
                  <td colSpan={8} className="px-6 py-12 text-center text-gray-500">
                    {searchQuery ? 'Tidak ada data yang cocok dengan pencarian Anda.' : 'Belum ada data barang.'}
                  </td>
                </tr>
              ) : (
                data.map((item, index) => (
                  <tr key={index} className="hover:bg-gray-50 transition-colors">
                    <td className="px-6 py-4 text-gray-500">{index + 1}</td>
                    <td className="px-6 py-4 font-medium text-blue-600">MB-{item.id_master_barang}</td>
                    <td className="px-6 py-4 font-medium text-gray-900">{item.nama_barang}</td>
                    <td className="px-6 py-4 text-gray-500">{item.kategori?.nama_kategori || '-'}</td>
                    <td className="px-6 py-4 text-gray-500">{item.merek?.nama_merek || '-'}</td>
                    <td className="px-6 py-4 text-right">
                      <button onClick={() => openDetailModal(item)} className="text-gray-400 hover:text-blue-600 mr-3 transition-colors cursor-pointer" title="Lihat Detail">
                        <Eye className="h-4 w-4" />
                      </button>
                      <button onClick={() => openModal(item)} className="text-gray-400 hover:text-gray-900 mr-3 transition-colors cursor-pointer" title="Edit">
                        <Pencil className="h-4 w-4" />
                      </button>
                      <button onClick={() => handleDelete(item.id_master_barang)} className="text-gray-400 hover:text-red-600 transition-colors cursor-pointer" title="Hapus">
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
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
              <h3 className="text-lg font-semibold text-gray-900">Detail Master Barang</h3>
              <button onClick={closeDetailModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>
            
            <div className="p-6 space-y-4 text-sm overflow-y-auto">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Kode Barang</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">MB-{selectedItem.id_master_barang}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Nama Barang</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.nama_barang || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Kategori</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.kategori?.nama_kategori || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Merek</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.merek?.nama_merek || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Satuan</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.satuan?.nama_satuan || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Jenis Barang</label>
                  <div className="text-gray-900 bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.jenis_barang || '-'}</div>
                </div>
                <div className="sm:col-span-2">
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Keterangan</label>
                  <div className="text-gray-900 bg-gray-50 p-2 rounded-md border border-gray-100 whitespace-pre-wrap">{selectedItem.keterangan || '-'}</div>
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

      {/* Pop Up Modal Form Master Barang */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4 py-8">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-full flex flex-col">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
              <h3 className="text-lg font-semibold text-gray-900">
                {editingId ? 'Edit Master Barang' : 'Tambah Master Barang'}
              </h3>
              <button onClick={closeModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>
            
            <div className="overflow-y-auto p-6">
              <form id="barangForm" onSubmit={handleSubmit} className="space-y-4 text-sm">
                
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  
                  {/* Kode Barang */}
                  <div className="sm:col-span-2">
                    <label className="block font-medium text-gray-700 mb-1">Kode Barang</label>
                    <input
                      type="text"
                      disabled
                      value={editingId ? `MB-${editingId}` : 'Otomatis dibuat oleh sistem'}
                      className="w-full rounded-md border border-gray-300 bg-gray-100 px-3 py-2 text-gray-500 cursor-not-allowed"
                    />
                  </div>

                  {/* Nama Barang */}
                  <div className="sm:col-span-2">
                    <label className="block font-medium text-gray-700 mb-1">Nama Barang</label>
                    <input
                      type="text"
                      required
                      value={formData.nama_barang}
                      onChange={(e) => setFormData({ ...formData, nama_barang: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                      placeholder="Misal: Laptop Asus"
                    />
                  </div>

                  {/* Dropdown Kategori */}
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Kategori</label>
                    <select
                      required
                      value={formData.id_kategori}
                      onChange={(e) => setFormData({ ...formData, id_kategori: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                    >
                      <option value="" disabled>Pilih Kategori</option>
                      {kategoriList.map((kat) => (
                        <option key={kat.id_kategori || kat.id} value={kat.id_kategori || kat.id}>
                          {kat.kode_kategori || kat.id_kategori} - {kat.nama_kategori}
                        </option>
                      ))}
                    </select>
                  </div>

                  {/* Dropdown Merek */}
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Merek</label>
                    <select
                      required
                      value={formData.id_merek}
                      onChange={(e) => setFormData({ ...formData, id_merek: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                    >
                      <option value="" disabled>Pilih Merek</option>
                      {merekList.map((merek) => (
                        <option key={merek.id_merek || merek.id} value={merek.id_merek || merek.id}>
                          {merek.nama_merek}
                        </option>
                      ))}
                    </select>
                  </div>

                  {/* Dropdown Satuan */}
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Satuan</label>
                    <select
                      required
                      value={formData.id_satuan}
                      onChange={(e) => setFormData({ ...formData, id_satuan: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                    >
                      <option value="" disabled>Pilih Satuan</option>
                      {satuanList.map((satuan) => (
                        <option key={satuan.id_satuan || satuan.id} value={satuan.id_satuan || satuan.id}>
                          {satuan.nama_satuan}
                        </option>
                      ))}
                    </select>
                  </div>

                  {/* Jenis Barang */}
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Jenis Barang</label>
                    <select
                      required
                      value={formData.jenis_barang}
                      onChange={(e) => setFormData({ ...formData, jenis_barang: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                    >
                      <option value="Aset">Aset</option>
                      <option value="Habis Pakai">Habis Pakai</option>
                    </select>
                  </div>

                  {/* Keterangan */}
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
                form="barangForm"
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