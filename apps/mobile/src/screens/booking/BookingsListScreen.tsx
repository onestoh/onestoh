import React, { useState, useEffect, useCallback } from 'react';
import { View, Text, FlatList, StyleSheet, RefreshControl, ActivityIndicator, TouchableOpacity } from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { BookingsStackParamList } from '../../navigation/AppNavigator';
import { bookingsApi } from '../../lib/api';
import { Booking, BookingStatus } from '../../lib/types';
import BookingCard from '../../components/BookingCard';

type Props = {
  navigation: NativeStackNavigationProp<BookingsStackParamList, 'BookingsList'>;
};

type Tab = 'upcoming' | 'active' | 'completed';

const TAB_STATUSES: Record<Tab, BookingStatus[]> = {
  upcoming: ['pending', 'confirmed'],
  active: ['active'],
  completed: ['completed', 'cancelled', 'disputed'],
};

export default function BookingsListScreen({ navigation }: Props) {
  const [bookings, setBookings] = useState<Booking[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [activeTab, setActiveTab] = useState<Tab>('upcoming');

  const fetchBookings = useCallback(async () => {
    try {
      const res = await bookingsApi.list();
      setBookings(res.data.bookings ?? []);
    } catch {
      // silently fail
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => {
    fetchBookings();
  }, [fetchBookings]);

  const filtered = bookings.filter((b) => TAB_STATUSES[activeTab].includes(b.status));

  return (
    <View style={styles.container}>
      {/* Tabs */}
      <View style={styles.tabs}>
        {(['upcoming', 'active', 'completed'] as Tab[]).map((tab) => (
          <TouchableOpacity
            key={tab}
            style={[styles.tab, activeTab === tab && styles.tabActive]}
            onPress={() => setActiveTab(tab)}
          >
            <Text style={[styles.tabText, activeTab === tab && styles.tabTextActive]}>
              {tab.charAt(0).toUpperCase() + tab.slice(1)}
            </Text>
          </TouchableOpacity>
        ))}
      </View>

      {loading ? (
        <View style={styles.centered}>
          <ActivityIndicator size="large" color="#E8922A" />
        </View>
      ) : (
        <FlatList
          data={filtered}
          keyExtractor={(item) => item.id}
          renderItem={({ item }) => (
            <BookingCard
              booking={item}
              onPress={() => navigation.navigate('BookingDetail', { bookingId: item.id })}
            />
          )}
          contentContainerStyle={styles.listContent}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); fetchBookings(); }} tintColor="#E8922A" />
          }
          ListEmptyComponent={
            <View style={styles.empty}>
              <Text style={styles.emptyText}>No {activeTab} bookings</Text>
            </View>
          }
        />
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#080C12' },
  tabs: { flexDirection: 'row', padding: 16, gap: 8 },
  tab: { flex: 1, paddingVertical: 9, backgroundColor: '#141D2B', borderRadius: 10, alignItems: 'center', borderWidth: 1, borderColor: '#1e2d40' },
  tabActive: { backgroundColor: '#E8922A', borderColor: '#E8922A' },
  tabText: { fontSize: 13, fontWeight: '600', color: '#9ca3af' },
  tabTextActive: { color: '#ffffff' },
  listContent: { padding: 16, paddingTop: 0, gap: 10 },
  centered: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  empty: { paddingTop: 60, alignItems: 'center' },
  emptyText: { color: '#6b7280', fontSize: 15 },
});
