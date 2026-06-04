import React, { useState } from 'react'
import { View, Text, TextInput, TouchableOpacity, StyleSheet, Alert, ScrollView, KeyboardAvoidingView, Platform } from 'react-native'
import { useNavigation } from '@react-navigation/native'
import api from '@/lib/api'
import { useAuthStore } from '@/store/auth'
import { colors, spacing, fontSize } from '@/theme'

export default function LoginScreen() {
  const navigation = useNavigation<any>()
  const setAuth    = useAuthStore((s) => s.setAuth)
  const [email, setEmail]       = useState('')
  const [password, setPassword] = useState('')
  const [loading, setLoading]   = useState(false)

  const handleLogin = async () => {
    if (!email || !password) { Alert.alert('Error', 'Please fill in all fields'); return }
    setLoading(true)
    try {
      const res = await api.post('/auth/login', { email, password })
      await setAuth(res.data.data.user, res.data.data.token)
    } catch (err: any) {
      Alert.alert('Login Failed', err.response?.data?.message || 'Invalid credentials')
    } finally {
      setLoading(false)
    }
  }

  return (
    <KeyboardAvoidingView style={styles.container} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <ScrollView contentContainerStyle={styles.inner} keyboardShouldPersistTaps="handled">
        <Text style={styles.logo}><Text style={{ color: colors.amber }}>Hustle</Text>Konnect</Text>
        <Text style={styles.subtitle}>Sign in to your account</Text>

        <View style={styles.card}>
          <Text style={styles.label}>Email Address</Text>
          <TextInput style={styles.input} value={email} onChangeText={setEmail} keyboardType="email-address" autoCapitalize="none" placeholder="you@example.com" placeholderTextColor={colors.muted} />

          <Text style={[styles.label, { marginTop: spacing.md }]}>Password</Text>
          <TextInput style={styles.input} value={password} onChangeText={setPassword} secureTextEntry placeholder="••••••••" placeholderTextColor={colors.muted} />

          <TouchableOpacity onPress={handleLogin} disabled={loading} style={[styles.btn, loading && styles.btnDisabled]}>
            <Text style={styles.btnText}>{loading ? 'Signing in...' : 'Sign In'}</Text>
          </TouchableOpacity>

          <TouchableOpacity onPress={() => navigation.navigate('Register')} style={styles.link}>
            <Text style={styles.linkText}>New to HustleKonnect? <Text style={{ color: colors.amber }}>Create account</Text></Text>
          </TouchableOpacity>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  )
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.dark },
  inner:     { flexGrow: 1, justifyContent: 'center', padding: spacing.lg },
  logo:      { fontSize: fontSize.xxl, fontWeight: '800', color: colors.white, textAlign: 'center', marginBottom: spacing.xs },
  subtitle:  { color: colors.muted, textAlign: 'center', marginBottom: spacing.xl, fontSize: fontSize.md },
  card:      { backgroundColor: colors.surface, borderRadius: 16, padding: spacing.lg, borderWidth: 1, borderColor: colors.border },
  label:     { color: colors.muted, fontSize: fontSize.sm, marginBottom: spacing.xs },
  input:     { backgroundColor: colors.dark, borderWidth: 1, borderColor: colors.border, borderRadius: 10, paddingHorizontal: spacing.md, paddingVertical: 12, color: colors.white, fontSize: fontSize.md },
  btn:       { backgroundColor: colors.amber, borderRadius: 10, paddingVertical: 14, alignItems: 'center', marginTop: spacing.lg },
  btnDisabled: { opacity: 0.6 },
  btnText:   { color: colors.white, fontWeight: '700', fontSize: fontSize.md },
  link:      { marginTop: spacing.lg, alignItems: 'center' },
  linkText:  { color: colors.muted, fontSize: fontSize.sm },
})
