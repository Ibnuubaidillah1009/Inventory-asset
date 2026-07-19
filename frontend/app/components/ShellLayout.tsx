'use client';

import { useEffect, useState, useCallback, useRef } from 'react';
import { useRouter, usePathname } from 'next/navigation';
import Link from 'next/link';
import api from '@/utils/api';
import useIdleTimeout from '@/hooks/useIdleTimeout';
import { AlertTriangle } from 'lucide-react';
import {
  LayoutDashboard,
  Users,
  Box,
  ArrowRightLeft,
  LogOut,
  Loader2,
  Database,
  ChevronDown,
  ChevronRight,
  Circle,
  Settings,
  Archive,
  PackageMinus,
  Key,
  Boxes,
  ClipboardList,
  Wrench,
  ShieldCheck,
  HelpCircle
} from 'lucide-react';

const ICON_MAP: Record<string, any> = {
  'Inventaris': Archive,
  'Proses': ArrowRightLeft,
  'Brg Habis Pakai': PackageMinus,
  'BRG_HABIS_PAKAI': PackageMinus,
  'Laporan': ClipboardList,
  'Tools': Wrench,
  'Admin': ShieldCheck,
  'Help': HelpCircle,
  'MASTER BARANG': Boxes,
  'MASTER_BARANG': Boxes,
  'AUTENTIKASI': Key,
  'MANAJEMEN PENGGUNA': Users,
  'MASTER SEKOLAH': Database,
  'MANAJEMEN ASET': Archive,
  'TRANSAKSI': ArrowRightLeft,
  'ASET NON-AKTIF': PackageMinus,
  'HABIS PAKAI': Box,
  'PENGATURAN SISTEM': Settings,
};

const LABEL_MAP: Record<string, string> = {
  'Inventaris': 'Inventaris',
  'Proses': 'Proses',
  'Brg Habis Pakai': 'Barang Habis Pakai',
  'Laporan': 'Laporan',
  'Tools': 'Peralatan',
  'Admin': 'Administrasi',
  'Help': 'Bantuan',
  'Pengadaan Barang': 'Pengadaan Barang',
  'Input Tanah': 'Input Tanah',
  'Input Bangunan': 'Input Bangunan',
  'Mutasi Barang': 'Mutasi Barang',
  'Proses Opname': 'Proses Opname',
  'Non Aktif Barang': 'Non-Aktif Barang',
  'peminjaman': 'Peminjaman',
  'Pengembalian': 'Pengembalian',
  'Master Data Brg Habis Pakai': 'Master Data Barang Habis Pakai',
  'Data Barang Habis Pakai': 'Data Barang Habis Pakai',
  'Barang Keluar': 'Barang Keluar',
  'Lap Stok Barang': 'Laporan Stok Barang',
  'Lap Stok Brg Habis Pakai': 'Laporan Stok Barang Habis Pakai',
  'Lap Stok Minimal Brg Habis Pakai': 'Laporan Stok Minimal Barang Habis Pakai',
  'Pengadaan Brg Habis Pakai': 'Pengadaan Barang Habis Pakai',
  'Permintaan Brg Habis Pakai': 'Permintaan Barang Habis Pakai',
  'Permintaan Barang': 'Permintaan Barang',
  'Koneksi': 'Koneksi',
  'Backup': 'Cadangan',
  'Restore': 'Pemulihan',
  'Reset': 'Atur Ulang',
  'Data Master': 'Data Master',
  'Set Lembaga': 'Atur Lembaga',
  'User': 'Pengguna',
  'Wallpaper': 'Gambar Latar',
  'Group User': 'Grup Pengguna',
  'About': 'Tentang',
  'Tutorial': 'Panduan',
  'Aktivasi': 'Aktivasi',
  'pengguna': 'Pengguna',
  'peran': 'Peran',
  'akses': 'Akses',
  'jurusan': 'Jurusan',
  'rombel': 'Rombel',
  'kelas': 'Kelas',
  'mapel': 'Mata Pelajaran',
  'unit': 'Unit',
  'tahun_ajaran': 'Tahun Ajaran',
  'kategori': 'Kategori',
  'merek': 'Merek',
  'satuan': 'Satuan',
  'master_barang': 'Master Barang',
  'pemasok': 'Pemasok',
  'gudang': 'Gudang',
  'sumber_perolehan': 'Sumber Perolehan',
  'aset': 'Aset',
  'lokasi': 'Lokasi',
  'ruang': 'Ruang',
  'lemari': 'Lemari',
  'kondisi': 'Kondisi',
  'status_barang': 'Status Barang',
  'opname_aset': 'Opname Aset',
  'pengadaan': 'Pengadaan',
  'permintaan': 'Permintaan',
  'mutasi': 'Mutasi',
  'barang_keluar': 'Barang Keluar',
  'kerusakan': 'Kerusakan',
  'perbaikan': 'Perbaikan',
  'penghapusan_aset': 'Penghapusan Aset',
  'barang_non_aktif': 'Barang Non-Aktif',
  'tanah_non_aktif': 'Tanah Non-Aktif',
  'bangunan_non_aktif': 'Bangunan Non-Aktif',
  'aset_habis_pakai': 'Aset Habis Pakai',
  'pengadaan_habis_pakai': 'Pengadaan Habis Pakai',
  'pengaturan': 'Pengaturan',
  'database': 'Basis Data',
  'laporan_barang': 'Laporan Barang',
  'laporan_transaksi': 'Laporan Transaksi',
  'laporan_peminjaman': 'Laporan Peminjaman',
  'MANAJEMEN PENGGUNA': 'Manajemen Pengguna',
  'MASTER SEKOLAH': 'Master Sekolah',
  'MASTER BARANG': 'Master Barang',
  'MANAJEMEN ASET': 'Manajemen Aset',
  'TRANSAKSI': 'Transaksi',
  'ASET NON-AKTIF': 'Aset Non-Aktif',
  'HABIS PAKAI': 'Habis Pakai',
  'PENGATURAN SISTEM': 'Pengaturan Sistem',
  'AUTENTIKASI': 'Otentikasi',
};

interface Akses {
  nama_modul: string;
  hak_baca: number | boolean;
}

interface User {
  username: string;
  peran?: {
    nama_peran: string;
    akses_list: Akses[];
  };
}

interface SubItem {
  label: string;
  href: string;
  modul: string;
}

interface MenuItem {
  label: string;
  href?: string;
  icon?: any;
  modul: string;
  subItems?: SubItem[];
}

const MODULE_ROUTE_MAP: Record<string, string> = {
  'MANAJEMEN PENGGUNA': 'admin',
};

function modulToPath(namaModul: string): string {
  if (MODULE_ROUTE_MAP[namaModul]) return MODULE_ROUTE_MAP[namaModul];
  return namaModul.toLowerCase().replace(/_/g, '-').replace(/ /g, '-');
}

export default function ShellLayout({ children }: { children: React.ReactNode }) {
  const router = useRouter();
  const pathname = usePathname();

  const handleInvalidSession = useCallback(() => {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    localStorage.removeItem('menu_tree');
    router.push('/login');
  }, [router]);

  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);
  const [allowedMenus, setAllowedMenus] = useState<MenuItem[]>([]);
  const [openMenus, setOpenMenus] = useState<Record<string, boolean>>({});
  const [showIdleWarning, setShowIdleWarning] = useState(false);
  const [idleCountdown, setIdleCountdown] = useState(300);
  const countdownRef = useRef<ReturnType<typeof setInterval> | null>(null);

  const toggleMenu = (label: string) => {
    setOpenMenus(prev => {
      const isOpen = prev[label];
      return { [label]: !isOpen };
    });
  };

  useEffect(() => {
    const loadUserAndAkses = () => {
      const token = localStorage.getItem('token');
      const userString = localStorage.getItem('user');

      if (!token || !userString) {
        handleInvalidSession();
        return;
      }

      try {
        const userData: User = JSON.parse(userString);
        setUser(userData);

        const treeString = localStorage.getItem('menu_tree');
        let dynamicMenus: MenuItem[] = [
          { label: 'Beranda', href: '/dashboard', icon: LayoutDashboard, modul: 'ALL' }
        ];

        if (treeString) {
          const menuTree = JSON.parse(treeString);
          menuTree.forEach((parent: any) => {
            if (parent.nama_modul === 'AUTENTIKASI') return;

            const parentIcon = ICON_MAP[parent.nama_modul] || Box;
            const parentLabel = LABEL_MAP[parent.nama_modul] || parent.nama_modul.split('_').map((word: string) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ');
            const parentPath = modulToPath(parent.nama_modul);

            if (parent.children_recursive && parent.children_recursive.length > 0) {
              const subItems = parent.children_recursive.map((child: any) => {
                const childPath = modulToPath(child.nama_modul);
                const childLabel = LABEL_MAP[child.nama_modul] || child.nama_modul.split('_').map((word: string) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
                return {
                  label: childLabel,
                  href: `/${parentPath}/${childPath}`,
                  modul: child.nama_modul
                };
              });

              dynamicMenus.push({
                label: parentLabel,
                icon: parentIcon,
                modul: parent.nama_modul,
                subItems: subItems
              });
            } else {
              dynamicMenus.push({
                label: parentLabel,
                href: `/${parentPath}`,
                icon: parentIcon,
                modul: parent.nama_modul
              });
            }
          });
        }

        setAllowedMenus(dynamicMenus);

        const newOpenMenus: Record<string, boolean> = {};
        dynamicMenus.forEach(m => {
          if (m.subItems && m.subItems.some((sub: { href: string; label: string; modul: string }) => pathname.startsWith(sub.href))) {
            newOpenMenus[m.label] = true;
          }
        });
        setOpenMenus(newOpenMenus);

      } catch (error) {
        console.error('Gagal membaca data user', error);
        handleInvalidSession();
      } finally {
        setLoading(false);
      }
    };

    loadUserAndAkses();
  }, [pathname, handleInvalidSession]);

  const handleLogout = async () => {
    try {
      await api.post('/logout');
    } catch (error) {
      console.error('Logout error', error);
    } finally {
      handleInvalidSession();
    }
  };

  const handleIdleWarning = useCallback(() => {
    setShowIdleWarning(true);
    setIdleCountdown(300);
  }, []);

  const handleIdleWarningDismissed = useCallback(() => {
    setShowIdleWarning(false);
    if (countdownRef.current) clearInterval(countdownRef.current);
  }, []);

  const { extendSession } = useIdleTimeout({
    onLogout: handleLogout,
    onWarning: handleIdleWarning,
    onWarningDismissed: handleIdleWarningDismissed,
  });

  // Countdown timer untuk peringatan idle
  useEffect(() => {
    if (!showIdleWarning) return;

    countdownRef.current = setInterval(() => {
      setIdleCountdown(prev => {
        if (prev <= 1) {
          if (countdownRef.current) clearInterval(countdownRef.current);
          return 0;
        }
        return prev - 1;
      });
    }, 1000);

    return () => {
      if (countdownRef.current) clearInterval(countdownRef.current);
    };
  }, [showIdleWarning]);

  // Server ping — cek koneksi tiap 5 menit
  useEffect(() => {
    if (!user) return;

    const pingServer = async () => {
      try {
        await api.get('/me');
      } catch (error: any) {
        if (!error.response) {
          handleInvalidSession();
        }
      }
    };

    const interval = setInterval(pingServer, 5 * 60 * 1000);
    return () => clearInterval(interval);
  }, [user, handleInvalidSession]);

  if (loading) {
    return (
      <div className="flex h-screen w-full items-center justify-center bg-gray-50">
        <Loader2 className="h-8 w-8 animate-spin text-gray-900" />
      </div>
    );
  }

  return (
    <div className="flex h-screen bg-gray-50 text-gray-900 font-sans">
      <aside className="w-64 border-r border-gray-200 bg-white flex flex-col">
        <div className="h-16 flex items-center px-6 border-b border-gray-200">
          <span className="font-semibold text-lg tracking-tight">Inventaris Sekolah</span>
        </div>

        <div className="flex-1 overflow-y-auto py-4">
          <nav className="space-y-1 px-3">
            {allowedMenus.map((menu, index) => {
              const Icon = menu.icon;

              if (menu.subItems) {
                const isOpen = !!openMenus[menu.label];
                return (
                  <div key={`dropdown-${index}`} className="flex flex-col space-y-1">
                    <button
                      onClick={() => toggleMenu(menu.label)}
                      className="flex w-full items-center justify-between px-3 py-2 text-sm font-medium text-gray-600 rounded-md transition-colors hover:bg-gray-50 hover:text-gray-900 cursor-pointer"
                    >
                      <div className="flex items-center">
                        <Icon className="mr-3 h-5 w-5 shrink-0" />
                        {menu.label}
                      </div>
                      <ChevronDown
                        className={`h-4 w-4 transition-transform duration-300 ease-in-out ${
                          isOpen ? 'rotate-0' : '-rotate-90'
                        }`}
                      />
                    </button>

                    <div
                      className="grid transition-[grid-template-rows] duration-300 ease-in-out"
                      style={{ gridTemplateRows: isOpen ? '1fr' : '0fr' }}
                    >
                      <div className="overflow-hidden">
                        <div className="pl-10 pr-2 space-y-1 pb-1">
                          {menu.subItems.map((sub: { href: string; label: string; modul: string }) => {
                            const isSubActive = pathname === sub.href || pathname.startsWith(sub.href + '/');
                            return (
                              <Link
                                key={sub.href}
                                href={sub.href}
                                className={`flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors ${
                                  isSubActive
                                    ? 'bg-gray-100 text-gray-900'
                                    : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'
                                }`}
                              >
                                <Circle className={`mr-3 h-2 w-2 shrink-0 ${isSubActive ? 'fill-gray-900 text-gray-900' : 'text-gray-400'}`} />
                                {sub.label}
                              </Link>
                            );
                          })}
                        </div>
                      </div>
                    </div>
                  </div>
                );
              }

              if (!menu.href) return null;
              const isActive = pathname === menu.href || pathname.startsWith(menu.href + '/');
              return (
                <Link
                  key={menu.href}
                  href={menu.href}
                  className={`flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors ${
                    isActive
                      ? 'bg-gray-100 text-gray-900'
                      : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                  }`}
                >
                  <Icon className="mr-3 h-5 w-5 shrink-0" />
                  {menu.label}
                </Link>
              );
            })}
          </nav>
        </div>
      </aside>

      <div className="flex-1 flex flex-col overflow-hidden">
        <header className="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8">
          <div>
            <h2 className="text-sm font-medium text-gray-700">
              Selamat datang, {user?.username}
              <span className="ml-2 text-xs bg-gray-100 px-2 py-1 rounded-full text-gray-500">
                {user?.peran?.nama_peran || 'User'}
              </span>
            </h2>
          </div>

          <button
            onClick={handleLogout}
            className="flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors cursor-pointer"
          >
            <LogOut className="mr-2 h-4 w-4 cursor-pointer" />
            Keluar
          </button>
        </header>

        <main className="flex-1 overflow-y-auto p-8">
          {children}
        </main>
      </div>

      {showIdleWarning && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
          <div className="bg-white rounded-lg shadow-xl p-6 max-w-sm w-full mx-4">
            <div className="flex items-center gap-3 mb-4">
              <div className="flex-shrink-0 w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                <AlertTriangle className="h-5 w-5 text-amber-600" />
              </div>
              <h3 className="text-lg font-semibold text-gray-900">Sesi Akan Berakhir</h3>
            </div>
            <p className="text-sm text-gray-600 mb-4">
              Anda tidak melakukan aktivitas selama lebih dari 1 jam 55 menit.
              Sesi akan berakhir dalam <span className="font-semibold text-gray-900">{Math.floor(idleCountdown / 60)}:{(idleCountdown % 60).toString().padStart(2, '0')}</span> menit.
            </p>
            <div className="flex gap-3">
              <button
                onClick={handleLogout}
                className="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors cursor-pointer"
              >
                Keluar Sekarang
              </button>
              <button
                onClick={() => { extendSession(); setShowIdleWarning(false); }}
                className="flex-1 px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-md hover:bg-gray-800 transition-colors cursor-pointer"
              >
                Tetap di Sini
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
