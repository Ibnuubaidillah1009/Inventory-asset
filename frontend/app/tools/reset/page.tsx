'use client';

import { useState } from 'react';
import api from '@/utils/api';
import { AlertTriangle, Loader2, CheckCircle2, AlertCircle } from 'lucide-react';

import { toast } from 'sonner';

export default function ResetPage() {
  const [isResetting, setIsResetting] = useState(false);
  const [status, setStatus] = useState<'success' | 'error' | null>(null);
  const [message, setMessage] = useState('');
  const [confirmation, setConfirmation] = useState('');

  const isConfirmed = confirmation === 'RESET';

  const handleReset = async () => {
    if (!isConfirmed) return;

    if (!window.confirm('PERINGATAN: Semua data akan dihapus secara permanen. Apakah Anda benar-benar yakin?')) {
      return;
    }

    setIsResetting(true);
    setStatus(null);
    setMessage('');
    try {
      const response = await api.post('/database/reset');
      setStatus('success');
      setMessage(response.data.message || 'Database berhasil direset.');
      setConfirmation('');
    } catch (error: any) {
      setStatus('error');
      setMessage(error.response?.data?.message || 'Gagal mereset database.');
    } finally {
      setIsResetting(false);
    }
  };

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Atur Ulang Basis Data</h1>
        <p className="text-sm text-gray-500 mt-1">Atur ulang seluruh data basis data ke kondisi awal.</p>
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-8 max-w-xl">
        <div className="flex items-start gap-4 mb-6">
          <div className="flex-shrink-0 mt-1">
            <AlertTriangle className="h-8 w-8 text-red-500" />
          </div>
          <div>
            <h2 className="text-lg font-semibold text-gray-900">Atur Ulang Basis Data</h2>
            <p className="text-sm text-gray-500 mt-1">
              Aksi ini akan menghapus semua data di basis data secara permanen. Semua data aset, transaksi, pengguna, dan data lainnya akan hilang. Tindakan ini tidak dapat dibatalkan.
            </p>
          </div>
        </div>

        <div className="p-4 mb-6 rounded-md bg-red-50 border border-red-200 text-sm text-red-700">
          <p className="font-semibold mb-1">Peringatan: Penghapusan Data Permanen</p>
          <ul className="list-disc list-inside space-y-1">
            <li>Seluruh data aset dan inventaris akan dihapus</li>
            <li>Seluruh data transaksi akan dihapus</li>
            <li>Data pengguna dan pengaturan akan dihapus</li>
            <li>Tindakan ini tidak dapat dibatalkan</li>
          </ul>
        </div>

        <div className="mb-6">
          <label className="block font-medium text-gray-700 mb-2">
            Ketik <span className="font-mono font-bold text-red-600">RESET</span> untuk mengonfirmasi:
          </label>
          <input
            type="text"
            value={confirmation}
            onChange={(e) => setConfirmation(e.target.value)}
            className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500"
            placeholder="Ketik RESET"
          />
        </div>

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

        <button
          onClick={handleReset}
          disabled={isResetting || !isConfirmed}
          className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-red-600 text-white hover:bg-red-700 h-10 px-6 py-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
        >
          {isResetting ? (
            <>
              <Loader2 className="mr-2 h-4 w-4 animate-spin" />
              Memproses...
            </>
          ) : (
            'Atur Ulang Basis Data'
          )}
        </button>
      </div>
    </div>
  );
}
