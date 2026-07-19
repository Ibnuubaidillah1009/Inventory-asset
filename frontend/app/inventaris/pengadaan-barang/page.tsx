'use client';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { extractData, formatDate } from '@/lib/utils';
import { Plus, Pencil, Trash2, X, Loader2, Search, Eye } from 'lucide-react';

import { toast } from 'sonner';
import DropdownMenu from '@/app/components/DropdownMenu';

export default function PengadaanBarangPage() {
  const [data, setData] = useState<any[]>([]);
  const [masterBarangList, setMasterBarangList] = useState<any[]>([]);
  const [gudangList, setGudangList] = useState<any[]>([]);
  const [pemasokList, setPemasokList] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDetailModalOpen, setIsDetailModalOpen] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedItem, setSelectedItem] = useState<any>(null);

  const [formData, setFormData] = useState({
    tanggal_pengadaan: '',
    id_pemasok: '',
    total_harga: '',
    keterangan: '',
    kode_gudang: '',
    jumlah_pengadaan: '',
    sumber_perolehan: '',
    status: 'diproses' });

  const [detailItems, setDetailItems] = useState<Array<{
    id_master_barang: string;
    jumlah_masuk: string;
    harga_satuan: string;
  }>>([{ id_master_barang: '', jumlah_masuk: '', harga_satuan: '' }]);

  const fetchData = async () => {
    setLoading(true);
    try {
      const [resPengadaan, resMaster, resGudang, resPemasok] = await Promise.all([
        api.get('/pengadaan').catch(() => ({ data: { data: [] } })),
        api.get('/master-barang').catch(() => ({ data: { data: [] } })),
        api.get('/gudang').catch(() => ({ data: { data: [] } })),
        api.get('/pemasok').catch(() => ({ data: { data: [] } })),
      ]);

      setData(extractData(resPengadaan.data.data));
      setMasterBarangList(extractData(resMaster.data.data));
      setGudangList(extractData(resGudang.data.data));
      setPemasokList(extractData(resPemasok.data.data));
    } catch (error) {
      console.error('Gagal mengambil data pengadaan', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  useEffect(() => {
    const delayDebounceFn = setTimeout(() => {
      if (searchQuery) {
        const filtered = data.filter((item: any) =>
          item.id_pengadaan?.toString().includes(searchQuery) ||
          item.tanggal_pengadaan?.includes(searchQuery) ||
          item.status?.toLowerCase().includes(searchQuery.toLowerCase()) ||
          item.keterangan?.toLowerCase().includes(searchQuery.toLowerCase())
        );
        setData(filtered);
      } else {
        fetchData();
      }
    }, 500);
    return () => clearTimeout(delayDebounceFn);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [searchQuery]);

  const openModal = (item: any = null) => {
    if (item) {
      setEditingId(item.id_pengadaan);
      const matchedPem = pemasokList.find((p: any) => (p.id_pemasok || p.id) == item.id_pemasok);
      setFormData({
        tanggal_pengadaan: item.tanggal_pengadaan || '',
        id_pemasok: matchedPem ? String(matchedPem.id_pemasok || matchedPem.id) : (item.id_pemasok || ''),
        total_harga: item.total_harga || '',
        keterangan: item.keterangan || '',
        kode_gudang: item.kode_gudang || '',
        jumlah_pengadaan: item.jumlah_pengadaan || '',
        sumber_perolehan: item.sumber_perolehan || '',
        status: item.status || 'diproses' });
      setDetailItems(
        item.detail_pengadaan?.map((d: any) => ({
          id_master_barang: d.id_master_barang?.toString() || '',
          jumlah_masuk: d.jumlah_masuk?.toString() || '',
          harga_satuan: d.harga_satuan?.toString() || '' })) || [{ id_master_barang: '', jumlah_masuk: '', harga_satuan: '' }]
      );
    } else {
      setEditingId(null);
      setFormData({
        tanggal_pengadaan: '',
        id_pemasok: '',
        total_harga: '',
        keterangan: '',
        kode_gudang: '',
        jumlah_pengadaan: '',
        sumber_perolehan: '',
        status: 'diproses' });
      setDetailItems([{ id_master_barang: '', jumlah_masuk: '', harga_satuan: '' }]);
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

  const addDetailItem = () => {
    setDetailItems([...detailItems, { id_master_barang: '', jumlah_masuk: '', harga_satuan: '' }]);
  };

  const removeDetailItem = (index: number) => {
    if (detailItems.length > 1) {
      setDetailItems(detailItems.filter((_, i) => i !== index));
    }
  };

  const updateDetailItem = (index: number, field: string, value: string) => {
    const updated = [...detailItems];
    updated[index] = { ...updated[index], [field]: value };
    setDetailItems(updated);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    try {
      const validDetails = detailItems.filter(d => d.id_master_barang && d.jumlah_masuk);
      if (validDetails.length === 0) {
        toast.error('Minimal harus ada 1 detail barang.');
        setIsSubmitting(false);
        return;
      }

      const matchedPemasok = pemasokList.find((p: any) => (p.id_pemasok || p.id) == formData.id_pemasok);
      const pemasokId = matchedPemasok ? (matchedPemasok.id_pemasok || matchedPemasok.id) : (formData.id_pemasok ? Number(formData.id_pemasok) : null);

      const payload = {
        tanggal_pengadaan: formData.tanggal_pengadaan,
        id_pemasok: pemasokId,
        total_harga: formData.total_harga ? Number(formData.total_harga) : null,
        keterangan: formData.keterangan || null,
        kode_gudang: formData.kode_gudang || null,
        jumlah_pengadaan: formData.jumlah_pengadaan ? Number(formData.jumlah_pengadaan) : validDetails.reduce((sum, d) => sum + Number(d.jumlah_masuk), 0),
        sumber_perolehan: formData.sumber_perolehan || null,
        status: formData.status,
        detail: validDetails.map(d => ({
          id_master_barang: Number(d.id_master_barang),
          jumlah_masuk: Number(d.jumlah_masuk),
          harga_satuan: d.harga_satuan ? Number(d.harga_satuan) : null })) };

      if (editingId) {
        await api.put(`/pengadaan/${editingId}`, payload);
      } else {
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
        toast.error('Gagal menghapus data. Pengadaan mungkin sudah terkait dengan aset.');
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
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Pengadaan Barang</h1>
          <p className="text-sm text-gray-500 mt-1">Kelola pengadaan barang inventaris.</p>
        </div>
        <button
          onClick={() => openModal()}
          className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-gray-900 text-white hover:bg-gray-800 h-10 px-4 py-2 cursor-pointer"
        >
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
            placeholder="Cari pengadaan berdasarkan ID, tanggal, status..."
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
                <th className="px-6 py-4 font-medium">ID Pengadaan</th>
                <th className="px-6 py-4 font-medium">Tanggal</th>
                <th className="px-6 py-4 font-medium">Jumlah</th>
                <th className="px-6 py-4 font-medium">Total Harga</th>
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
                    {searchQuery ? 'Tidak ada data yang cocok dengan pencarian Anda.' : 'Belum ada data pengadaan.'}
                  </td>
                </tr>
              ) : (
                data.map((item: any, index: number) => (
                  <tr key={item.id_pengadaan || index} className="hover:bg-gray-50 transition-colors">
                    <td className="px-6 py-4 text-gray-500">{index + 1}</td>
                    <td className="px-6 py-4 font-medium text-gray-900">#{item.id_pengadaan}</td>
                    <td className="px-6 py-4 text-gray-500">{formatDate(item.tanggal_pengadaan)}</td>
                    <td className="px-6 py-4 text-gray-500">{item.jumlah_pengadaan || '-'} item</td>
                    <td className="px-6 py-4 text-gray-900">{formatRupiah(item.total_harga || 0)}</td>
                    <td className="px-6 py-4">
                      <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium uppercase tracking-wider ${
                        item.status === 'selesai' ? 'bg-green-100 text-green-800' :
                        item.status === 'dibelanjakan' ? 'bg-blue-100 text-blue-800' :
                        'bg-yellow-100 text-yellow-800'
                      }`}>
                        {item.status || '-'}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-right">
                                          <DropdownMenu actions={[
                      { label: 'Lihat Detail', icon: Eye, onClick: () => openDetailModal(item), variant: 'default' },
                      { label: 'Ubah', icon: Pencil, onClick: () => openModal(item), variant: 'default' },
                      { label: 'Hapus', icon: Trash2, onClick: () => handleDelete(item.id_pengadaan), variant: 'danger' },
                    ]} />
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>

      {isDetailModalOpen && selectedItem && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4 py-8">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
              <h3 className="text-lg font-semibold text-gray-900">Detail Pengadaan #{selectedItem.id_pengadaan}</h3>
              <button onClick={closeDetailModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>
            <div className="p-6 space-y-4 text-sm overflow-y-auto">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Tanggal Pengadaan</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{formatDate(selectedItem.tanggal_pengadaan)}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Status</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.status || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Jumlah Pengadaan</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.jumlah_pengadaan || '-'} item</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Total Harga</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{formatRupiah(selectedItem.total_harga || 0)}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Gudang</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.gudang?.nama_gudang || selectedItem.kode_gudang || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Pemasok</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.pemasok?.nama_pemasok || '-'}</div>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Sumber Perolehan</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100">{selectedItem.sumber_perolehan || '-'}</div>
                </div>
                <div className="sm:col-span-2">
                  <label className="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Keterangan</label>
                  <div className="text-gray-900 font-medium bg-gray-50 p-2 rounded-md border border-gray-100 whitespace-pre-wrap">{selectedItem.keterangan || '-'}</div>
                </div>
              </div>

              {selectedItem.detail_pengadaan && selectedItem.detail_pengadaan.length > 0 && (
                <div className="mt-4">
                  <label className="block text-xs font-medium text-gray-500 mb-2 uppercase tracking-wider">Detail Barang</label>
                  <table className="w-full text-sm text-left border border-gray-200 rounded-md overflow-hidden">
                    <thead className="text-xs text-gray-500 bg-gray-50 border-b border-gray-200">
                      <tr>
                        <th className="px-4 py-2 font-medium">Nama Barang</th>
                        <th className="px-4 py-2 font-medium">Jumlah</th>
                        <th className="px-4 py-2 font-medium">Harga Satuan</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-200">
                      {selectedItem.detail_pengadaan.map((d: any, i: number) => (
                        <tr key={i}>
                          <td className="px-4 py-2">{d.master_barang?.nama_barang || '-'}</td>
                          <td className="px-4 py-2">{d.jumlah_masuk}</td>
                          <td className="px-4 py-2">{formatRupiah(d.harga_satuan || 0)}</td>
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
          <div className="bg-white rounded-xl shadow-xl w-full max-w-3xl max-h-full flex flex-col">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
              <h3 className="text-lg font-semibold text-gray-900">
                {editingId ? `Edit Pengadaan #${editingId}` : 'Tambah Pengadaan'}
              </h3>
              <button onClick={closeModal} className="text-gray-400 hover:text-gray-500">
                <X className="h-5 w-5" />
              </button>
            </div>
            <div className="overflow-y-auto p-6">
              <form id="pengadaanForm" onSubmit={handleSubmit} className="space-y-4 text-sm">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Tanggal Pengadaan *</label>
                    <input
                      type="date"
                      required
                      value={formData.tanggal_pengadaan}
                      onChange={(e) => setFormData({ ...formData, tanggal_pengadaan: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                    />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Status</label>
                    <select
                      value={formData.status}
                      onChange={(e) => setFormData({ ...formData, status: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                    >
                      <option value="diproses">Diproses</option>
                      <option value="dibelanjakan">Dibelanjakan</option>
                      <option value="selesai">Selesai</option>
                    </select>
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Gudang</label>
                    <select
                      value={formData.kode_gudang}
                      onChange={(e) => setFormData({ ...formData, kode_gudang: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                    >
                      <option value="">Pilih Gudang</option>
                      {gudangList.map((g: any) => (
                        <option key={g.kode_gudang || g.id} value={g.kode_gudang || g.id}>
                          {g.nama_gudang || g.nama}
                        </option>
                      ))}
                    </select>
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Pemasok</label>
                    <input
                      type="text"
                      value={pemasokList.find((p: any) => (p.id_pemasok || p.id) == formData.id_pemasok)?.nama_pemasok || formData.id_pemasok}
                      onChange={(e) => {
                        const val = e.target.value;
                        setFormData({ ...formData, id_pemasok: val });
                      }}
                      onFocus={(e) => e.target.select()}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                      placeholder="Ketik nama pemasok..."
                      list="pemasok-list-pengadaan"
                      autoComplete="off"
                    />
                    <datalist id="pemasok-list-pengadaan">
                      {pemasokList.map((p: any) => (
                        <option key={p.id_pemasok || p.id} value={p.nama_pemasok || p.nama} data-id={p.id_pemasok || p.id} />
                      ))}
                    </datalist>
                    {formData.id_pemasok && pemasokList.find((p: any) => (p.id_pemasok || p.id) == formData.id_pemasok) && (
                      <button type="button" onClick={() => setFormData({ ...formData, id_pemasok: '' })} className="text-xs text-gray-400 hover:text-red-500 mt-1 cursor-pointer">Hapus pilihan</button>
                    )}
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Total Harga (Rp)</label>
                    <input
                      type="number"
                      min="0"
                      value={formData.total_harga}
                      onChange={(e) => setFormData({ ...formData, total_harga: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                      placeholder="Misal: 50000000"
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
                      <option value="BOS">BOS</option>
                      <option value="Hibah">Hibah</option>
                      <option value="Swadaya">Swadaya</option>
                      <option value="Lainnya">Lainnya</option>
                    </select>
                  </div>
                  <div className="sm:col-span-2">
                    <label className="block font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea
                      rows={2}
                      value={formData.keterangan}
                      onChange={(e) => setFormData({ ...formData, keterangan: e.target.value })}
                      className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                      placeholder="Catatan tambahan (opsional)"
                    />
                  </div>
                </div>

                <div className="border-t border-gray-200 pt-4">
                  <div className="flex justify-between items-center mb-3">
                    <label className="block font-medium text-gray-700">Detail Barang *</label>
                    <button
                      type="button"
                      onClick={addDetailItem}
                      className="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 cursor-pointer"
                    >
                      <Plus className="h-4 w-4 mr-1" /> Tambah Item
                    </button>
                  </div>
                  <div className="space-y-3">
                    {detailItems.map((item, index) => (
                      <div key={index} className="flex gap-3 items-start">
                        <select
                          required
                          value={item.id_master_barang}
                          onChange={(e) => updateDetailItem(index, 'id_master_barang', e.target.value)}
                          className="flex-1 rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white"
                        >
                          <option value="">Pilih Barang</option>
                          {masterBarangList.map((mb: any) => (
                            <option key={mb.id_master_barang || mb.id} value={mb.id_master_barang || mb.id}>
                              {mb.nama_barang}
                            </option>
                          ))}
                        </select>
                        <input
                          type="number"
                          required
                          min="1"
                          value={item.jumlah_masuk}
                          onChange={(e) => updateDetailItem(index, 'jumlah_masuk', e.target.value)}
                          className="w-24 rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                          placeholder="Jumlah"
                        />
                        <input
                          type="number"
                          min="0"
                          value={item.harga_satuan}
                          onChange={(e) => updateDetailItem(index, 'harga_satuan', e.target.value)}
                          className="w-32 rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                          placeholder="Harga"
                        />
                        {detailItems.length > 1 && (
                          <button
                            type="button"
                            onClick={() => removeDetailItem(index)}
                            className="text-gray-400 hover:text-red-600 p-2 cursor-pointer"
                          >
                            <Trash2 className="h-4 w-4" />
                          </button>
                        )}
                      </div>
                    ))}
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
                form="pengadaanForm"
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
