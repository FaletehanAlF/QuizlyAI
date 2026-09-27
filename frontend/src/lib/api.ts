// API client sederhana untuk QuizlyAI backend (PHP Native REST API).

export const API_BASE_URL = "http://127.0.0.1:8000";

export interface LoginUser {
  id: number;
  name: string;
  email: string;
}

export interface LoginResponse {
  message: string;
  token: string;
  user: LoginUser;
}

export interface CurrentUser {
  id: number;
  name: string;
  email: string;
  created_at: string;
}

export interface MeResponse {
  user: CurrentUser;
}

// Key penyimpanan JWT di localStorage. Hanya token yang disimpan.
export const TOKEN_KEY = "quizlyai_token";

export interface RegisterResponse {
  message: string;
}

// POST /api/auth/login.php
// Berhasil: return data backend (message, token, user).
// Gagal: throw Error dengan message dari response JSON jika tersedia.
export async function login(
  email: string,
  password: string
): Promise<LoginResponse> {
  const res = await fetch(`${API_BASE_URL}/api/auth/login.php`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ email, password }),
  });

  let data: LoginResponse & { message?: string };
  try {
    data = await res.json();
  } catch {
    throw new Error(`Login gagal (HTTP ${res.status})`);
  }

  if (!res.ok) {
    throw new Error(data.message || `Login gagal (HTTP ${res.status})`);
  }

  return data as LoginResponse;
}

// GET /api/auth/me.php
// Token diambil dari localStorage. Token tidak ada → throw Error yang jelas.
// Response bukan 2xx → throw Error dengan message backend jika tersedia.
export async function getCurrentUser(): Promise<CurrentUser> {
  const token = localStorage.getItem(TOKEN_KEY);

  if (!token) {
    throw new Error("Token tidak ditemukan, silakan login kembali");
  }

  const res = await fetch(`${API_BASE_URL}/api/auth/me.php`, {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
    },
  });

  let data: MeResponse & { message?: string };
  try {
    data = await res.json();
  } catch {
    throw new Error(`Gagal memuat data user (HTTP ${res.status})`);
  }

  if (!res.ok) {
    throw new Error(data.message || `Gagal memuat data user (HTTP ${res.status})`);
  }

  return data.user;
}

// Logout client-side: hapus JWT dari localStorage.
// Tidak ada request ke backend karena JWT bersifat stateless.
export function logout(): void {
  localStorage.removeItem(TOKEN_KEY);
}

// POST /api/auth/register.php
// Berhasil: return data response (message).
// Gagal: throw Error dengan message dari response JSON jika tersedia.
// Catatan: backend saat ini mengembalikan body kosong saat sukses,
// jadi body kosong pada response 2xx dianggap berhasil.
export async function register(
  name: string,
  email: string,
  password: string
): Promise<RegisterResponse> {
  const res = await fetch(`${API_BASE_URL}/api/auth/register.php`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ name, email, password }),
  });

  let data: RegisterResponse & { message?: string };
  try {
    const text = await res.text();
    data = text ? JSON.parse(text) : { message: "" };
  } catch {
    data = { message: "" };
  }

  if (!res.ok) {
    throw new Error(data.message || `Registrasi gagal (HTTP ${res.status})`);
  }

  return { message: data.message || "Akun berhasil dibuat" };
}
