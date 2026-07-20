'use client';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import { extractData, formatDate } from '@/lib/utils';
import { Loader2, Archive, ShoppingCart, ClipboardList } from 'lucide-react';

export default function InventarisPage() {
  const [stats, setStats] = useState({
    totalAset: 0,
    asetAktif: 0,
    asetGudang: 0,
    totalPengadaan: 0,
    pengadaanDiproses: 0,
    totalPermintaan: 0,
    permintaanMenunggu: 0,
  });
  const [recentAset, setRecentAset] = useState<any[]>([]);
  const [recentPengadaan, setRecentPengadaan] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [resAset, resPengadaan, resPermintaan] = await Promise.all([
          api.get('/aset').catch(() => ({ data: { data: [] } })),
          api.get('/pengadaan').catch(() => ({ data: { data: [] } })),
          api.get('/permintaan').catch(() => ({ data: { data: [] } })),
        ]);

        const asetData = extractData(resAset.data.data);
        const pengadaanData = extractData(resPengadaan.data.data);
        const permintaanData = extractData(resPermintaan.data.data);

        setStats({
          totalAset: asetData.length,
          asetAktif: asetData.filter((a: any) => a.status === 'Aktif').length,
          asetGudang: asetData.filter((a: any) => a.status === 'Di Gudang').length,
          totalPengadaan: pengadaanData.length,
          pengadaanDiproses: pengadaanData.filter((p: any) => p.status === 'Menunggu Proses').length,
          totalPermintaan: permintaanData.length,
          permintaanMenunggu: permintaanData.filter((p: any) => p.status_persetujuan === 'Menunggu').length,
        });

        setRecentAset(asetData.slice(0, 5));
        setRecentPengadaan(pengadaanData.slice(0, 5));
      } catch (err) {
        console.error('Gagal mengambil data inventaris:', err);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  const statCards = [
    { label: 'Total Aset', value: stats.totalAset, icon: Archive, color: 'bg-blue-500' },
    { label: 'Aset Aktif', value: stats.asetAktif, icon: Archive, color: 'bg-green-500' },
    { label: 'Aset di Gudang', value: stats.asetGudang, icon: Archive, color: 'bg-yellow-500' },
    { label: 'Total Pengadaan', value: stats.totalPengadaan, icon: ShoppingCart, color: 'bg-purple-500' },
    { label: 'Pengadaan Diproses', value: stats.pengadaanDiproses, icon: ShoppingCart, color: 'bg-orange-500' },
    { label: 'Total Permintaan', value: stats.totalPermintaan, icon: ClipboardList, color: 'bg-indigo-500' },
    { label: 'Permintaan Menunggu', value: stats.permintaanMenunggu, icon: ClipboardList, color: 'bg-red-500' },
  ];

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Inventaris</h1>
        <p className="text-sm text-gray-500 mt-1">Ringkasan data inventaris sekolah.</p>
      </div>

      {loading ? (
        <div className="flex justify-center py-12">
          <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
        </div>
      ) : (
        <>
          {/* Stats Cards */}
          <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            {statCards.map((card) => (
              <div key={card.label} className="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <div className="flex items-center gap-3">
                  <div className={`${card.color} rounded-lg p-2`}>
                    <card.icon className="h-5 w-5 text-white" />
                  </div>
                  <div>
                    <p className="text-2xl font-bold text-gray-900">{card.value}</p>
                    <p className="text-xs text-gray-500">{card.label}</p>
                  </div>
                </div>
              </div>
            ))}
          </div>

          {/* Recent Aset */}
          <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div className="px-6 py-4 border-b border-gray-100">
              <h2 className="text-lg font-semibold text-gray-900">Aset Terbaru</h2>
            </div>
            <div className="overflow-x-auto">
              <table className="w-full text-sm text-left">
                <thead className="text-xs text-gray-500 bg-gray-50 border-b border-gray-200 uppercase">
                  <tr>
                    <th className="px-6 py-3 font-medium">No.</th>
                    <th className="px-6 py-3 font-medium">Kode Barang</th>
                    <th className="px-6 py-3 font-medium">Nama Barang</th>
                    <th className="px-6 py-3 font-medium">Status</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200">
                  {recentAset.length === 0 ? (
                    <tr>
                      <td colSpan={4} className="px-6 py-8 text-center text-gray-500">
                        Belum ada data aset.
                      </td>
                    </tr>
                  ) : (
                    recentAset.map((item: any, index: number) => (
                      <tr key={item.kode_barang || index} className="hover:bg-gray-50 transition-colors">
                        <td className="px-6 py-3 text-gray-500">{index + 1}</td>
                        <td className="px-6 py-3 font-medium text-gray-900">{item.kode_barang || '-'}</td>
                        <td className="px-6 py-3 text-gray-900">
                          {item.master_barang?.nama_barang || '-'}
                        </td>
                        <td className="px-6 py-3">
                          <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium uppercase tracking-wider ${
                            item.status === 'Aktif' ? 'bg-green-100 text-green-800' :
                            item.status === 'Dipinjam' ? 'bg-blue-100 text-blue-800' :
                            item.status === 'Nonaktif' ? 'bg-red-100 text-red-800' :
                            'bg-yellow-100 text-yellow-800'
                          }`}>
                            {item.status || 'Di Gudang'}
                          </span>
                        </td>
                      </tr>
                    ))
                  )}
                </tbody>
              </table>
            </div>
          </div>

          {/* Recent Pengadaan */}
          <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div className="px-6 py-4 border-b border-gray-100">
              <h2 className="text-lg font-semibold text-gray-900">Pengadaan Terbaru</h2>
            </div>
            <div className="overflow-x-auto">
              <table className="w-full text-sm text-left">
                <thead className="text-xs text-gray-500 bg-gray-50 border-b border-gray-200 uppercase">
                  <tr>
                    <th className="px-6 py-3 font-medium">No.</th>
                    <th className="px-6 py-3 font-medium">ID Pengadaan</th>
                    <th className="px-6 py-3 font-medium">Tanggal</th>
                    <th className="px-6 py-3 font-medium">Status</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200">
                  {recentPengadaan.length === 0 ? (
                    <tr>
                      <td colSpan={4} className="px-6 py-8 text-center text-gray-500">
                        Belum ada data pengadaan.
                      </td>
                    </tr>
                  ) : (
                    recentPengadaan.map((item: any, index: number) => (
                      <tr key={item.id_pengadaan || index} className="hover:bg-gray-50 transition-colors">
                        <td className="px-6 py-3 text-gray-500">{index + 1}</td>
                        <td className="px-6 py-3 font-medium text-gray-900">#{item.id_pengadaan}</td>
                        <td className="px-6 py-3 text-gray-500">
                          {formatDate(item.tanggal_pengadaan)}
                        </td>
                        <td className="px-6 py-3">
                          <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium uppercase tracking-wider ${
                            item.status === 'Selesai' ? 'bg-green-100 text-green-800' :
                            item.status === 'Dibelanjakan' ? 'bg-blue-100 text-blue-800' :
                            'bg-yellow-100 text-yellow-800'
                          }`}>
                            {item.status || '-'}
                          </span>
                        </td>
                      </tr>
                    ))
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </>
      )}
    </div>
  );
}
