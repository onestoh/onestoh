export type UserRole = 'client' | 'owner' | 'admin' | 'broker';

export type BookingStatus =
  | 'pending'
  | 'confirmed'
  | 'active'
  | 'completed'
  | 'cancelled'
  | 'disputed';

export type RentalType = 'self_drive' | 'chauffeur';

export interface User {
  id: string;
  name: string;
  email: string;
  phone: string;
  role: UserRole;
  kycStatus: 'pending' | 'approved' | 'rejected';
  avatarUrl?: string;
  walletBalanceKES: number;
}

export interface Asset {
  id: string;
  ownerId: string;
  name: string;
  make: string;
  model: string;
  year: number;
  category: string;
  description: string;
  location: string;
  county: string;
  images: string[];
  hourlyRateKES?: number;
  dailyRateKES: number;
  weeklyRateKES?: number;
  monthlyRateKES?: number;
  isAvailable: boolean;
  utilisation: number;
  rating: number;
  reviewCount: number;
  specs: Record<string, string | number>;
}

export interface Booking {
  id: string;
  assetId: string;
  assetName: string;
  assetImage?: string;
  clientId: string;
  ownerId: string;
  rentalType: RentalType;
  fromDate: string;
  toDate: string;
  totalKES: number;
  status: BookingStatus;
  pickupLocation: string;
  driverName?: string;
  createdAt: string;
}

export interface Transaction {
  id: string;
  type: 'credit' | 'debit';
  amountKES: number;
  description: string;
  reference: string;
  createdAt: string;
  status: 'completed' | 'pending' | 'failed';
}

export interface Notification {
  id: string;
  title: string;
  body: string;
  read: boolean;
  createdAt: string;
  data?: Record<string, string>;
}
