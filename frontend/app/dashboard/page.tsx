"use client";

import { useEffect, useState } from "react";
import api from "@/utils/api";
import {
  Loader2,
  Info,
} from "lucide-react";


export default function DashboardPage() {
  const [stats, setStats] = useState({
    aset: 0,
    peminjaman: 0,
    perbaikan: 0,
  });


  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchDashboardData = async () => {
      try {
        setLoading(true);

        // Menembak 3 endpoint secara bersamaan
        const [resAset, resPeminjaman, resPerbaikan] = await Promise.all([
          api.get("/aset"),
          api.get("/peminjaman"),
          api.get("/perbaikan"),
        ]);

        // Mengambil array data (Menyesuaikan dengan standar API Resource Laravel)
        const dataAset = resAset.data?.data || [];
        const dataPeminjaman = resPeminjaman.data?.data || [];
        const dataPerbaikan = resPerbaikan.data?.data || [];

        // 1. Set Statistik
        setStats({
          aset: dataAset.length,
          peminjaman: dataPeminjaman.length,
          perbaikan: dataPerbaikan.length,
        });


      } catch (err) {
        console.error("Gagal mengambil data dashboard:", err);
        setError("Gagal memuat data dashboard. Pastikan server aktif.");
      } finally {
        setLoading(false);
      }
    };

    fetchDashboardData();
  }, []);


  return (
    <div className="max-w-5xl mx-auto space-y-6">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">
          Dashboard
        </h1>
        <p className="text-sm text-gray-500 mt-1">
          Ringkasan sistem manajemen inventaris sekolah.
        </p>
      </div>

      {error && (
        <div className="bg-red-50 text-red-600 p-4 rounded-md text-sm border border-red-100 flex items-center">
          <Info className="w-4 h-4 mr-2" />
          {error}
        </div>
      )}

      {/* Grid Statistik */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        {/* Card 1 */}
        <div className="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
          <h3 className="text-sm font-medium text-gray-500 mb-1">Total Aset</h3>
          <div className="flex items-center">
            {loading ? (
              <Loader2 className="h-6 w-6 animate-spin text-gray-400 mt-1" />
            ) : (
              <p className="text-3xl font-semibold text-gray-900">
                {stats.aset}
              </p>
            )}
          </div>
        </div>

        {/* Card 2 */}
        <div className="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
          <h3 className="text-sm font-medium text-gray-500 mb-1">
            Sedang Dipinjam
          </h3>
          <div className="flex items-center">
            {loading ? (
              <Loader2 className="h-6 w-6 animate-spin text-gray-400 mt-1" />
            ) : (
              <p className="text-3xl font-semibold text-gray-900">
                {stats.peminjaman}
              </p>
            )}
          </div>
        </div>

        {/* Card 3 */}
        <div className="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
          <h3 className="text-sm font-medium text-gray-500 mb-1">
            Menunggu Perbaikan
          </h3>
          <div className="flex items-center">
            {loading ? (
              <Loader2 className="h-6 w-6 animate-spin text-gray-400 mt-1" />
            ) : (
              <p className="text-3xl font-semibold text-gray-900">
                {stats.perbaikan}
              </p>
            )}
          </div>
        </div>
      </div>

      {/* Aktivitas Terbaru
      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
          <h3 className="text-sm font-semibold text-gray-900">Aktivitas Terbaru</h3>
        </div>
        <div className="divide-y divide-gray-100">
          {loading ? (
            <div className="p-12 flex justify-center">
              <Loader2 className="h-6 w-6 animate-spin text-gray-300" />
            </div>
          ) : activities.length === 0 ? (
            <div className="p-12 text-center text-sm text-gray-500">
              Belum ada aktivitas tercatat.
            </div>
          ) : (
            activities.map((activity) => (
              <div key={activity.id} className="p-4 hover:bg-gray-50 transition-colors flex items-start gap-4">
                <div className="mt-1 p-2 bg-gray-100 rounded-lg text-gray-600">
                  <activity.icon className="w-4 h-4" />
                </div>
                <div className="flex-1 min-w-0">
                  <div className="flex justify-between items-start gap-2">
                    <p className="text-sm font-medium text-gray-900 truncate">
                      {activity.title}
                    </p>
                    <span className="text-xs text-gray-400 whitespace-nowrap">
                      {formatDate(activity.date)}
                    </span>
                  </div>
                  <p className="text-xs text-gray-500 mt-0.5">{activity.type}</p>
                </div>
              </div>
            ))
          )} */}
        {/* </div> */}
      {/* </div> */}
    </div>
  );
}
