'use client';

import { Users } from 'lucide-react';

export default function GroupUserPage() {
  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Grup Pengguna</h1>
        <p className="text-sm text-gray-500 mt-1">Kelola grup pengguna.</p>
      </div>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="p-8 text-center">
          <Users className="h-12 w-12 mx-auto text-gray-300 mb-4" />
          <p className="text-gray-600 text-lg font-medium">Manajemen grup pengguna akan segera hadir.</p>
        </div>
      </div>
    </div>
  );
}
