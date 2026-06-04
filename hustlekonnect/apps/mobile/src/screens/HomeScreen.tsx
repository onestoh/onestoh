import React, { useEffect, useState } from 'react'
import { View, Text, StyleSheet, ScrollView, TouchableOpacity, RefreshControl } from 'react-native'
import { useNavigation } from '@react-navigation/native'
import api from '@/lib/api'
import { useAuthStore } from '@/store/auth'
import { colors, spacing, fontSize } from '@/theme'

interface Stats { bookings: number; walletBalance: number; unreadNotifications: number }

export default function HomeScreen() {
  const navigation = useNavigation<any>()
  const { user }   = useAuthStore()
  const [stats, setStats] = useState<Stats>({ bookings: 0, walletBalance: 0, unreadNotifications: 0 })
  const [refreshing, setRefreshing] = useState(false)

  const loadStats = async () => {
    try {
      const [bookRes, walletRes, notifRes] = await Promise.all([
        api.get('/bookings?per_page=1'),
        api.get('/wallet/balance'),
        api.get('/notifications?per_page=1'),
      ])
      setStats({
        bookings: bookRes.data.total || 0,
        walletBalance: walletRes.data.data?.balance || 0,
        unreadNotifications: notifRes.data.data?.filter((n: any) => !n.read_at).length || 0,
      })
    } catch {}
  }

  useEffect(() => { loadStats() }, [])

  const onRefresh = async () => { setRefreshing(true); await loadStats(); setRefreshing(false) }

  return (
    <ScrollView style={styles.container} refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.amber} />}>
      {/* Header */}
      <View style={styles.header}>
        <View>
          <Text style={styles.greeting}>Hello, {user?.name?.split(' ')[0] ?? 'there'} 👋</Text>
          <Text style={styles.subgreeting}>Welcome to HustleKonnect</Text>
        </View>
        {user?.kyc_status !== 'approved' && (
          <TouchableOpacity onPress={() => navigation.navigate('Kyc')} style={styles.kycBadge}>
            <Text style={styles.kycText}>Verify KYC</Text>
          </TouchableOpacity>
        )}
      </View>

      {/* Stats */}
      <View style={styles.statsGrid}>
        {[
          { label: 'Bookings', value: stats.bookings, emoji: '🚗' },
          { label: 'Wallet (KES)', value: stats.walletBalance.toLocaleString(), emoji: '💰' },
          { label: 'Unread Alerts', value: stats.unreadNotifications, emoji: '🔔' },
          { label: 'Trust Score', value: `${user?.trust_score ?? 0}%`, emoji: '⭐' },
        ].map(({ label, value, emoji }) => (
          <View key={label} style={styles.statCard}>
            <Text style={styles.statEmoji}>{emoji}</Text>
            <Text style={styles.statValue}>{value}</Text>
            <Text style={styles.statLabel}>{label}</Text>
          </View>
        ))}
      </View>

      {/* Quick actions */}
      <Text style={styles.sectionTitle}>Quick Actions</Text>
      <View style={styles.actionsGrid}>
        {[
          { label: 'Browse Vehicles', emoji: '🔍', screen: 'Browse' },
          { label: 'My Bookings', emoji: '📋', screen: 'Bookings' },
          { label: 'Notifications', emoji: '🔔', screen: 'Alerts' },
          { label: 'My Profile', emoji: '👤', screen: 'Profile' },
        ].map(({ label, emoji, screen }) => (
          <TouchableOpacity key={label} onPress={() => navigation.navigate(screen)} style={styles.actionCard}>
            <Text style={styles.actionEmoji}>{emoji}</Text>
            <Text style={styles.actionLabel}>{label}</Text>
          </TouchableOpacity>
        ))}
      </View>

      {/* Markets */}
      <Text style={styles.sectionTitle}>Available Markets</Text>
      <ScrollView horizontal showsHorizontalScrollIndicator={false} style={{ paddingLeft: spacing.md }}>
        {[['🇰🇪','KE','Kenya'],['🇺🇬','UG','Uganda'],['🇹🇿','TZ','Tanzania'],['🇳🇬','NG','Nigeria'],['🇬🇭','GH','Ghana'],['🇿🇦','ZA','S. Africa']].map(([flag, code, name]) => (
          <TouchableOpacity key={code} onPress={() => navigation.navigate('Browse', { country: code })}
            style={{ backgroundColor: colors.surface, borderRadius: 12, padding: spacing.md, marginRight: spacing.sm, alignItems: 'center', borderWidth: 1, borderColor: colors.border, minWidth: 90 }}>
            <Text style={{ fontSize: 28 }}>{flag}</Text>
            <Text style={{ color: colors.white, fontSize: fontSize.sm, marginTop: 4 }}>{name}</Text>
            <Text style={{ color: colors.muted, fontSize: fontSize.xs }}>{code}</Text>
          </TouchableOpacity>
        ))}
      </ScrollView>

      <View style={{ height: spacing.xl }} />
    </ScrollView>
  )
}

const styles = StyleSheet.create({
  container:    { flex: 1, backgroundColor: colors.dark },
  header:       { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start', padding: spacing.lg, paddingTop: 60, backgroundColor: colors.surface, borderBottomWidth: 1, borderBottomColor: colors.border },
  greeting:     { fontSize: fontSize.xl, fontWeight: '700', color: colors.white },
  subgreeting:  { color: colors.muted, fontSize: fontSize.sm, marginTop: 2 },
  kycBadge:     { backgroundColor: '#F59E0B20', borderWidth: 1, borderColor: '#F59E0B50', borderRadius: 20, paddingHorizontal: 12, paddingVertical: 6 },
  kycText:      { color: '#F59E0B', fontSize: fontSize.xs, fontWeight: '600' },
  statsGrid:    { flexDirection: 'row', flexWrap: 'wrap', padding: spacing.md, gap: spacing.sm },
  statCard:     { flex: 1, minWidth: '45%', backgroundColor: colors.surface, borderRadius: 12, padding: spacing.md, borderWidth: 1, borderColor: colors.border },
  statEmoji:    { fontSize: 24, marginBottom: 8 },
  statValue:    { fontSize: fontSize.xl, fontWeight: '700', color: colors.white },
  statLabel:    { color: colors.muted, fontSize: fontSize.xs, marginTop: 2 },
  sectionTitle: { fontSize: fontSize.lg, fontWeight: '700', color: colors.white, paddingHorizontal: spacing.lg, marginTop: spacing.lg, marginBottom: spacing.md },
  actionsGrid:  { flexDirection: 'row', flexWrap: 'wrap', paddingHorizontal: spacing.md, gap: spacing.sm },
  actionCard:   { flex: 1, minWidth: '45%', backgroundColor: colors.surface, borderRadius: 12, padding: spacing.md, alignItems: 'center', borderWidth: 1, borderColor: colors.border },
  actionEmoji:  { fontSize: 32, marginBottom: spacing.sm },
  actionLabel:  { color: colors.white, fontSize: fontSize.sm, textAlign: 'center' },
})
