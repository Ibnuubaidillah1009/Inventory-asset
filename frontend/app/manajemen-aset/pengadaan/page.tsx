'use client';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { extractData, formatDate } from '@/lib/utils';
import { Plus, Pencil, Trash2, X, Loader2, Search, Eye } from 'lucide-react';

import { toast } from 'sonner';
import DropdownMenu from '@/app/components/DropdownMenu';

export default function PengadaanPage() {
  const [data, setData] = useState<any[]>([]);
  const [pemasokList, setPemasokList] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDetailModalOpen, setIsDetailModalOpen] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedItem, setSelectedItem] = useState<any>(null);
  const [currentPage, setCurrentPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);

  const emptyForm = {
    nomor_po: '',
    nomor_faktur: '',
    tanggal_pengadaan: '',
    id_pemasok: '',
    total_harga: '',
    persentase_ppn: '',
    nominal_ppn: '',
    grand_total: '',
    keterangan: '',
    sumber_perolehan: '',
    tanggal_pengiriman: '',
    status: 'diproses' };

  const [formData, setFormData] = useState(emptyForm);

  const fetchData = async (page = 1, search = searchQuery) => {
    setLoading(true);
    try {
      const [resPengadaan, resPemasok] = await Promise.all([
        api.get(`/pengadaan?page=${page}&search=${encodeURIComponent(search)}`),
        api.get('/pemasok').catch(() => ({ data: { data: [] } })),
      ]);

      setData(extractData(resPengadaan.data.data));
      if (resPengadaan.data.meta) {
        setCurrentPage(resPengadaan.data.meta.current_page);
        setLastPage(resPengadaan.data.meta.last_page);
      }
      setPemasokList(extractData(resPemasok.data.data));
    } catch (error) {
      console.error('Gagal mengambil data pengadaan', error);
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
      setEditingId(item.id_pengadaan || item.id);
      const matchedPem = pemasokList.find((pem: any) => (pem.id_pemasok || pem.id) == item.id_pemasok);
      setFormData({
        nomor_po: item.nomor_po || '',
        nomor_faktur: item.nomor_faktur || '',
        tanggal_pengadaan: item.tanggal_pengadaan ? item.tanggal_pengadaan.split('T')[0] : '',
        id_pemasok: matchedPem ? String(matchedPem.id_pemasok || matchedPem.id) : (item.id_pemasok || ''),
        total_harga: item.total_harga || '',
        persentase_ppn: item.persentase_ppn || '',
        nominal_ppn: item.nominal_ppn || '',
        grand_total: item.grand_total || '',
        keterangan: item.keterangan || '',
        sumber_perolehan: item.sumber_perolehan || '',
        tanggal_pengiriman: item.tanggal_pengiriman || '',
        status: item.status || 'diproses' });
    } else {
      setEditingId(null);
      const today = new Date().toISOString().split('T')[0];
      setFormData({ ...emptyForm, tanggal_pengadaan: today });
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

  const handleAutoCalcPPN = (field: string, value: string) => {
    const updated = { ...formData, [field]: value };
    const total = Number(updated.total_harga) || 0;
    const ppnRate = Number(updated.persentase_ppn) || 0;

    if (field === 'total_harga' || field === 'persentase_ppn') {
      const ppn = total * (ppnRate / 100);
      updated.nominal_ppn = ppn > 0 ? String(Math.round(ppn)) : '';
      updated.grand_total = total > 0 ? String(Math.round(total + ppn)) : '';
    }
    setFormData(updated);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    try {
      const matchedPemasok = pemasokList.find((pem: any) => (pem.id_pemasok || pem.id) == formData.id_pemasok);
      const pemasokId = matchedPemasok ? (matchedPemasok.id_pemasok || matchedPemasok.id) : (formData.id_pemasok ? Number(formData.id_pemasok) : null);

      const payload: any = {
        tanggal_pengadaan: formData.tanggal_pengadaan,
        status: formData.status };
      if (formData.nomor_po) payload.nomor_po = formData.nomor_po;
      if (formData.nomor_faktur) payload.nomor_faktur = formData.nomor_faktur;
      if (pemasokId) payload.id_pemasok = pemasokId;
      if (formData.total_harga) payload.total_harga = Number(formData.total_harga);
      if (formData.persentase_ppn) payload.persentase_ppn = Number(formData.persentase_ppn);
      if (formData.nominal_ppn) payload.nominal_ppn = Number(formData.nominal_ppn);
      if (formData.grand_total) payload.grand_total = Number(formData.grand_total);
      if (formData.keterangan) payload.keterangan = formData.keterangan;
      if (formData.sumber_perolehan) payload.sumber_perolehan = formData.sumber_perolehan;
      if (formData.tanggal_pengiriman) payload.tanggal_pengiriman = formData.tanggal_pengiriman;

      if (editingId) {
        await api.put(`/pengadaan/${editingId}`, payload);
      } else {
        payload.detail = [];
        await api.post('/pengadaan', payload);
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
    if (window.confirm('Apakah Anda yakin ingin menghapus pengadaan ini?')) {
      try {
        await api.delete(`/pengadaan/${id}`);
        toast.success('Data berhasil dihapus');
        fetchData();
      } catch (error) {
        console.error('Gagal menghapus data', error);
        toast.error('Gagal menghapus data. Pengadaan mungkin sedang digunakan.');
      }
    }
  };

  const formatRupiah = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0 }).format(value);
  };

  const inputClass = "w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white";

  return (
    <div className="space-y-6 max-w-7xl mx-auto">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Data Pengadaan</h1>
          <p className="text-sm text-gray-500 mt-1">Kelola transaksi pengadaan barang inventaris sekolah.</p>
        </div>
        <button onClick={() => openModal()} className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-gray-950 text-white hover:bg-gray-800 h-10 px-4 py-2 cursor-pointer">
          <Plus className="mr-2 h-4 w-4" />
          Tambah Pengadaan
        </button>
      </div>

      <div className="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div className="relative w-full sm:max-w-md">
          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search className="h-4 w-4 text-gray-400" />
          </div>
          <input
            type="text"
            placeholder="Cari berdasarkan nomor PO, faktur, status..."
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
                <th className="px-4 py-3 font-medium">Nomor PO</th>
                <th className="px-4 py-3 font-medium">Tanggal</th>
                <th className="px-4 py-3 font-medium">Pemasok</th>
                <th className="px-4 py-3 font-medium">Total Harga</th>
                <th className="px-4 py-3 font-medium">PPN</th>
                <th className="px-4 py-3 font-medium">Grand Total</th>
                <th className="px-4 py-3 font-medium">Status</th>
                <th className="px-4 py-3 font-medium text-right">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {loading ? (
                <tr>
                  <td colSpan={9} className="px-6 py-12 text-center">
                    <Loader2 className="h-6 w-6 animate-spin mx-auto text-gray-400" />
                  </td>
                </tr>
              ) : data.length === 0 ? (
                <tr>
                  <td colSpan={9} className="px-6 py-12 text-center text-gray-500">
                    {searchQuery ? 'Tidak ada data yang cocok dengan pencarian Anda.' : 'Belum ada data pengadaan.'}
                  </td>
                </tr>
              ) : (
                data.map((item, index) => (
                  <tr key={index} className="hover:bg-gray-50 transition-colors">
                    <td className="px-4 py-3 text-gray-500">{index + 1}</td>
                    <td className="px-4 py-3 font-medium text-gray-900">{item.nomor_po || `#${item.id_pengadaan}`}</td>
                    <td className="px-4 py-3 text-gray-900">{formatDate(item.tanggal_pengadaan)}</td>
                    <td className="px-4 py-3 text-gray-500">{item.pemasok?.nama_pemasok || '-'}</td>
                    <td className="px-4 py-3 text-gray-900 font-medium">{formatRupiah(item.total_harga || 0)}</td>
                    <td className="px-4 py-3 text-gray-500">{item.nominal_ppn ? formatRupiah(item.nominal_ppn) : '-'}</td>
                    <td className="px-4 py-3 text-gray-900 font-semibold">{item.grand_total ? formatRupiah(item.grand_total) : '-'}</td>
                    <td className="px-4 py-3">
                      <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium uppercase tracking-wider ${
                        item.status === 'selesai' ? 'bg-green-100 text-green-800' :
                        item.status === 'dibelanjakan' ? 'bg-blue-100 text-blue-800' :
                        'bg-yellow-100 text-yellow-800'
                      }`}>
                        {item.status || 'diproses'}
                      </span>
                    </td>
                    <td className="px-4 py-3 text-right">
                                          <DropdownMenu actions={[
                      { label: 'Lihat Detail', icon: Eye, onClick: () => openDetailModal(item), variant: 'default' },
                      { label: 'Ubah', icon: Pencil, onClick: () => openModal(item), variant: 'default' },
                      { label: 'Hapus', icon: Trash2, onClick: () => handleDelete(item.id_pengadaan || item.id), variant: 'danger' },
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
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
              <h3 className="text-lg font-semibold text-gray-900">Detail Pengadaan</h3>
              <button onClick={closeDetailModal} className="text-gray-400 hover:text-gray-500"><X className="h-5 w-5" /></button>
            </div>
            <div className="p-6 space-y-4 text-sm overflow-y-auto">
              {[
                ['Nomor PO', selectedItem.nomor_po],
                ['Nomor Faktur', selectedItem.nomor_faktur],
                ['Tanggal Pengadaan', formatDate(selectedItem.tanggal_pengadaan)],
                ['Pemasok', selectedItem.pemasok?.nama_pemasok],
                ['Sumber Perolehan', selectedItem.sumber_perolehan],
                ['Tanggal Pengiriman', selectedItem.tanggal_pengiriman],
                ['Total Harga', selectedItem.total_harga ? formatRupiah(selectedItem.total_harga) : '-'],
                ['Persentase PPN', selectedItem.persentase_ppn ? `${selectedItem.persentase_ppn}%` : '-'],
                ['Nominal PPN', selectedItem.nominal_ppn ? formatRupiah(selectedItem.nominal_ppn) : '-'],
                ['Grand Total', selectedItem.grand_total ? formatRupiah(selectedItem.grand_total) : '-'],
                ['Keterangan', selectedItem.keterangan],
                ['Status', selectedItem.status],
              ].map(([label, value]) => (
                <div key={label}>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">{label}</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{value || '-'}</div>
                </div>
              ))}
              <div className="pt-4 flex justify-end">
                <button type="button" onClick={closeDetailModal} className="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 font-medium transition-colors cursor-pointer">Tutup</button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Modal Form */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4 py-8">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-full flex flex-col">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
              <h3 className="text-lg font-semibold text-gray-900">{editingId ? 'Edit Pengadaan' : 'Tambah Pengadaan'}</h3>
              <button onClick={closeModal} className="text-gray-400 hover:text-gray-500"><X className="h-5 w-5" /></button>
            </div>
            <div className="overflow-y-auto p-6">
              <form onSubmit={handleSubmit} className="space-y-4 text-sm">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Nomor PO</label>
                    <input type="text" value={formData.nomor_po} onChange={(e) => setFormData({ ...formData, nomor_po: e.target.value })} className={inputClass} placeholder="PO-2026-001" />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Nomor Faktur</label>
                    <input type="text" value={formData.nomor_faktur} onChange={(e) => setFormData({ ...formData, nomor_faktur: e.target.value })} className={inputClass} placeholder="Faktur nomor" />
                  </div>
                </div>

                <div>
                  <label className="block font-medium text-gray-700 mb-1">Tanggal Pengadaan *</label>
                  <input type="date" required value={formData.tanggal_pengadaan} onChange={(e) => setFormData({ ...formData, tanggal_pengadaan: e.target.value })} className={inputClass} />
                </div>

                <div>
                  <label className="block font-medium text-gray-700 mb-1">Pemasok</label>
                  <input
                    type="text"
                    value={pemasokList.find((pem: any) => (pem.id_pemasok || pem.id) == formData.id_pemasok)?.nama_pemasok || formData.id_pemasok}
                    onChange={(e) => setFormData({ ...formData, id_pemasok: e.target.value })}
                    onFocus={(e) => e.target.select()}
                    className={inputClass}
                    placeholder="Ketik nama pemasok..."
                    list="pemasok-list-aset"
                    autoComplete="off"
                  />
                  <datalist id="pemasok-list-aset">
                    {pemasokList.map((pem: any) => (
                      <option key={pem.id_pemasok || pem.id} value={pem.nama_pemasok} data-id={pem.id_pemasok || pem.id} />
                    ))}
                  </datalist>
                  {formData.id_pemasok && pemasokList.find((pem: any) => (pem.id_pemasok || pem.id) == formData.id_pemasok) && (
                    <button type="button" onClick={() => setFormData({ ...formData, id_pemasok: '' })} className="text-xs text-gray-400 hover:text-red-500 mt-1 cursor-pointer">Hapus pilihan</button>
                  )}
                </div>

                <div>
                  <label className="block font-medium text-gray-700 mb-1">Sumber Perolehan</label>
                  <input type="text" value={formData.sumber_perolehan} onChange={(e) => setFormData({ ...formData, sumber_perolehan: e.target.value })} className={inputClass} placeholder="APBD, BOS, dll" />
                </div>

                <div>
                  <label className="block font-medium text-gray-700 mb-1">Tanggal Pengiriman</label>
                  <input type="date" value={formData.tanggal_pengiriman} onChange={(e) => setFormData({ ...formData, tanggal_pengiriman: e.target.value })} className={inputClass} />
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Total Harga (Rp)</label>
                    <input type="number" min="0" value={formData.total_harga} onChange={(e) => handleAutoCalcPPN('total_harga', e.target.value)} className={inputClass} placeholder="1500000" />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">PPN (%)</label>
                    <input type="number" min="0" max="100" step="0.01" value={formData.persentase_ppn} onChange={(e) => handleAutoCalcPPN('persentase_ppn', e.target.value)} className={inputClass} placeholder="11" />
                  </div>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Nominal PPN (Rp)</label>
                    <input type="number" min="0" value={formData.nominal_ppn} onChange={(e) => setFormData({ ...formData, nominal_ppn: e.target.value })} className={inputClass} placeholder="Otomatis" />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Grand Total (Rp)</label>
                    <input type="number" min="0" value={formData.grand_total} onChange={(e) => setFormData({ ...formData, grand_total: e.target.value })} className={inputClass} placeholder="Otomatis" />
                  </div>
                </div>

                <div>
                  <label className="block font-medium text-gray-700 mb-1">Status</label>
                  <select required value={formData.status} onChange={(e) => setFormData({ ...formData, status: e.target.value })} className={inputClass}>
                    <option value="diproses">Diproses</option>
                    <option value="dibelanjakan">Dibelanjakan</option>
                    <option value="selesai">Selesai</option>
                  </select>
                </div>

                <div>
                  <label className="block font-medium text-gray-700 mb-1">Keterangan</label>
                  <textarea rows={3} value={formData.keterangan} onChange={(e) => setFormData({ ...formData, keterangan: e.target.value })} className={inputClass} placeholder="Catatan tambahan (opsional)" />
                </div>

                <div className="pt-4 flex justify-end gap-3">
                  <button type="button" onClick={closeModal} className="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 font-medium transition-colors cursor-pointer">Batal</button>
                  <button type="submit" disabled={isSubmitting} className="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 font-medium transition-colors flex items-center disabled:opacity-50 cursor-pointer">
                    {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                    Simpan
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
