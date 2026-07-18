'use client';

import { useEffect } from 'react';
import { useRouter } from 'next/navigation';

export default function WallpaperPage() {
  const router = useRouter();

  useEffect(() => {
    router.replace('/admin/set-lembaga');
  }, [router]);

  return null;
}
