"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { getCurrentUser, logout, TOKEN_KEY, type CurrentUser } from "@/lib/api";

export default function DashboardPage() {
  const router = useRouter();
  const [user, setUser] = useState<CurrentUser | null>(null);
  const [loading, setLoading] = useState(true);

  function handleLogout() {
    logout();
    router.replace("/login");
  }

  useEffect(() => {
    async function load() {
      try {
        setUser(await getCurrentUser());
      } catch {
        // Token tidak ada / tidak valid: bersihkan lalu kembali ke login.
        localStorage.removeItem(TOKEN_KEY);
        router.replace("/login");
      } finally {
        setLoading(false);
      }
    }
    load();
  }, [router]);

  if (loading) {
    return (
      <div className="flex min-h-full flex-1 items-center justify-center bg-zinc-50 dark:bg-black">
        <p className="text-sm text-zinc-600 dark:text-zinc-400">
          Memuat data...
        </p>
      </div>
    );
  }

  // Redirect ke /login sedang berjalan.
  if (!user) {
    return null;
  }

  return (
    <div className="flex min-h-full flex-1 items-center justify-center bg-zinc-50 px-4 py-12 dark:bg-black">
      <main className="w-full max-w-md rounded-xl border border-zinc-200 bg-white p-8 dark:border-zinc-800 dark:bg-zinc-950">
        <h1 className="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-50">
          Halo, {user.name}
        </h1>
        <p className="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
          {user.email}
        </p>
        <button
          type="button"
          onClick={handleLogout}
          className="mt-6 h-11 w-full rounded-md border border-zinc-300 text-sm font-medium text-zinc-900 transition-colors hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-50 dark:hover:bg-zinc-800"
        >
          Logout
        </button>
      </main>
    </div>
  );
}
