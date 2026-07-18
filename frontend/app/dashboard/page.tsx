"use client";

import { useEffect, useState } from "react";
import api from "@/utils/api";
import {
  Loader2,
  Info,
  Clock,
  Box,
} from "lucide-react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Progress } from "@/components/ui/progress";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";

interface DashboardStats {
  totalAset: number;
  sedangDipinjam: number;
  menungguPerbaikan: number;
  totalPengadaan: number;
}

interface AssetStatus {
  status: string;
  count: number;
  percentage: number;
  color: string;
}

interface RecentActivity {
  id: string;
  type: string;
  description: string;
  date: string;
  status: string;
}

interface RecentAsset {
  id: string;
  kode: string;
  nama: string;
  status: string;
  ruang: string;
}

export default function DashboardPage() {
  const [stats, setStats] = useState<DashboardStats>({
    totalAset: 0,
    sedangDipinjam: 0,
    menungguPerbaikan: 0,
    totalPengadaan: 0,
  });

  const [assetStatuses, setAssetStatuses] = useState<AssetStatus[]>([]);
  const [recentActivities, setRecentActivities] = useState<RecentActivity[]>([]);
  const [recentAssets, setRecentAssets] = useState<RecentAsset[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchDashboardData = async () => {
      try {
        setLoading(true);

        const [resAset, resPeminjaman, resPerbaikan, resPengadaan] =
          await Promise.all([
            api.get("/aset"),
            api.get("/peminjaman"),
            api.get("/perbaikan"),
            api.get("/pengadaan").catch(() => ({ data: { data: [] } })),
          ]);

        const dataAset = resAset.data?.data || [];
        const dataPeminjaman = resPeminjaman.data?.data || [];
        const dataPerbaikan = resPerbaikan.data?.data || [];
        const dataPengadaan = resPengadaan.data?.data || [];

        // Hitung statistik
        const totalAset = dataAset.length;
        const sedangDipinjam = dataAset.filter(
          (a: any) => a.status === "Dipinjam"
        ).length;
        const menungguPerbaikan = dataPerbaikan.length;

        setStats({
          totalAset,
          sedangDipinjam,
          menungguPerbaikan,
          totalPengadaan: dataPengadaan.length,
        });

        // Hitung distribusi status aset
        const statusCount: Record<string, number> = {};
        dataAset.forEach((a: any) => {
          const status = a.status || "Di Gudang";
          statusCount[status] = (statusCount[status] || 0) + 1;
        });

        const statusColors: Record<string, string> = {
          Aktif: "bg-green-500",
          Dipinjam: "bg-blue-500",
          "Di Gudang": "bg-yellow-500",
          Nonaktif: "bg-red-500",
        };

        const statuses: AssetStatus[] = Object.entries(statusCount).map(
          ([status, count]) => ({
            status,
            count,
            percentage: totalAset > 0 ? Math.round((count / totalAset) * 100) : 0,
            color: statusColors[status] || "bg-gray-500",
          })
        );

        setAssetStatuses(statuses.sort((a, b) => b.count - a.count));

        // Aktivitas terbaru dari peminjaman
        const activities: RecentActivity[] = dataPeminjaman
          .slice(0, 5)
          .map((p: any, index: number) => ({
            id: `pinjam-${p.id_peminjaman || p.id || index}`,
            type: "Peminjaman",
            description: `${p.nama_peminjam || "Pengguna"} meminjam barang`,
            date: p.tanggal_peminjaman || p.created_at,
            status: p.status || "Aktif",
          }));

        setRecentActivities(activities);

        // Aset terbaru
        const latestAssets: RecentAsset[] = dataAset.slice(0, 5).map((a: any, index: number) => ({
          id: String(a.id ?? `${a.kode_inventaris}-${index}`),
          kode: a.kode_inventaris || "-",
          nama: a.master_barang?.nama_barang || a.nama_barang || "-",
          status: a.status || "Di Gudang",
          ruang: a.ruang?.nama_ruang || a.nama_ruang || "-",
        }));

        setRecentAssets(latestAssets);
      } catch (err) {
        console.error("Gagal mengambil data dashboard:", err);
        setError("Gagal memuat data dashboard. Pastikan server aktif.");
      } finally {
        setLoading(false);
      }
    };

    fetchDashboardData();
  }, []);

  const formatDate = (dateString: string) => {
    if (!dateString) return "-";
    const date = new Date(dateString);
    return date.toLocaleDateString("id-ID", {
      day: "numeric",
      month: "short",
      year: "numeric",
    });
  };

  const getStatusBadgeVariant = (status: string) => {
    switch (status) {
      case "Aktif":
        return "success";
      case "Dipinjam":
        return "info";
      case "Nonaktif":
        return "destructive";
      default:
        return "warning";
    }
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">
          Dasbor
        </h1>
        <p className="text-sm text-gray-500 mt-1">
          Ringkasan sistem manajemen inventaris sekolah.
        </p>
      </div>

      {/* Error Alert */}
      {error && (
        <div className="bg-red-50 text-red-600 p-4 rounded-md text-sm border border-red-100 flex items-center">
          <Info className="w-4 h-4 mr-2" />
          {error}
        </div>
      )}

      {/* Statistik Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {/* Total Aset */}
        <Card>
          <CardHeader className="space-y-0 pb-2">
            <CardTitle className="text-sm font-medium text-gray-500">
              Total Aset
            </CardTitle>
          </CardHeader>
          <CardContent>
            {loading ? (
              <Loader2 className="h-6 w-6 animate-spin text-gray-400" />
            ) : (
              <div className="text-2xl font-bold">{stats.totalAset}</div>
            )}
            <p className="text-xs text-gray-500 mt-1">Seluruh aset terdaftar</p>
          </CardContent>
        </Card>

        {/* Sedang Dipinjam */}
        <Card>
          <CardHeader className="space-y-0 pb-2">
            <CardTitle className="text-sm font-medium text-gray-500">
              Sedang Dipinjam
            </CardTitle>
          </CardHeader>
          <CardContent>
            {loading ? (
              <Loader2 className="h-6 w-6 animate-spin text-gray-400" />
            ) : (
              <div className="text-2xl font-bold text-blue-600">
                {stats.sedangDipinjam}
              </div>
            )}
            <p className="text-xs text-gray-500 mt-1">Aset yang dipinjam</p>
          </CardContent>
        </Card>

        {/* Menunggu Perbaikan */}
        <Card>
          <CardHeader className="space-y-0 pb-2">
            <CardTitle className="text-sm font-medium text-gray-500">
              Menunggu Perbaikan
            </CardTitle>
          </CardHeader>
          <CardContent>
            {loading ? (
              <Loader2 className="h-6 w-6 animate-spin text-gray-400" />
            ) : (
              <div className="text-2xl font-bold text-orange-600">
                {stats.menungguPerbaikan}
              </div>
            )}
            <p className="text-xs text-gray-500 mt-1">Perlu perbaikan</p>
          </CardContent>
        </Card>

        {/* Total Pengadaan */}
        <Card>
          <CardHeader className="space-y-0 pb-2">
            <CardTitle className="text-sm font-medium text-gray-500">
              Total Pengadaan
            </CardTitle>
          </CardHeader>
          <CardContent>
            {loading ? (
              <Loader2 className="h-6 w-6 animate-spin text-gray-400" />
            ) : (
              <div className="text-2xl font-bold text-purple-600">
                {stats.totalPengadaan}
              </div>
            )}
            <p className="text-xs text-gray-500 mt-1">Transaksi pengadaan</p>
          </CardContent>
        </Card>
      </div>

      {/* Distribusi Status Aset */}
      <Card>
        <CardHeader>
          <CardTitle className="text-base font-semibold">
            Distribusi Status Aset
          </CardTitle>
        </CardHeader>
        <CardContent>
          {loading ? (
            <div className="flex justify-center py-8">
              <Loader2 className="h-6 w-6 animate-spin text-gray-400" />
            </div>
          ) : assetStatuses.length === 0 ? (
            <p className="text-sm text-gray-500 text-center py-8">
              Belum ada data aset.
            </p>
          ) : (
            <div className="space-y-4">
              {assetStatuses.map((status) => (
                <div key={status.status} className="space-y-2">
                  <div className="flex items-center justify-between">
                    <div className="flex items-center gap-2">
                      <div className={`w-3 h-3 rounded-full ${status.color}`} />
                      <span className="text-sm font-medium">{status.status}</span>
                    </div>
                    <div className="flex items-center gap-2">
                      <span className="text-sm text-gray-500">{status.count} barang</span>
                      <Badge variant="secondary">{status.percentage}%</Badge>
                    </div>
                  </div>
                  <Progress value={status.percentage} className="h-2" />
                </div>
              ))}
            </div>
          )}
        </CardContent>
      </Card>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Aktivitas Terbaru */}
        <Card>
          <CardHeader>
            <CardTitle className="text-base font-semibold flex items-center gap-2">
              <Clock className="h-4 w-4" />
              Aktivitas Terbaru
            </CardTitle>
          </CardHeader>
          <CardContent>
            {loading ? (
              <div className="flex justify-center py-8">
                <Loader2 className="h-6 w-6 animate-spin text-gray-400" />
              </div>
            ) : recentActivities.length === 0 ? (
              <p className="text-sm text-gray-500 text-center py-8">
                Belum ada aktivitas tercatat.
              </p>
            ) : (
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Jenis</TableHead>
                    <TableHead>Deskripsi</TableHead>
                    <TableHead>Tanggal</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {recentActivities.map((activity) => (
                    <TableRow key={activity.id}>
                      <TableCell>
                        <Badge variant="info">{activity.type}</Badge>
                      </TableCell>
                      <TableCell className="text-gray-600">
                        {activity.description}
                      </TableCell>
                      <TableCell className="text-gray-500">
                        {formatDate(activity.date)}
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            )}
          </CardContent>
        </Card>

        {/* Aset Terbaru */}
        <Card>
          <CardHeader>
            <CardTitle className="text-base font-semibold flex items-center gap-2">
              <Box className="h-4 w-4" />
              Aset Terbaru
            </CardTitle>
          </CardHeader>
          <CardContent>
            {loading ? (
              <div className="flex justify-center py-8">
                <Loader2 className="h-6 w-6 animate-spin text-gray-400" />
              </div>
            ) : recentAssets.length === 0 ? (
              <p className="text-sm text-gray-500 text-center py-8">
                Belum ada data aset.
              </p>
            ) : (
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Kode</TableHead>
                    <TableHead>Nama Barang</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Ruang</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {recentAssets.map((asset) => (
                    <TableRow key={asset.id}>
                      <TableCell className="font-medium">{asset.kode}</TableCell>
                      <TableCell>{asset.nama}</TableCell>
                      <TableCell>
                        <Badge variant={getStatusBadgeVariant(asset.status)}>
                          {asset.status}
                        </Badge>
                      </TableCell>
                      <TableCell className="text-gray-500">{asset.ruang}</TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
