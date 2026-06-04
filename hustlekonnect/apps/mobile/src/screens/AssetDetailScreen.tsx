import React, { useEffect, useState } from 'react'
import { View, Text, ScrollView, TouchableOpacity, StyleSheet, Alert, ActivityIndicator } from 'react-native'
import { useNavigation, useRoute } from '@react-navigation/native'
import api from '@/lib/api'
import { useAuthStore } from '@/store/auth'
import { colors, spacing, fontSize } from '@/theme'

export default function AssetDetailScreen() {
  const route      = useRoute<any>()
  const navigation = useNavigation<any>()
  const { user }   = useAuthStore()
  const [asset, setAsset]     = useState<any>(null)
  const [loading, setLoading] = useState(true)
  const [startDate, setStartDate] = useState('')
  const [endDate, setEndDate]     = useState('')
  const [withDriver, setWithDriver] = useState(false)
  const [booking, setBooking]     = useState(false)

  useEffect(() => {
    api.get(`/listings/assets/${route.params.assetId}`)
      .then(r => setAsset(r.data.data))
      .catch(() => Alert.alert('Error', 'Asset not found'))
      .finally(() => setLoading(false))
  }, [])

  const handleBook = async () => {
    if (!user) { navigation.navigate('Login'); return }
    if (user.kyc_status !== 'approved') { Alert.alert('KYC Required', 'Please complete KYC verification.'); navigation.navigate('Kyc'); return }
    if (!startDate || !endDate) { Alert.alert('Error', 'Select pickup and return dates'); return }

    setBooking(true)
    try {
      await api.post('/bookings', { asset_id: asset.id, start_date: startDate, end_date: endDate, with_driver: withDriver })
      Alert.alert('Success!', 'Booking created. Proceed to payment in My Bookings.')
      navigation.goBack()
    } catch (err: any) {
      Alert.alert('Booking Failed', err.response?.data?.message || 'Please try again.')
    } finally {
      setBooking(false) }
  }

  if (loading) return <View style={{ flex: 1, backgroundColor: colors.dark, justifyContent: 'center', alignItems: 'center' }}><ActivityIndicator color={colors.amber} size="large" /></View>
  if (!asset)  return <View style={{ flex: 1, backgroundColor: colors.dark, justifyContent: 'center', alignItems: 'center' }}><Text style={{ color: colors.muted }}>Not found</Text></View>

  return (
    <View style={{ flex: 1, backgroundColor: colors.dark }}>
      <ScrollView>
        {/* Back button */}
        <View style={{ backgroundColor: colors.surface, padding: spacing.lg, paddingTop: 60, flexDirection: 'row', alignItems: 'center', borderBottomWidth: 1, borderBottomColor: colors.border }}>
          <TouchableOpacity onPress={() => navigation.goBack()} style={{ marginRight: spacing.md }}>
            <Text style={{ color: colors.amber, fontSize: fontSize.md }}>← Back</Text>
          </TouchableOpacity>
          <Text style={{ color: colors.white, fontSize: fontSize.lg, fontWeight: '700' }} numberOfLines={1}>{asset.title}</Text>
        </View>

        {/* Image placeholder */}
        <View style={{ height: 200, backgroundColor: colors.surface, justifyContent: 'center', alignItems: 'center' }}>
          <Text style={{ fontSize: 72 }}>🚗</Text>
        </View>

        <View style={{ padding: spacing.lg }}>
          <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: spacing.md }}>
            <View>
              <Text style={{ color: colors.white, fontSize: fontSize.xl, fontWeight: '700' }}>{asset.title}</Text>
              <Text style={{ color: colors.muted, fontSize: fontSize.sm, marginTop: 4 }}>📍 {asset.yard?.city}, {asset.yard?.country}</Text>
            </View>
            <View style={{ alignItems: 'flex-end' }}>
              <Text style={{ color: colors.amber, fontSize: fontSize.xl, fontWeight: '700' }}>KES {asset.daily_rate?.toLocaleString()}</Text>
              <Text style={{ color: colors.muted, fontSize: fontSize.xs }}>per day</Text>
            </View>
          </View>

          <Text style={{ color: colors.muted, fontSize: fontSize.md, lineHeight: 22, marginBottom: spacing.lg }}>{asset.description}</Text>

          {/* Specs */}
          <View style={{ flexDirection: 'row', gap: spacing.sm, marginBottom: spacing.lg }}>
            {[['Make', asset.make], ['Model', asset.model], ['Year', asset.year]].map(([l,v]) => (
              <View key={l as string} style={{ flex: 1, backgroundColor: colors.surface, borderRadius: 10, padding: spacing.md, borderWidth: 1, borderColor: colors.border }}>
                <Text style={{ color: colors.muted, fontSize: fontSize.xs }}>{l}</Text>
                <Text style={{ color: colors.white, fontWeight: '600', marginTop: 2 }}>{v}</Text>
              </View>
            ))}
          </View>

          {/* Booking form */}
          <View style={{ backgroundColor: colors.surface, borderRadius: 16, padding: spacing.lg, borderWidth: 1, borderColor: colors.border }}>
            <Text style={{ color: colors.white, fontSize: fontSize.lg, fontWeight: '700', marginBottom: spacing.md }}>📅 Book This Vehicle</Text>

            <Text style={{ color: colors.muted, fontSize: fontSize.sm, marginBottom: 4 }}>Pickup Date (YYYY-MM-DD)</Text>
            <View style={{ backgroundColor: colors.dark, borderWidth: 1, borderColor: colors.border, borderRadius: 10, marginBottom: spacing.md }}>
              <Text style={{ color: colors.muted, padding: 12, fontSize: fontSize.sm }}>
                {/* In production, use a DatePicker component */}
                Use a DatePicker library for date selection
              </Text>
            </View>

            <TouchableOpacity onPress={() => setWithDriver(v => !v)}
              style={{ flexDirection: 'row', alignItems: 'center', gap: spacing.sm, marginBottom: spacing.md }}>
              <View style={{ width: 20, height: 20, borderRadius: 4, borderWidth: 2, borderColor: withDriver ? colors.amber : colors.border, backgroundColor: withDriver ? colors.amber : 'transparent', justifyContent: 'center', alignItems: 'center' }}>
                {withDriver && <Text style={{ color: colors.white, fontSize: 12 }}>✓</Text>}
              </View>
              <Text style={{ color: colors.white, fontSize: fontSize.md }}>Include Driver</Text>
            </TouchableOpacity>

            <View style={{ backgroundColor: colors.dark, borderRadius: 10, padding: spacing.md, flexDirection: 'row', gap: spacing.sm, marginBottom: spacing.md }}>
              <Text style={{ fontSize: 16 }}>🛡️</Text>
              <Text style={{ color: colors.muted, fontSize: fontSize.xs, flex: 1 }}>Payment held in secure escrow. Released only after successful rental.</Text>
            </View>
          </View>
        </View>
      </ScrollView>

      <View style={{ padding: spacing.lg, paddingBottom: 32, backgroundColor: colors.surface, borderTopWidth: 1, borderTopColor: colors.border }}>
        <TouchableOpacity onPress={handleBook} disabled={booking}
          style={{ backgroundColor: colors.amber, borderRadius: 12, paddingVertical: 16, alignItems: 'center', opacity: booking ? 0.6 : 1 }}>
          <Text style={{ color: colors.white, fontWeight: '700', fontSize: fontSize.lg }}>{booking ? 'Creating Booking...' : 'Book Now'}</Text>
        </TouchableOpacity>
      </View>
    </View>
  )
}
