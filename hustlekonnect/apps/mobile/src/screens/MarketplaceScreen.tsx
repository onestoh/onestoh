import React, { useEffect, useState } from 'react'
import { View, Text, FlatList, TouchableOpacity, StyleSheet, ActivityIndicator, TextInput } from 'react-native'
import { useNavigation, useRoute } from '@react-navigation/native'
import api from '@/lib/api'
import { colors, spacing, fontSize } from '@/theme'

interface Asset {
  id: number; title: string; asset_type: string
  daily_rate: number; rating?: number
  yard?: { city: string; country: string }
}

export default function MarketplaceScreen() {
  const navigation = useNavigation<any>()
  const route      = useRoute<any>()
  const [assets, setAssets]   = useState<Asset[]>([])
  const [loading, setLoading] = useState(true)
  const [country, setCountry] = useState(route.params?.country || '')
  const [type, setType]       = useState('')

  const COUNTRIES = ['','KE','UG','TZ','NG','GH','ZA']
  const TYPES     = ['','car','truck','bus','excavator','crane','loader']

  const fetch = async () => {
    setLoading(true)
    try {
      const q = new URLSearchParams()
      if (country) q.set('country', country)
      if (type)    q.set('type', type)
      const res = await api.get(`/listings/assets?${q}`)
      setAssets(res.data.data || [])
    } catch {} finally { setLoading(false) }
  }

  useEffect(() => { fetch() }, [country, type])

  const renderAsset = ({ item }: { item: Asset }) => (
    <TouchableOpacity onPress={() => navigation.navigate('AssetDetail', { assetId: item.id })} style={styles.card}>
      <View style={styles.assetImg}><Text style={{ fontSize: 32 }}>🚗</Text></View>
      <View style={styles.assetInfo}>
        <Text style={styles.assetTitle} numberOfLines={1}>{item.title}</Text>
        <Text style={styles.assetLocation}>{item.yard?.city}, {item.yard?.country}</Text>
        <View style={styles.assetMeta}>
          <Text style={styles.price}>KES {item.daily_rate?.toLocaleString()}<Text style={styles.perDay}>/day</Text></Text>
          {item.rating && <Text style={styles.rating}>⭐ {item.rating.toFixed(1)}</Text>}
        </View>
      </View>
    </TouchableOpacity>
  )

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.title}>Browse Vehicles</Text>
      </View>

      {/* Filters */}
      <View style={{ paddingHorizontal: spacing.md, paddingVertical: spacing.sm }}>
        <View style={{ flexDirection: 'row', gap: spacing.sm }}>
          <View style={styles.filterWrap}>
            {COUNTRIES.map(c => (
              <TouchableOpacity key={c || 'all'} onPress={() => setCountry(c)}
                style={[styles.filterBtn, country === c && styles.filterBtnActive]}>
                <Text style={{ color: country === c ? colors.amber : colors.muted, fontSize: fontSize.xs }}>
                  {c || 'All'}
                </Text>
              </TouchableOpacity>
            ))}
          </View>
        </View>
      </View>

      {loading ? (
        <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
          <ActivityIndicator color={colors.amber} size="large" />
        </View>
      ) : (
        <FlatList
          data={assets}
          keyExtractor={i => String(i.id)}
          renderItem={renderAsset}
          contentContainerStyle={{ padding: spacing.md, gap: spacing.sm }}
          ListEmptyComponent={
            <View style={{ alignItems: 'center', paddingTop: 60 }}>
              <Text style={{ fontSize: 48 }}>🔍</Text>
              <Text style={{ color: colors.muted, marginTop: spacing.md }}>No vehicles found</Text>
            </View>
          }
        />
      )}
    </View>
  )
}

const styles = StyleSheet.create({
  container:       { flex: 1, backgroundColor: colors.dark },
  header:          { backgroundColor: colors.surface, borderBottomWidth: 1, borderBottomColor: colors.border, padding: spacing.lg, paddingTop: 60 },
  title:           { fontSize: fontSize.xl, fontWeight: '700', color: colors.white },
  card:            { backgroundColor: colors.surface, borderRadius: 12, flexDirection: 'row', borderWidth: 1, borderColor: colors.border, overflow: 'hidden' },
  assetImg:        { width: 90, backgroundColor: colors.dark, justifyContent: 'center', alignItems: 'center' },
  assetInfo:       { flex: 1, padding: spacing.md },
  assetTitle:      { color: colors.white, fontWeight: '600', fontSize: fontSize.md },
  assetLocation:   { color: colors.muted, fontSize: fontSize.xs, marginTop: 2 },
  assetMeta:       { flexDirection: 'row', justifyContent: 'space-between', marginTop: spacing.sm },
  price:           { color: colors.amber, fontWeight: '700', fontSize: fontSize.md },
  perDay:          { color: colors.muted, fontSize: fontSize.xs },
  rating:          { color: colors.white, fontSize: fontSize.sm },
  filterWrap:      { flexDirection: 'row', flexWrap: 'wrap', gap: 6 },
  filterBtn:       { borderWidth: 1, borderColor: colors.border, borderRadius: 20, paddingHorizontal: 12, paddingVertical: 5 },
  filterBtnActive: { borderColor: colors.amber },
})
