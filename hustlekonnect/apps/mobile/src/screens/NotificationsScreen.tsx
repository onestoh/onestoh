import React, { useEffect, useState } from 'react'
import { View, Text, FlatList, TouchableOpacity, RefreshControl, ActivityIndicator } from 'react-native'
import api from '@/lib/api'
import { colors, spacing, fontSize } from '@/theme'

interface Notification { id: string; title: string; body: string; read_at: string | null; created_at: string }

export default function NotificationsScreen() {
  const [notifs, setNotifs]   = useState<Notification[]>([])
  const [loading, setLoading] = useState(true)
  const [refreshing, setRefreshing] = useState(false)

  const load = async () => {
    try { const r = await api.get('/notifications'); setNotifs(r.data.data || []) }
    catch {} finally { setLoading(false) }
  }

  useEffect(() => { load() }, [])
  const onRefresh = async () => { setRefreshing(true); await load(); setRefreshing(false) }

  const markRead = async (id: string) => {
    try {
      await api.put(`/notifications/${id}/read`)
      setNotifs(n => n.map(x => x.id === id ? { ...x, read_at: new Date().toISOString() } : x))
    } catch {}
  }

  const markAll = async () => {
    try { await api.post('/notifications/read-all'); setNotifs(n => n.map(x => ({ ...x, read_at: x.read_at ?? new Date().toISOString() }))) }
    catch {}
  }

  if (loading) return <View style={{ flex: 1, backgroundColor: colors.dark, justifyContent: 'center', alignItems: 'center' }}><ActivityIndicator color={colors.amber} /></View>

  return (
    <View style={{ flex: 1, backgroundColor: colors.dark }}>
      <View style={{ backgroundColor: colors.surface, borderBottomWidth: 1, borderBottomColor: colors.border, padding: spacing.lg, paddingTop: 60, flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
        <Text style={{ fontSize: fontSize.xl, fontWeight: '700', color: colors.white }}>Notifications</Text>
        {notifs.some(n => !n.read_at) && (
          <TouchableOpacity onPress={markAll}>
            <Text style={{ color: colors.amber, fontSize: fontSize.sm }}>Mark all read</Text>
          </TouchableOpacity>
        )}
      </View>

      <FlatList
        data={notifs}
        keyExtractor={i => i.id}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.amber} />}
        renderItem={({ item }) => (
          <TouchableOpacity onPress={() => markRead(item.id)}
            style={{ backgroundColor: colors.surface, borderBottomWidth: 1, borderBottomColor: colors.border, padding: spacing.md, flexDirection: 'row', gap: spacing.sm }}>
            {!item.read_at && <View style={{ width: 8, height: 8, borderRadius: 4, backgroundColor: colors.amber, marginTop: 6 }} />}
            <View style={{ flex: 1, marginLeft: item.read_at ? 16 : 0 }}>
              <Text style={{ color: colors.white, fontWeight: '600', fontSize: fontSize.md }}>{item.title}</Text>
              <Text style={{ color: colors.muted, fontSize: fontSize.sm, marginTop: 2 }}>{item.body}</Text>
            </View>
          </TouchableOpacity>
        )}
        ListEmptyComponent={
          <View style={{ alignItems: 'center', paddingTop: 80 }}>
            <Text style={{ fontSize: 48 }}>🔔</Text>
            <Text style={{ color: colors.muted, marginTop: spacing.md }}>No notifications yet</Text>
          </View>
        }
      />
    </View>
  )
}
