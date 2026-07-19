'use client';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { Loader2, Save } from 'lucide-react';

import { toast } from 'sonner';

export default function PengaturanPage() {
  const [loading, setLoading] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [settingsId, setSettingsId] = useState<number | null>(null);
  const [formData, setFormData] = useState({
    nama_sekolah: '',
    alamat: '',
    telepon: '',
    email: '',
    website: '',
    logo: '',
  });

  const inputClass = "w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white";

  const fetchData = async () => {
    setLoading(true);
    try {
      const response = await api.get('/pengaturan');
      if (response.data.data) {
        const d = response.data.data;
        setSettingsId(d.id || null);
        setFormData({
          nama_sekolah: d.nama_sekolah || d.nama_instansi || '',
          alamat: d.alamat || d.alamat_instansi || '',
          telepon: d.telepon || d.telpon || '',
          email: d.email || '',
          website: d.website || '',
          logo: d.logo || '',
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
      if (settingsId) {
        await api.post(`/pengaturan/${settingsId}`, formData);
      } else {
        await api.post('/pengaturan', formData);
      }
      toast.success('Pengaturan berhasil disimpan.');
    } catch (error) {
      console.error('Gagal menyimpan pengaturan', error);
      toast.error('Gagal menyimpan pengaturan. Periksa kembali input Anda.');
    } finally {
      setIsSubmitting(false);
    }
  };

  if (loading) {
    return (
      <div className="space-y-6 max-w-6xl mx-auto">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Pengaturan Sistem</h1>
          <p className="text-sm text-gray-500 mt-1">Konfigurasi pengaturan aplikasi.</p>
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
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Pengaturan Sistem</h1>
        <p className="text-sm text-gray-500 mt-1">Konfigurasi pengaturan aplikasi.</p>
      </div>

      <form onSubmit={handleSubmit} className="space-y-6">
        <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
          <div className="px-6 py-4 border-b border-gray-100">
            <h3 className="text-sm font-semibold text-gray-900 uppercase tracking-wider">Informasi Sekolah</h3>
          </div>
          <div className="p-6">
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="sm:col-span-2">
                <label className="block font-medium text-gray-700 mb-1">Nama Sekolah *</label>
                <input type="text" required value={formData.nama_sekolah} onChange={(e) => setFormData({ ...formData, nama_sekolah: e.target.value })} className={inputClass} placeholder="SMK Negeri 1 Contoh" />
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Telepon</label>
                <input type="text" value={formData.telepon} onChange={(e) => setFormData({ ...formData, telepon: e.target.value })} className={inputClass} placeholder="021-12345678" />
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Email</label>
                <input type="email" value={formData.email} onChange={(e) => setFormData({ ...formData, email: e.target.value })} className={inputClass} placeholder="info@sekolah.sch.id" />
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Website</label>
                <input type="text" value={formData.website} onChange={(e) => setFormData({ ...formData, website: e.target.value })} className={inputClass} placeholder="https://sekolah.sch.id" />
              </div>
              <div>
                <label className="block font-medium text-gray-700 mb-1">Logo URL</label>
                <input type="text" value={formData.logo} onChange={(e) => setFormData({ ...formData, logo: e.target.value })} className={inputClass} placeholder="https://example.com/logo.png" />
              </div>
              <div className="sm:col-span-2">
                <label className="block font-medium text-gray-700 mb-1">Alamat</label>
                <textarea rows={2} value={formData.alamat} onChange={(e) => setFormData({ ...formData, alamat: e.target.value })} className={inputClass} placeholder="Jl. Pendidikan No.1" />
              </div>
            </div>
          </div>
        </div>

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
