'use client';

import { extractData } from '@/lib/utils';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { Plus, Pencil, Trash2, X, Loader2, Search, Eye } from 'lucide-react';

import { toast } from 'sonner';

export default function OpnameAsetPage() {
  const [data, setData] = useState<any[]>([]);
  const [asetList, setAsetList] = useState<any[]>([]);
  const [penggunaList, setPenggunaList] = useState<any[]>([]);

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
    kode_barang: '',
    tanggal_opname: '',
    kondisi_ditemukan: '',
    keterangan: '',
    id_pemeriksa: '',
  });

  const fetchData = async (page = 1, search = searchQuery) => {
    setLoading(true);
    try {
      const [resOpname, resAset, resPengguna] = await Promise.all([
        api.get(`/opname-aset?page=${page}&search=${encodeURIComponent(search)}`),
        api.get('/aset').catch(() => ({ data: { data: [] } })),
        api.get('/pengguna').catch(() => ({ data: { data: [] } }))
      ]);

      setData(extractData(resOpname.data.data));
      if (resOpname.data.meta) {
        setCurrentPage(resOpname.data.meta.current_page);
        setLastPage(resOpname.data.meta.last_page);
      }
      setAsetList(extractData(resAset.data.data));
      setPenggunaList(extractData(resPengguna.data.data) || resPengguna.data || []);
    } catch (error) {
      console.error('Gagal mengambil data opname aset', error);
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
      setEditingId(item.id_opname_aset || item.id);
      
      let formattedDate = '';
      if (item.tanggal_opname) {
        formattedDate = item.tanggal_opname.split('T')[0];
      }

      setFormData({
        kode_barang: item.kode_barang || '',
        tanggal_opname: formattedDate,
        kondisi_ditemukan: item.kondisi_ditemukan || '',
        keterangan: item.keterangan || '',
        id_pemeriksa: item.id_pemeriksa || '',
      });
    } else {
      setEditingId(null);
      
      const today = new Date().toISOString().split('T')[0];
      setFormData({
        kode_barang: '',
        tanggal_opname: today,
        kondisi_ditemukan: '',
        keterangan: '',
        id_pemeriksa: '',
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
      const payload = {
        ...formData,
        id_pemeriksa: Number(formData.id_pemeriksa),
      };

      if (editingId) {
        await api.put(`/opname-aset/${editingId}`, payload);
      } else {
        await api.post('/opname-aset', payload);
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
    if (window.confirm('Apakah Anda yakin ingin menghapus opname aset ini?')) {
      try {
        await api.delete(`/opname-aset/${id}`);
        toast.success('Data berhasil dihapus');
        fetchData();
      } catch (error) {
        console.error('Gagal menghapus data', error);
        toast.error('Gagal menghapus data. Opname aset mungkin sedang digunakan.');
      }
    }
  };

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      {/* Header Halaman */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Opname Aset</h1>
          <p className="text-sm text-gray-500 mt-1">Lakukan audit fisik berkala dan cek kondisi aset sekolah.</p>
        </div>
        <button
          onClick={() => openModal()}
          className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-gray-900 text-white hover:bg-gray-800 h-10 px-4 py-2 cursor-pointer"
        >
          <Plus className="mr-2 h-4 w-4" />
          Tambah Opname
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
            placeholder="Cari data opname..."
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
                <th className="px-6 py-4 font-medium">ID Opname</th>
                <th className="px-6 py-4 font-medium">Kode Barang</th>
                <th className="px-6 py-4 font-medium">Tanggal</th>
                <th className="px-6 py-4 font-medium">Kondisi Ditemukan</th>
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
                    {searchQuery ? 'Tidak ada data yang cocok dengan pencarian Anda.' : 'Belum ada data opname.'}
                  </td>
                </tr>
              ) : (
                data.map((item, index) => (
                  <tr key={index} className="hover:bg-gray-50 transition-colors">
                    <td className="px-6 py-4 text-gray-500">{index + 1}</td>
                    <td className="px-6 py-4 font-medium text-gray-900">#OP-0{item.id_opname_aset || item.id}</td>
                    <td className="px-6 py-4 font-medium text-blue-600">{item.kode_barang || '-'}</td>
                    <td className="px-6 py-4 text-gray-900">
                      {item.tanggal_opname ? item.tanggal_opname.split('T')[0] : '-'}
                    </td>
                    <td className="px-6 py-4 text-gray-700 font-medium">{item.kondisi_ditemukan}</td>
                    <td className="px-6 py-4 text-right">
                      <button onClick={() => openDetailModal(item)} className="text-gray-400 hover:text-blue-600 mr-3 transition-colors cursor-pointer" title="Lihat Detail">
                        <Eye className="h-4 w-4" />
                      </button>
                      <button onClick={() => openModal(item)} className="text-gray-400 hover:text-gray-900 mr-3 transition-colors cursor-pointer" title="Ubah">
                        <Pencil className="h-4 w-4" />
                      </button>
                      <button onClick={() => handleDelete(item.id_opname_aset || item.id)} className="text-gray-400 hover:text-red-600 transition-colors cursor-pointer" title="Hapus">
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
              <h3 className="text-lg font-semibold text-gray-900">Detail Opname Aset</h3>
              <button onClick={closeDetailModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>
            
            <div className="p-6 space-y-4 text-sm">
              <div>
                <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">ID Opname</label>
                <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">#OP-0{selectedItem.id_opname_aset || selectedItem.id}</div>
              </div>
              <div>
                <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Kode Barang (Inventaris)</label>
                <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.kode_barang || '-'}</div>
              </div>
              <div>
                <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Tanggal Audit</label>
                <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">
                  {selectedItem.tanggal_opname ? selectedItem.tanggal_opname.split('T')[0] : '-'}
                </div>
              </div>
              <div>
                <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Kondisi Ditemukan</label>
                <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.kondisi_ditemukan || '-'}</div>
              </div>
              <div>
                <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Pemeriksa</label>
                <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">
                  {selectedItem.pemeriksa?.username || selectedItem.username || `ID User: ${selectedItem.id_pemeriksa || '-'}`}
                </div>
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
                {editingId ? 'Edit Opname Aset' : 'Tambah Opname Aset'}
              </h3>
              <button onClick={closeModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>
            
            <form onSubmit={handleSubmit} className="p-6 space-y-4 text-sm">
              <div>
                <label className="block font-medium text-gray-700 mb-1">Aset (Kode Inventaris)</label>
                <select
                  required
                  value={formData.kode_barang}
                  onChange={(e) => setFormData({ ...formData, kode_barang: e.target.value })}
                  className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                >
                  <option value="" disabled>Pilih Aset</option>
                  {asetList.map((aset) => (
                    <option key={aset.kode_inventaris} value={aset.kode_inventaris}>
                      {aset.kode_inventaris} - {aset.master_barang?.nama_barang || aset.nama_barang || ''}
                    </option>
                  ))}
                </select>
              </div>

              <div>
                <label className="block font-medium text-gray-700 mb-1">Tanggal Audit</label>
                <input
                  type="date"
                  required
                  value={formData.tanggal_opname}
                  onChange={(e) => setFormData({ ...formData, tanggal_opname: e.target.value })}
                  className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <div>
                <label className="block font-medium text-gray-700 mb-1">Kondisi Ditemukan</label>
                <input
                  type="text"
                  required
                  value={formData.kondisi_ditemukan}
                  onChange={(e) => setFormData({ ...formData, kondisi_ditemukan: e.target.value })}
                  className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                  placeholder="Misal: Baik / Layak Pakai"
                />
              </div>

              <div>
                <label className="block font-medium text-gray-700 mb-1">Pemeriksa (User)</label>
                <select
                  required
                  value={formData.id_pemeriksa}
                  onChange={(e) => setFormData({ ...formData, id_pemeriksa: e.target.value })}
                  className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                >
                  <option value="" disabled>Pilih Pemeriksa</option>
                  {penggunaList.map((user) => (
                    <option key={user.id_pengguna || user.id} value={user.id_pengguna || user.id}>
                      {user.username}
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
