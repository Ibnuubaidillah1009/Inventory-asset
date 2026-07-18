'use client';

import { useState } from 'react';
import api from '@/utils/api';
import { Settings, Loader2, CheckCircle2, AlertCircle, Wifi, WifiOff } from 'lucide-react';

export default function KoneksiPage() {
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [isTesting, setIsTesting] = useState(false);
  const [status, setStatus] = useState<'success' | 'error' | null>(null);
  const [message, setMessage] = useState('');
  const [testStatus, setTestStatus] = useState<'success' | 'error' | null>(null);
  const [testMessage, setTestMessage] = useState('');

  const [formData, setFormData] = useState({
    DB_HOST: '',
    DB_PORT: '3306',
    DB_DATABASE: '',
    DB_USERNAME: '',
    DB_PASSWORD: '',
  });

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
    setTestStatus(null);
    setTestMessage('');
  };

  const handleTest = async () => {
    setIsTesting(true);
    setTestStatus(null);
    setTestMessage('');
    try {
      const response = await api.post('/database/test-connection', formData);
      setTestStatus('success');
      setTestMessage(response.data.message || 'Koneksi berhasil.');
    } catch (error: any) {
      setTestStatus('error');
      setTestMessage(error.response?.data?.message || 'Koneksi gagal. Periksa pengaturan Anda.');
    } finally {
      setIsTesting(false);
    }
  };

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    setStatus(null);
    setMessage('');
    try {
      const response = await api.post('/database/change-connection', formData);
      setStatus('success');
      setMessage(response.data.message || 'Pengaturan koneksi berhasil disimpan.');
    } catch (error: any) {
      setStatus('error');
      setMessage(error.response?.data?.message || 'Gagal menyimpan pengaturan koneksi.');
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Pengaturan Koneksi Database</h1>
        <p className="text-sm text-gray-500 mt-1">Konfigurasi koneksi ke database server.</p>
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-8 max-w-xl">
        <div className="flex items-start gap-4 mb-6">
          <div className="flex-shrink-0 mt-1">
            <Settings className="h-8 w-8 text-gray-400" />
          </div>
          <div>
            <h2 className="text-lg font-semibold text-gray-900">Konfigurasi Koneksi</h2>
            <p className="text-sm text-gray-500 mt-1">
              Masukkan pengaturan koneksi database. Pastikan informasi yang dimasukkan benar sebelum menyimpan.
            </p>
          </div>
        </div>

        {testStatus === 'success' && (
          <div className="flex items-center gap-2 p-4 mb-6 rounded-md bg-green-50 border border-green-200 text-sm text-green-700">
            <Wifi className="h-5 w-5 flex-shrink-0" />
            {testMessage}
          </div>
        )}

        {testStatus === 'error' && (
          <div className="flex items-center gap-2 p-4 mb-6 rounded-md bg-red-50 border border-red-200 text-sm text-red-700">
            <WifiOff className="h-5 w-5 flex-shrink-0" />
            {testMessage}
          </div>
        )}

        {status === 'success' && (
          <div className="flex items-center gap-2 p-4 mb-6 rounded-md bg-green-50 border border-green-200 text-sm text-green-700">
            <CheckCircle2 className="h-5 w-5 flex-shrink-0" />
            {message}
          </div>
        )}

        {status === 'error' && (
          <div className="flex items-center gap-2 p-4 mb-6 rounded-md bg-red-50 border border-red-200 text-sm text-red-700">
            <AlertCircle className="h-5 w-5 flex-shrink-0" />
            {message}
          </div>
        )}

        <form onSubmit={handleSave} className="space-y-4 text-sm">
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block font-medium text-gray-700 mb-1">Host</label>
              <input
                type="text"
                name="DB_HOST"
                required
                value={formData.DB_HOST}
                onChange={handleChange}
                className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                placeholder="localhost"
              />
            </div>
            <div>
              <label className="block font-medium text-gray-700 mb-1">Port</label>
              <input
                type="text"
                name="DB_PORT"
                required
                value={formData.DB_PORT}
                onChange={handleChange}
                className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                placeholder="3306"
              />
            </div>
          </div>
          <div>
            <label className="block font-medium text-gray-700 mb-1">Nama Database</label>
            <input
              type="text"
              name="DB_DATABASE"
              required
              value={formData.DB_DATABASE}
              onChange={handleChange}
              className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
              placeholder="Nama database"
            />
          </div>
          <div>
            <label className="block font-medium text-gray-700 mb-1">Nama Pengguna</label>
            <input
              type="text"
              name="DB_USERNAME"
              required
              value={formData.DB_USERNAME}
              onChange={handleChange}
              className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
              placeholder="Nama pengguna basis data"
            />
          </div>
          <div>
            <label className="block font-medium text-gray-700 mb-1">Kata Sandi</label>
            <input
              type="password"
              name="DB_PASSWORD"
              required
              value={formData.DB_PASSWORD}
              onChange={handleChange}
              className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
              placeholder="Kata sandi basis data"
            />
          </div>

          <div className="pt-4 flex justify-end gap-3">
            <button
              type="button"
              onClick={handleTest}
              disabled={isTesting}
              className="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 font-medium transition-colors flex items-center disabled:opacity-50 cursor-pointer"
            >
              {isTesting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
              Uji Koneksi
            </button>
            <button
              type="submit"
              disabled={isSubmitting}
              className="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 font-medium transition-colors flex items-center disabled:opacity-50 cursor-pointer"
            >
              {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
