'use client';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { Loader2, Save, Info } from 'lucide-react';

export default function SetLembagaPage() {
  const [loading, setLoading] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [pengaturanId, setPengaturanId] = useState<number | null>(null);
  const [formData, setFormData] = useState({
    nama_instansi: '',
    alamat_instansi: '',
    telpon: '',
    website: '',
    email: '',
    kota: '',
    kepala_sekolah: '',
    NIP: '',
    bagian_inventaris: '',
    kode_inventaris_prefix: 'INV',
  });

  const inputClass = "w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white";

  const fetchData = async () => {
    setLoading(true);
    try {
      const response = await api.get('/pengaturan');
      if (response.data.data) {
        const d = response.data.data;
        setPengaturanId(d.id_pengaturan);
        setFormData({
          nama_instansi: d.nama_instansi || '',
          alamat_instansi: d.alamat_instansi || '',
          telpon: d.telpon || '',
          website: d.website || '',
          email: d.email || '',
          kota: d.kota || '',
          kepala_sekolah: d.kepala_sekolah || '',
          NIP: d.NIP || '',
          bagian_inventaris: d.bagian_inventaris || '',
          kode_inventaris_prefix: d.kode_inventaris_prefix || 'INV',
        });
      }
    } catch (error) {
      console.error('Gagal mengambil data pengaturan', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    try {
      const payload: any = { ...formData };
      if (pengaturanId) {
        await api.post(`/pengaturan/${pengaturanId}`, payload);
      } else {
        const res = await api.post('/pengaturan', payload);
        if (res.data.data) {
          setPengaturanId(res.data.data.id_pengaturan);
        }
      }
      alert('Pengaturan lembaga berhasil disimpan.');
    } catch (error) {
      console.error('Gagal menyimpan pengaturan', error);
      alert('Gagal menyimpan pengaturan. Periksa kembali input Anda.');
    } finally {
      setIsSubmitting(false);
    }
  };

  const formatPreview = () => {
    const prefix = formData.kode_inventaris_prefix || 'INV';
    return `${prefix}-2026-001-001-010-001`;
  };

  if (loading) {
    return (
      <div className="space-y-6 max-w-6xl mx-auto">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Set Lembaga</h1>
          <p className="text-sm text-gray-500 mt-1">Pengaturan data lembaga.</p>
        </div>
        <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
          <Loader2 className="h-6 w-6 animate-spin mx-auto text-gray-400" />
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Set Lembaga</h1>
        <p className="text-sm text-gray-500 mt-1">Pengaturan data lembaga dan format kode inventaris.</p>
      </div>

      <form onSubmit={handleSubmit} className="space-y-6">
        {/* Section: Informasi Lembaga */}
        <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
          <div className="px-6 py-4 border-b border-gray-100">
            <h3 className="text-sm font-semibold text-gray-900 uppercase tracking-wider">Informasi Lembaga</h3>
          </div>
          <div className="p-6">
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="sm:col-span-2">
                <label className="block font-medium text-gray-700 mb-1">Nama Instansi *</label>
                <input type="text" required value={formData.nama_instansi} onChange={(e) => setFormData({ ...formData, nama_instansi: e.target.value })} className={inputClass} placeholder="SMK Negeri 1 Contoh" />
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Kota</label>
                <input type="text" value={formData.kota} onChange={(e) => setFormData({ ...formData, kota: e.target.value })} className={inputClass} placeholder="Jakarta" />
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Telepon</label>
                <input type="text" value={formData.telpon} onChange={(e) => setFormData({ ...formData, telpon: e.target.value })} className={inputClass} placeholder="021-12345678" />
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Email</label>
                <input type="email" value={formData.email} onChange={(e) => setFormData({ ...formData, email: e.target.value })} className={inputClass} placeholder="info@sekolah.sch.id" />
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Website</label>
                <input type="text" value={formData.website} onChange={(e) => setFormData({ ...formData, website: e.target.value })} className={inputClass} placeholder="https://sekolah.sch.id" />
              </div>
              <div className="sm:col-span-2">
                <label className="block font-medium text-gray-700 mb-1">Alamat Instansi</label>
                <textarea rows={2} value={formData.alamat_instansi} onChange={(e) => setFormData({ ...formData, alamat_instansi: e.target.value })} className={inputClass} placeholder="Jl. Pendidikan No.1" />
              </div>
            </div>
          </div>
        </div>

        {/* Section: Kepala Sekolah */}
        <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
          <div className="px-6 py-4 border-b border-gray-100">
            <h3 className="text-sm font-semibold text-gray-900 uppercase tracking-wider">Kepala Sekolah</h3>
          </div>
          <div className="p-6">
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="block font-medium text-gray-700 mb-1">Nama Kepala Sekolah</label>
                <input type="text" value={formData.kepala_sekolah} onChange={(e) => setFormData({ ...formData, kepala_sekolah: e.target.value })} className={inputClass} placeholder="Dr. Budi Santoso, M.Pd" />
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">NIP</label>
                <input type="text" value={formData.NIP} onChange={(e) => setFormData({ ...formData, NIP: e.target.value })} className={inputClass} placeholder="196801011990031001" />
              </div>
              <div className="sm:col-span-2">
                <label className="block font-medium text-gray-700 mb-1">Bagian Inventaris</label>
                <input type="text" value={formData.bagian_inventaris} onChange={(e) => setFormData({ ...formData, bagian_inventaris: e.target.value })} className={inputClass} placeholder="Bagian Sarana dan Prasarana" />
              </div>
            </div>
          </div>
        </div>

        {/* Section: Format Kode Inventaris */}
        <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
          <div className="px-6 py-4 border-b border-gray-100">
            <h3 className="text-sm font-semibold text-gray-900 uppercase tracking-wider">Format Kode Inventaris</h3>
          </div>
          <div className="p-6 space-y-4">
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="block font-medium text-gray-700 mb-1">Prefix Kode Inventaris</label>
                <input type="text" maxLength={10} value={formData.kode_inventaris_prefix} onChange={(e) => setFormData({ ...formData, kode_inventaris_prefix: e.target.value.toUpperCase() })} className={inputClass} placeholder="INV" />
                <p className="text-xs text-gray-500 mt-1">Awalan kode inventaris. Default: INV</p>
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Contoh Format</label>
                <div className="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-gray-900 font-mono text-sm">
                  {formatPreview()}
                </div>
              </div>
            </div>
            <div className="bg-blue-50 border border-blue-200 rounded-md p-4 text-sm text-blue-800">
              <div className="flex items-start gap-2">
                <Info className="h-4 w-4 mt-0.5 shrink-0" />
                <div>
                  <p className="font-medium mb-1">Struktur Format:</p>
                  <p className="font-mono text-xs">{formData.kode_inventaris_prefix || 'INV'}-TAHUN-ID_PENGADAAN-URUTAN-JUMLAH-UNIT</p>
                  <ul className="mt-2 space-y-1 text-xs text-blue-700">
                    <li><strong>TAHUN</strong>: Tahun pengadaan (4 digit)</li>
                    <li><strong>ID_PENGADAAN</strong>: Nomor pengadaan (3 digit, zero-padded)</li>
                    <li><strong>URUTAN</strong>: Posisi item dalam pengadaan (3 digit)</li>
                    <li><strong>JUMLAH</strong>: Jumlah unit item tersebut (3 digit)</li>
                    <li><strong>UNIT</strong>: Urutan unit dari jumlah tersebut (3 digit)</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Submit */}
        <div className="flex justify-end">
          <button type="submit" disabled={isSubmitting} className="px-6 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 font-medium transition-colors flex items-center disabled:opacity-50 cursor-pointer">
            {isSubmitting ? (
              <Loader2 className="mr-2 h-4 w-4 animate-spin" />
            ) : (
              <Save className="mr-2 h-4 w-4" />
            )}
            Simpan Pengaturan
          </button>
        </div>
      </form>
    </div>
  );
}
