'use client';

import { useState, useEffect, useRef } from 'react';
import api from '@/utils/api';
import { extractData, formatDate } from '@/lib/utils';
import { Plus, X, Loader2, Search, Eye, Printer } from 'lucide-react';

import { toast } from 'sonner';

export default function PeminjamanPage() {
  const [data, setData] = useState<any[]>([]);
  const [asetList, setAsetList] = useState<any[]>([]);
  const [pengaturan, setPengaturan] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isDetailOpen, setIsDetailOpen] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [selectedItem, setSelectedItem] = useState<any>(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [currentPage, setCurrentPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);
  const [printData, setPrintData] = useState<any>(null);

  const [formData, setFormData] = useState({
    nomor_peminjaman: '',
    tanggal_pinjam: '',
    nama_peminjam: '',
    nomor_telepon: '',
    lama_pinjam_hari: '',
    keterangan: '',
  });

  const [selectedBarang, setSelectedBarang] = useState<any[]>([]);
  const [asetSearch, setAsetSearch] = useState('');
  const [asetDropdownOpen, setAsetDropdownOpen] = useState(false);
  const [asetLoading, setAsetLoading] = useState(false);
  const dropdownRef = useRef<HTMLDivElement>(null);

  const inputClass = "w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white disabled:bg-gray-100 disabled:text-gray-500";

  const fetchData = async (page = 1, search = searchQuery) => {
    setLoading(true);
    try {
      const res = await api.get(`/peminjaman?page=${page}&search=${encodeURIComponent(search)}`).catch(() => ({ data: { data: { data: [] } } }));
      const items = extractData(res.data.data);
      setData(items);
      const meta = res.data.data?.meta || res.data.meta;
      if (meta) {
        setCurrentPage(meta.current_page);
        setLastPage(meta.last_page);
      }
    } catch (error) {
      console.error('Gagal mengambil data peminjaman', error);
    } finally {
      setLoading(false);
    }
  };

  const fetchAset = async (search = '') => {
    setAsetLoading(true);
    try {
      const res = await api.get(`/aset?search=${encodeURIComponent(search)}&status=Di Gudang`).catch(() => ({ data: { data: [] } }));
      setAsetList(extractData(res.data.data));
    } catch {
      setAsetList([]);
    } finally {
      setAsetLoading(false);
    }
  };

  const fetchPengaturan = async () => {
    try {
      const res = await api.get('/pengaturan').catch(() => ({ data: { data: null } }));
      const d = res.data.data;
      setPengaturan(Array.isArray(d) ? d[0] || null : d);
    } catch {}
  };

  useEffect(() => { fetchData(); fetchPengaturan(); }, []);

  useEffect(() => {
    const delay = setTimeout(() => {
      if (asetDropdownOpen) fetchAset(asetSearch);
    }, 300);
    return () => clearTimeout(delay);
  }, [asetSearch, asetDropdownOpen]);

  useEffect(() => {
    const handler = (e: MouseEvent) => {
      if (dropdownRef.current && !dropdownRef.current.contains(e.target as Node)) {
        setAsetDropdownOpen(false);
      }
    };
    document.addEventListener('mousedown', handler);
    return () => document.removeEventListener('mousedown', handler);
  }, []);

  useEffect(() => {
    const delay = setTimeout(() => {
      fetchData(1, searchQuery);
    }, 500);
    return () => clearTimeout(delay);
  }, [searchQuery]);

  const openModal = () => {
    const today = new Date().toISOString().split('T')[0];
    setFormData({
      nomor_peminjaman: '',
      tanggal_pinjam: today,
      nama_peminjam: '',
      nomor_telepon: '',
      lama_pinjam_hari: '',
      keterangan: '',
    });
    setSelectedBarang([]);
    setAsetSearch('');
    setIsModalOpen(true);
  };

  const closeModal = () => {
    setIsModalOpen(false);
    setSelectedBarang([]);
  };

  const addBarang = (aset: any) => {
    if (selectedBarang.find((b) => b.kode_barang === aset.kode_barang)) return;
    setSelectedBarang([...selectedBarang, aset]);
    setAsetSearch('');
    setAsetDropdownOpen(false);
  };

  const removeBarang = (kodeBarang: string) => {
    setSelectedBarang(selectedBarang.filter((b) => b.kode_barang !== kodeBarang));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (selectedBarang.length === 0) {
      toast.error('Minimal pilih 1 barang untuk dipinjam.');
      return;
    }
    setIsSubmitting(true);
    try {
      const payload = {
        ...formData,
        lama_pinjam_hari: Number(formData.lama_pinjam_hari),
        detail: selectedBarang.map((b) => ({ kode_barang: b.kode_barang })),
      };
      const res = await api.post('/peminjaman', payload);
      toast.success('Data berhasil disimpan');
      closeModal();
      fetchData();
      if (res.data.data) {
        setPrintData(extractData(res.data.data));
      }
    } catch (error: any) {
      toast.error(error?.response?.data?.message || 'Gagal menyimpan peminjaman.');
    } finally {
      setIsSubmitting(false);
    }
  };

  const handlePrint = () => {
    if (!printData) return;
    const printWindow = window.open('', '_blank');
    if (!printWindow) return;

    const prefix = pengaturan?.kode_inventaris_prefix || 'INV';
    const items = printData.detail_peminjaman || [];

    printWindow.document.write(`
      <!DOCTYPE html>
      <html>
      <head>
        <title>Bukti Peminjaman - ${printData.nomor_peminjaman}</title>
        <style>
          * { margin: 0; padding: 0; box-sizing: border-box; }
          body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px; color: #1a1a1a; }
          .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #111; padding-bottom: 15px; }
          .header h1 { font-size: 18px; font-weight: 700; text-transform: uppercase; }
          .header p { font-size: 12px; color: #555; margin-top: 4px; }
          .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 30px; margin-bottom: 20px; font-size: 13px; }
          .info-grid .label { color: #666; }
          .info-grid .value { font-weight: 600; }
          table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 12px; }
          th, td { border: 1px solid #ccc; padding: 8px 10px; text-align: left; }
          th { background: #f3f4f6; font-weight: 600; }
          .footer { margin-top: 40px; font-size: 12px; }
          .signatures { display: flex; justify-content: space-between; margin-top: 50px; }
          .signatures .sig-block { text-align: center; width: 150px; }
          .signatures .sig-line { border-top: 1px solid #333; margin-top: 60px; padding-top: 5px; }
          @media print { body { padding: 20px; } }
        </style>
      </head>
      <body>
        <div class="header">
          <h1>${pengaturan?.nama_instansi || 'Nama Instansi'}</h1>
          <p>${pengaturan?.alamat_instansi || ''}</p>
          <p>${pengaturan?.kota ? 'Kota ' + pengaturan.kota : ''} ${pengaturan?.telpon ? '| Telp: ' + pengaturan.telpon : ''}</p>
        </div>

        <h2 style="text-align:center;font-size:16px;margin-bottom:20px;text-decoration:underline;">BUKTI PEMINJAMAN BARANG</h2>

        <div class="info-grid">
          <div><span class="label">Nomor Peminjaman</span></div>
          <div><span class="value">${printData.nomor_peminjaman}</span></div>
          <div><span class="label">Tanggal Pinjam</span></div>
          <div><span class="value">${formatDate(printData.tanggal_pinjam)}</span></div>
          <div><span class="label">Nama Peminjam</span></div>
          <div><span class="value">${printData.nama_peminjam}</span></div>
          <div><span class="label">No. Telepon</span></div>
          <div><span class="value">${printData.nomor_telepon || '-'}</span></div>
          <div><span class="label">Lama Pinjam</span></div>
          <div><span class="value">${printData.lama_pinjam_hari} hari</span></div>
          <div><span class="label">Keterangan</span></div>
          <div><span class="value">${printData.keterangan || '-'}</span></div>
        </div>

        <table>
          <thead>
            <tr>
              <th>No.</th>
              <th>Kode Inventaris</th>
              <th>Nama Barang</th>
              <th>Kondisi</th>
            </tr>
          </thead>
          <tbody>
            ${items.map((d: any, i: number) => `
              <tr>
                <td>${i + 1}</td>
                <td>${d.aset?.kode_inventaris || d.kode_barang}</td>
                <td>${d.aset?.master_barang?.nama_barang || '-'}</td>
                <td>${d.aset?.kondisi?.nama_kondisi || '-'}</td>
              </tr>
            `).join('')}
          </tbody>
        </table>

        <div class="signatures">
          <div class="sig-block">
            <div class="sig-line">Peminjam</div>
          </div>
          <div class="sig-block">
            <div class="sig-line">Mengetahui</div>
          </div>
          <div class="sig-block">
            <div class="sig-line">Penanggung Jawab</div>
          </div>
        </div>
      </body>
      </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => printWindow.print(), 500);
  };

  const activeLoans = data.filter((i) => i.status_peminjaman === 'Sedang Dipinjam');
  const returnedLoans = data.filter((i) => i.status_peminjaman === 'Dikembalikan');

  return (
    <div className="space-y-6 max-w-7xl mx-auto">
      {printData && (
        <div className="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center justify-between">
          <div>
            <p className="text-sm font-medium text-blue-900">Peminjaman <strong>{printData.nomor_peminjaman}</strong> berhasil dibuat.</p>
            <p className="text-xs text-blue-700 mt-0.5">Klik tombol cetak untuk mencetak bukti peminjaman.</p>
          </div>
          <div className="flex gap-2">
            <button onClick={handlePrint} className="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-md hover:bg-blue-700 cursor-pointer">
              <Printer className="h-3 w-3 mr-1" /> Cetak Bukti
            </button>
            <button onClick={() => setPrintData(null)} className="text-blue-400 hover:text-blue-600 cursor-pointer"><X className="h-4 w-4" /></button>
          </div>
        </div>
      )}

      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900 tracking-tight">Data Peminjaman</h1>
          <p className="text-sm text-gray-500 mt-1">Kelola peminjaman barang inventaris.</p>
        </div>
        <button onClick={openModal} className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-gray-900 text-white hover:bg-gray-800 h-10 px-4 py-2 cursor-pointer">
          <Plus className="mr-2 h-4 w-4" /> Tambah Peminjaman
        </button>
      </div>

      <div className="relative w-full sm:max-w-md">
        <Search className="absolute left-3 top-2.5 h-4 w-4 text-gray-400" />
        <input type="text" placeholder="Cari berdasarkan nomor, nama peminjam..." value={searchQuery} onChange={(e) => setSearchQuery(e.target.value)} className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 sm:text-sm" />
      </div>

      {/* Sedang Dipinjam */}
      <div>
        <h2 className="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Sedang Dipinjam ({activeLoans.length})</h2>
        <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full text-sm text-left">
              <thead className="text-xs text-gray-500 bg-gray-50 border-b border-gray-200 uppercase">
                <tr>
                  <th className="px-4 py-3 font-medium">No.</th>
                  <th className="px-4 py-3 font-medium">Nomor</th>
                  <th className="px-4 py-3 font-medium">Peminjam</th>
                  <th className="px-4 py-3 font-medium">Tgl Pinjam</th>
                  <th className="px-4 py-3 font-medium">Lama</th>
                  <th className="px-4 py-3 font-medium">Barang</th>
                  <th className="px-4 py-3 font-medium">Status</th>
                  <th className="px-4 py-3 font-medium text-right">Aksi</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {loading ? (
                  <tr><td colSpan={8} className="px-6 py-12 text-center"><Loader2 className="h-6 w-6 animate-spin mx-auto text-gray-400" /></td></tr>
                ) : activeLoans.length === 0 ? (
                  <tr><td colSpan={8} className="px-6 py-12 text-center text-gray-500">Tidak ada peminjaman aktif.</td></tr>
                ) : activeLoans.map((item, i) => (
                  <tr key={item.nomor_peminjaman || i} className="hover:bg-gray-50 transition-colors">
                    <td className="px-4 py-3 text-gray-500">{i + 1}</td>
                    <td className="px-4 py-3 font-medium text-gray-900">{item.nomor_peminjaman}</td>
                    <td className="px-4 py-3 text-gray-900">{item.nama_peminjam}</td>
                    <td className="px-4 py-3 text-gray-500">{formatDate(item.tanggal_pinjam)}</td>
                    <td className="px-4 py-3 text-gray-500">{item.lama_pinjam_hari} hari</td>
                    <td className="px-4 py-3 text-gray-500">{item.detail_peminjaman?.length || 0} barang</td>
                    <td className="px-4 py-3">
                      <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{item.status_peminjaman}</span>
                    </td>
                    <td className="px-4 py-3 text-right">
                      <button onClick={() => { setSelectedItem(item); setIsDetailOpen(true); }} className="text-gray-400 hover:text-blue-600 mr-3 cursor-pointer" title="Rincian"><Eye className="h-4 w-4" /></button>
                      <button onClick={() => { setPrintData(item); }} className="text-gray-400 hover:text-blue-600 cursor-pointer" title="Cetak"><Printer className="h-4 w-4" /></button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {/* Sudah Dikembalikan */}
      {returnedLoans.length > 0 && (
        <div>
          <h2 className="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Sudah Dikembalikan ({returnedLoans.length})</h2>
          <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div className="overflow-x-auto">
              <table className="w-full text-sm text-left">
                <thead className="text-xs text-gray-500 bg-gray-50 border-b border-gray-200 uppercase">
                  <tr>
                    <th className="px-4 py-3 font-medium">No.</th>
                    <th className="px-4 py-3 font-medium">Nomor</th>
                    <th className="px-4 py-3 font-medium">Peminjam</th>
                    <th className="px-4 py-3 font-medium">Tgl Pinjam</th>
                    <th className="px-4 py-3 font-medium">Status</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200">
                  {returnedLoans.map((item, i) => (
                    <tr key={item.nomor_peminjaman || i} className="hover:bg-gray-50 transition-colors">
                      <td className="px-4 py-3 text-gray-500">{i + 1}</td>
                      <td className="px-4 py-3 font-medium text-gray-900">{item.nomor_peminjaman}</td>
                      <td className="px-4 py-3 text-gray-900">{item.nama_peminjam}</td>
                      <td className="px-4 py-3 text-gray-500">{formatDate(item.tanggal_pinjam)}</td>
                      <td className="px-4 py-3">
                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{item.status_peminjaman}</span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}

      {/* Detail Modal */}
      {isDetailOpen && selectedItem && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
              <h3 className="text-lg font-semibold text-gray-900">Detail Peminjaman</h3>
              <button onClick={() => { setIsDetailOpen(false); setSelectedItem(null); }} className="text-gray-400 hover:text-gray-500"><X className="h-5 w-5" /></button>
            </div>
            <div className="p-6 space-y-4 text-sm overflow-y-auto">
              {[
                ['Nomor Peminjaman', selectedItem.nomor_peminjaman],
                ['Nama Peminjam', selectedItem.nama_peminjam],
                ['No. Telepon', selectedItem.nomor_telepon],
                ['Tanggal Pinjam', formatDate(selectedItem.tanggal_pinjam)],
                ['Lama Pinjam', `${selectedItem.lama_pinjam_hari} hari`],
                ['Status', selectedItem.status_peminjaman],
                ['Keterangan', selectedItem.keterangan],
              ].map(([label, value]) => (
                <div key={label} className="flex justify-between">
                  <span className="text-gray-500">{label}</span>
                  <span className="text-gray-900 font-medium text-right">{value || '-'}</span>
                </div>
              ))}
              {selectedItem.detail_peminjaman?.length > 0 && (
                <div className="pt-2">
                  <span className="text-gray-500 text-xs uppercase tracking-wider">Barang Dipinjam:</span>
                  <ul className="mt-2 space-y-1">
                    {selectedItem.detail_peminjaman.map((d: any, idx: number) => (
                      <li key={idx} className="text-gray-900 bg-gray-50 p-2 rounded-md border border-gray-100 text-xs">
                        {d.aset?.kode_inventaris || d.kode_barang} - {d.aset?.master_barang?.nama_barang || 'Barang'}
                        {d.aset?.kondisi?.nama_kondisi ? ` (${d.aset.kondisi.nama_kondisi})` : ''}
                      </li>
                    ))}
                  </ul>
                </div>
              )}
            </div>
            <div className="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50 shrink-0">
              <button onClick={() => { setPrintData(selectedItem); setIsDetailOpen(false); setSelectedItem(null); }} className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium transition-colors flex items-center cursor-pointer">
                <Printer className="h-4 w-4 mr-1" /> Cetak
              </button>
              <button onClick={() => { setIsDetailOpen(false); setSelectedItem(null); }} className="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 font-medium cursor-pointer">Tutup</button>
            </div>
          </div>
        </div>
      )}

      {/* Modal Form Tambah Peminjaman */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4 py-8">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-full flex flex-col">
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-100 shrink-0">
              <h3 className="text-lg font-semibold text-gray-900">Tambah Peminjaman</h3>
              <button onClick={closeModal} className="text-gray-400 hover:text-gray-500"><X className="h-5 w-5" /></button>
            </div>
            <div className="overflow-y-auto p-6">
              <form id="peminjamanForm" onSubmit={handleSubmit} className="space-y-5 text-sm">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Nomor Peminjaman *</label>
                    <input type="text" required value={formData.nomor_peminjaman} onChange={(e) => setFormData({ ...formData, nomor_peminjaman: e.target.value })} className={inputClass} placeholder="PJM-2026-001" />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Tanggal Pinjam *</label>
                    <input type="date" required value={formData.tanggal_pinjam} onChange={(e) => setFormData({ ...formData, tanggal_pinjam: e.target.value })} className={inputClass} />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Nama Peminjam *</label>
                    <input type="text" required value={formData.nama_peminjam} onChange={(e) => setFormData({ ...formData, nama_peminjam: e.target.value })} className={inputClass} placeholder="Nama peminjam" />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">No. Telepon</label>
                    <input type="text" value={formData.nomor_telepon} onChange={(e) => setFormData({ ...formData, nomor_telepon: e.target.value })} className={inputClass} placeholder="081234567890" />
                  </div>
                  <div>
                    <label className="block font-medium text-gray-700 mb-1">Lama Pinjam (Hari) *</label>
                    <input type="number" required min="1" value={formData.lama_pinjam_hari} onChange={(e) => setFormData({ ...formData, lama_pinjam_hari: e.target.value })} className={inputClass} placeholder="7" />
                  </div>
                </div>

                {/* Pilih Barang */}
                <div>
                  <label className="block font-medium text-gray-700 mb-1">Barang yang Dipinjam *</label>
                  <div ref={dropdownRef} className="relative">
                    <input
                      type="text"
                      value={asetSearch}
                      onFocus={() => { setAsetDropdownOpen(true); fetchAset(asetSearch); }}
                      onChange={(e) => { setAsetSearch(e.target.value); setAsetDropdownOpen(true); }}
                      className={inputClass}
                      placeholder="Ketik nama atau kode barang..."
                    />
                    {asetDropdownOpen && (
                      <div className="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-y-auto">
                        {asetLoading ? (
                          <div className="px-4 py-3 text-center text-gray-500 text-xs"><Loader2 className="h-4 w-4 animate-spin mx-auto" /></div>
                        ) : asetList.length === 0 ? (
                          <div className="px-4 py-3 text-center text-gray-500 text-xs">Tidak ada barang ditemukan.</div>
                        ) : asetList.map((a) => (
                          <button
                            key={a.kode_barang}
                            type="button"
                            onClick={() => addBarang(a)}
                            disabled={selectedBarang.some((b) => b.kode_barang === a.kode_barang)}
                            className="w-full text-left px-4 py-2 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed border-b border-gray-50 last:border-0 cursor-pointer"
                          >
                            <div className="flex items-center justify-between">
                              <div>
                                <span className="text-xs font-mono text-gray-500">{a.kode_inventaris || a.kode_barang}</span>
                                <span className="text-sm font-medium text-gray-900 ml-2">{a.master_barang?.nama_barang || 'Barang'}</span>
                              </div>
                              <span className="text-xs text-gray-500">{a.kondisi?.nama_kondisi || ''}</span>
                            </div>
                          </button>
                        ))}
                      </div>
                    )}
                  </div>

                  {/* Selected Barang */}
                  {selectedBarang.length > 0 && (
                    <div className="mt-2 space-y-1">
                      {selectedBarang.map((b) => (
                        <div key={b.kode_barang} className="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-md px-3 py-2">
                          <div className="text-xs">
                            <span className="font-mono text-gray-500">{b.kode_inventaris || b.kode_barang}</span>
                            <span className="font-medium text-gray-900 ml-2">{b.master_barang?.nama_barang || 'Barang'}</span>
                            {b.kondisi?.nama_kondisi && <span className="text-gray-500 ml-1">({b.kondisi.nama_kondisi})</span>}
                          </div>
                          <button type="button" onClick={() => removeBarang(b.kode_barang)} className="text-gray-400 hover:text-red-500 cursor-pointer"><X className="h-3 w-3" /></button>
                        </div>
                      ))}
                    </div>
                  )}
                </div>

                <div>
                  <label className="block font-medium text-gray-700 mb-1">Keterangan</label>
                  <textarea rows={2} value={formData.keterangan} onChange={(e) => setFormData({ ...formData, keterangan: e.target.value })} className={inputClass} placeholder="Catatan tambahan (opsional)" />
                </div>
              </form>
            </div>
            <div className="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50 shrink-0 rounded-b-xl">
              <button type="button" onClick={closeModal} className="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 font-medium cursor-pointer">Batal</button>
              <button form="peminjamanForm" type="submit" disabled={isSubmitting} className="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 font-medium transition-colors flex items-center disabled:opacity-50 cursor-pointer">
                {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                Simpan
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
