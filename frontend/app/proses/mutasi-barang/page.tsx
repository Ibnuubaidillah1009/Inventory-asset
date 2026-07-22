'use client';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { Plus, X, Loader2, Search, ArrowRightLeft, Pencil, Trash2, Eye } from 'lucide-react';
import { extractData, formatDate } from '@/lib/utils';

import { toast } from 'sonner';
import DropdownMenu from '@/app/components/DropdownMenu';

interface MutasiItem {
  id_mutasi: number;
  kode_inventaris: string;
  id_jurusan_asal: number | null;
  id_jurusan_tujuan: number;
  tanggal_mutasi: string;
  alasan_mutasi: string | null;
  jurusan_asal?: { id_jurusan: number; nama_jurusan: string } | null;
  jurusan_tujuan?: { id_jurusan: number; nama_jurusan: string } | null;
}

export default function MutasiBarangPage() {
  const [data, setData] = useState<MutasiItem[]>([]);
  const [asetList, setAsetList] = useState<any[]>([]);
  const [jurusanList, setJurusanList] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDetailOpen, setIsDetailOpen] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const [editingId, setEditingId] = useState<number | null>(null);
  const [detailItem, setDetailItem] = useState<MutasiItem | null>(null);

  const emptyForm = {
    kode_inventaris: '',
    id_jurusan_asal: '',
    id_jurusan_tujuan: '',
    tanggal_mutasi: '',
    alasan_mutasi: '',
  };
  const [formData, setFormData] = useState(emptyForm);

  const fetchData = async () => {
    setLoading(true);
    try {
      const [resMutasi, resAset, resJurusan] = await Promise.all([
        api.get('/mutasi').catch(() => ({ data: { data: [] } })),
        api.get('/aset').catch(() => ({ data: { data: [] } })),
        api.get('/jurusan').catch(() => ({ data: { data: [] } })),
      ]);
      setData(extractData(resMutasi.data.data));
      setAsetList(extractData(resAset.data.data));
      setJurusanList(extractData(resJurusan.data.data));
    } catch (error) {
      console.error('Gagal mengambil data mutasi', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchData(); }, []);

  const openAddModal = () => {
    setEditingId(null);
    setFormData({ ...emptyForm, tanggal_mutasi: new Date().toISOString().split('T')[0] });
    setIsModalOpen(true);
  };

  const openEditModal = (item: MutasiItem) => {
    setEditingId(item.id_mutasi);
    setFormData({
      kode_inventaris: item.kode_inventaris || '',
      id_jurusan_asal: item.id_jurusan_asal ? String(item.id_jurusan_asal) : '',
      id_jurusan_tujuan: item.id_jurusan_tujuan ? String(item.id_jurusan_tujuan) : '',
      tanggal_mutasi: item.tanggal_mutasi || '',
      alasan_mutasi: item.alasan_mutasi || '',
    });
    setIsModalOpen(true);
  };

  const openDetailModal = (item: MutasiItem) => {
    setDetailItem(item);
    setIsDetailOpen(true);
  };

  const closeModal = () => { setIsModalOpen(false); setEditingId(null); };
  const closeDetailModal = () => { setIsDetailOpen(false); setDetailItem(null); };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    try {
      const payload: any = {
        kode_inventaris: formData.kode_inventaris,
        id_jurusan_tujuan: Number(formData.id_jurusan_tujuan),
        tanggal_mutasi: formData.tanggal_mutasi,
      };
      if (formData.id_jurusan_asal) payload.id_jurusan_asal = Number(formData.id_jurusan_asal);
      if (formData.alasan_mutasi) payload.alasan_mutasi = formData.alasan_mutasi;

      if (editingId) {
        await api.put(`/mutasi/${editingId}`, payload);
      } else {
        await api.post('/mutasi', payload);
      }
      toast.success('Data berhasil disimpan');
      closeModal();
      fetchData();
    } catch (error) {
      console.error('Gagal menyimpan mutasi', error);
      alert(editingId ? 'Gagal memperbarui mutasi.' : 'Gagal menyimpan mutasi.');
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleDelete = async (item: MutasiItem) => {
    if (!confirm(`Hapus mutasi ${item.kode_inventaris}?`)) return;
    try {
      await api.delete(`/mutasi/${item.id_mutasi}`);
      toast.success('Data berhasil dihapus');
      fetchData();
    } catch (error) {
      console.error('Gagal menghapus mutasi', error);
      toast.error('Gagal menghapus mutasi.');
    }
  };

  const filtered = data.filter((item) =>
    !searchQuery ||
    item.kode_inventaris?.toLowerCase().includes(searchQuery.toLowerCase()) ||
    item.jurusan_asal?.nama_jurusan?.toLowerCase().includes(searchQuery.toLowerCase()) ||
    item.jurusan_tujuan?.nama_jurusan?.toLowerCase().includes(searchQuery.toLowerCase())
  );

  const inputClass = "w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white";

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight flex items-center gap-2"><ArrowRightLeft className="h-6 w-6" /> Mutasi Barang</h1>
          <p className="text-sm text-gray-500 mt-1">Proses mutasi aset antar jurusan/unit.</p>
        </div>
        <button onClick={openAddModal} className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-gray-900 text-white hover:bg-gray-800 h-10 px-4 py-2 cursor-pointer">
          <Plus className="mr-2 h-4 w-4" /> Tambah Mutasi
        </button>
      </div>

      <div className="relative w-full sm:max-w-md">
        <Search className="absolute left-3 top-2.5 h-4 w-4 text-gray-400" />
        <input type="text" placeholder="Cari berdasarkan kode inventaris, jurusan..." value={searchQuery} onChange={(e) => setSearchQuery(e.target.value)} className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 sm:text-sm" />
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm text-left">
            <thead className="text-xs text-gray-500 bg-gray-50 border-b border-gray-200 uppercase">
              <tr>
                <th className="px-6 py-4 font-medium">No.</th>
                <th className="px-6 py-4 font-medium">Kode Inventaris</th>
                <th className="px-6 py-4 font-medium">Jurusan Asal</th>
                <th className="px-6 py-4 font-medium">Jurusan Tujuan</th>
                <th className="px-6 py-4 font-medium">Tanggal</th>
                <th className="px-6 py-4 font-medium">Alasan</th>
                <th className="px-6 py-4 font-medium text-right">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {loading ? (
                <tr><td colSpan={7} className="px-6 py-12 text-center"><Loader2 className="h-6 w-6 animate-spin mx-auto text-gray-400" /></td></tr>
              ) : filtered.length === 0 ? (
                <tr><td colSpan={7} className="px-6 py-12 text-center text-gray-500">Belum ada data mutasi.</td></tr>
              ) : filtered.map((item, i) => (
                <tr key={item.id_mutasi || i} className="hover:bg-gray-50 transition-colors">
                  <td className="px-6 py-4 text-gray-500">{i + 1}</td>
                  <td className="px-6 py-4 font-medium text-gray-900">{item.kode_inventaris || '-'}</td>
                  <td className="px-6 py-4 text-gray-500">{item.jurusan_asal?.nama_jurusan || '-'}</td>
                  <td className="px-6 py-4 text-gray-900 font-medium">{item.jurusan_tujuan?.nama_jurusan || '-'}</td>
                  <td className="px-6 py-4 text-gray-500">{formatDate(item.tanggal_mutasi)}</td>
                  <td className="px-6 py-4 text-gray-500 max-w-xs truncate">{item.alasan_mutasi || '-'}</td>
                  <td className="px-6 py-4 text-right">
                    <DropdownMenu actions={[
                      { label: 'Lihat Detail', icon: Eye, onClick: () => openDetailModal(item), variant: 'default' },
                      { label: 'Ubah', icon: Pencil, onClick: () => openEditModal(item), variant: 'default' },
                      { label: 'Hapus', icon: Trash2, onClick: () => handleDelete(item), variant: 'danger' },
                    ]} />
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100">
              <h3 className="text-lg font-semibold text-gray-900">{editingId ? 'Edit Mutasi' : 'Tambah Mutasi'}</h3>
              <button onClick={closeModal} className="text-gray-400 hover:text-gray-500"><X className="h-5 w-5" /></button>
            </div>
            <form onSubmit={handleSubmit} className="p-6 space-y-4 text-sm">
              <div>
                <label className="block font-medium text-gray-700 mb-1">Kode Inventaris *</label>
                <select required value={formData.kode_inventaris} onChange={(e) => setFormData({ ...formData, kode_inventaris: e.target.value })} className={inputClass} disabled={!!editingId}>
                  <option value="" disabled>Pilih Aset</option>
                  {asetList.map((a) => <option key={a.kode_barang} value={a.kode_inventaris || a.kode_barang}>{a.kode_inventaris || a.kode_barang} - {a.master_barang?.nama_barang || ''}</option>)}
                </select>
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Jurusan Asal</label>
                <select value={formData.id_jurusan_asal} onChange={(e) => setFormData({ ...formData, id_jurusan_asal: e.target.value })} className={inputClass}>
                  <option value="">Pilih Jurusan Asal</option>
                  {jurusanList.map((j) => <option key={j.id_jurusan} value={j.id_jurusan}>{j.nama_jurusan}</option>)}
                </select>
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Jurusan Tujuan *</label>
                <select required value={formData.id_jurusan_tujuan} onChange={(e) => setFormData({ ...formData, id_jurusan_tujuan: e.target.value })} className={inputClass}>
                  <option value="" disabled>Pilih Jurusan Tujuan</option>
                  {jurusanList.map((j) => <option key={j.id_jurusan} value={j.id_jurusan}>{j.nama_jurusan}</option>)}
                </select>
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Tanggal Mutasi *</label>
                <input type="date" required value={formData.tanggal_mutasi} onChange={(e) => setFormData({ ...formData, tanggal_mutasi: e.target.value })} className={inputClass} />
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Alasan Mutasi</label>
                <textarea rows={3} value={formData.alasan_mutasi} onChange={(e) => setFormData({ ...formData, alasan_mutasi: e.target.value })} className={inputClass} placeholder="Alasan mutasi..." />
              </div>
              <div className="pt-4 flex justify-end gap-3">
                <button type="button" onClick={closeModal} className="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 font-medium cursor-pointer">Batal</button>
                <button type="submit" disabled={isSubmitting} className="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 font-medium flex items-center disabled:opacity-50 cursor-pointer">
                  {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />} {editingId ? 'Perbarui' : 'Simpan'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {isDetailOpen && detailItem && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100">
              <h3 className="text-lg font-semibold text-gray-900">Detail Mutasi</h3>
              <button onClick={closeDetailModal} className="text-gray-400 hover:text-gray-500"><X className="h-5 w-5" /></button>
            </div>
            <div className="p-6 space-y-4 text-sm">
              {[
                ['ID Mutasi', detailItem.id_mutasi],
                ['Kode Inventaris', detailItem.kode_inventaris],
                ['Jurusan Asal', detailItem.jurusan_asal?.nama_jurusan || '-'],
                ['Jurusan Tujuan', detailItem.jurusan_tujuan?.nama_jurusan || '-'],
                ['Tanggal Mutasi', formatDate(detailItem.tanggal_mutasi)],
                ['Alasan Mutasi', detailItem.alasan_mutasi || '-'],
              ].map(([label, value]) => (
                <div key={String(label)} className="flex gap-3">
                  <span className="w-40 shrink-0 font-medium text-gray-500">{String(label)}</span>
                  <span className="text-gray-900">{value}</span>
                </div>
              ))}
            </div>
            <div className="px-6 py-4 border-t border-gray-100 flex justify-end">
              <button onClick={closeDetailModal} className="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 font-medium cursor-pointer">Tutup</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
