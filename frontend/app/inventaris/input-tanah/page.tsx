'use client';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { Plus, Pencil, Trash2, X, Loader2, Search, Eye } from 'lucide-react';
import { extractData, formatDate } from '@/lib/utils';

import { toast } from 'sonner';
import DropdownMenu from '@/app/components/DropdownMenu';

export default function InputTanahPage() {
  const [data, setData] = useState<any[]>([]);
  const [lokasiList, setLokasiList] = useState<any[]>([]);
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
    nama_pemilik: '',
    id_lokasi: '',
    luas_tanah: '',
    letak_tanah: '',
    nomor_sertifikat: '',
    status_hak: '',
    nilai_aset: '',
    penggunaan: '',
    tanggal_perolehan: '',
    sumber_perolehan: '' });

  const fetchData = async (page = 1, search = searchQuery) => {
    setLoading(true);
    try {
      const [resAset, resLokasi] = await Promise.all([
        api.get(`/aset?page=${page}&search=${encodeURIComponent(search)}`).catch(() => ({ data: { data: [] } })),
        api.get('/lokasi').catch(() => ({ data: { data: [] } })),
      ]);

      const asetData = (extractData(resAset.data.data)).filter((a: any) => a.aset_tanah || a.nama_pemilik);
      setData(asetData);
      setLokasiList(extractData(resLokasi.data.data));

      if (resAset.data.meta) {
        setCurrentPage(resAset.data.meta.current_page);
        setLastPage(resAset.data.meta.last_page);
      }
    } catch (error) {
      console.error('Gagal mengambil data aset tanah', error);
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
        nama_pemilik: item.aset_tanah?.nama_pemilik || item.nama_pemilik || '',
        id_lokasi: item.aset_tanah?.id_lokasi || item.id_lokasi || '',
        luas_tanah: item.aset_tanah?.luas_tanah || item.luas_tanah || '',
        letak_tanah: item.aset_tanah?.letak_tanah || item.letak_tanah || '',
        nomor_sertifikat: item.aset_tanah?.nomor_sertifikat || item.nomor_sertifikat || '',
        status_hak: item.aset_tanah?.status_hak || item.status_hak || '',
        nilai_aset: item.aset_tanah?.nilai_aset || item.nilai_aset || '',
        penggunaan: item.aset_tanah?.penggunaan || item.penggunaan || '',
        tanggal_perolehan: item.aset_tanah?.tanggal_perolehan || item.tanggal_perolehan || '',
        sumber_perolehan: item.aset_tanah?.sumber_perolehan || item.sumber_perolehan || '' });
    } else {
      setEditingId(null);
      setFormData({
        kode_inventaris: '',
        nama_pemilik: '',
        id_lokasi: '',
        luas_tanah: '',
        letak_tanah: '',
        nomor_sertifikat: '',
        status_hak: '',
        nilai_aset: '',
        penggunaan: '',
        tanggal_perolehan: '',
        sumber_perolehan: '' });
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
        nama_pemilik: formData.nama_pemilik,
        id_lokasi: formData.id_lokasi ? Number(formData.id_lokasi) : null,
        luas_tanah: formData.luas_tanah ? Number(formData.luas_tanah) : null,
        letak_tanah: formData.letak_tanah,
        nomor_sertifikat: formData.nomor_sertifikat,
        status_hak: formData.status_hak,
        nilai_aset: formData.nilai_aset ? Number(formData.nilai_aset) : null,
        penggunaan: formData.penggunaan,
        tanggal_perolehan: formData.tanggal_perolehan || null,
        sumber_perolehan: formData.sumber_perolehan };

      if (editingId) {
        await api.put(`/aset/${editingId}`, { keterangan: JSON.stringify(payload) });
      } else {
        await api.post('/aset', {
          kode_inventaris: formData.kode_inventaris,
          id_master_barang: 1,
          status: 'Aktif',
          keterangan: JSON.stringify(payload) });
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

  const handleDelete = async (id: string) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus aset tanah ini?')) {
      try {
        await api.delete(`/aset/${id}`);
        toast.success('Data berhasil dihapus');
        fetchData();
      } catch (error) {
        console.error('Gagal menghapus data', error);
        toast.error('Gagal menghapus data. Aset mungkin sedang digunakan.');
      }
    }
  };

  const formatRupiah = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0 }).format(value);
  };

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Input Tanah</h1>
          <p className="text-sm text-gray-500 mt-1">Input data aset tanah.</p>
        </div>
        <button
          onClick={() => openModal()}
          className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-gray-900 text-white hover:bg-gray-800 h-10 px-4 py-2 cursor-pointer"
        >
          <Plus className="mr-2 h-4 w-4" />
          Tambah Tanah
        </button>
      </div>

      <div className="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div className="relative w-full sm:max-w-md">
          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search className="h-4 w-4 text-gray-400" />
          </div>
          <input
            type="text"
            placeholder="Cari tanah berdasarkan kode, nama pemilik..."
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
                <th className="px-6 py-4 font-medium">Kode Inventaris</th>
                <th className="px-6 py-4 font-medium">Nama Pemilik</th>
                <th className="px-6 py-4 font-medium">Luas (m²)</th>
                <th className="px-6 py-4 font-medium">Status Hak</th>
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
                    {searchQuery ? 'Tidak ada data yang cocok dengan pencarian Anda.' : 'Belum ada data aset tanah.'}
                  </td>
                </tr>
              ) : (
                data.map((item: any, index: number) => (
                  <tr key={item.kode_barang || index} className="hover:bg-gray-50 transition-colors">
                    <td className="px-6 py-4 text-gray-500">{index + 1}</td>
                    <td className="px-6 py-4 font-medium text-gray-900">{item.kode_barang || '-'}</td>
                    <td className="px-6 py-4 text-gray-900 font-medium">
                      {item.aset_tanah?.nama_pemilik || item.nama_pemilik || '-'}
                    </td>
                    <td className="px-6 py-4 text-gray-500">
                      {item.aset_tanah?.luas_tanah || item.luas_tanah || '-'} m²
                    </td>
                    <td className="px-6 py-4 text-gray-500">
                      {item.aset_tanah?.status_hak || item.status_hak || '-'}
                    </td>
                    <td className="px-6 py-4 text-right">
                                          <DropdownMenu actions={[
                      { label: 'Lihat Detail', icon: Eye, onClick: () => openDetailModal(item), variant: 'default' },
                      { label: 'Ubah', icon: Pencil, onClick: () => openModal(item), variant: 'default' },
                      { label: 'Hapus', icon: Trash2, onClick: () => handleDelete(item.kode_barang), variant: 'danger' },
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
              <h3 className="text-lg font-semibold text-gray-900">Detail Aset Tanah</h3>
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
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Nama Pemilik</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.aset_tanah?.nama_pemilik || selectedItem.nama_pemilik || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Luas Tanah</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.aset_tanah?.luas_tanah || selectedItem.luas_tanah || '-'} m²</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Letak Tanah</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.aset_tanah?.letak_tanah || selectedItem.letak_tanah || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Nomor Sertifikat</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.aset_tanah?.nomor_sertifikat || selectedItem.nomor_sertifikat || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Status Hak</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.aset_tanah?.status_hak || selectedItem.status_hak || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Nilai Aset</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{formatRupiah(selectedItem.aset_tanah?.nilai_aset || selectedItem.nilai_aset || 0)}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Penggunaan</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.aset_tanah?.penggunaan || selectedItem.penggunaan || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Tanggal Perolehan</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{formatDate(selectedItem.aset_tanah?.tanggal_perolehan || selectedItem.tanggal_perolehan)}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Sumber Perolehan</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.aset_tanah?.sumber_perolehan || selectedItem.sumber_perolehan || '-'}</div>
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
                {editingId ? 'Edit Aset Tanah' : 'Tambah Aset Tanah'}
              </h3>
              <button onClick={closeModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>
            <div className="overflow-y-auto p-6">
              <form id="tanahForm" onSubmit={handleSubmit} className="space-y-4 text-sm">
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
                      placeholder="Misal: TNH-2026-001"
                    />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Nama Pemilik</label>
                    <input
                      type="text"
                      required
                      value={formData.nama_pemilik}
                      onChange={(e) => setFormData({ ...formData, nama_pemilik: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                      placeholder="Misal: Pemerintah Kabupaten"
                    />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Luas Tanah (m²)</label>
                    <input
                      type="number"
                      min="0"
                      step="0.01"
                      value={formData.luas_tanah}
                      onChange={(e) => setFormData({ ...formData, luas_tanah: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                      placeholder="Misal: 1000"
                    />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Letak Tanah</label>
                    <input
                      type="text"
                      value={formData.letak_tanah}
                      onChange={(e) => setFormData({ ...formData, letak_tanah: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                      placeholder="Misal: Jl. Pendidikan No. 1"
                    />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Nomor Sertifikat</label>
                    <input
                      type="text"
                      value={formData.nomor_sertifikat}
                      onChange={(e) => setFormData({ ...formData, nomor_sertifikat: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                      placeholder="Misal: SHM-12345"
                    />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Status Hak</label>
                    <select
                      value={formData.status_hak}
                      onChange={(e) => setFormData({ ...formData, status_hak: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                    >
                      <option value="">Pilih Status Hak</option>
                      <option value="Hak Milik">Hak Milik</option>
                      <option value="Hak Guna Bangunan">Hak Guna Bangunan</option>
                      <option value="Hak Pakai">Hak Pakai</option>
                      <option value="Hak Sewa">Hak Sewa</option>
                      <option value="Tanah Negara">Tanah Negara</option>
                    </select>
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Nilai Aset (Rp)</label>
                    <input
                      type="number"
                      min="0"
                      value={formData.nilai_aset}
                      onChange={(e) => setFormData({ ...formData, nilai_aset: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                      placeholder="Misal: 2000000000"
                    />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Penggunaan</label>
                    <input
                      type="text"
                      value={formData.penggunaan}
                      onChange={(e) => setFormData({ ...formData, penggunaan: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                      placeholder="Misal: Gedung Sekolah"
                    />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Tanggal Perolehan</label>
                    <input
                      type="date"
                      value={formData.tanggal_perolehan}
                      onChange={(e) => setFormData({ ...formData, tanggal_perolehan: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                    />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Sumber Perolehan</label>
                    <select
                      value={formData.sumber_perolehan}
                      onChange={(e) => setFormData({ ...formData, sumber_perolehan: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                    >
                      <option value="">Pilih Sumber</option>
                      <option value="APBD">APBD</option>
                      <option value="Hibah">Hibah</option>
                      <option value="Bantuan Pemerintah">Bantuan Pemerintah</option>
                      <option value="Swadaya">Swadaya</option>
                      <option value="Lainnya">Lainnya</option>
                    </select>
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Lokasi</label>
                    <select
                      value={formData.id_lokasi}
                      onChange={(e) => setFormData({ ...formData, id_lokasi: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                    >
                      <option value="">Pilih Lokasi</option>
                      {lokasiList.map((l: any) => (
                        <option key={l.id_lokasi || l.id} value={l.id_lokasi || l.id}>
                          {l.nama_lokasi}
                        </option>
                      ))}
                    </select>
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
                form="tanahForm"
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
