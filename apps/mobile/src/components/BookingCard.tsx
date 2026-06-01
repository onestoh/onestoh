import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { Booking } from '../lib/types';
import StatusBadge from './StatusBadge';

interface BookingCardProps {
  booking: Booking;
  onPress: () => void;
}

export default function BookingCard({ booking, onPress }: BookingCardProps) {
  return (
    <TouchableOpacity style={styles.card} onPress={onPress} activeOpacity={0.85}>
      <View style={styles.topRow}>
        <Text style={styles.assetName} numberOfLines={1}>{booking.assetName}</Text>
        <StatusBadge status={booking.status} small />
      </View>
      <View style={styles.dateRow}>
        <Text style={styles.dateText}>{booking.fromDate}</Text>
        <Text style={styles.dateSep}>→</Text>
        <Text style={styles.dateText}>{booking.toDate}</Text>
      </View>
      <View style={styles.bottomRow}>
        <Text style={styles.location} numberOfLines={1}>📍 {booking.pickupLocation}</Text>
        <Text style={styles.total}>KES {booking.totalKES.toLocaleString()}</Text>
      </View>
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  card: { backgroundColor: '#141D2B', borderRadius: 14, padding: 14, borderWidth: 1, borderColor: '#1e2d40', gap: 8 },
  topRow: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  assetName: { flex: 1, fontSize: 15, fontWeight: '700', color: '#ffffff' },
  dateRow: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  dateText: { fontSize: 13, color: '#9ca3af', fontWeight: '500' },
  dateSep: { color: '#6b7280' },
  bottomRow: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  location: { flex: 1, fontSize: 12, color: '#6b7280', marginRight: 8 },
  total: { fontSize: 15, fontWeight: '800', color: '#E8922A' },
});
