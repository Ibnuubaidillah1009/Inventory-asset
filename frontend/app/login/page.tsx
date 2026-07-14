"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { Loader2 } from "lucide-react";
import api from "@/utils/api";

export default function LoginPage() {
    const router = useRouter();
    const [username, setUsername] = useState("");
    const [password, setPassword] = useState("");
    const [error, setError] = useState<string | null>(null);
    const [loading, setLoading] = useState(false);

    const handleLogin = async (e: React.FormEvent) => {
        e.preventDefault();
        setError(null);

        if (!username || !password) {
            setError("Username dan password wajib diisi");
            return;
        }

        setLoading(true);

        try {
            // 1. Hit API Login
            const response = await api.post("/login", {
                username,
                password,
            });

            const result = response.data.data || response.data;

            if (result.token) {
                // 2. WAJIB: Simpan token dulu agar axios interceptor bisa langsung memakainya untuk request selanjutnya
                localStorage.setItem("token", result.token);

                try {
                    // 3. Langsung hit endpoint /me untuk mendapatkan profil lengkap beserta hak akses
                    const meResponse = await api.get("/me");
                    const fullUserData = meResponse.data.data;

                    console.log("Data Get Me:", fullUserData);

                    // 4. Simpan data lengkap ke localStorage
                    localStorage.setItem("user", JSON.stringify(fullUserData));
                    if (meResponse.data.menu_tree) {
                        localStorage.setItem("menu_tree", JSON.stringify(meResponse.data.menu_tree));
                    }

                    // 5. Lempar ke dashboard
                    router.push("/dashboard");
                } catch (errMe) {
                    console.error("Gagal mengambil data /me:", errMe);
                    setError("Gagal mengambil profil pengguna yang lengkap.");
                    localStorage.removeItem("token"); // Rollback jika gagal
                }
            } else {
                setError("Respons server tidak valid: Token tidak ditemukan");
            }
        } catch (error) {
            const err = error as { response?: { data?: { message?: string } }; message?: string };
            console.error("Login Error:", err.response?.data || err.message);
            if (
                err.response &&
                err.response.data &&
                err.response.data.message
            ) {
                setError(err.response.data.message);
            } else {
                setError("Terjadi kesalahan saat login");
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="flex h-screen w-full items-center justify-center bg-gray-50 px-4">
            <div className="w-full max-w-md bg-white rounded-xl border border-gray-200 shadow-sm p-8">
                <div className="mb-8 text-center">
                    <h1 className="text-2xl font-semibold text-gray-900">
                        Inventaris Sekolah
                    </h1>
                    <p className="text-sm text-gray-500 mt-2">
                        Masuk ke akun Anda
                    </p>
                </div>

                <form onSubmit={handleLogin} className="space-y-4 text-sm">
                    <div>
                        <label
                            htmlFor="username"
                            className="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Username
                        </label>
                        <input
                            id="username"
                            type="text"
                            value={username}
                            onChange={(e) => setUsername(e.target.value)}
                            className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-400 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                            placeholder="Masukkan username" required
                        />
                    </div>

                    <div>
                        <label
                            htmlFor="password"
                            className="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Password
                        </label>
                        <input
                            id="password"
                            type="password"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            className="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-400 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                            placeholder="Masukkan password" required
                        />
                        {error && (
                            <p className="mt-2 text-xs text-red-600">{error}</p>
                        )}
                    </div>

                    <button
                        type="submit"
                        disabled={loading}
                        className="w-full rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center mt-6"
                    >
                        {loading ? (
                            <>
                                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                Masuk...
                            </>
                        ) : (
                            "Masuk"
                        )}
                    </button>
                </form>
            </div>
        </div>
    );
}
