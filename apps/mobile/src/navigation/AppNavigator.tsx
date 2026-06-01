import React from 'react';
import { ActivityIndicator, View } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { useAuthStore } from '../lib/store';

// Auth Screens
import LoginScreen from '../screens/auth/LoginScreen';
import RegisterScreen from '../screens/auth/RegisterScreen';
import OTPScreen from '../screens/auth/OTPScreen';
import KYCScreen from '../screens/auth/KYCScreen';

// Main Tab Screens
import MarketplaceScreen from '../screens/marketplace/MarketplaceScreen';
import BookingsListScreen from '../screens/booking/BookingsListScreen';
import WalletScreen from '../screens/wallet/WalletScreen';
import ProfileScreen from '../screens/profile/ProfileScreen';

// Detail Screens
import ListingDetailScreen from '../screens/marketplace/ListingDetailScreen';
import BookingScreen from '../screens/booking/BookingScreen';
import MpesaPaymentScreen from '../screens/booking/MpesaPaymentScreen';
import BookingDetailScreen from '../screens/booking/BookingDetailScreen';

// Owner Screens
import FleetScreen from '../screens/owner/FleetScreen';
import AddListingScreen from '../screens/owner/AddListingScreen';

export type AuthStackParamList = {
  Login: undefined;
  Register: undefined;
  OTPVerify: { phone: string };
  KYC: undefined;
};

export type MarketplaceStackParamList = {
  MarketplaceHome: undefined;
  ListingDetail: { assetId: string };
  Booking: { assetId: string };
  MpesaPayment: { bookingId: string; phone: string; amountKES: number };
};

export type BookingsStackParamList = {
  BookingsList: undefined;
  BookingDetail: { bookingId: string };
};

export type OwnerStackParamList = {
  Fleet: undefined;
  AddListing: undefined;
};

export type MainTabParamList = {
  Marketplace: undefined;
  Bookings: undefined;
  Wallet: undefined;
  Profile: undefined;
};

const AuthStack = createNativeStackNavigator<AuthStackParamList>();
const MarketplaceStack = createNativeStackNavigator<MarketplaceStackParamList>();
const BookingsStack = createNativeStackNavigator<BookingsStackParamList>();
const OwnerStack = createNativeStackNavigator<OwnerStackParamList>();
const Tab = createBottomTabNavigator<MainTabParamList>();

function AuthNavigator() {
  return (
    <AuthStack.Navigator screenOptions={{ headerShown: false }}>
      <AuthStack.Screen name="Login" component={LoginScreen} />
      <AuthStack.Screen name="Register" component={RegisterScreen} />
      <AuthStack.Screen name="OTPVerify" component={OTPScreen} />
      <AuthStack.Screen name="KYC" component={KYCScreen} />
    </AuthStack.Navigator>
  );
}

function MarketplaceNavigator() {
  return (
    <MarketplaceStack.Navigator
      screenOptions={{
        headerStyle: { backgroundColor: '#141D2B' },
        headerTintColor: '#E8922A',
        headerTitleStyle: { color: '#ffffff' },
      }}
    >
      <MarketplaceStack.Screen
        name="MarketplaceHome"
        component={MarketplaceScreen}
        options={{ title: 'TheOnlineYard' }}
      />
      <MarketplaceStack.Screen
        name="ListingDetail"
        component={ListingDetailScreen}
        options={{ title: 'Listing Details' }}
      />
      <MarketplaceStack.Screen
        name="Booking"
        component={BookingScreen}
        options={{ title: 'Book Asset' }}
      />
      <MarketplaceStack.Screen
        name="MpesaPayment"
        component={MpesaPaymentScreen}
        options={{ title: 'M-Pesa Payment', headerBackVisible: false }}
      />
    </MarketplaceStack.Navigator>
  );
}

function BookingsNavigator() {
  return (
    <BookingsStack.Navigator
      screenOptions={{
        headerStyle: { backgroundColor: '#141D2B' },
        headerTintColor: '#E8922A',
        headerTitleStyle: { color: '#ffffff' },
      }}
    >
      <BookingsStack.Screen
        name="BookingsList"
        component={BookingsListScreen}
        options={{ title: 'My Bookings' }}
      />
      <BookingsStack.Screen
        name="BookingDetail"
        component={BookingDetailScreen}
        options={{ title: 'Booking Details' }}
      />
    </BookingsStack.Navigator>
  );
}

function OwnerNavigator() {
  return (
    <OwnerStack.Navigator
      screenOptions={{
        headerStyle: { backgroundColor: '#141D2B' },
        headerTintColor: '#E8922A',
        headerTitleStyle: { color: '#ffffff' },
      }}
    >
      <OwnerStack.Screen name="Fleet" component={FleetScreen} options={{ title: 'My Fleet' }} />
      <OwnerStack.Screen name="AddListing" component={AddListingScreen} options={{ title: 'Add Listing' }} />
    </OwnerStack.Navigator>
  );
}

function MainTabs() {
  const user = useAuthStore((s) => s.user);
  const isOwner = user?.role === 'owner';

  return (
    <Tab.Navigator
      screenOptions={{
        headerShown: false,
        tabBarStyle: {
          backgroundColor: '#141D2B',
          borderTopColor: '#1e2d40',
        },
        tabBarActiveTintColor: '#E8922A',
        tabBarInactiveTintColor: '#6b7280',
      }}
    >
      <Tab.Screen
        name="Marketplace"
        component={isOwner ? OwnerNavigator : MarketplaceNavigator}
        options={{ tabBarLabel: isOwner ? 'Fleet' : 'Browse' }}
      />
      <Tab.Screen name="Bookings" component={BookingsNavigator} />
      <Tab.Screen name="Wallet" component={WalletScreen} />
      <Tab.Screen name="Profile" component={ProfileScreen} />
    </Tab.Navigator>
  );
}

export default function AppNavigator() {
  const { isAuthenticated, user, isLoading } = useAuthStore();

  if (isLoading) {
    return (
      <View style={{ flex: 1, backgroundColor: '#080C12', alignItems: 'center', justifyContent: 'center' }}>
        <ActivityIndicator size="large" color="#E8922A" />
      </View>
    );
  }

  return (
    <NavigationContainer>
      {!isAuthenticated ? (
        <AuthNavigator />
      ) : user?.kycStatus !== 'approved' && user?.role !== 'admin' ? (
        <AuthStack.Navigator screenOptions={{ headerShown: false }}>
          <AuthStack.Screen name="KYC" component={KYCScreen} />
        </AuthStack.Navigator>
      ) : (
        <MainTabs />
      )}
    </NavigationContainer>
  );
}
