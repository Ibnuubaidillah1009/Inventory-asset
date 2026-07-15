'use client';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { Plus, Pencil, Trash2, X, Loader2, Search, Eye } from 'lucide-react';

export default function AsetPage() {
  const [data, setData] = useState<any[]>([]);
  const [pengadaanList, setPengadaanList] = useState<any[]>([]);
  const [masterBarangList, setMasterBarangList] = useState<any[]>([]);
  const [jurusanList, setJurusanList] = useState<any[]>([]);
  const [ruangList, setRuangList] = useState<any[]>([]);
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

  // Form State
  const [formData, setFormData] = useState({
    kode_inventaris: '',
    id_pengadaan: '',
    id_master_barang: '',
    id_jurusan: '',
    id_ruang: '',
    id_lokasi: '',
    no_seri: '',
    harga_satuan: '',
    nilai_residu: '',
    umur_ekonomi: '',
    status: 'Di Gudang',
  });

  const fetchData = async (page = 1, search = searchQuery) => {
    setLoading(true);
    try {
      const [resAset, resPengadaan, resMaster, resJurusan, resRuang, resLokasi] = await Promise.all([
        api.get(`/aset?page=${page}&search=${encodeURIComponent(search)}`),
        api.get('/pengadaan').catch(() => ({ data: { data: [] } })),
        api.get('/master-barang').catch(() => ({ data: { data: [] } })),
        api.get('/jurusan').catch(() => ({ data: { data: [] } })),
        api.get('/ruang').catch(() => ({ data: { data: [] } })),
        api.get('/lokasi').catch(() => ({ data: { data: [] } })),
      ]);

      setData(resAset.data.data || []);
      if (resAset.data.meta) {
        setCurrentPage(resAset.data.meta.current_page);
        setLastPage(resAset.data.meta.last_page);
      }
      setPengadaanList(resPengadaan.data.data || []);
      setMasterBarangList(resMaster.data.data || []);
      setJurusanList(resJurusan.data.data || []);
      setRuangList(resRuang.data.data || []);
      setLokasiList(resLokasi.data.data || []);
    } catch (error) {
      console.error('Gagal mengambil data aset', error);
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
      setEditingId(item.kode_inventaris || item.id);
      setFormData({
        kode_inventaris: item.kode_inventaris || '',
        id_pengadaan: item.id_pengadaan || '',
        id_master_barang: item.id_master_barang || '',
        id_jurusan: item.id_jurusan || '',
        id_ruang: item.id_ruang || '',
        id_lokasi: item.id_lokasi || '',
        no_seri: item.no_seri || '',
        harga_satuan: item.harga_satuan !== undefined ? String(item.harga_satuan) : '',
        nilai_residu: item.nilai_residu !== undefined ? String(item.nilai_residu) : '',
        umur_ekonomi: item.umur_ekonomi !== undefined ? String(item.umur_ekonomi) : '',
        status: item.status || 'Di Gudang',
      });
    } else {
      setEditingId(null);
      setFormData({
        kode_inventaris: '',
        id_pengadaan: '',
        id_master_barang: '',
        id_jurusan: '',
        id_ruang: '',
        id_lokasi: '',
        no_seri: '',
        harga_satuan: '',
        nilai_residu: '',
        umur_ekonomi: '',
        status: 'Di Gudang',
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
        id_pengadaan: formData.id_pengadaan ? Number(formData.id_pengadaan) : null,
        id_master_barang: Number(formData.id_master_barang),
        id_jurusan: Number(formData.id_jurusan),
        id_ruang: Number(formData.id_ruang),
        id_lokasi: Number(formData.id_lokasi),
        harga_satuan: Number(formData.harga_satuan),
        nilai_residu: Number(formData.nilai_residu),
        umur_ekonomi: Number(formData.umur_ekonomi),
      };

      if (editingId) {
        await api.put(`/aset/${editingId}`, payload);
      } else {
        await api.post('/aset', payload);
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
    if (window.confirm('Apakah Anda yakin ingin menghapus aset ini?')) {
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
      {/* Header Halaman */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Data Aset</h1>
          <p className="text-sm text-gray-500 mt-1">Kelola data inventaris aset aktif sekolah.</p>
        </div>
        <button
          onClick={() => openModal()}
          className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-gray-900 text-white hover:bg-gray-800 h-10 px-4 py-2 cursor-pointer"
        >
          <Plus className="mr-2 h-4 w-4" />
          Tambah Aset
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
            placeholder="Cari aset berdasarkan kode, nama barang, status..."
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
                <th className="px-6 py-4 font-medium">Kode Inventaris</th>
                <th className="px-6 py-4 font-medium">Nama Barang</th>
                <th className="px-6 py-4 font-medium">Ruang</th>
                <th className="px-6 py-4 font-medium">Status</th>
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
                    {searchQuery ? 'Tidak ada data yang cocok dengan pencarian Anda.' : 'Belum ada data aset.'}
                  </td>
                </tr>
              ) : (
                data.map((item, index) => (
                  <tr key={index} className="hover:bg-gray-50 transition-colors">
                    <td className="px-6 py-4 text-gray-500">{index + 1}</td>
                    <td className="px-6 py-4 font-medium text-gray-900">{item.kode_inventaris || '-'}</td>
                    <td className="px-6 py-4 text-gray-900 font-medium">
                      {item.master_barang?.nama_barang || item.nama_barang || '-'}
                    </td>
                    <td className="px-6 py-4 text-gray-500">
                      {item.ruang?.nama_ruang || item.nama_ruang || '-'}
                    </td>
                    <td className="px-6 py-4">
                      <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium uppercase tracking-wider ${
                        item.status === 'Aktif' ? 'bg-green-100 text-green-800' :
                        item.status === 'Dipinjam' ? 'bg-blue-100 text-blue-800' :
                        item.status === 'Nonaktif' ? 'bg-red-100 text-red-800' :
                        'bg-yellow-100 text-yellow-800'
                      }`}>
                        {item.status || 'Di Gudang'}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-right">
                      <button onClick={() => openDetailModal(item)} className="text-gray-400 hover:text-blue-600 mr-3 transition-colors cursor-pointer" title="Lihat Detail">
                        <Eye className="h-4 w-4" />
                      </button>
                      <button onClick={() => openModal(item)} className="text-gray-400 hover:text-gray-900 mr-3 transition-colors cursor-pointer" title="Edit">
                        <Pencil className="h-4 w-4" />
                      </button>
                      <button onClick={() => handleDelete(item.kode_inventaris || item.id)} className="text-gray-400 hover:text-red-600 transition-colors cursor-pointer" title="Hapus">
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
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4 py-8">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
              <h3 className="text-lg font-semibold text-gray-900">Detail Aset</h3>
              <button onClick={closeDetailModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>
            
            <div className="p-6 space-y-4 text-sm overflow-y-auto">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Kode Inventaris</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.kode_inventaris || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">No Seri</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.no_seri || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Nama Barang</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.master_barang?.nama_barang || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Jurusan</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.jurusan?.nama_jurusan || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Ruang</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.ruang?.nama_ruang || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Lokasi</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.lokasi?.nama_lokasi || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">ID Pengadaan</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">
                    {selectedItem.id_pengadaan ? `Pengadaan #${selectedItem.id_pengadaan}` : 'Tanpa Pengadaan (Nullable)'}
                  </div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Status</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.status || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Harga Satuan</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{formatRupiah(selectedItem.harga_satuan || 0)}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Nilai Residu</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{formatRupiah(selectedItem.nilai_residu || 0)}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Umur Ekonomi</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.umur_ekonomi || '0'} Tahun</div>
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

      {/* Pop Up Modal Form */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4 py-8">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-full flex flex-col">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
              <h3 className="text-lg font-semibold text-gray-900">
                {editingId ? 'Edit Aset' : 'Tambah Aset'}
              </h3>
              <button onClick={closeModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>
            
            <div className="overflow-y-auto p-6">
              <form id="asetForm" onSubmit={handleSubmit} className="space-y-4 text-sm">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  {/* Kode Inventaris */}
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Kode Inventaris</label>
                    <input
                      type="text"
                      required
                      disabled={!!editingId}
                      value={formData.kode_inventaris}
                      onChange={(e) => setFormData({ ...formData, kode_inventaris: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 disabled:bg-gray-100 disabled:text-gray-500"
                      placeholder="Misal: INV-2026-001"
                    />
                  </div>

                  {/* No Seri */}
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Nomor Seri</label>
                    <input
                      type="text"
                      required
                      value={formData.no_seri}
                      onChange={(e) => setFormData({ ...formData, no_seri: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                      placeholder="Misal: SN-823901"
                    />
                  </div>

                  {/* Master Barang */}
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Barang</label>
                    <select
                      required
                      value={formData.id_master_barang}
                      onChange={(e) => setFormData({ ...formData, id_master_barang: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                    >
                      <option value="" disabled>Pilih Barang</option>
                      {masterBarangList.map((mb) => (
                        <option key={mb.id_master_barang || mb.id} value={mb.id_master_barang || mb.id}>
                          {mb.nama_barang}
                        </option>
                      ))}
                    </select>
                  </div>

                  {/* Jurusan */}
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Jurusan</label>
                    <select
                      required
                      value={formData.id_jurusan}
                      onChange={(e) => setFormData({ ...formData, id_jurusan: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                    >
                      <option value="" disabled>Pilih Jurusan</option>
                      {jurusanList.map((jur) => (
                        <option key={jur.id_jurusan || jur.id} value={jur.id_jurusan || jur.id}>
                          {jur.nama_jurusan || jur.nama}
                        </option>
                      ))}
                    </select>
                  </div>

                  {/* Ruang */}
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Ruang</label>
                    <select
                      required
                      value={formData.id_ruang}
                      onChange={(e) => setFormData({ ...formData, id_ruang: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                    >
                      <option value="" disabled>Pilih Ruang</option>
                      {ruangList.map((r) => (
                        <option key={r.id_ruang || r.id} value={r.id_ruang || r.id}>
                          {r.nama_ruang}
                        </option>
                      ))}
                    </select>
                  </div>

                  {/* Lokasi */}
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Lokasi</label>
                    <select
                      required
                      value={formData.id_lokasi}
                      onChange={(e) => setFormData({ ...formData, id_lokasi: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                    >
                      <option value="" disabled>Pilih Lokasi</option>
                      {lokasiList.map((l) => (
                        <option key={l.id_lokasi || l.id} value={l.id_lokasi || l.id}>
                          {l.nama_lokasi}
                        </option>
                      ))}
                    </select>
                  </div>

                  {/* Pengadaan */}
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Transaksi Pengadaan (Opsional)</label>
                    <select
                      value={formData.id_pengadaan}
                      onChange={(e) => setFormData({ ...formData, id_pengadaan: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                    >
                      <option value="">Bukan dari Pengadaan</option>
                      {pengadaanList.map((p) => (
                        <option key={p.id_pengadaan || p.id} value={p.id_pengadaan || p.id}>
                          Pengadaan #{p.id_pengadaan || p.id} ({p.tanggal_pengadaan ? p.tanggal_pengadaan.split('T')[0] : ''})
                        </option>
                      ))}
                    </select>
                  </div>

                  {/* Status */}
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Status</label>
                    <select
                      required
                      value={formData.status}
                      onChange={(e) => setFormData({ ...formData, status: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                    >
                      <option value="Di Gudang">Di Gudang</option>
                      <option value="Aktif">Aktif</option>
                      <option value="Dipinjam">Dipinjam</option>
                      <option value="Nonaktif">Nonaktif</option>
                    </select>
                  </div>

                  {/* Harga Satuan */}
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Harga Satuan (Rp)</label>
                    <input
                      type="number"
                      required
                      min="0"
                      value={formData.harga_satuan}
                      onChange={(e) => setFormData({ ...formData, harga_satuan: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                      placeholder="Misal: 10000000"
                    />
                  </div>

                  {/* Nilai Residu */}
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Nilai Residu (Rp)</label>
                    <input
                      type="number"
                      required
                      min="0"
                      value={formData.nilai_residu}
                      onChange={(e) => setFormData({ ...formData, nilai_residu: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                      placeholder="Misal: 1000000"
                    />
                  </div>

                  {/* Umur Ekonomi */}
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Umur Ekonomi (Tahun)</label>
                    <input
                      type="number"
                      required
                      min="0"
                      value={formData.umur_ekonomi}
                      onChange={(e) => setFormData({ ...formData, umur_ekonomi: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                      placeholder="Misal: 5"
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
                form="asetForm"
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
