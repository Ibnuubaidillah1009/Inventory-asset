'use client';
import Link from 'next/link';
import { GraduationCap, School, Users, BookOpen, Building2, Calendar } from 'lucide-react';

export default function Page() {
  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Master Sekolah</h1>
        <p className="text-sm text-gray-500 mt-1">Kelola data master sekolah.</p>
      </div>
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <Link href="/master-sekolah/jurusan" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <GraduationCap className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Jurusan</h3>
              <p className="text-xs text-gray-500">Kelola data jurusan</p>
            </div>
          </div>
        </Link>
        <Link href="/master-sekolah/kelas" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <School className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Kelas</h3>
              <p className="text-xs text-gray-500">Kelola data kelas</p>
            </div>
          </div>
        </Link>
        <Link href="/master-sekolah/rombel" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <Users className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Rombel</h3>
              <p className="text-xs text-gray-500">Kelola data rombel</p>
            </div>
          </div>
        </Link>
        <Link href="/master-sekolah/mapel" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <BookOpen className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Mapel</h3>
              <p className="text-xs text-gray-500">Kelola mata pelajaran</p>
            </div>
          </div>
        </Link>
        <Link href="/master-sekolah/unit" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <Building2 className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Unit</h3>
              <p className="text-xs text-gray-500">Kelola data unit</p>
            </div>
          </div>
        </Link>
        <Link href="/master-sekolah/tahun-ajaran" className="group block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-900 group-hover:text-white transition-colors">
              <Calendar className="h-5 w-5" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900">Tahun Ajaran</h3>
              <p className="text-xs text-gray-500">Kelola tahun ajaran</p>
            </div>
          </div>
        </Link>
      </div>
    </div>
  );
}
