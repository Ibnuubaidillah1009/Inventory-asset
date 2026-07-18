'use client';

import { useState } from 'react';
import api from '@/utils/api';
import { Database, Loader2, CheckCircle2, AlertCircle } from 'lucide-react';

export default function BackupPage() {
  const [isBacking, setIsBacking] = useState(false);
  const [status, setStatus] = useState<'success' | 'error' | null>(null);
  const [message, setMessage] = useState('');

  const handleBackup = async () => {
    setIsBacking(true);
    setStatus(null);
    setMessage('');
    try {
      const response = await api.post('/database/backup');
      setStatus('success');
      setMessage(response.data.message || 'Backup berhasil dilakukan.');
    } catch (error: any) {
      setStatus('error');
      setMessage(error.response?.data?.message || 'Gagal melakukan backup database.');
    } finally {
      setIsBacking(false);
    }
  };

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Cadangan Basis Data</h1>
        <p className="text-sm text-gray-500 mt-1">Cadangkan seluruh data basis data sistem.</p>
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-8 max-w-xl">
        <div className="flex items-start gap-4 mb-6">
          <div className="flex-shrink-0 mt-1">
            <Database className="h-8 w-8 text-gray-400" />
          </div>
          <div>
            <h2 className="text-lg font-semibold text-gray-900">Cadangkan Sekarang</h2>
            <p className="text-sm text-gray-500 mt-1">
              Proses cadangan akan membuat salinan lengkap dari seluruh data basis data, termasuk tabel, data, dan struktur. Berkas cadangan akan tersimpan di server.
            </p>
          </div>
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
          onClick={handleBackup}
          disabled={isBacking}
          className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-gray-900 text-white hover:bg-gray-800 h-10 px-6 py-2 disabled:opacity-50 cursor-pointer"
        >
          {isBacking ? (
            <>
              <Loader2 className="mr-2 h-4 w-4 animate-spin" />
              Memproses...
            </>
          ) : (
            'Cadangkan Sekarang'
          )}
        </button>
      </div>
    </div>
  );
}
