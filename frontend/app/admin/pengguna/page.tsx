"use client";

import React, { useState, useEffect } from 'react';
import { Plus, Pencil, Trash2, X, Loader2, Search } from 'lucide-react';
import api from '@/utils/api';

interface Peran {
  id_peran: number;
  nama_peran: string;
}

interface Kelas {
  id_kelas: number;
  nama_kelas: string;
}

interface Mapel {
  id_mapel: number;
  nama_mapel: string;
}

interface Unit {
  id_unit: number;
  nama_unit: string;
}

interface Pengguna {
  id_pengguna: number;
  username: string;
  id_peran: number;
  id_kelas: number | null;
  id_mapel: number | null;
  id_unit: number | null;
  peran?: Peran;
  kelas?: Kelas;
  mapel?: Mapel;
  unit?: Unit;
}

export default function ManajemenPengguna() {
  const [pengguna, setPengguna] = useState<Pengguna[]>([]);
  const [peranList, setPeranList] = useState<Peran[]>([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [modalMode, setModalMode] = useState<'add' | 'edit'>('add');
  const [formLoading, setFormLoading] = useState(false);
  const [currentPage, setCurrentPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);
  const [searchQuery, setSearchQuery] = useState('');

  const [formData, setFormData] = useState({
    id_pengguna: 0,
    username: '',
    password: '',
    id_peran: '',
  });

  const fetchApiData = async (page = 1, search = searchQuery) => {
    try {
      const [penggunaRes, peranRes] = await Promise.all([
        api.get(`/pengguna?page=${page}&search=${encodeURIComponent(search)}`),
        api.get('/peran')
      ]);
      setPengguna(penggunaRes.data?.data || penggunaRes.data || []);
      if (penggunaRes.data?.meta) {
        setCurrentPage(penggunaRes.data.meta.current_page);
        setLastPage(penggunaRes.data.meta.last_page);
      }
      setPeranList(peranRes.data?.data || peranRes.data || []);
    } catch (error) {
      console.error('Error fetching data:', error);
      alert('Gagal memuat data.');
    } finally {
      setLoading(false);
    }
  };

  const fetchData = async (page = 1, search = searchQuery) => {
    setLoading(true);
    await fetchApiData(page, search);
  };

  useEffect(() => {
    const delayDebounceFn = setTimeout(() => {
      fetchData(1, searchQuery);
    }, 500);
    return () => clearTimeout(delayDebounceFn);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [searchQuery]);

  const handleOpenModal = (mode: 'add' | 'edit', data?: Pengguna) => {
    setModalMode(mode);
    if (mode === 'edit' && data) {
      setFormData({
        id_pengguna: data.id_pengguna,
        username: data.username,
        password: '', // Kosongkan saat edit kecuali ingin diubah
        id_peran: data.id_peran.toString(),
      });
    } else {
      setFormData({ id_pengguna: 0, username: '', password: '', id_peran: '' });
    }
    setIsModalOpen(true);
  };

  const handleCloseModal = () => {
    setIsModalOpen(false);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setFormLoading(true);
    try {
      const payload: Record<string, string | number> = {
        username: formData.username,
        id_peran: formData.id_peran,
      };
      
      if (formData.password) {
        payload.password = formData.password;
      }

      if (modalMode === 'add') {
        await api.post('/pengguna', payload);
      } else {
        await api.put(`/pengguna/${formData.id_pengguna}`, payload);
      }
      
      handleCloseModal();
      fetchData();
    } catch (error) {
      console.error('Error submitting data:', error);
      alert('Gagal menyimpan data.');
    } finally {
      setFormLoading(false);
    }
  };

  const handleDelete = async (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
      try {
        await api.delete(`/pengguna/${id}`);
        fetchData();
      } catch (error) {
        console.error('Error deleting data:', error);
        alert('Gagal menghapus data.');
      }
    }
  };

  return (
    <div className="p-6">
      <div className="flex justify-between items-center mb-6">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900">Manajemen Pengguna</h1>
          <p className="text-sm text-gray-500 mt-1">Kelola data pengguna, peran, dan akses sistem.</p>
        </div>
        <button
          onClick={() => handleOpenModal('add')}
          className="flex items-center gap-2 bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg transition-colors text-sm font-medium"
        >
          <Plus className="w-4 h-4" />
          Tambah Pengguna
        </button>
      </div>

      <div className="flex flex-col sm:flex-row gap-4 items-center justify-between mb-4">
        <div className="relative w-full sm:max-w-md">
          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search className="h-4 w-4 text-gray-400" />
          </div>
          <input
            type="text"
            placeholder="Cari pengguna berdasarkan username atau peran..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 sm:text-sm transition-colors"
          />
        </div>
      </div>

      <div className="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <div className="overflow-x-auto">
          <table className="w-full text-left text-sm">
            <thead className="bg-gray-50 text-gray-600 border-b border-gray-200">
              <tr>
                <th className="px-6 py-4 font-medium">No.</th>
                <th className="px-6 py-4 font-medium">Nama Pengguna</th>
                <th className="px-6 py-4 font-medium">Peran</th>
                <th className="px-6 py-4 font-medium">Kelas</th>
                <th className="px-6 py-4 font-medium">Mapel</th>
                <th className="px-6 py-4 font-medium">Unit</th>
                <th className="px-6 py-4 font-medium text-right">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {loading ? (
                <tr>
                  <td colSpan={7} className="px-6 py-12 text-center">
                    <div className="flex flex-col items-center justify-center">
                      <Loader2 className="w-8 h-8 animate-spin text-gray-400 mb-3" />
                      <span className="text-gray-500">Memuat data pengguna...</span>
                    </div>
                  </td>
                </tr>
              ) : pengguna.length === 0 ? (
                <tr>
                  <td colSpan={7} className="px-6 py-12 text-center text-gray-500">
                    Tidak ada data pengguna.
                  </td>
                </tr>
              ) : (
                pengguna.map((item, index) => (
                  <tr key={item.id_pengguna} className="hover:bg-gray-50 transition-colors">
                    <td className="px-6 py-4 text-gray-500">{index + 1}</td>
                    <td className="px-6 py-4 font-medium text-gray-900">{item.username}</td>
                    <td className="px-6 py-4">
                      <span className="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                        {item.peran?.nama_peran || '-'}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-gray-600">{item.kelas?.nama_kelas || '-'}</td>
                    <td className="px-6 py-4 text-gray-600">{item.mapel?.nama_mapel || '-'}</td>
                    <td className="px-6 py-4 text-gray-600">{item.unit?.nama_unit || '-'}</td>
                    <td className="px-6 py-4 text-right">
                      <div className="flex items-center justify-end gap-2">
                        <button
                          onClick={() => handleOpenModal('edit', item)}
                          className="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                          title="Ubah"
                        >
                          <Pencil className="w-4 h-4" />
                        </button>
                        <button
                          onClick={() => handleDelete(item.id_pengguna)}
                          className="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                          title="Hapus"
                        >
                          <Trash2 className="w-4 h-4" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
        {/* Pagination */}
        <div className="px-6 py-4 border-t border-gray-200 flex items-center justify-between bg-gray-50">
          <span className="text-sm text-gray-500">
            Halaman {currentPage} dari {lastPage}
          </span>
          <div className="flex gap-2">
            <button
              onClick={() => fetchData(currentPage - 1)}
              disabled={currentPage === 1}
              className="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Sebelumnya
            </button>
            <button
              onClick={() => fetchData(currentPage + 1)}
              disabled={currentPage === lastPage}
              className="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Selanjutnya &rarr;
            </button>
          </div>
        </div>
      </div>

      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-200">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100">
              <h2 className="text-lg font-semibold text-gray-900">
                {modalMode === 'add' ? 'Tambah Pengguna' : 'Edit Pengguna'}
              </h2>
              <button
                onClick={handleCloseModal}
                className="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-colors"
              >
                <X className="w-5 h-5" />
              </button>
            </div>
            
            <form onSubmit={handleSubmit} className="p-6">
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    Nama Pengguna
                  </label>
                  <input
                    type="text"
                    required
                    value={formData.username}
                    onChange={(e) => setFormData({ ...formData, username: e.target.value })}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent text-sm transition-shadow"
                    placeholder="Masukkan nama pengguna"
                  />
                </div>
                
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    Password {modalMode === 'edit' && <span className="text-xs text-gray-400 font-normal">(Kosongkan jika tidak diubah)</span>}
                  </label>
                  <input
                    type="password"
                    required={modalMode === 'add'}
                    value={formData.password}
                    onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent text-sm transition-shadow"
                    placeholder="Masukkan password"
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    Peran
                  </label>
                  <select
                    required
                    value={formData.id_peran}
                    onChange={(e) => setFormData({ ...formData, id_peran: e.target.value })}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent text-sm bg-white transition-shadow"
                  >
                    <option value="" disabled>Pilih Peran</option>
                    {peranList.map((peran) => (
                      <option key={peran.id_peran} value={peran.id_peran.toString()}>
                        {peran.nama_peran}
                      </option>
                    ))}
                  </select>
                </div>
              </div>

              <div className="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-50">
                <button
                  type="button"
                  onClick={handleCloseModal}
                  className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  disabled={formLoading}
                  className="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 rounded-lg transition-colors disabled:opacity-50"
                >
                  {formLoading ? <Loader2 className="w-4 h-4 animate-spin" /> : null}
                  Simpan
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
