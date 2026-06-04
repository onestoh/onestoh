import React, { useEffect, useState } from 'react'
import { View, Text, FlatList, StyleSheet, ActivityIndicator, RefreshControl } from 'react-native'
import api from '@/lib/api'
import { colors, spacing, fontSize } from '@/theme'

interface Booking { id: string; status: string; start_date: string; end_date: string; total_amount_kes: number; asset?: { title: string } }

const STATUS_COLOR: Record<string, string> = {
  pending_payment: '#F59E0B', confirmed: '#3B82F6', active: '#22C55E',
  completed: '#E8922A', cancelled: '#EF4444', closed: '#8892A4',
}

export default function BookingsScreen() {
  const [bookings, setBookings] = useState<Booking[]>([])
  const [loading, setLoading]   = useState(true)
  const [refreshing, setRefreshing] = useState(false)

  const load = async () => {
    try {
      const res = await api.get('/bookings')
      setBookings(res.data.data || [])
    } catch {} finally { setLoading(false) }
  }

  useEffect(() => { load() }, [])
  const onRefresh = async () => { setRefreshing(true); await load(); setRefreshing(false) }

  const renderItem = ({ item }: { item: Booking }) => (
    <View style={styles.card}>
      <View style={styles.row}>
        <Text style={styles.assetTitle} numberOfLines={1}>{item.asset?.title ?? 'Vehicle'}</Text>
        <View style={[styles.badge, { backgroundColor: (STATUS_COLOR[item.status] || colors.muted) + '20' }]}>
          <Text style={[styles.badgeText, { color: STATUS_COLOR[item.status] || colors.muted }]}>
            {item.status.replace(/_/g, ' ')}
          </Text>
        </View>
      </View>
      <Text style={styles.dates}>{item.start_date} → {item.end_date}</Text>
      <Text style={styles.amount}>KES {item.total_amount_kes?.toLocaleString()}</Text>
    </View>
  )

  if (loading) return <View style={{ flex: 1, backgroundColor: colors.dark, justifyContent: 'center', alignItems: 'center' }}><ActivityIndicator color={colors.amber} size="large" /></View>

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.title}>My Bookings</Text>
      </View>
      <FlatList
        data={bookings}
        keyExtractor={i => i.id}
        renderItem={renderItem}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.amber} />}
        contentContainerStyle={{ padding: spacing.md, gap: spacing.sm }}
        ListEmptyComponent={
          <View style={{ alignItems: 'center', paddingTop: 80 }}>
            <Text style={{ fontSize: 48 }}>📋</Text>
            <Text style={{ color: colors.muted, marginTop: spacing.md, fontSize: fontSize.md }}>No bookings yet</Text>
          </View>
        }
      />
    </View>
  )
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.dark },
  header:    { backgroundColor: colors.surface, borderBottomWidth: 1, borderBottomColor: colors.border, padding: spacing.lg, paddingTop: 60 },
  title:     { fontSize: fontSize.xl, fontWeight: '700', color: colors.white },
  card:      { backgroundColor: colors.surface, borderRadius: 12, padding: spacing.md, borderWidth: 1, borderColor: colors.border },
  row:       { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 4 },
  assetTitle:{ color: colors.white, fontWeight: '600', fontSize: fontSize.md, flex: 1, marginRight: spacing.sm },
  badge:     { borderRadius: 20, paddingHorizontal: 10, paddingVertical: 3 },
  badgeText: { fontSize: fontSize.xs, fontWeight: '600', textTransform: 'capitalize' },
  dates:     { color: colors.muted, fontSize: fontSize.xs, marginBottom: 4 },
  amount:    { color: colors.amber, fontWeight: '700', fontSize: fontSize.md },
})
