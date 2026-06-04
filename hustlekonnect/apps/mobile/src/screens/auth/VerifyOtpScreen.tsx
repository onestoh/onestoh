import React, { useState } from 'react'
import { View, Text, TextInput, TouchableOpacity, StyleSheet, Alert } from 'react-native'
import api from '@/lib/api'
import { useAuthStore } from '@/store/auth'
import { colors, spacing, fontSize } from '@/theme'

export default function VerifyOtpScreen() {
  const [otp, setOtp]       = useState('')
  const [loading, setLoading] = useState(false)
  const updateUser = useAuthStore((s) => s.updateUser)

  const handleVerify = async () => {
    if (otp.length !== 6) { Alert.alert('Error', 'Enter 6-digit OTP'); return }
    setLoading(true)
    try {
      await api.post('/auth/verify-otp', { otp })
      const me = await api.get('/auth/me')
      updateUser(me.data.data)
    } catch (err: any) {
      Alert.alert('Invalid OTP', err.response?.data?.message || 'Please try again.')
    } finally {
      setLoading(false)
    }
  }

  const resend = async () => {
    try { await api.post('/auth/resend-otp'); Alert.alert('Sent', 'OTP resent to your phone.') }
    catch { Alert.alert('Error', 'Failed to resend OTP.') }
  }

  return (
    <View style={{ flex: 1, backgroundColor: colors.dark, justifyContent: 'center', padding: spacing.lg }}>
      <View style={{ backgroundColor: colors.surface, borderRadius: 16, padding: spacing.lg, borderWidth: 1, borderColor: colors.border }}>
        <Text style={{ fontSize: fontSize.xl, fontWeight: '700', color: colors.white, textAlign: 'center', marginBottom: spacing.xs }}>Verify Your Phone</Text>
        <Text style={{ color: colors.muted, textAlign: 'center', marginBottom: spacing.xl, fontSize: fontSize.sm }}>Enter the 6-digit code sent to your phone.</Text>

        <TextInput
          style={{ backgroundColor: colors.dark, borderWidth: 1, borderColor: colors.border, borderRadius: 10, padding: 16, color: colors.white, fontSize: 28, textAlign: 'center', letterSpacing: 12, fontFamily: 'monospace' }}
          value={otp}
          onChangeText={v => setOtp(v.replace(/\D/g,'').slice(0,6))}
          keyboardType="number-pad"
          maxLength={6}
          placeholder="000000"
          placeholderTextColor={colors.muted}
        />

        <TouchableOpacity onPress={handleVerify} disabled={loading}
          style={{ backgroundColor: colors.amber, borderRadius: 10, paddingVertical: 14, alignItems: 'center', marginTop: spacing.md, opacity: loading ? 0.6 : 1 }}>
          <Text style={{ color: colors.white, fontWeight: '700', fontSize: fontSize.md }}>{loading ? 'Verifying...' : 'Verify'}</Text>
        </TouchableOpacity>

        <TouchableOpacity onPress={resend} style={{ marginTop: spacing.md, alignItems: 'center' }}>
          <Text style={{ color: colors.amber, fontSize: fontSize.sm }}>Resend OTP</Text>
        </TouchableOpacity>
      </View>
    </View>
  )
}
