'use client';
import Link from 'next/link';
import { Users, Shield, Key, Building2, Image } from 'lucide-react';

export default function Page() {
  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Admin</h1>
        <p className="text-sm text-gray-500 mt-1">Kelola pengguna, peran, dan pengaturan admin.</p>
      </div>
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <Link href="/admin/user" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <Users className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">User</h3>
              <p className="text-xs text-gray-500">Kelola data pengguna</p>
            </div>
          </div>
        </Link>
        <Link href="/admin/peran" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <Shield className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Peran</h3>
              <p className="text-xs text-gray-500">Kelola peran pengguna</p>
            </div>
          </div>
        </Link>
        <Link href="/admin/akses" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <Key className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Akses</h3>
              <p className="text-xs text-gray-500">Kelola hak akses</p>
            </div>
          </div>
        </Link>
        <Link href="/admin/set-lembaga" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <Building2 className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Set Lembaga</h3>
              <p className="text-xs text-gray-500">Pengaturan lembaga</p>
            </div>
          </div>
        </Link>
        <Link href="/admin/wallpaper" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <Image className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Wallpaper</h3>
              <p className="text-xs text-gray-500">Kelola wallpaper sistem</p>
            </div>
          </div>
        </Link>
      </div>
    </div>
  );
}
