'use client';

import { useState, useRef } from 'react';
import api from '@/utils/api';
import { Upload, Loader2, CheckCircle2, AlertCircle, AlertTriangle } from 'lucide-react';

import { toast } from 'sonner';

export default function RestorePage() {
  const [isRestoring, setIsRestoring] = useState(false);
  const [status, setStatus] = useState<'success' | 'error' | null>(null);
  const [message, setMessage] = useState('');
  const [selectedFile, setSelectedFile] = useState<File | null>(null);
  const fileInputRef = useRef<HTMLInputElement>(null);

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      setSelectedFile(file);
      setStatus(null);
      setMessage('');
    }
  };

  const handleRestore = async () => {
    if (!selectedFile) {
      toast.error('Pilih file backup terlebih dahulu.');
      return;
    }

    setIsRestoring(true);
    setStatus(null);
    setMessage('');

    const formData = new FormData();
    formData.append('file', selectedFile);

    try {
      const response = await api.post('/database/restore', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      setStatus('success');
      setMessage(response.data.message || 'Restore berhasil dilakukan.');
      setSelectedFile(null);
      if (fileInputRef.current) fileInputRef.current.value = '';
    } catch (error: any) {
      setStatus('error');
      setMessage(error.response?.data?.message || 'Gagal melakukan restore database.');
    } finally {
      setIsRestoring(false);
    }
  };

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Pemulihan Basis Data</h1>
        <p className="text-sm text-gray-500 mt-1">Pulihkan data basis data dari berkas cadangan.</p>
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-8 max-w-xl">
        <div className="flex items-start gap-4 mb-6">
          <div className="flex-shrink-0 mt-1">
            <Upload className="h-8 w-8 text-gray-400" />
          </div>
          <div>
            <h2 className="text-lg font-semibold text-gray-900">Pulihkan dari Berkas</h2>
            <p className="text-sm text-gray-500 mt-1">
              Unggah berkas cadangan (.sql) untuk memulihkan data basis data. Proses ini akan menimpa data yang ada.
            </p>
          </div>
        </div>

        <div className="flex items-center gap-2 p-4 mb-6 rounded-md bg-yellow-50 border border-yellow-200 text-sm text-yellow-700">
          <AlertTriangle className="h-5 w-5 flex-shrink-0" />
          <span>Peringatan: Proses pemulihan akan mengganti seluruh data basis data saat ini dengan data dari berkas cadangan.</span>
        </div>

        <div className="mb-6">
          <label className="block font-medium text-gray-700 mb-2">Berkas Cadangan (.sql)</label>
          <input
            type="file"
            accept=".sql"
            ref={fileInputRef}
            onChange={handleFileChange}
            className="block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer bg-white focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-gray-900 file:text-white hover:file:bg-gray-800"
          />
          {selectedFile && (
            <p className="mt-2 text-sm text-gray-500">Berkas dipilih: {selectedFile.name}</p>
          )}
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
          onClick={handleRestore}
          disabled={isRestoring || !selectedFile}
          className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-gray-900 text-white hover:bg-gray-800 h-10 px-6 py-2 disabled:opacity-50 cursor-pointer"
        >
          {isRestoring ? (
            <>
              <Loader2 className="mr-2 h-4 w-4 animate-spin" />
              Memproses...
            </>
          ) : (
            'Pulihkan'
          )}
        </button>
      </div>
    </div>
  );
}
