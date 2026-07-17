'use client';

import { useState, useEffect } from 'react';
import api from '@/utils/api';
import Link from 'next/link';
import { ClipboardCheck, ArrowRightLeft, RotateCcw, Ban, Loader2 } from 'lucide-react';

export default function ProsesPage() {
  const [stats, setStats] = useState({ opname: 0, mutasi: 0, dipinjam: 0, nonAktif: 0 });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchStats = async () => {
      setLoading(true);
      try {
        const [resOpname, resMutasi, resPeminjaman, resNonAktif] = await Promise.all([
          api.get('/opname-aset').catch(() => ({ data: { data: [] } })),
          api.get('/mutasi').catch(() => ({ data: { data: [] } })),
          api.get('/peminjaman').catch(() => ({ data: { data: [] } })),
          api.get('/barang-non-aktif').catch(() => ({ data: { data: [] } })),
        ]);
        setStats({
          opname: (resOpname.data.data || []).length,
          mutasi: (resMutasi.data.data || []).length,
          dipinjam: (resPeminjaman.data.data || []).filter((p: any) => p.status_peminjaman === 'Sedang Dipinjam').length,
          nonAktif: (resNonAktif.data.data || []).length,
        });
      } catch { } finally { setLoading(false); }
    };
    fetchStats();
  }, []);

  const cards = [
    { title: 'Proses Opname', desc: 'Lakukan opname inventaris untuk memeriksa kondisi aset.', href: '/proses/proses-opname', icon: ClipboardCheck, value: stats.opname, color: 'bg-blue-50 text-blue-600' },
    { title: 'Mutasi Barang', desc: 'Proses mutasi aset antar jurusan/unit.', href: '/proses/mutasi-barang', icon: ArrowRightLeft, value: stats.mutasi, color: 'bg-purple-50 text-purple-600' },
    { title: 'Pengembalian', desc: 'Proses pengembalian barang yang sedang dipinjam.', href: '/proses/pengembalian', icon: RotateCcw, value: stats.dipinjam, color: 'bg-green-50 text-green-600' },
    { title: 'Non Aktif Barang', desc: 'Non-aktifkan barang dari inventaris aktif.', href: '/proses/non-aktif-barang', icon: Ban, value: stats.nonAktif, color: 'bg-red-50 text-red-600' },
  ];

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Proses</h1>
        <p className="text-sm text-gray-500 mt-1">Proses operasional inventaris sekolah.</p>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {cards.map((card) => (
          <Link key={card.title} href={card.href} className="block bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow p-6 group">
            <div className="flex items-start justify-between">
              <div className={`p-2.5 rounded-lg ${card.color}`}>
                <card.icon className="h-5 w-5" />
              </div>
              {loading ? (
                <Loader2 className="h-4 w-4 animate-spin text-gray-300" />
              ) : (
                <span className="text-2xl font-bold text-gray-900">{card.value}</span>
              )}
            </div>
            <h3 className="mt-4 text-sm font-semibold text-gray-900 group-hover:text-gray-700">{card.title}</h3>
            <p className="mt-1 text-xs text-gray-500">{card.desc}</p>
          </Link>
        ))}
      </div>
    </div>
  );
}
