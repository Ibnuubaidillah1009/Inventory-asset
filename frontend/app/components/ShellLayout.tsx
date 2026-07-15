'use client';

import { useEffect, useState, useCallback } from 'react';
import { useRouter, usePathname } from 'next/navigation';
import Link from 'next/link';
import api from '@/utils/api';
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

function modulToPath(namaModul: string): string {
  return namaModul.toLowerCase().replace(/_/g, '-').replace(/ /g, '-');
}

export default function ShellLayout({ children }: { children: React.ReactNode }) {
  const router = useRouter();
  const pathname = usePathname();

  const handleInvalidSession = useCallback(() => {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    router.push('/login');
  }, [router]);

  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);
  const [allowedMenus, setAllowedMenus] = useState<MenuItem[]>([]);
  const [openMenus, setOpenMenus] = useState<Record<string, boolean>>({});

  const toggleMenu = (label: string) => {
    setOpenMenus(prev => ({ ...prev, [label]: !prev[label] }));
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
            const parentLabel = parent.nama_modul.split('_').map((word: string) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ');
            const parentPath = modulToPath(parent.nama_modul);

            if (parent.children_recursive && parent.children_recursive.length > 0) {
              const subItems = parent.children_recursive.map((child: any) => {
                const childPath = modulToPath(child.nama_modul);
                const childLabel = child.nama_modul.split('_').map((word: string) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
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
                      {openMenus[menu.label] ? (
                        <ChevronDown className="h-4 w-4" />
                      ) : (
                        <ChevronRight className="h-4 w-4" />
                      )}
                    </button>

                    {openMenus[menu.label] && (
                      <div className="pl-10 pr-2 space-y-1 mt-1">
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
                    )}
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
            Logout
          </button>
        </header>

        <main className="flex-1 overflow-y-auto p-8">
          {children}
        </main>
      </div>
    </div>
  );
}
