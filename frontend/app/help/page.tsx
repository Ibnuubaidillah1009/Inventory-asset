'use client';

import Link from 'next/link';
import { BookOpen, Info, Key } from 'lucide-react';

export default function HelpPage() {
  const cards = [
    {
      title: 'Tutorial',
      description: 'Panduan langkah demi langkah menggunakan sistem inventaris.',
      href: '/help/tutorial',
      icon: BookOpen,
    },
    {
      title: 'About',
      description: 'Informasi tentang aplikasi dan tim pengembang.',
      href: '/help/about',
      icon: Info,
    },
    {
      title: 'Aktivasi',
      description: 'Status dan informasi aktivasi lisensi sistem.',
      href: '/help/aktivasi',
      icon: Key,
    },
  ];

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Bantuan</h1>
        <p className="text-sm text-gray-500 mt-1">Bantuan dan panduan sistem.</p>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {cards.map((card) => (
          <Link
            key={card.href}
            href={card.href}
            className="group bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:border-gray-900 hover:shadow-md transition-all"
          >
            <div className="p-3 rounded-lg bg-gray-100 group-hover:bg-gray-900 group-hover:text-white text-gray-600 transition-colors w-fit mb-4">
              <card.icon className="h-6 w-6" />
            </div>
            <h3 className="font-semibold text-gray-900 mb-1">{card.title}</h3>
            <p className="text-sm text-gray-500">{card.description}</p>
          </Link>
        ))}
      </div>
    </div>
  );
}
