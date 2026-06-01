import axios, { InternalAxiosRequestConfig } from 'axios';
import { MMKV } from 'react-native-mmkv';

export const storage = new MMKV({ id: 'toy-storage' });

const TOKEN_KEY = 'auth_token';

export const getToken = (): string | undefined => storage.getString(TOKEN_KEY);
export const setToken = (token: string) => storage.set(TOKEN_KEY, token);
export const clearToken = () => storage.delete(TOKEN_KEY);

const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL ?? 'https://api.theonlineyard.co.ke/v1',
  timeout: 15000,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
});

// Attach auth token to every request
api.interceptors.request.use((config: InternalAxiosRequestConfig) => {
  const token = getToken();
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Global error handler
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      clearToken();
      // Navigation to login handled by auth store subscriber
    }
    return Promise.reject(error);
  },
);

// ---- Auth ----
export const authApi = {
  login: (data: { identifier: string; password: string }) =>
    api.post('/auth/login', data),
  register: (data: {
    name: string;
    email: string;
    phone: string;
    password: string;
    role: string;
  }) => api.post('/auth/register', data),
  verifyOtp: (data: { phone: string; otp: string }) =>
    api.post('/auth/verify-otp', data),
  resendOtp: (data: { phone: string }) => api.post('/auth/resend-otp', data),
  me: () => api.get('/auth/me'),
};

// ---- KYC ----
export const kycApi = {
  upload: (formData: FormData) =>
    api.post('/kyc/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }),
  status: () => api.get('/kyc/status'),
};

// ---- Listings / Assets ----
export const assetsApi = {
  list: (params?: {
    category?: string;
    search?: string;
    page?: number;
    limit?: number;
  }) => api.get('/assets', { params }),
  detail: (id: string) => api.get(`/assets/${id}`),
  availability: (id: string, params: { from: string; to: string }) =>
    api.get(`/assets/${id}/availability`, { params }),
  ownerList: () => api.get('/owner/assets'),
  create: (data: FormData) =>
    api.post('/owner/assets', data, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }),
  update: (id: string, data: Partial<unknown>) =>
    api.patch(`/owner/assets/${id}`, data),
};

// ---- Bookings ----
export const bookingsApi = {
  create: (data: {
    assetId: string;
    fromDate: string;
    toDate: string;
    rentalType: string;
    insuranceProducts: string[];
  }) => api.post('/bookings', data),
  list: (params?: { status?: string }) => api.get('/bookings', { params }),
  detail: (id: string) => api.get(`/bookings/${id}`),
  initiatePayment: (bookingId: string, data: { phone: string; method: 'mpesa' | 'card' }) =>
    api.post(`/bookings/${bookingId}/pay`, data),
  pollPaymentStatus: (bookingId: string) =>
    api.get(`/bookings/${bookingId}/payment-status`),
  uploadPhoto: (bookingId: string, type: 'pre' | 'post', formData: FormData) =>
    api.post(`/bookings/${bookingId}/photos/${type}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }),
  raiseDispute: (bookingId: string, data: { reason: string; description: string }) =>
    api.post(`/bookings/${bookingId}/dispute`, data),
};

// ---- Wallet ----
export const walletApi = {
  balance: () => api.get('/wallet/balance'),
  transactions: (params?: { page?: number; limit?: number }) =>
    api.get('/wallet/transactions', { params }),
  requestPayout: (data: { amountKES: number; accountNumber: string; accountName: string }) =>
    api.post('/wallet/payout', data),
};

export default api;
