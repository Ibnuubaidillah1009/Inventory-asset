import type { Metadata } from 'next';
import { Inter } from 'next/font/google';
import './globals.css';

const inter = Inter({ subsets: ['latin'] });

export const metadata: Metadata = {
  title: 'Sistem Informasi Manajemen Inventaris Sekolah',
  description: 'Sistem Informasi Manajemen Inventaris Sekolah menggunakan Next.js',
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="id" className="h-full antialiased bg-gray-50">
      <body className={`${inter.className} min-h-screen flex flex-col m-0 p-0 text-gray-900 bg-gray-50`}>
        {children}
      </body>
    </html>
  );
}
