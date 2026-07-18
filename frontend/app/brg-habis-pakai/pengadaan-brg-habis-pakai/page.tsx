'use client';

import { useEffect } from 'react';
import { useRouter } from 'next/navigation';
import { Loader2 } from 'lucide-react';

export default function PengadaanBrgHabisPakaiPage() {
  const router = useRouter();

  useEffect(() => {
    router.replace('/habis-pakai/pengadaan-habis-pakai');
  }, [router]);

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div className="flex justify-center items-center py-20">
        <Loader2 className="h-6 w-6 animate-spin text-gray-400" />
      </div>
    </div>
  );
}
