'use client';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { extractData, formatDate, formatRupiah } from '@/lib/utils';
import { Plus, Pencil, Trash2, X, Loader2, Search, Eye } from 'lucide-react';

import { toast } from 'sonner';
import DropdownMenu from '@/app/components/DropdownMenu';
import CurrencyInput from '@/app/components/CurrencyInput';

export default function AsetPage() {
  const [data, setData] = useState<any[]>([]);
  const [pengadaanList, setPengadaanList] = useState<any[]>([]);
  const [masterBarangList, setMasterBarangList] = useState<any[]>([]);
  const [jurusanList, setJurusanList] = useState<any[]>([]);
  const [ruangList, setRuangList] = useState<any[]>([]);
  const [lokasiList, setLokasiList] = useState<any[]>([]);
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

  const emptyForm = {
    kode_barang: '',
    kode_inventaris: '',
    id_pengadaan: '',
    id_master_barang: '',
    id_kondisi: '',
    id_jurusan: '',
    id_ruang: '',
    id_lokasi: '',
    penanggung_jawab: '',
    no_seri: '',
    model_tipe: '',
    barcode: '',
    harga_satuan: '',
    nilai_residu: '',
    umur_ekonomi: '',
    metode_penyusutan: '',
    status: 'Di Gudang',
    keterangan: '',
    tanggal_garansi_mulai: '',
    tanggal_garansi_akhir: '',
    info_garansi: '',
    jadwal_pemeliharaan_berikutnya: '',
    nomor_polis_asuransi: '',
    nilai_pertanggungan: '',
    tanggal_akhir_asuransi: '' };

  const [formData, setFormData] = useState(emptyForm);

  const fetchData = async (page = 1, search = searchQuery) => {
    setLoading(true);
    try {
      const [resAset, resPengadaan, resMaster, resJurusan, resRuang, resLokasi, resKondisi] = await Promise.all([
        api.get(`/aset?page=${page}&search=${encodeURIComponent(search)}`),
        api.get('/pengadaan').catch(() => ({ data: { data: [] } })),
        api.get('/master-barang').catch(() => ({ data: { data: [] } })),
        api.get('/jurusan').catch(() => ({ data: { data: [] } })),
        api.get('/ruang').catch(() => ({ data: { data: [] } })),
        api.get('/lokasi').catch(() => ({ data: { data: [] } })),
        api.get('/aset-kondisi').catch(() => ({ data: { data: [] } })),
      ]);

      setData(extractData(resAset.data.data));
      if (resAset.data.meta) {
        setCurrentPage(resAset.data.meta.current_page);
        setLastPage(resAset.data.meta.last_page);
      }
      setPengadaanList(extractData(resPengadaan.data.data));
      setMasterBarangList(extractData(resMaster.data.data));
      setJurusanList(extractData(resJurusan.data.data));
      setRuangList(extractData(resRuang.data.data));
      setLokasiList(extractData(resLokasi.data.data));
      setKondisiList(extractData(resKondisi.data.data));
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
  }, [searchQuery]);

  const openModal = (item: any = null) => {
    if (item) {
      setEditingId(item.kode_barang || item.id);
      setFormData({
        kode_barang: item.kode_barang || '',
        kode_inventaris: item.kode_inventaris || '',
        id_pengadaan: item.id_pengadaan || '',
        id_master_barang: item.id_master_barang || '',
        id_kondisi: item.id_kondisi || '',
        id_jurusan: item.id_jurusan || '',
        id_ruang: item.id_ruang || '',
        id_lokasi: item.id_lokasi || '',
        penanggung_jawab: item.penanggung_jawab || '',
        no_seri: item.no_seri || '',
        model_tipe: item.model_tipe || '',
        barcode: item.barcode || '',
        harga_satuan: item.harga_satuan !== undefined ? String(item.harga_satuan) : '',
        nilai_residu: item.nilai_residu !== undefined ? String(item.nilai_residu) : '',
        umur_ekonomi: item.umur_ekonomi !== undefined ? String(item.umur_ekonomi) : '',
        metode_penyusutan: item.metode_penyusutan || '',
        status: item.status || 'Di Gudang',
        keterangan: item.keterangan || '',
        tanggal_garansi_mulai: item.tanggal_garansi_mulai || '',
        tanggal_garansi_akhir: item.tanggal_garansi_akhir || '',
        info_garansi: item.info_garansi || '',
        jadwal_pemeliharaan_berikutnya: item.jadwal_pemeliharaan_berikutnya || '',
        nomor_polis_asuransi: item.nomor_polis_asuransi || '',
        nilai_pertanggungan: item.nilai_pertanggungan !== undefined ? String(item.nilai_pertanggungan) : '',
        tanggal_akhir_asuransi: item.tanggal_akhir_asuransi || '' });
    } else {
      setEditingId(null);
      setFormData({ ...emptyForm });
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
        kode_barang: formData.kode_barang,
        id_master_barang: Number(formData.id_master_barang),
        status: formData.status,
        harga_satuan: Number(formData.harga_satuan) || 0 };
      if (formData.kode_inventaris) payload.kode_inventaris = formData.kode_inventaris;
      if (formData.id_pengadaan) payload.id_pengadaan = Number(formData.id_pengadaan);
      if (formData.id_kondisi) payload.id_kondisi = Number(formData.id_kondisi);
      if (formData.id_jurusan) payload.id_jurusan = Number(formData.id_jurusan);
      if (formData.id_ruang) payload.id_ruang = Number(formData.id_ruang);
      if (formData.id_lokasi) payload.id_lokasi = Number(formData.id_lokasi);
      if (formData.penanggung_jawab) payload.penanggung_jawab = formData.penanggung_jawab;
      if (formData.no_seri) payload.no_seri = formData.no_seri;
      if (formData.model_tipe) payload.model_tipe = formData.model_tipe;
      if (formData.barcode) payload.barcode = formData.barcode;
      if (formData.nilai_residu) payload.nilai_residu = Number(formData.nilai_residu);
      if (formData.umur_ekonomi) payload.umur_ekonomi = Number(formData.umur_ekonomi);
      if (formData.metode_penyusutan) payload.metode_penyusutan = formData.metode_penyusutan;
      if (formData.keterangan) payload.keterangan = formData.keterangan;
      if (formData.tanggal_garansi_mulai) payload.tanggal_garansi_mulai = formData.tanggal_garansi_mulai;
      if (formData.tanggal_garansi_akhir) payload.tanggal_garansi_akhir = formData.tanggal_garansi_akhir;
      if (formData.info_garansi) payload.info_garansi = formData.info_garansi;
      if (formData.jadwal_pemeliharaan_berikutnya) payload.jadwal_pemeliharaan_berikutnya = formData.jadwal_pemeliharaan_berikutnya;
      if (formData.nomor_polis_asuransi) payload.nomor_polis_asuransi = formData.nomor_polis_asuransi;
      if (formData.nilai_pertanggungan) payload.nilai_pertanggungan = Number(formData.nilai_pertanggungan);
      if (formData.tanggal_akhir_asuransi) payload.tanggal_akhir_asuransi = formData.tanggal_akhir_asuransi;

      if (editingId) {
        await api.put(`/aset/${editingId}`, payload);
      } else {
        await api.post('/aset', payload);
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
    if (window.confirm('Apakah Anda yakin ingin menghapus aset ini?')) {
      try {
        await api.delete(`/aset/${encodeURIComponent(id)}`);
        toast.success('Data berhasil dihapus');
        fetchData();
      } catch (error) {
        console.error('Gagal menghapus data', error);
        toast.error('Gagal menghapus data. Aset mungkin sedang digunakan.');
      }
    }
  };

  const inputClass = "w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white disabled:bg-gray-100 disabled:text-gray-500";

  return (
    <div className="space-y-6 max-w-7xl mx-auto">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Data Aset</h1>
          <p className="text-sm text-gray-500 mt-1">Kelola data inventaris aset aktif sekolah.</p>
        </div>
        <button onClick={() => openModal()} className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-gray-900 text-white hover:bg-gray-800 h-10 px-4 py-2 cursor-pointer">
          <Plus className="mr-2 h-4 w-4" />
          Tambah Aset
        </button>
      </div>

      <div className="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div className="relative w-full sm:max-w-md">
          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search className="h-4 w-4 text-gray-400" />
          </div>
          <input
            type="text"
            placeholder="Cari aset berdasarkan kode, seri, barcode, status..."
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
                <th className="px-4 py-3 font-medium">No.</th>
                <th className="px-4 py-3 font-medium">Kode Inventaris</th>
                <th className="px-4 py-3 font-medium">Nama Barang</th>
                <th className="px-4 py-3 font-medium">Kondisi</th>
                <th className="px-4 py-3 font-medium">Penanggung Jawab</th>
                <th className="px-4 py-3 font-medium">Ruang</th>
                <th className="px-4 py-3 font-medium">Status</th>
                <th className="px-4 py-3 font-medium text-right">Aksi</th>
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
                    {searchQuery ? 'Tidak ada data yang cocok dengan pencarian Anda.' : 'Belum ada data aset.'}
                  </td>
                </tr>
              ) : (
                data.map((item, index) => (
                  <tr key={item.kode_barang || index} className="hover:bg-gray-50 transition-colors">
                    <td className="px-4 py-3 text-gray-500">{index + 1}</td>
                    <td className="px-4 py-3 font-medium text-gray-900">{item.kode_inventaris || '-'}</td>
                    <td className="px-4 py-3 text-gray-900 font-medium">{item.master_barang?.nama_barang || '-'}</td>
                    <td className="px-4 py-3 text-gray-500">{item.kondisi?.nama_kondisi || '-'}</td>
                    <td className="px-4 py-3 text-gray-500">{item.penanggung_jawab || '-'}</td>
                    <td className="px-4 py-3 text-gray-500">{item.ruang?.nama_ruang || '-'}</td>
                    <td className="px-4 py-3">
                      <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium uppercase tracking-wider ${
                        item.status === 'Aktif' ? 'bg-green-100 text-green-800' :
                        item.status === 'Dipinjam' ? 'bg-blue-100 text-blue-800' :
                        item.status === 'Nonaktif' ? 'bg-red-100 text-red-800' :
                        'bg-yellow-100 text-yellow-800'
                      }`}>
                        {item.status || 'Di Gudang'}
                      </span>
                    </td>
                    <td className="px-4 py-3 text-right">
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
          <span className="text-sm text-gray-500">Halaman {currentPage} dari {lastPage}</span>
          <div className="flex gap-2">
            <button onClick={() => fetchData(currentPage - 1)} disabled={currentPage === 1} className="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">Sebelumnya</button>
            <button onClick={() => fetchData(currentPage + 1)} disabled={currentPage === lastPage} className="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">Selanjutnya &rarr;</button>
          </div>
        </div>
      </div>

      {/* Modal Detail */}
      {isDetailModalOpen && selectedItem && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4 py-8">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
              <h3 className="text-lg font-semibold text-gray-900">Detail Aset</h3>
              <button onClick={closeDetailModal} className="text-gray-400 hover:text-gray-500"><X className="h-5 w-5" /></button>
            </div>
            <div className="p-6 space-y-6 text-sm overflow-y-auto">
              {/* Informasi Dasar */}
              <div>
                <h4 className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Informasi Dasar</h4>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  {[
                    ['Kode Barang', selectedItem.kode_barang],
                    ['Kode Inventaris', selectedItem.kode_inventaris],
                    ['Barcode', selectedItem.barcode],
                    ['No Seri', selectedItem.no_seri],
                    ['Model/Tipe', selectedItem.model_tipe],
                    ['Status', selectedItem.status],
                    ['Kondisi', selectedItem.kondisi?.nama_kondisi],
                    ['Nama Barang', selectedItem.master_barang?.nama_barang],
                    ['Penanggung Jawab', selectedItem.penanggung_jawab],
                  ].map(([label, value]) => (
                    <div key={label}>
                      <label className="block text-xs font-medium text-gray-500 mb-1">{label}</label>
                      <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{value || '-'}</div>
                    </div>
                  ))}
                </div>
              </div>

              {/* Lokasi & Penempatan */}
              <div>
                <h4 className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Lokasi & Penempatan</h4>
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                  {[
                    ['Lokasi', selectedItem.lokasi?.nama_lokasi],
                    ['Ruang', selectedItem.ruang?.nama_ruang],
                    ['Jurusan', selectedItem.jurusan?.nama_jurusan],
                  ].map(([label, value]) => (
                    <div key={label}>
                      <label className="block text-xs font-medium text-gray-500 mb-1">{label}</label>
                      <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{value || '-'}</div>
                    </div>
                  ))}
                </div>
              </div>

              {/* Nilai & Keuangan */}
              <div>
                <h4 className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Nilai & Keuangan</h4>
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                  {[
                    ['Harga Satuan', formatRupiah(selectedItem.harga_satuan || 0)],
                    ['Nilai Residu', formatRupiah(selectedItem.nilai_residu || 0)],
                    ['Umur Ekonomi', selectedItem.umur_ekonomi ? `${selectedItem.umur_ekonomi} Tahun` : '-'],
                    ['Metode Penyusutan', selectedItem.metode_penyusutan],
                    ['ID Pengadaan', selectedItem.id_pengadaan ? `#${selectedItem.id_pengadaan}` : '-'],
                  ].map(([label, value]) => (
                    <div key={label}>
                      <label className="block text-xs font-medium text-gray-500 mb-1">{label}</label>
                      <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{value || '-'}</div>
                    </div>
                  ))}
                </div>
              </div>

              {/* Garansi */}
              <div>
                <h4 className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Garansi</h4>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  {[
                    ['Tanggal Mulai', formatDate(selectedItem.tanggal_garansi_mulai)],
                    ['Tanggal Akhir', formatDate(selectedItem.tanggal_garansi_akhir)],
                    ['Info Garansi', selectedItem.info_garansi],
                  ].map(([label, value]) => (
                    <div key={label}>
                      <label className="block text-xs font-medium text-gray-500 mb-1">{label}</label>
                      <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{value || '-'}</div>
                    </div>
                  ))}
                </div>
              </div>

              {/* Pemeliharaan & Asuransi */}
              <div>
                <h4 className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Pemeliharaan & Asuransi</h4>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  {[
                    ['Jadwal Pemeliharaan Berikutnya', formatDate(selectedItem.jadwal_pemeliharaan_berikutnya)],
                    ['No Polis Asuransi', selectedItem.nomor_polis_asuransi],
                    ['Nilai Pertanggungan', selectedItem.nilai_pertanggungan ? formatRupiah(selectedItem.nilai_pertanggungan) : '-'],
                    ['Tanggal Akhir Asuransi', formatDate(selectedItem.tanggal_akhir_asuransi)],
                  ].map(([label, value]) => (
                    <div key={label}>
                      <label className="block text-xs font-medium text-gray-500 mb-1">{label}</label>
                      <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{value || '-'}</div>
                    </div>
                  ))}
                </div>
              </div>

              {/* Keterangan */}
              {selectedItem.keterangan && (
                <div>
                  <h4 className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Keterangan</h4>
                  <div className="text-gray-900 bg-gray-50 p-3 rounded-md border border-gray-100 whitespace-pre-wrap">{selectedItem.keterangan}</div>
                </div>
              )}
            </div>
            <div className="px-6 py-4 border-t border-gray-100 flex justify-end shrink-0 bg-gray-50">
              <button type="button" onClick={closeDetailModal} className="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 font-medium transition-colors cursor-pointer">Tutup</button>
            </div>
          </div>
        </div>
      )}

      {/* Modal Form */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4 py-8">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-3xl max-h-full flex flex-col">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
              <h3 className="text-lg font-semibold text-gray-900">{editingId ? 'Edit Aset' : 'Tambah Aset'}</h3>
              <button onClick={closeModal} className="text-gray-400 hover:text-gray-500"><X className="h-5 w-5" /></button>
            </div>
            <div className="overflow-y-auto p-6">
              <form id="asetForm" onSubmit={handleSubmit} className="space-y-6 text-sm">
                {/* Section: Informasi Dasar */}
                <div>
                  <h4 className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Informasi Dasar</h4>
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Kode Barang *</label>
                      <input type="text" required disabled={!!editingId} value={formData.kode_barang} onChange={(e) => setFormData({ ...formData, kode_barang: e.target.value })} className={inputClass} placeholder="ASET-2026-001" />
                    </div>
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Kode Inventaris</label>
                      <input type="text" disabled={!!editingId} value={formData.kode_inventaris} onChange={(e) => setFormData({ ...formData, kode_inventaris: e.target.value })} className={inputClass} placeholder="INV-2026-001" />
                    </div>
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Barcode</label>
                      <input type="text" value={formData.barcode} onChange={(e) => setFormData({ ...formData, barcode: e.target.value })} className={inputClass} placeholder="Barcode item" />
                    </div>
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Nomor Seri</label>
                      <input type="text" value={formData.no_seri} onChange={(e) => setFormData({ ...formData, no_seri: e.target.value })} className={inputClass} placeholder="SN-823901" />
                    </div>
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Model/Tipe</label>
                      <input type="text" value={formData.model_tipe} onChange={(e) => setFormData({ ...formData, model_tipe: e.target.value })} className={inputClass} placeholder="Model atau tipe barang" />
                    </div>
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Barang *</label>
                      <select required value={formData.id_master_barang} onChange={(e) => setFormData({ ...formData, id_master_barang: e.target.value })} className={inputClass}>
                        <option value="" disabled>Pilih Barang</option>
                        {masterBarangList.map((mb) => (
                          <option key={mb.id_master_barang || mb.id} value={mb.id_master_barang || mb.id}>{mb.nama_barang}</option>
                        ))}
                      </select>
                    </div>
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Status *</label>
                      <select required value={formData.status} onChange={(e) => setFormData({ ...formData, status: e.target.value })} className={inputClass}>
                        <option value="Di Gudang">Di Gudang</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Dipinjam">Dipinjam</option>
                        <option value="Nonaktif">Nonaktif</option>
                      </select>
                    </div>
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Kondisi</label>
                      <select value={formData.id_kondisi} onChange={(e) => setFormData({ ...formData, id_kondisi: e.target.value })} className={inputClass}>
                        <option value="">Pilih Kondisi</option>
                        {kondisiList.map((k) => (
                          <option key={k.id_kondisi || k.id} value={k.id_kondisi || k.id}>{k.nama_kondisi || k.nama}</option>
                        ))}
                      </select>
                    </div>
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Penanggung Jawab</label>
                      <input type="text" value={formData.penanggung_jawab} onChange={(e) => setFormData({ ...formData, penanggung_jawab: e.target.value })} className={inputClass} placeholder="Nama penanggung jawab" />
                    </div>
                  </div>
                </div>

                {/* Section: Lokasi & Penempatan */}
                <div>
                  <h4 className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Lokasi & Penempatan</h4>
                  <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Lokasi</label>
                      <select value={formData.id_lokasi} onChange={(e) => setFormData({ ...formData, id_lokasi: e.target.value })} className={inputClass}>
                        <option value="">Pilih Lokasi</option>
                        {lokasiList.map((l) => (
                          <option key={l.id_lokasi || l.id} value={l.id_lokasi || l.id}>{l.nama_lokasi}</option>
                        ))}
                      </select>
                    </div>
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Ruang</label>
                      <select value={formData.id_ruang} onChange={(e) => setFormData({ ...formData, id_ruang: e.target.value })} className={inputClass}>
                        <option value="">Pilih Ruang</option>
                        {ruangList.map((r) => (
                          <option key={r.id_ruang || r.id} value={r.id_ruang || r.id}>{r.nama_ruang}</option>
                        ))}
                      </select>
                    </div>
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Jurusan</label>
                      <select value={formData.id_jurusan} onChange={(e) => setFormData({ ...formData, id_jurusan: e.target.value })} className={inputClass}>
                        <option value="">Pilih Jurusan</option>
                        {jurusanList.map((j) => (
                          <option key={j.id_jurusan || j.id} value={j.id_jurusan || j.id}>{j.nama_jurusan || j.nama}</option>
                        ))}
                      </select>
                    </div>
                  </div>
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Pengadaan (Opsional)</label>
                      <select value={formData.id_pengadaan} onChange={(e) => setFormData({ ...formData, id_pengadaan: e.target.value })} className={inputClass}>
                        <option value="">Bukan dari Pengadaan</option>
                        {pengadaanList.map((p) => (
                          <option key={p.id_pengadaan || p.id} value={p.id_pengadaan || p.id}>Pengadaan #{p.id_pengadaan || p.id}</option>
                        ))}
                      </select>
                    </div>
                  </div>
                </div>

                {/* Section: Nilai & Keuangan */}
                <div>
                  <h4 className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Nilai & Keuangan</h4>
                  <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Harga Satuan (Rp) *</label>
                      <CurrencyInput value={formData.harga_satuan} onChange={(val) => setFormData({ ...formData, harga_satuan: val })} className={inputClass} placeholder="0" required />
                    </div>
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Nilai Residu (Rp)</label>
                      <CurrencyInput value={formData.nilai_residu} onChange={(val) => setFormData({ ...formData, nilai_residu: val })} className={inputClass} placeholder="0" />
                    </div>
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Umur Ekonomi (Tahun)</label>
                      <input type="number" min="0" value={formData.umur_ekonomi} onChange={(e) => setFormData({ ...formData, umur_ekonomi: e.target.value })} className={inputClass} placeholder="5" />
                    </div>
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Metode Penyusutan</label>
                      <select value={formData.metode_penyusutan} onChange={(e) => setFormData({ ...formData, metode_penyusutan: e.target.value })} className={inputClass}>
                        <option value="">Pilih Metode</option>
                        <option value="Garis Lurus">Garis Lurus</option>
                        <option value="Saldo Menurun">Saldo Menurun</option>
                        <option value="Unit Produksi">Unit Produksi</option>
                        <option value="Lainnya">Lainnya</option>
                      </select>
                    </div>
                  </div>
                </div>

                {/* Section: Garansi */}
                <div>
                  <h4 className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Garansi</h4>
                  <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Tanggal Mulai Garansi</label>
                      <input type="date" value={formData.tanggal_garansi_mulai} onChange={(e) => setFormData({ ...formData, tanggal_garansi_mulai: e.target.value })} className={inputClass} />
                    </div>
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Tanggal Akhir Garansi</label>
                      <input type="date" value={formData.tanggal_garansi_akhir} onChange={(e) => setFormData({ ...formData, tanggal_garansi_akhir: e.target.value })} className={inputClass} />
                    </div>
                    <div className="sm:col-span-2 lg:col-span-1">
                      <label className="block font-medium text-gray-700 mb-1">Info Garansi</label>
                      <input type="text" value={formData.info_garansi} onChange={(e) => setFormData({ ...formData, info_garansi: e.target.value })} className={inputClass} placeholder="Keterangan garansi" />
                    </div>
                  </div>
                </div>

                {/* Section: Pemeliharaan & Asuransi */}
                <div>
                  <h4 className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Pemeliharaan & Asuransi</h4>
                  <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Jadwal Pemeliharaan</label>
                      <input type="date" value={formData.jadwal_pemeliharaan_berikutnya} onChange={(e) => setFormData({ ...formData, jadwal_pemeliharaan_berikutnya: e.target.value })} className={inputClass} />
                    </div>
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">No Polis Asuransi</label>
                      <input type="text" value={formData.nomor_polis_asuransi} onChange={(e) => setFormData({ ...formData, nomor_polis_asuransi: e.target.value })} className={inputClass} placeholder="Nomor polis" />
                    </div>
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Nilai Pertanggungan (Rp)</label>
                      <CurrencyInput value={formData.nilai_pertanggungan} onChange={(val) => setFormData({ ...formData, nilai_pertanggungan: val })} className={inputClass} placeholder="0" />
                    </div>
                    <div>
                      <label className="block font-medium text-gray-700 mb-1">Tanggal Akhir Asuransi</label>
                      <input type="date" value={formData.tanggal_akhir_asuransi} onChange={(e) => setFormData({ ...formData, tanggal_akhir_asuransi: e.target.value })} className={inputClass} />
                    </div>
                  </div>
                </div>

                {/* Section: Keterangan */}
                <div>
                  <label className="block font-medium text-gray-700 mb-1">Keterangan</label>
                  <textarea rows={3} value={formData.keterangan} onChange={(e) => setFormData({ ...formData, keterangan: e.target.value })} className={inputClass} placeholder="Catatan tambahan (opsional)" />
                </div>
              </form>
            </div>
            <div className="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50 shrink-0 rounded-b-xl">
              <button type="button" onClick={closeModal} className="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 font-medium transition-colors cursor-pointer">Batal</button>
              <button form="asetForm" type="submit" disabled={isSubmitting} className="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 font-medium transition-colors flex items-center disabled:opacity-50 cursor-pointer">
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
