'use client';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { Plus, Pencil, Trash2, X, Loader2, Search, ClipboardCheck } from 'lucide-react';
import { extractData, formatDate } from '@/lib/utils';

import { toast } from 'sonner';
import DropdownMenu from '@/app/components/DropdownMenu';

export default function ProsesOpnamePage() {
  const [data, setData] = useState<any[]>([]);
  const [asetList, setAsetList] = useState<any[]>([]);
  const [kondisiList, setKondisiList] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [searchQuery, setSearchQuery] = useState('');

  const emptyForm = {
    kode_inventaris: '',
    tanggal_opname: '',
    id_kondisi: '',
    keterangan: '' };
  const [formData, setFormData] = useState(emptyForm);

  const fetchData = async (search = searchQuery) => {
    setLoading(true);
    try {
      const [resOpname, resAset, resKondisi] = await Promise.all([
        api.get('/opname-aset').catch(() => ({ data: { data: [] } })),
        api.get('/aset').catch(() => ({ data: { data: [] } })),
        api.get('/kondisi').catch(() => ({ data: { data: [] } })),
      ]);
      setData(extractData(resOpname.data.data));
      setAsetList(extractData(resAset.data.data));
      setKondisiList(extractData(resKondisi.data.data));
    } catch (error) {
      console.error('Gagal mengambil data opname', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    const t = setTimeout(() => fetchData(), 500);
    return () => clearTimeout(t);
  }, [searchQuery]);

  const openModal = (item: any = null) => {
    if (item) {
      setEditingId(item.id_opname_aset);
      setFormData({
        kode_inventaris: item.kode_inventaris || '',
        tanggal_opname: item.tanggal_opname || '',
        id_kondisi: item.id_kondisi || '',
        keterangan: item.keterangan || '' });
    } else {
      setEditingId(null);
      setFormData({ ...emptyForm, tanggal_opname: new Date().toISOString().split('T')[0] });
    }
    setIsModalOpen(true);
  };

  const closeModal = () => { setIsModalOpen(false); setEditingId(null); };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    try {
      const payload: any = {
        kode_inventaris: formData.kode_inventaris,
        tanggal_opname: formData.tanggal_opname };
      if (formData.id_kondisi) payload.id_kondisi = Number(formData.id_kondisi);
      if (formData.keterangan) payload.keterangan = formData.keterangan;

      if (editingId) {
        await api.put(`/opname-aset/${editingId}`, payload);
      } else {
        await api.post('/opname-aset', payload);
      }
      toast.success('Data berhasil disimpan');
      closeModal();
      fetchData();
    } catch (error) {
      console.error('Gagal menyimpan data', error);
      toast.error('Gagal menyimpan data.');
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleDelete = async (id: number) => {
    if (window.confirm('Hapus data opname ini?')) {
      try { await api.delete(`/opname-aset/${id}`); fetchData(); } catch { alert('Gagal menghapus.'); }
    }
  };

  const filtered = data.filter((item) =>
    !searchQuery || item.kode_inventaris?.toLowerCase().includes(searchQuery.toLowerCase()) ||
    item.keterangan?.toLowerCase().includes(searchQuery.toLowerCase())
  );

  const inputClass = "w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white";

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight flex items-center gap-2"><ClipboardCheck className="h-6 w-6" /> Proses Opname</h1>
          <p className="text-sm text-gray-500 mt-1">Lakukan opname inventaris untuk memeriksa kondisi aset.</p>
        </div>
        <button onClick={() => openModal()} className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-gray-900 text-white hover:bg-gray-800 h-10 px-4 py-2 cursor-pointer">
          <Plus className="mr-2 h-4 w-4" /> Tambah Opname
        </button>
      </div>

      <div className="relative w-full sm:max-w-md">
        <Search className="absolute left-3 top-2.5 h-4 w-4 text-gray-400" />
        <input type="text" placeholder="Cari berdasarkan kode inventaris..." value={searchQuery} onChange={(e) => setSearchQuery(e.target.value)} className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 sm:text-sm" />
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm text-left">
            <thead className="text-xs text-gray-500 bg-gray-50 border-b border-gray-200 uppercase">
              <tr>
                <th className="px-6 py-4 font-medium">No.</th>
                <th className="px-6 py-4 font-medium">Kode Inventaris</th>
                <th className="px-6 py-4 font-medium">Tanggal Opname</th>
                <th className="px-6 py-4 font-medium">Kondisi</th>
                <th className="px-6 py-4 font-medium">Keterangan</th>
                <th className="px-6 py-4 font-medium text-right">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {loading ? (
                <tr><td colSpan={6} className="px-6 py-12 text-center"><Loader2 className="h-6 w-6 animate-spin mx-auto text-gray-400" /></td></tr>
              ) : filtered.length === 0 ? (
                <tr><td colSpan={6} className="px-6 py-12 text-center text-gray-500">Belum ada data opname.</td></tr>
              ) : filtered.map((item, i) => (
                <tr key={item.id_opname_aset || i} className="hover:bg-gray-50 transition-colors">
                  <td className="px-6 py-4 text-gray-500">{i + 1}</td>
                  <td className="px-6 py-4 font-medium text-gray-900">{item.kode_inventaris || '-'}</td>
                  <td className="px-6 py-4 text-gray-900">{formatDate(item.tanggal_opname)}</td>
                  <td className="px-6 py-4 text-gray-500">{item.kondisi?.nama_kondisi || '-'}</td>
                  <td className="px-6 py-4 text-gray-500 max-w-xs truncate">{item.keterangan || '-'}</td>
                  <td className="px-6 py-4 text-right">
                                      <DropdownMenu actions={[
                    { label: 'Ubah', icon: Pencil, onClick: () => openModal(item), variant: 'default' },
                    { label: 'Hapus', icon: Trash2, onClick: () => handleDelete(item.id_opname_aset), variant: 'danger' },
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
              <h3 className="text-lg font-semibold text-gray-900">{editingId ? 'Edit Opname' : 'Tambah Opname'}</h3>
              <button onClick={closeModal} className="text-gray-400 hover:text-gray-500"><X className="h-5 w-5" /></button>
            </div>
            <form onSubmit={handleSubmit} className="p-6 space-y-4 text-sm">
              <div>
                <label className="block font-medium text-gray-700 mb-1">Kode Inventaris *</label>
                <select required value={formData.kode_inventaris} onChange={(e) => setFormData({ ...formData, kode_inventaris: e.target.value })} className={inputClass}>
                  <option value="" disabled>Pilih Aset</option>
                  {asetList.map((a) => <option key={a.kode_barang} value={a.kode_inventaris || a.kode_barang}>{a.kode_inventaris || a.kode_barang} - {a.master_barang?.nama_barang || ''}</option>)}
                </select>
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Tanggal Opname *</label>
                <input type="date" required value={formData.tanggal_opname} onChange={(e) => setFormData({ ...formData, tanggal_opname: e.target.value })} className={inputClass} />
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Kondisi Ditemukan</label>
                <select value={formData.id_kondisi} onChange={(e) => setFormData({ ...formData, id_kondisi: e.target.value })} className={inputClass}>
                  <option value="">Pilih Kondisi</option>
                  {kondisiList.map((k) => <option key={k.id_kondisi} value={k.id_kondisi}>{k.nama_kondisi || k.nama}</option>)}
                </select>
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea rows={3} value={formData.keterangan} onChange={(e) => setFormData({ ...formData, keterangan: e.target.value })} className={inputClass} placeholder="Catatan opname..." />
              </div>
              <div className="pt-4 flex justify-end gap-3">
                <button type="button" onClick={closeModal} className="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 font-medium cursor-pointer">Batal</button>
                <button type="submit" disabled={isSubmitting} className="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 font-medium flex items-center disabled:opacity-50 cursor-pointer">
                  {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />} Simpan
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
