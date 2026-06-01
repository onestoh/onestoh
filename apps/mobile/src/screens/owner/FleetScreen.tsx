import React, { useState, useEffect } from 'react';
import { View, Text, FlatList, StyleSheet, TouchableOpacity, ActivityIndicator, RefreshControl } from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { OwnerStackParamList } from '../../navigation/AppNavigator';
import { assetsApi } from '../../lib/api';
import { Asset } from '../../lib/types';
import StatusBadge from '../../components/StatusBadge';

type Props = {
  navigation: NativeStackNavigationProp<OwnerStackParamList, 'Fleet'>;
};

function StatCard({ label, value, color }: { label: string; value: string | number; color?: string }) {
  return (
    <View style={styles.statCard}>
      <Text style={[styles.statValue, color ? { color } : {}]}>{value}</Text>
      <Text style={styles.statLabel}>{label}</Text>
    </View>
  );
}

export default function FleetScreen({ navigation }: Props) {
  const [assets, setAssets] = useState<Asset[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchFleet = async () => {
    try {
      const res = await assetsApi.ownerList();
      setAssets(res.data.assets ?? []);
    } catch { /* silent */ }
    finally { setLoading(false); setRefreshing(false); }
  };

  useEffect(() => { fetchFleet(); }, []);

  const active = assets.filter((a) => a.isAvailable).length;
  const avgUtil = assets.length
    ? Math.round(assets.reduce((s, a) => s + a.utilisation, 0) / assets.length)
    : 0;

  return (
    <View style={styles.container}>
      {/* Stats Row */}
      <View style={styles.statsRow}>
        <StatCard label="Total Assets" value={assets.length} />
        <StatCard label="Active" value={active} color="#4ade80" />
        <StatCard label="Avg Util" value={`${avgUtil}%`} color="#E8922A" />
      </View>

      {loading ? (
        <View style={styles.centered}><ActivityIndicator size="large" color="#E8922A" /></View>
      ) : (
        <FlatList
          data={assets}
          keyExtractor={(item) => item.id}
          renderItem={({ item }) => (
            <View style={styles.assetRow}>
              <View style={{ flex: 1 }}>
                <Text style={styles.assetName}>{item.name}</Text>
                <Text style={styles.assetMeta}>{item.year} · {item.location}</Text>
                <View style={styles.utilRow}>
                  <View style={styles.utilBg}>
                    <View style={[styles.utilFill, {
                      width: `${item.utilisation}%` as `${number}%`,
                      backgroundColor: item.utilisation >= 70 ? '#4ade80' : item.utilisation >= 40 ? '#E8922A' : '#f87171',
                    }]} />
                  </View>
                  <Text style={styles.utilLabel}>{item.utilisation}%</Text>
                </View>
              </View>
              <View style={{ alignItems: 'flex-end', gap: 6 }}>
                <StatusBadge status={item.isAvailable ? 'Active' : 'Inactive'} small />
                <Text style={styles.revenueText}>★ {item.rating.toFixed(1)}</Text>
              </View>
            </View>
          )}
          contentContainerStyle={styles.list}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); fetchFleet(); }} tintColor="#E8922A" />
          }
          ListEmptyComponent={
            <View style={styles.empty}>
              <Text style={styles.emptyText}>No assets yet.</Text>
            </View>
          }
        />
      )}

      <TouchableOpacity style={styles.fab} onPress={() => navigation.navigate('AddListing')}>
        <Text style={styles.fabText}>+ Add Asset</Text>
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#080C12' },
  statsRow: { flexDirection: 'row', padding: 16, gap: 10 },
  statCard: { flex: 1, backgroundColor: '#141D2B', borderRadius: 12, padding: 14, alignItems: 'center' },
  statValue: { fontSize: 22, fontWeight: '800', color: '#ffffff' },
  statLabel: { fontSize: 11, color: '#6b7280', marginTop: 2 },
  centered: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  list: { padding: 16, paddingTop: 0, gap: 10 },
  assetRow: { backgroundColor: '#141D2B', borderRadius: 14, padding: 14, flexDirection: 'row', gap: 12 },
  assetName: { fontSize: 15, fontWeight: '700', color: '#ffffff' },
  assetMeta: { fontSize: 12, color: '#9ca3af', marginTop: 2 },
  utilRow: { flexDirection: 'row', alignItems: 'center', gap: 8, marginTop: 8 },
  utilBg: { flex: 1, height: 5, backgroundColor: '#1e2d40', borderRadius: 3, overflow: 'hidden' },
  utilFill: { height: '100%', borderRadius: 3 },
  utilLabel: { fontSize: 11, color: '#9ca3af', width: 30, textAlign: 'right' },
  revenueText: { fontSize: 12, color: '#E8922A', fontWeight: '600' },
  empty: { paddingTop: 60, alignItems: 'center' },
  emptyText: { color: '#6b7280', fontSize: 15 },
  fab: { position: 'absolute', bottom: 20, right: 20, backgroundColor: '#E8922A', paddingHorizontal: 20, paddingVertical: 13, borderRadius: 50, elevation: 6 },
  fabText: { fontSize: 14, fontWeight: '700', color: '#ffffff' },
});
