'use client';

import { extractData } from '@/lib/utils';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { Plus, Pencil, Trash2, X, Loader2, Search } from 'lucide-react';

import { toast } from 'sonner';

export default function RombelPage() {
  const [data, setData] = useState<any[]>([]);
  const [jurusanList, setJurusanList] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [editingId, setEditingId] = useState<string | number | null>(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [currentPage, setCurrentPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);

  const [formData, setFormData] = useState({
    kode_rombel: '',
    nama: '',
    tingkat: '10',
    is_active: true,
    id_jurusan: '',
  });

  const fetchData = async (page = 1, search = searchQuery) => {
    setLoading(true);
    try {
      const [resRombel, resJurusan] = await Promise.all([
        api.get(`/rombel?page=${page}&search=${encodeURIComponent(search)}`),
        api.get('/jurusan').catch(() => ({ data: { data: [] } })),
      ]);

      setData(extractData(resRombel.data.data));
      setJurusanList(extractData(resJurusan.data.data));

      if (resRombel.data.meta) {
        setCurrentPage(resRombel.data.meta.current_page);
        setLastPage(resRombel.data.meta.last_page);
      } else {
        setCurrentPage(1);
        setLastPage(1);
      }
    } catch (error) {
      console.error('Gagal mengambil data rombel', error);
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
      setEditingId(item.id_rombel);
      setFormData({
        kode_rombel: item.kode_rombel || '',
        nama: item.nama || '',
        tingkat: item.tingkat || '10',
        is_active: item.is_active ?? true,
        id_jurusan: item.id_jurusan || '',
      });
    } else {
      setEditingId(null);
      setFormData({
        kode_rombel: '',
        nama: '',
        tingkat: '10',
        is_active: true,
        id_jurusan: '',
      });
    }
    setIsModalOpen(true);
  };

  const closeModal = () => {
    setIsModalOpen(false);
    setEditingId(null);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    try {
      const payload = {
        ...formData,
        id_jurusan: formData.id_jurusan ? Number(formData.id_jurusan) : null,
      };

      if (editingId) {
        await api.put(`/rombel/${editingId}`, payload);
      } else {
        await api.post('/rombel', payload);
      }
      toast.success('Data berhasil disimpan');
      closeModal();
      fetchData(currentPage);
    } catch (error) {
      console.error('Gagal menyimpan data rombel', error);
      toast.error('Gagal menyimpan data. Periksa kembali input Anda.');
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleDelete = async (id: number) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus data rombel ini?')) {
      try {
        await api.delete(`/rombel/${id}`);
        fetchData(currentPage);
      } catch (error) {
        console.error('Gagal menghapus data', error);
        toast.error('Gagal menghapus data. Rombel mungkin masih digunakan di data kelas.');
      }
    }
  };

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Data Rombongan Belajar (Rombel)</h1>
          <p className="text-sm text-gray-500 mt-1">Kelola rombongan belajar / rombel sekolah.</p>
        </div>
        <button
          onClick={() => openModal()}
          className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-gray-900 text-white hover:bg-gray-800 h-10 px-4 py-2 cursor-pointer"
        >
          <Plus className="mr-2 h-4 w-4" />
          Tambah Rombel
        </button>
      </div>

      <div className="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div className="relative w-full sm:max-w-md">
          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search className="h-4 w-4 text-gray-400" />
          </div>
          <input
            type="text"
            placeholder="Cari rombel..."
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
                <th className="px-6 py-4 font-medium">Kode Rombel</th>
                <th className="px-6 py-4 font-medium">Nama Rombel</th>
                <th className="px-6 py-4 font-medium">Tingkat</th>
                <th className="px-6 py-4 font-medium">Jurusan</th>
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
                    {searchQuery ? 'Tidak ada data yang cocok dengan pencarian Anda.' : 'Belum ada data rombel.'}
                  </td>
                </tr>
              ) : (
                data.map((item: any, index: number) => (
                  <tr key={item.id_rombel || index} className="hover:bg-gray-50 transition-colors">
                    <td className="px-6 py-4 text-gray-500">{(currentPage - 1) * 10 + index + 1}</td>
                    <td className="px-6 py-4 font-medium text-gray-900">{item.kode_rombel || '-'}</td>
                    <td className="px-6 py-4 text-gray-900 font-medium">{item.nama || '-'}</td>
                    <td className="px-6 py-4 text-gray-500">{item.tingkat || '-'}</td>
                    <td className="px-6 py-4 text-gray-500">{item.jurusan?.nama_jurusan || '-'}</td>
                    <td className="px-6 py-4">
                      <span
                        className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium uppercase tracking-wider ${
                          item.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                        }`}
                      >
                        {item.is_active ? 'Aktif' : 'Non-aktif'}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-right">
                      <button
                        onClick={() => openModal(item)}
                        className="text-gray-400 hover:text-gray-900 mr-3 transition-colors cursor-pointer"
                        title="Ubah"
                      >
                        <Pencil className="h-4 w-4" />
                      </button>
                      <button
                        onClick={() => handleDelete(item.id_rombel)}
                        className="text-gray-400 hover:text-red-600 transition-colors cursor-pointer"
                        title="Hapus"
                      >
                        <Trash2 className="h-4 w-4" />
                      </button>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
        {lastPage > 1 && (
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
        )}
      </div>

      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden flex flex-col">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
              <h3 className="text-lg font-semibold text-gray-900 font-sans">
                {editingId ? 'Edit Rombel' : 'Tambah Rombel'}
              </h3>
              <button onClick={closeModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>
            <form onSubmit={handleSubmit} className="p-6 space-y-4 text-sm font-sans">
              <div>
                <label className="block font-medium text-gray-700 mb-1">Kode Rombel</label>
                <input
                  type="text"
                  required
                  value={formData.kode_rombel}
                  onChange={(e) => setFormData({ ...formData, kode_rombel: e.target.value })}
                  className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                  placeholder="Misal: X-RPL-A, XI-TKJ-B"
                />
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Nama Rombel</label>
                <input
                  type="text"
                  required
                  value={formData.nama}
                  onChange={(e) => setFormData({ ...formData, nama: e.target.value })}
                  className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                  placeholder="Misal: Rombel X RPL A"
                />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block font-medium text-gray-700 mb-1">Tingkat</label>
                  <select
                    value={formData.tingkat}
                    onChange={(e) => setFormData({ ...formData, tingkat: e.target.value })}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                  >
                    <option value="10">Tingkat 10</option>
                    <option value="11">Tingkat 11</option>
                    <option value="12">Tingkat 12</option>
                    <option value="13">Tingkat 13</option>
                  </select>
                </div>
                <div>
                  <label className="block font-medium text-gray-700 mb-1">Jurusan</label>
                  <select
                    value={formData.id_jurusan}
                    required
                    onChange={(e) => setFormData({ ...formData, id_jurusan: e.target.value })}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                  >
                    <option value="">Pilih Jurusan</option>
                    {jurusanList.map((j) => (
                      <option key={j.id_jurusan} value={j.id_jurusan}>
                        {j.nama_jurusan}
                      </option>
                    ))}
                  </select>
                </div>
              </div>
              <div className="flex items-center gap-2 pt-2">
                <input
                  type="checkbox"
                  id="is_active"
                  checked={formData.is_active}
                  onChange={(e) => setFormData({ ...formData, is_active: e.target.checked })}
                  className="rounded border-gray-300 text-gray-900 focus:ring-gray-900 h-4 w-4"
                />
                <label htmlFor="is_active" className="font-medium text-gray-700 select-none">
                  Rombel Aktif
                </label>
              </div>
              <div className="flex justify-end gap-3 pt-4 border-t border-gray-100">
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
