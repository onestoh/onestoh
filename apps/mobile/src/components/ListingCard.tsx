import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, Image } from 'react-native';
import { Asset } from '../lib/types';

interface ListingCardProps {
  asset: Asset;
  onPress: () => void;
}

export default function ListingCard({ asset, onPress }: ListingCardProps) {
  return (
    <TouchableOpacity style={styles.card} onPress={onPress} activeOpacity={0.85}>
      <Image
        source={{ uri: asset.images[0] }}
        style={styles.image}
        defaultSource={require('../assets/placeholder.png')}
      />
      <View style={styles.body}>
        <View style={styles.topRow}>
          <Text style={styles.name} numberOfLines={1}>{asset.name}</Text>
          <View style={styles.ratingBadge}>
            <Text style={styles.ratingText}>⭐ {asset.rating.toFixed(1)}</Text>
          </View>
        </View>
        <Text style={styles.location} numberOfLines={1}>📍 {asset.location}</Text>
        <View style={styles.bottomRow}>
          <View>
            <Text style={styles.price}>KES {asset.dailyRateKES.toLocaleString()}</Text>
            <Text style={styles.priceLabel}>/day</Text>
          </View>
          <View style={[styles.availBadge, !asset.isAvailable && styles.unavailBadge]}>
            <Text style={styles.availText}>{asset.isAvailable ? 'Available' : 'Unavailable'}</Text>
          </View>
        </View>
      </View>
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  card: { backgroundColor: '#141D2B', borderRadius: 16, overflow: 'hidden', borderWidth: 1, borderColor: '#1e2d40' },
  image: { width: '100%', height: 180, backgroundColor: '#0f1621' },
  body: { padding: 14, gap: 6 },
  topRow: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  name: { flex: 1, fontSize: 16, fontWeight: '700', color: '#ffffff' },
  ratingBadge: { backgroundColor: '#2a1a08', paddingHorizontal: 8, paddingVertical: 3, borderRadius: 20 },
  ratingText: { fontSize: 12, color: '#E8922A', fontWeight: '600' },
  location: { fontSize: 13, color: '#9ca3af' },
  bottomRow: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginTop: 4 },
  price: { fontSize: 18, fontWeight: '800', color: '#E8922A' },
  priceLabel: { fontSize: 11, color: '#9ca3af' },
  availBadge: { backgroundColor: '#052e16', paddingHorizontal: 10, paddingVertical: 4, borderRadius: 20 },
  unavailBadge: { backgroundColor: '#450a0a' },
  availText: { fontSize: 11, color: '#4ade80', fontWeight: '600' },
});
