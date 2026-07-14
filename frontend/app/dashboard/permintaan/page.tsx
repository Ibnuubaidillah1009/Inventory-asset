'use client';

import { useState, useEffect, useRef } from 'react';
import api from '@/utils/api';
import { Plus, Pencil, Trash2, X, Loader2, Search, Eye, Check } from 'lucide-react';

interface MasterBarang {
  id_master_barang: number;
  nama_barang: string;
}

interface Permintaan {
  kode_permintaan: string;
  tanggal_permintaan: string;
  keterangan_keperluan: string;
  status_persetujuan: string;
  detail_permintaan?: Array<{
    id_detail_permintaan: number;
    id_master_barang: number;
    jumlah_diminta: number;
    alasan_kebutuhan: string;
    master_barang?: MasterBarang;
  }>;
  pengguna?: { id_pengguna: number; username: string };
  jurusan?: { id_jurusan: number; nama_jurusan: string } | null;
  alasan_disetujui?: string | null;
  tanggal_persetujuan?: string | null;
}

export default function PermintaanBarangPage() {
  const [data, setData] = useState<Permintaan[]>([]);
  const [masterBarangList, setMasterBarangList] = useState<MasterBarang[]>([]);
  const [loading, setLoading] = useState(true);

  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDetailModalOpen, setIsDetailModalOpen] = useState(false);
  const [isApprovalModalOpen, setIsApprovalModalOpen] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [editingKode, setEditingKode] = useState<string | null>(null);
  const [selectedItem, setSelectedItem] = useState<Permintaan | null>(null);
  const [currentPage, setCurrentPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);

  // Approval State
  const [approvalStatus, setApprovalStatus] = useState('disetujui');
  const [approvalAlasan, setApprovalAlasan] = useState('');

  // Form State
  const [idMasterBarang, setIdMasterBarang] = useState<number | ''>('');
  const [jumlahDiminta, setJumlahDiminta] = useState<number | ''>('');
  const [alasanKebutuhan, setAlasanKebutuhan] = useState('');
  const [keteranganKeperluan, setKeteranganKeperluan] = useState('');

  // Searchable Select State
  const [isSelectOpen, setIsSelectOpen] = useState(false);
  const [barangSearchQuery, setBarangSearchQuery] = useState('');
  const selectRef = useRef<HTMLDivElement>(null);
  
  // Main Search State
  const [searchQuery, setSearchQuery] = useState('');

  const fetchApiData = async (page = 1, search = searchQuery) => {
    try {
      const [permintaanRes, barangRes] = await Promise.all([
        api.get(`/permintaan?page=${page}&search=${encodeURIComponent(search)}`),
        api.get('/master-barang')
      ]);
      setData(permintaanRes.data?.data || permintaanRes.data || []);
      if (permintaanRes.data?.meta) {
        setCurrentPage(permintaanRes.data.meta.current_page);
        setLastPage(permintaanRes.data.meta.last_page);
      }
      setMasterBarangList(barangRes.data?.data || barangRes.data || []);
    } catch (error) {
      console.error('Gagal mengambil data permintaan', error);
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

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (selectRef.current && !selectRef.current.contains(event.target as Node)) {
        setIsSelectOpen(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const openModal = (item: Permintaan | null = null) => {
    if (item && item.detail_permintaan && item.detail_permintaan.length > 0) {
      setEditingKode(item.kode_permintaan);
      const detail = item.detail_permintaan[0];
      setIdMasterBarang(detail.id_master_barang);
      setJumlahDiminta(detail.jumlah_diminta);
      setAlasanKebutuhan(detail.alasan_kebutuhan || '');
      setKeteranganKeperluan(item.keterangan_keperluan || '');
    } else {
      setEditingKode(null);
      setIdMasterBarang('');
      setJumlahDiminta('');
      setAlasanKebutuhan('');
      setKeteranganKeperluan('');
    }
    setBarangSearchQuery('');
    setIsModalOpen(true);
  };

  const closeModal = () => {
    setIsModalOpen(false);
    setEditingKode(null);
  };

  const openDetailModal = (item: Permintaan) => {
    setSelectedItem(item);
    setIsDetailModalOpen(true);
  };

  const closeDetailModal = () => {
    setIsDetailModalOpen(false);
    setSelectedItem(null);
  };

  const openApprovalModal = (item: Permintaan) => {
    setSelectedItem(item);
    setApprovalStatus(item.status_persetujuan === 'ditolak' ? 'ditolak' : 'disetujui');
    setApprovalAlasan(item.alasan_disetujui || '');
    setIsApprovalModalOpen(true);
  };

  const closeApprovalModal = () => {
    setIsApprovalModalOpen(false);
    setSelectedItem(null);
  };

  const handleApprovalSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedItem) return;
    setIsSubmitting(true);
    try {
      await api.put(`/permintaan/${selectedItem.kode_permintaan}/keputusan`, {
        status_persetujuan: approvalStatus,
        alasan_disetujui: approvalAlasan,
      });
      closeApprovalModal();
      fetchData();
    } catch (error) {
      console.error('Gagal menyimpan keputusan', error);
      alert('Gagal menyimpan keputusan persetujuan.');
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!idMasterBarang || !jumlahDiminta || !alasanKebutuhan) {
      alert('Mohon lengkapi semua field yang wajib.');
      return;
    }
    setIsSubmitting(true);
    try {
      const payload = {
        id_master_barang: Number(idMasterBarang),
        jumlah_diminta: Number(jumlahDiminta),
        alasan_kebutuhan: alasanKebutuhan,
        keterangan_keperluan: keteranganKeperluan,
      };
      if (editingKode) {
        await api.put(`/permintaan/${editingKode}`, payload);
      } else {
        await api.post('/permintaan', payload);
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

  const handleDelete = async (kode: string) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus permintaan ini?')) {
      try {
        await api.delete(`/permintaan/${kode}`);
        fetchData();
      } catch (error) {
        console.error('Gagal menghapus data', error);
        alert('Gagal menghapus data. Permintaan mungkin sudah disetujui.');
      }
    }
  };

  const filteredBarang = masterBarangList.filter(b =>
    b.nama_barang.toLowerCase().includes(barangSearchQuery.toLowerCase())
  );

  const selectedBarangName = masterBarangList.find(b => b.id_master_barang === idMasterBarang)?.nama_barang || 'Pilih Barang...';

  const isEditable = (status: string) => status === 'diproses';

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      {/* Header Halaman */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Permintaan Barang</h1>
          <p className="text-sm text-gray-500 mt-1">Ajukan permintaan pengadaan barang kebutuhan jurusan.</p>
        </div>
        <button
          onClick={() => openModal()}
          className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-gray-900 text-white hover:bg-gray-800 h-10 px-4 py-2 cursor-pointer"
        >
          <Plus className="mr-2 h-4 w-4" />
          Buat Permintaan
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
            placeholder="Cari berdasarkan kode, status..."
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
                <th className="px-6 py-4 font-medium">Kode Permintaan</th>
                <th className="px-6 py-4 font-medium">Tanggal</th>
                <th className="px-6 py-4 font-medium">Barang (Jumlah)</th>
                <th className="px-6 py-4 font-medium">Status</th>
                <th className="px-6 py-4 font-medium text-right">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {loading ? (
                <tr>
                  <td colSpan={5} className="px-6 py-12 text-center">
                    <div className="flex flex-col items-center justify-center">
                      <Loader2 className="h-6 w-6 animate-spin mx-auto text-gray-400 mb-3" />
                      <span className="text-gray-500">Memuat data permintaan...</span>
                    </div>
                  </td>
                </tr>
              ) : data.length === 0 ? (
                <tr>
                  <td colSpan={5} className="px-6 py-12 text-center text-gray-500">
                    Belum ada data permintaan.
                  </td>
                </tr>
              ) : (
                data.map((item) => (
                  <tr key={item.kode_permintaan} className="hover:bg-gray-50 transition-colors">
                    <td className="px-6 py-4 font-medium text-gray-900">{item.kode_permintaan}</td>
                    <td className="px-6 py-4 text-gray-500">{new Date(item.tanggal_permintaan).toLocaleDateString('id-ID')}</td>
                    <td className="px-6 py-4 text-gray-500">
                      {item.detail_permintaan?.map((d, i) => (
                        <div key={i}>
                          {d.master_barang?.nama_barang || 'Barang ID: ' + d.id_master_barang} ({d.jumlah_diminta})
                        </div>
                      ))}
                    </td>
                    <td className="px-6 py-4">
                      <span className={`inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium ${
                        item.status_persetujuan === 'disetujui' ? 'bg-green-100 text-green-800' :
                        item.status_persetujuan === 'ditolak' ? 'bg-red-100 text-red-800' :
                        'bg-yellow-100 text-yellow-800'
                      }`}>
                        {item.status_persetujuan.toUpperCase()}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-right">
                      <button onClick={() => openDetailModal(item)} className="text-gray-400 hover:text-blue-600 mr-3 transition-colors cursor-pointer" title="Lihat Detail">
                        <Eye className="h-4 w-4" />
                      </button>
                      <button onClick={() => openApprovalModal(item)} className="text-gray-400 hover:text-green-600 transition-colors cursor-pointer" title="Persetujuan">
                        <Check className="h-4 w-4" />
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
              className="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Sebelumnya
            </button>
            <button
              onClick={() => fetchData(currentPage + 1)}
              disabled={currentPage === lastPage}
              className="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
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
              <h3 className="text-lg font-semibold text-gray-900">Detail Permintaan</h3>
              <button onClick={closeDetailModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>

            <div className="p-6 space-y-4 text-sm overflow-y-auto">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Kode Permintaan</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.kode_permintaan}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Tanggal</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{new Date(selectedItem.tanggal_permintaan).toLocaleDateString('id-ID')}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Status</label>
                  <div className="bg-gray-50 p-2 rounded-md border border-gray-100">
                    <span className={`inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium ${
                      selectedItem.status_persetujuan === 'disetujui' ? 'bg-green-100 text-green-800' :
                      selectedItem.status_persetujuan === 'ditolak' ? 'bg-red-100 text-red-800' :
                      'bg-yellow-100 text-yellow-800'
                    }`}>
                      {selectedItem.status_persetujuan.toUpperCase()}
                    </span>
                  </div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Pengguna</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.pengguna?.username || '-'}</div>
                </div>
                <div className="sm:col-span-2">
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Keterangan Keperluan</label>
                  <div className="text-gray-900 bg-gray-50 p-2 rounded-md border border-gray-100 whitespace-pre-wrap">{selectedItem.keterangan_keperluan || '-'}</div>
                </div>
              </div>

              {selectedItem.detail_permintaan && selectedItem.detail_permintaan.length > 0 && (
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-2 uppercase tracking-wider">Detail Barang</label>
                  <div className="space-y-2">
                    {selectedItem.detail_permintaan.map((d, i) => (
                      <div key={i} className="bg-gray-50 p-3 rounded-md border border-gray-100">
                        <div className="font-medium text-gray-900">{d.master_barang?.nama_barang || 'Barang ID: ' + d.id_master_barang}</div>
                        <div className="text-gray-500 mt-1">Jumlah: {d.jumlah_diminta}</div>
                        {d.alasan_kebutuhan && <div className="text-gray-500 mt-1">Alasan: {d.alasan_kebutuhan}</div>}
                      </div>
                    ))}
                  </div>
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

      {/* Pop Up Modal Persetujuan */}
      {isApprovalModalOpen && selectedItem && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4 py-8">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-md max-h-full flex flex-col">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
              <h3 className="text-lg font-semibold text-gray-900">Persetujuan Permintaan</h3>
              <button onClick={closeApprovalModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>

            <div className="overflow-y-auto p-6">
              <form id="approvalForm" onSubmit={handleApprovalSubmit} className="space-y-4 text-sm">
                <div>
                  <label className="block font-medium text-gray-700 mb-1">Status Persetujuan <span className="text-red-500">*</span></label>
                  <select
                    value={approvalStatus}
                    onChange={(e) => setApprovalStatus(e.target.value)}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                    required
                  >
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak">Ditolak</option>
                  </select>
                </div>

                <div>
                  <label className="block font-medium text-gray-700 mb-1">Alasan / Catatan</label>
                  <textarea
                    rows={3}
                    value={approvalAlasan}
                    onChange={(e) => setApprovalAlasan(e.target.value)}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                    placeholder="Masukkan alasan atau catatan persetujuan..."
                  />
                </div>
              </form>
            </div>

            <div className="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50 shrink-0 rounded-b-xl">
              <button
                type="button"
                onClick={closeApprovalModal}
                className="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 font-medium transition-colors cursor-pointer"
              >
                Batal
              </button>
              <button
                form="approvalForm"
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

      {/* Pop Up Modal Form */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4 py-8">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-full flex flex-col">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
              <h3 className="text-lg font-semibold text-gray-900">
                {editingKode ? 'Edit Permintaan' : 'Buat Permintaan Baru'}
              </h3>
              <button onClick={closeModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>

            <div className="overflow-y-auto p-6">
              <form id="permintaanForm" onSubmit={handleSubmit} className="space-y-4 text-sm">
                {/* Searchable Select Barang */}
                <div className="relative" ref={selectRef}>
                  <label className="block font-medium text-gray-700 mb-1">Nama Barang <span className="text-red-500">*</span></label>
                  <div
                    className="w-full rounded-md border border-gray-300 px-3 py-2 bg-white flex justify-between items-center cursor-pointer hover:border-gray-900 transition-colors"
                    onClick={() => setIsSelectOpen(!isSelectOpen)}
                  >
                    <span className={idMasterBarang ? 'text-gray-900' : 'text-gray-400'}>{selectedBarangName}</span>
                    <Search className="h-4 w-4 text-gray-400" />
                  </div>

                  {isSelectOpen && (
                    <div className="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 flex flex-col">
                      <div className="p-2 border-b">
                        <input
                          type="text"
                          placeholder="Cari barang..."
                          className="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-gray-900 text-sm"
                          value={barangSearchQuery}
                          onChange={(e) => setBarangSearchQuery(e.target.value)}
                          onClick={(e) => e.stopPropagation()}
                        />
                      </div>
                      <div className="overflow-y-auto p-1 flex-1">
                        {filteredBarang.length > 0 ? (
                          filteredBarang.map(barang => (
                            <div
                              key={barang.id_master_barang}
                              className={`px-3 py-2 text-sm rounded-md cursor-pointer hover:bg-gray-100 flex items-center justify-between ${idMasterBarang === barang.id_master_barang ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-700'}`}
                              onClick={() => {
                                setIdMasterBarang(barang.id_master_barang);
                                setIsSelectOpen(false);
                              }}
                            >
                              {barang.nama_barang}
                              {idMasterBarang === barang.id_master_barang && <Check className="h-4 w-4" />}
                            </div>
                          ))
                        ) : (
                          <div className="px-3 py-4 text-center text-sm text-gray-500">Barang tidak ditemukan</div>
                        )}
                      </div>
                    </div>
                  )}
                </div>

                {/* Jumlah Diminta */}
                <div>
                  <label className="block font-medium text-gray-700 mb-1">Jumlah Diminta <span className="text-red-500">*</span></label>
                  <input
                    type="number"
                    required
                    min="1"
                    value={jumlahDiminta}
                    onChange={(e) => setJumlahDiminta(e.target.value ? Number(e.target.value) : '')}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                    placeholder="Masukkan jumlah"
                  />
                </div>

                {/* Alasan Kebutuhan */}
                <div>
                  <label className="block font-medium text-gray-700 mb-1">Alasan Kebutuhan <span className="text-red-500">*</span></label>
                  <textarea
                    required
                    rows={3}
                    value={alasanKebutuhan}
                    onChange={(e) => setAlasanKebutuhan(e.target.value)}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                    placeholder="Mengapa barang ini dibutuhkan?"
                  />
                </div>

                {/* Keterangan Keperluan */}
                <div>
                  <label className="block font-medium text-gray-700 mb-1">Keterangan Keperluan <span className="text-xs text-gray-400 font-normal">(Opsional)</span></label>
                  <textarea
                    rows={2}
                    value={keteranganKeperluan}
                    onChange={(e) => setKeteranganKeperluan(e.target.value)}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                    placeholder="Catatan tambahan (opsional)"
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
                Simpan
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
