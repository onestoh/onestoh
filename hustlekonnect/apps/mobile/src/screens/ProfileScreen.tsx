import React from 'react'
import { View, Text, TouchableOpacity, ScrollView, Alert } from 'react-native'
import { useNavigation } from '@react-navigation/native'
import api from '@/lib/api'
import { useAuthStore } from '@/store/auth'
import { colors, spacing, fontSize } from '@/theme'

export default function ProfileScreen() {
  const navigation = useNavigation<any>()
  const { user, logout } = useAuthStore()

  const handleLogout = async () => {
    Alert.alert('Log Out', 'Are you sure you want to log out?', [
      { text: 'Cancel', style: 'cancel' },
      {
        text: 'Log Out', style: 'destructive',
        onPress: async () => {
          try { await api.post('/auth/logout') } catch {}
          await logout()
        }
      }
    ])
  }

  const kycColor = { not_submitted: colors.muted, pending: '#F59E0B', approved: colors.success, rejected: colors.error }

  return (
    <ScrollView style={{ flex: 1, backgroundColor: colors.dark }}>
      <View style={{ backgroundColor: colors.surface, borderBottomWidth: 1, borderBottomColor: colors.border, padding: spacing.lg, paddingTop: 60 }}>
        <Text style={{ fontSize: fontSize.xl, fontWeight: '700', color: colors.white }}>My Profile</Text>
      </View>

      <View style={{ padding: spacing.lg }}>
        {/* Avatar */}
        <View style={{ alignItems: 'center', marginBottom: spacing.xl }}>
          <View style={{ width: 80, height: 80, borderRadius: 40, backgroundColor: colors.surface, borderWidth: 2, borderColor: colors.amber, justifyContent: 'center', alignItems: 'center', marginBottom: spacing.md }}>
            <Text style={{ fontSize: 36 }}>👤</Text>
          </View>
          <Text style={{ fontSize: fontSize.xl, fontWeight: '700', color: colors.white }}>{user?.name}</Text>
          <Text style={{ color: colors.muted, fontSize: fontSize.sm }}>{user?.email}</Text>
          <View style={{ flexDirection: 'row', alignItems: 'center', gap: 6, marginTop: spacing.sm }}>
            <View style={{ width: 8, height: 8, borderRadius: 4, backgroundColor: (kycColor as any)[user?.kyc_status ?? 'not_submitted'] }} />
            <Text style={{ color: (kycColor as any)[user?.kyc_status ?? 'not_submitted'], fontSize: fontSize.sm, textTransform: 'capitalize' }}>
              KYC: {user?.kyc_status?.replace(/_/g,' ') ?? 'Not submitted'}
            </Text>
          </View>
        </View>

        {/* Info */}
        {[
          ['Phone', user?.phone ?? '—'],
          ['Country', user?.country ?? '—'],
          ['Role', user?.role ?? '—'],
          ['Currency', user?.preferred_currency ?? 'KES'],
          ['Trust Score', `${user?.trust_score ?? 0}%`],
        ].map(([label, value]) => (
          <View key={label as string} style={{ backgroundColor: colors.surface, borderRadius: 10, padding: spacing.md, marginBottom: spacing.sm, flexDirection: 'row', justifyContent: 'space-between', borderWidth: 1, borderColor: colors.border }}>
            <Text style={{ color: colors.muted, fontSize: fontSize.md }}>{label}</Text>
            <Text style={{ color: colors.white, fontWeight: '600', fontSize: fontSize.md, textTransform: 'capitalize' }}>{value}</Text>
          </View>
        ))}

        {/* Actions */}
        {user?.kyc_status !== 'approved' && (
          <TouchableOpacity onPress={() => navigation.navigate('Kyc')}
            style={{ backgroundColor: colors.amber, borderRadius: 12, paddingVertical: 14, alignItems: 'center', marginTop: spacing.md }}>
            <Text style={{ color: colors.white, fontWeight: '700', fontSize: fontSize.md }}>🛡️ Complete KYC Verification</Text>
          </TouchableOpacity>
        )}

        <TouchableOpacity onPress={handleLogout}
          style={{ backgroundColor: colors.surface, borderRadius: 12, paddingVertical: 14, alignItems: 'center', marginTop: spacing.md, borderWidth: 1, borderColor: colors.error + '50' }}>
          <Text style={{ color: colors.error, fontWeight: '600', fontSize: fontSize.md }}>Log Out</Text>
        </TouchableOpacity>
      </View>
    </ScrollView>
  )
}
