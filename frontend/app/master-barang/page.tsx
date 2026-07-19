'use client';

import { extractData } from '@/lib/utils';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { Plus, Pencil, Trash2, X, Loader2, Search } from 'lucide-react';

import { toast } from 'sonner';
import DropdownMenu from '@/app/components/DropdownMenu';

export default function MasterBarangPage() {
  const [data, setData] = useState<any[]>([]);
  const [kategoriList, setKategoriList] = useState<any[]>([]);
  const [merekList, setMerekList] = useState<any[]>([]);
  const [satuanList, setSatuanList] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [editingId, setEditingId] = useState<string | number | null>(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [currentPage, setCurrentPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);

  const [formData, setFormData] = useState({
    nama_barang: '',
    id_kategori: '',
    id_merek: '',
    id_satuan: '',
    jenis_barang: 'Aset Tetap',
    keterangan: '' });

  const fetchData = async (page = 1, search = searchQuery) => {
    setLoading(true);
    try {
      const [resBarang, resKategori, resMerek, resSatuan] = await Promise.all([
        api.get(`/master-barang?page=${page}&search=${encodeURIComponent(search)}`),
        api.get('/kategori').catch(() => ({ data: { data: [] } })),
        api.get('/merek').catch(() => ({ data: { data: [] } })),
        api.get('/satuan').catch(() => ({ data: { data: [] } })),
      ]);

      setData(extractData(resBarang.data.data));
      setKategoriList(extractData(resKategori.data.data));
      setMerekList(extractData(resMerek.data.data));
      setSatuanList(extractData(resSatuan.data.data));

      if (resBarang.data.meta) {
        setCurrentPage(resBarang.data.meta.current_page);
        setLastPage(resBarang.data.meta.last_page);
      } else {
        setCurrentPage(1);
        setLastPage(1);
      }
    } catch (error) {
      console.error('Gagal mengambil data master barang', error);
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
      setEditingId(item.id_master_barang);
      setFormData({
        nama_barang: item.nama_barang || '',
        id_kategori: item.id_kategori || '',
        id_merek: item.id_merek || '',
        id_satuan: item.id_satuan || '',
        jenis_barang: item.jenis_barang || 'Aset Tetap',
        keterangan: item.keterangan || '' });
    } else {
      setEditingId(null);
      setFormData({
        nama_barang: '',
        id_kategori: '',
        id_merek: '',
        id_satuan: '',
        jenis_barang: 'Aset Tetap',
        keterangan: '' });
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
        id_kategori: formData.id_kategori ? Number(formData.id_kategori) : null,
        id_merek: formData.id_merek ? Number(formData.id_merek) : null,
        id_satuan: formData.id_satuan ? Number(formData.id_satuan) : null };

      if (editingId) {
        await api.put(`/master-barang/${editingId}`, payload);
      } else {
        await api.post('/master-barang', payload);
      }
      toast.success('Data berhasil disimpan');
      closeModal();
      fetchData(currentPage);
    } catch (error) {
      console.error('Gagal menyimpan data', error);
      toast.error('Gagal menyimpan data. Periksa kembali input Anda.');
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleDelete = async (id: number) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus data barang ini dari katalog?')) {
      try {
        await api.delete(`/master-barang/${id}`);
        fetchData(currentPage);
      } catch (error) {
        console.error('Gagal menghapus data', error);
        toast.error('Gagal menghapus data. Barang mungkin sedang digunakan pada data aset fisik.');
      }
    }
  };

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Katalog Barang (Master)</h1>
          <p className="text-sm text-gray-500 mt-1">Daftar master katalog tipe barang yang dikelola.</p>
        </div>
        <button
          onClick={() => openModal()}
          className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-gray-900 text-white hover:bg-gray-800 h-10 px-4 py-2 cursor-pointer"
        >
          <Plus className="mr-2 h-4 w-4" />
          Tambah Katalog
        </button>
      </div>

      <div className="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div className="relative w-full sm:max-w-md">
          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search className="h-4 w-4 text-gray-400" />
          </div>
          <input
            type="text"
            placeholder="Cari barang..."
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
                <th className="px-6 py-4 font-medium">Nama Barang</th>
                <th className="px-6 py-4 font-medium">Kategori</th>
                <th className="px-6 py-4 font-medium">Merek</th>
                <th className="px-6 py-4 font-medium">Satuan</th>
                <th className="px-6 py-4 font-medium">Jenis</th>
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
                    {searchQuery ? 'Tidak ada data yang cocok dengan pencarian Anda.' : 'Belum ada data barang.'}
                  </td>
                </tr>
              ) : (
                data.map((item: any, index: number) => (
                  <tr key={item.id_master_barang || index} className="hover:bg-gray-50 transition-colors">
                    <td className="px-6 py-4 text-gray-500">{(currentPage - 1) * 10 + index + 1}</td>
                    <td className="px-6 py-4 font-medium text-gray-900">{item.nama_barang || '-'}</td>
                    <td className="px-6 py-4 text-gray-500">{item.kategori?.nama_kategori || '-'}</td>
                    <td className="px-6 py-4 text-gray-500">{item.merek?.nama_merek || '-'}</td>
                    <td className="px-6 py-4 text-gray-500">{item.satuan?.nama_satuan || '-'}</td>
                    <td className="px-6 py-4 text-gray-500">
                      <span className={`inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${item.jenis_barang === 'Habis Pakai' ? 'bg-yellow-50 text-yellow-700 border border-yellow-200' : 'bg-blue-50 text-blue-700 border border-blue-200'}`}>
                        {item.jenis_barang || 'Aset Tetap'}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-right">
                                          <DropdownMenu actions={[
                      { label: 'Ubah', icon: Pencil, onClick: () => openModal(item), variant: 'default' },
                      { label: 'Hapus', icon: Trash2, onClick: () => handleDelete(item.id_master_barang), variant: 'danger' },
                    ]} />
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
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
              <h3 className="text-lg font-semibold text-gray-900 font-sans">
                {editingId ? 'Edit Katalog Barang' : 'Tambah Katalog Barang'}
              </h3>
              <button onClick={closeModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>
            <form onSubmit={handleSubmit} className="p-6 space-y-4 text-sm font-sans">
              <div>
                <label className="block font-medium text-gray-700 mb-1">Nama Barang</label>
                <input
                  type="text"
                  required
                  value={formData.nama_barang}
                  onChange={(e) => setFormData({ ...formData, nama_barang: e.target.value })}
                  className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                  placeholder="Misal: Laptop Core i5, Spidol Hitam"
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block font-medium text-gray-700 mb-1">Kategori</label>
                  <select
                    value={formData.id_kategori}
                    onChange={(e) => setFormData({ ...formData, id_kategori: e.target.value })}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                  >
                    <option value="">Pilih Kategori</option>
                    {kategoriList.map((k) => (
                      <option key={k.id_kategori} value={k.id_kategori}>
                        {k.nama_kategori}
                      </option>
                    ))}
                  </select>
                </div>

                <div>
                  <label className="block font-medium text-gray-700 mb-1">Merek</label>
                  <select
                    value={formData.id_merek}
                    onChange={(e) => setFormData({ ...formData, id_merek: e.target.value })}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                  >
                    <option value="">Pilih Merek</option>
                    {merekList.map((m) => (
                      <option key={m.id_merek} value={m.id_merek}>
                        {m.nama_merek}
                      </option>
                    ))}
                  </select>
                </div>
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block font-medium text-gray-700 mb-1">Satuan</label>
                  <select
                    value={formData.id_satuan}
                    onChange={(e) => setFormData({ ...formData, id_satuan: e.target.value })}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                  >
                    <option value="">Pilih Satuan</option>
                    {satuanList.map((s) => (
                      <option key={s.id_satuan} value={s.id_satuan}>
                        {s.nama_satuan}
                      </option>
                    ))}
                  </select>
                </div>

                <div>
                  <label className="block font-medium text-gray-700 mb-1">Jenis Barang</label>
                  <select
                    value={formData.jenis_barang}
                    onChange={(e) => setFormData({ ...formData, jenis_barang: e.target.value })}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                  >
                    <option value="Aset Tetap">Aset Tetap</option>
                    <option value="Habis Pakai">Habis Pakai</option>
                  </select>
                </div>
              </div>

              <div>
                <label className="block font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea
                  rows={3}
                  value={formData.keterangan}
                  onChange={(e) => setFormData({ ...formData, keterangan: e.target.value })}
                  className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                  placeholder="Keterangan tambahan (opsional)"
                />
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
