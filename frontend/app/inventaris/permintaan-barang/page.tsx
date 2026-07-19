'use client';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { Plus, Trash2, X, Loader2, Search, Eye, Check } from 'lucide-react';
import { extractData, formatDate } from '@/lib/utils';

import { toast } from 'sonner';
import DropdownMenu from '@/app/components/DropdownMenu';

export default function PermintaanBarangPage() {
  const [data, setData] = useState<any[]>([]);
  const [masterBarangList, setMasterBarangList] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDetailModalOpen, setIsDetailModalOpen] = useState(false);
  const [isKeputusanModalOpen, setIsKeputusanModalOpen] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedItem, setSelectedItem] = useState<any>(null);
  const [keputusanId, setKeputusanId] = useState<string | null>(null);
  const [currentPage, setCurrentPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);

  const [formData, setFormData] = useState({
    id_master_barang: '',
    jumlah_diminta: '',
    alasan_kebutuhan: '',
    keterangan_keperluan: '' });

  const [keputusanData, setKeputusanData] = useState({
    status_persetujuan: 'disetujui',
    alasan_disetujui: '' });

  const fetchData = async (page = 1, search = searchQuery) => {
    setLoading(true);
    try {
      const [resPermintaan, resMaster] = await Promise.all([
        api.get(`/permintaan?page=${page}&search=${encodeURIComponent(search)}`).catch(() => ({ data: { data: [] } })),
        api.get('/master-barang').catch(() => ({ data: { data: [] } })),
      ]);

      setData(extractData(resPermintaan.data.data));
      setMasterBarangList(extractData(resMaster.data.data));

      if (resPermintaan.data.meta) {
        setCurrentPage(resPermintaan.data.meta.current_page);
        setLastPage(resPermintaan.data.meta.last_page);
      }
    } catch (error) {
      console.error('Gagal mengambil data permintaan', error);
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

  const openModal = () => {
    setFormData({
      id_master_barang: '',
      jumlah_diminta: '',
      alasan_kebutuhan: '',
      keterangan_keperluan: '' });
    setIsModalOpen(true);
  };

  const closeModal = () => {
    setIsModalOpen(false);
  };

  const openDetailModal = (item: any) => {
    setSelectedItem(item);
    setIsDetailModalOpen(true);
  };

  const closeDetailModal = () => {
    setIsDetailModalOpen(false);
    setSelectedItem(null);
  };

  const openKeputusanModal = (id: string) => {
    setKeputusanId(id);
    setKeputusanData({ status_persetujuan: 'disetujui', alasan_disetujui: '' });
    setIsKeputusanModalOpen(true);
  };

  const closeKeputusanModal = () => {
    setIsKeputusanModalOpen(false);
    setKeputusanId(null);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    try {
      await api.post('/permintaan', {
        id_master_barang: Number(formData.id_master_barang),
        jumlah_diminta: Number(formData.jumlah_diminta),
        alasan_kebutuhan: formData.alasan_kebutuhan,
        keterangan_keperluan: formData.keterangan_keperluan || null });
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

  const handleKeputusan = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!keputusanId) return;
    setIsSubmitting(true);
    try {
      await api.put(`/permintaan/${keputusanId}/keputusan`, keputusanData);
      closeKeputusanModal();
      fetchData();
    } catch (error) {
      console.error('Gagal memproses keputusan', error);
      toast.error('Gagal memproses keputusan. Permintaan mungkin sudah diproses sebelumnya.');
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleDelete = async (id: string) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus permintaan ini?')) {
      try {
        await api.delete(`/permintaan/${id}`);
        toast.success('Data berhasil dihapus');
        fetchData();
      } catch (error) {
        console.error('Gagal menghapus data', error);
        toast.error('Gagal menghapus data. Permintaan mungkin sudah diproses.');
      }
    }
  };

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Permintaan Barang</h1>
          <p className="text-sm text-gray-500 mt-1">Kelola permintaan barang inventaris.</p>
        </div>
        <button
          onClick={() => openModal()}
          className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-gray-900 text-white hover:bg-gray-800 h-10 px-4 py-2 cursor-pointer"
        >
          <Plus className="mr-2 h-4 w-4" />
          Buat Permintaan
        </button>
      </div>

      <div className="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div className="relative w-full sm:max-w-md">
          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search className="h-4 w-4 text-gray-400" />
          </div>
          <input
            type="text"
            placeholder="Cari permintaan berdasarkan kode, status, keterangan..."
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
                <th className="px-6 py-4 font-medium">Kode Permintaan</th>
                <th className="px-6 py-4 font-medium">Tanggal</th>
                <th className="px-6 py-4 font-medium">Pemohon</th>
                <th className="px-6 py-4 font-medium">Keterangan</th>
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
                    {searchQuery ? 'Tidak ada data yang cocok dengan pencarian Anda.' : 'Belum ada data permintaan.'}
                  </td>
                </tr>
              ) : (
                data.map((item: any, index: number) => (
                  <tr key={item.kode_permintaan || index} className="hover:bg-gray-50 transition-colors">
                    <td className="px-6 py-4 text-gray-500">{index + 1}</td>
                    <td className="px-6 py-4 font-medium text-gray-900">{item.kode_permintaan || '-'}</td>
                    <td className="px-6 py-4 text-gray-500">{formatDate(item.tanggal_permintaan)}</td>
                    <td className="px-6 py-4 text-gray-900">
                      {item.pengguna?.full_name || item.pengguna?.username || '-'}
                    </td>
                    <td className="px-6 py-4 text-gray-500 max-w-[200px] truncate">
                      {item.keterangan_keperluan || '-'}
                    </td>
                    <td className="px-6 py-4">
                      <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium uppercase tracking-wider ${
                        item.status_persetujuan === 'disetujui' ? 'bg-green-100 text-green-800' :
                        item.status_persetujuan === 'ditolak' ? 'bg-red-100 text-red-800' :
                        'bg-yellow-100 text-yellow-800'
                      }`}>
                        {item.status_persetujuan || '-'}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-right">
                                          <DropdownMenu actions={[
                      { label: 'Lihat Detail', icon: Eye, onClick: () => openDetailModal(item), variant: 'default' },
                      { label: 'Hapus', icon: Trash2, onClick: () => handleDelete(item.kode_permintaan), variant: 'danger' },
                    ]} />
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
              <h3 className="text-lg font-semibold text-gray-900">Detail Permintaan {selectedItem.kode_permintaan}</h3>
              <button onClick={closeDetailModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>
            <div className="p-6 space-y-4 text-sm overflow-y-auto">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Kode Permintaan</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.kode_permintaan || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Tanggal Permintaan</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{formatDate(selectedItem.tanggal_permintaan)}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Pemohon</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.pengguna?.full_name || selectedItem.pengguna?.username || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Status</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.status_persetujuan || '-'}</div>
                </div>
                <div className="sm:col-span-2">
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Keterangan Keperluan</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100 whitespace-pre-wrap">{selectedItem.keterangan_keperluan || '-'}</div>
                </div>
                {selectedItem.alasan_disetujui && (
                  <div className="sm:col-span-2">
                    <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Alasan Keputusan</label>
                    <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100 whitespace-pre-wrap">{selectedItem.alasan_disetujui}</div>
                  </div>
                )}
              </div>

              {selectedItem.detail_permintaan && selectedItem.detail_permintaan.length > 0 && (
                <div className="mt-4">
                  <label className="block text-xs font-medium text-gray-500 mb-2 uppercase tracking-wider">Detail Barang</label>
                  <table className="w-full text-sm text-left border border-gray-200 rounded-md overflow-hidden">
                    <thead className="text-xs text-gray-500 bg-gray-50 border-b border-gray-200">
                      <tr>
                        <th className="px-4 py-2 font-medium">Nama Barang</th>
                        <th className="px-4 py-2 font-medium">Jumlah</th>
                        <th className="px-4 py-2 font-medium">Alasan</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-200">
                      {selectedItem.detail_permintaan.map((d: any, i: number) => (
                        <tr key={i}>
                          <td className="px-4 py-2">{d.master_barang?.nama_barang || '-'}</td>
                          <td className="px-4 py-2">{d.jumlah_diminta}</td>
                          <td className="px-4 py-2 text-gray-500">{d.alasan_kebutuhan || '-'}</td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              )}
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
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-full flex flex-col">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
              <h3 className="text-lg font-semibold text-gray-900">Buat Permintaan Barang</h3>
              <button onClick={closeModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>
            <div className="overflow-y-auto p-6">
              <form id="permintaanForm" onSubmit={handleSubmit} className="space-y-4 text-sm">
                <div>
                  <label className="block font-medium text-gray-700 mb-1">Barang *</label>
                  <select
                    required
                    value={formData.id_master_barang}
                    onChange={(e) => setFormData({ ...formData, id_master_barang: e.target.value })}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                  >
                    <option value="" disabled>Pilih Barang</option>
                    {masterBarangList.map((mb: any) => (
                      <option key={mb.id_master_barang || mb.id} value={mb.id_master_barang || mb.id}>
                        {mb.nama_barang}
                      </option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="block font-medium text-gray-700 mb-1">Jumlah Diminta *</label>
                  <input
                    type="number"
                    required
                    min="1"
                    value={formData.jumlah_diminta}
                    onChange={(e) => setFormData({ ...formData, jumlah_diminta: e.target.value })}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                    placeholder="Misal: 5"
                  />
                </div>
                <div>
                  <label className="block font-medium text-gray-700 mb-1">Alasan Kebutuhan *</label>
                  <textarea
                    rows={3}
                    required
                    value={formData.alasan_kebutuhan}
                    onChange={(e) => setFormData({ ...formData, alasan_kebutuhan: e.target.value })}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                    placeholder="Jelaskan alasan kebutuhan barang..."
                  />
                </div>
                <div>
                  <label className="block font-medium text-gray-700 mb-1">Keterangan Keperluan (Opsional)</label>
                  <textarea
                    rows={2}
                    value={formData.keterangan_keperluan}
                    onChange={(e) => setFormData({ ...formData, keterangan_keperluan: e.target.value })}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                    placeholder="Informasi tambahan (opsional)"
                  />
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
                form="permintaanForm"
                type="submit"
                disabled={isSubmitting}
                className="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 font-medium transition-colors flex items-center disabled:opacity-50 cursor-pointer"
              >
                {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                Kirim Permintaan
              </button>
            </div>
          </div>
        </div>
      )}

      {isKeputusanModalOpen && keputusanId && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4 py-8">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-md max-h-full flex flex-col">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
              <h3 className="text-lg font-semibold text-gray-900">Proses Keputusan</h3>
              <button onClick={closeKeputusanModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>
            <div className="overflow-y-auto p-6">
              <form id="keputusanForm" onSubmit={handleKeputusan} className="space-y-4 text-sm">
                <div>
                  <label className="block font-medium text-gray-700 mb-1">Keputusan *</label>
                  <select
                    required
                    value={keputusanData.status_persetujuan}
                    onChange={(e) => setKeputusanData({ ...keputusanData, status_persetujuan: e.target.value })}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                  >
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak">Ditolak</option>
                  </select>
                </div>
                <div>
                  <label className="block font-medium text-gray-700 mb-1">Alasan (Opsional)</label>
                  <textarea
                    rows={3}
                    value={keputusanData.alasan_disetujui}
                    onChange={(e) => setKeputusanData({ ...keputusanData, alasan_disetujui: e.target.value })}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                    placeholder="Alasan keputusan (opsional)"
                  />
                </div>
              </form>
            </div>
            <div className="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50 shrink-0 rounded-b-xl">
              <button
                type="button"
                onClick={closeKeputusanModal}
                className="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 font-medium transition-colors cursor-pointer"
              >
                Batal
              </button>
              <button
                form="keputusanForm"
                type="submit"
                disabled={isSubmitting}
                className="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 font-medium transition-colors flex items-center disabled:opacity-50 cursor-pointer"
              >
                {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                Simpan Keputusan
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
