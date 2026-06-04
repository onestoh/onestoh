import React, { useState } from 'react'
import { View, Text, TextInput, TouchableOpacity, StyleSheet, Alert, ScrollView, KeyboardAvoidingView, Platform } from 'react-native'
import { useNavigation } from '@react-navigation/native'
import api from '@/lib/api'
import { useAuthStore } from '@/store/auth'
import { colors, spacing, fontSize } from '@/theme'

const COUNTRIES = [
  { code: 'KE', name: '🇰🇪 Kenya' }, { code: 'UG', name: '🇺🇬 Uganda' },
  { code: 'TZ', name: '🇹🇿 Tanzania' }, { code: 'NG', name: '🇳🇬 Nigeria' },
  { code: 'GH', name: '🇬🇭 Ghana' }, { code: 'ZA', name: '🇿🇦 South Africa' },
]

export default function RegisterScreen() {
  const navigation = useNavigation<any>()
  const setAuth    = useAuthStore((s) => s.setAuth)
  const [form, setForm] = useState({ name: '', email: '', phone: '', password: '', password_confirmation: '', role: 'client', country: 'KE' })
  const [loading, setLoading] = useState(false)

  const update = (k: string, v: string) => setForm(f => ({ ...f, [k]: v }))

  const handleRegister = async () => {
    if (!form.name || !form.email || !form.phone || !form.password) {
      Alert.alert('Error', 'Please fill in all required fields'); return
    }
    if (form.password !== form.password_confirmation) {
      Alert.alert('Error', 'Passwords do not match'); return
    }
    setLoading(true)
    try {
      const res = await api.post('/auth/register', form)
      await setAuth(res.data.data.user, res.data.data.token)
      navigation.navigate('VerifyOtp')
    } catch (err: any) {
      const msg = err.response?.data?.message || 'Registration failed.'
      Alert.alert('Error', msg)
    } finally {
      setLoading(false)
    }
  }

  return (
    <KeyboardAvoidingView style={{ flex: 1, backgroundColor: colors.dark }} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <ScrollView contentContainerStyle={{ padding: spacing.lg, paddingTop: 60 }} keyboardShouldPersistTaps="handled">
        <Text style={{ fontSize: fontSize.xxl, fontWeight: '800', color: colors.white, textAlign: 'center', marginBottom: spacing.xs }}>
          <Text style={{ color: colors.amber }}>Hustle</Text>Konnect
        </Text>
        <Text style={{ color: colors.muted, textAlign: 'center', marginBottom: spacing.xl }}>Create your free account</Text>

        <View style={{ backgroundColor: colors.surface, borderRadius: 16, padding: spacing.lg, borderWidth: 1, borderColor: colors.border }}>
          {/* Role */}
          <Text style={styles.label}>I want to</Text>
          <View style={{ flexDirection: 'row', gap: spacing.sm, marginBottom: spacing.md }}>
            {(['client','owner','broker'] as const).map(r => (
              <TouchableOpacity key={r} onPress={() => update('role', r)} style={[styles.roleBtn, form.role === r && styles.roleBtnActive]}>
                <Text style={{ color: form.role === r ? colors.amber : colors.muted, fontSize: fontSize.xs, textTransform: 'capitalize' }}>
                  {r === 'client' ? '🚗 Rent' : r === 'owner' ? '🏭 List' : '🤝 Broker'}
                </Text>
              </TouchableOpacity>
            ))}
          </View>

          {[
            { key: 'name',  label: 'Full Name',      type: 'default',       placeholder: 'John Doe' },
            { key: 'email', label: 'Email',           type: 'email-address', placeholder: 'you@example.com' },
            { key: 'phone', label: 'Phone',           type: 'phone-pad',     placeholder: '+254700000000' },
            { key: 'password',             label: 'Password',         type: 'default', placeholder: 'Min 8 chars', secure: true },
            { key: 'password_confirmation',label: 'Confirm Password', type: 'default', placeholder: 'Repeat password', secure: true },
          ].map(({ key, label, type, placeholder, secure }) => (
            <View key={key} style={{ marginBottom: spacing.md }}>
              <Text style={styles.label}>{label}</Text>
              <TextInput
                style={styles.input}
                value={(form as any)[key]}
                onChangeText={v => update(key, v)}
                keyboardType={type as any}
                autoCapitalize="none"
                secureTextEntry={secure}
                placeholder={placeholder}
                placeholderTextColor={colors.muted}
              />
            </View>
          ))}

          <Text style={styles.label}>Country</Text>
          <ScrollView horizontal showsHorizontalScrollIndicator={false} style={{ marginBottom: spacing.md }}>
            {COUNTRIES.map(c => (
              <TouchableOpacity key={c.code} onPress={() => update('country', c.code)}
                style={[styles.countryBtn, form.country === c.code && styles.countryBtnActive]}>
                <Text style={{ color: form.country === c.code ? colors.amber : colors.muted, fontSize: fontSize.sm }}>{c.name}</Text>
              </TouchableOpacity>
            ))}
          </ScrollView>

          <TouchableOpacity onPress={handleRegister} disabled={loading} style={[styles.btn, loading && { opacity: 0.6 }]}>
            <Text style={styles.btnText}>{loading ? 'Creating...' : 'Create Account'}</Text>
          </TouchableOpacity>

          <TouchableOpacity onPress={() => navigation.navigate('Login')} style={{ marginTop: spacing.md, alignItems: 'center' }}>
            <Text style={{ color: colors.muted, fontSize: fontSize.sm }}>
              Have an account? <Text style={{ color: colors.amber }}>Sign in</Text>
            </Text>
          </TouchableOpacity>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  )
}

const styles = StyleSheet.create({
  label:          { color: colors.muted, fontSize: fontSize.sm, marginBottom: 4 },
  input:          { backgroundColor: colors.dark, borderWidth: 1, borderColor: colors.border, borderRadius: 10, paddingHorizontal: 16, paddingVertical: 12, color: colors.white, fontSize: fontSize.md },
  btn:            { backgroundColor: colors.amber, borderRadius: 10, paddingVertical: 14, alignItems: 'center', marginTop: 8 },
  btnText:        { color: colors.white, fontWeight: '700', fontSize: fontSize.md },
  roleBtn:        { flex: 1, borderWidth: 1, borderColor: colors.border, borderRadius: 8, paddingVertical: 10, alignItems: 'center' },
  roleBtnActive:  { borderColor: colors.amber },
  countryBtn:     { borderWidth: 1, borderColor: colors.border, borderRadius: 8, paddingHorizontal: 12, paddingVertical: 8, marginRight: 8 },
  countryBtnActive: { borderColor: colors.amber },
})
