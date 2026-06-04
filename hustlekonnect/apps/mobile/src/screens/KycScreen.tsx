import React, { useState } from 'react'
import { View, Text, TouchableOpacity, ScrollView, Alert, ActivityIndicator } from 'react-native'
import { useNavigation } from '@react-navigation/native'
import DocumentPicker from 'react-native-document-picker'
import api from '@/lib/api'
import { useAuthStore } from '@/store/auth'
import { colors, spacing, fontSize } from '@/theme'

export default function KycScreen() {
  const navigation = useNavigation<any>()
  const updateUser = useAuthStore((s) => s.updateUser)
  const user       = useAuthStore((s) => s.user)
  const [docType, setDocType] = useState('national_id')
  const [docNumber, setDocNumber] = useState('')
  const [country, setCountry]   = useState(user?.country || 'KE')
  const [file, setFile]   = useState<any>(null)
  const [loading, setLoading] = useState(false)

  const pickFile = async () => {
    try {
      const res = await DocumentPicker.pickSingle({ type: [DocumentPicker.types.images, DocumentPicker.types.pdf] })
      setFile(res)
    } catch (e) {
      if (!DocumentPicker.isCancel(e)) Alert.alert('Error', 'Failed to pick file')
    }
  }

  const handleSubmit = async () => {
    if (!docNumber || !file) { Alert.alert('Error', 'Please fill all fields and upload document'); return }

    const fd = new FormData()
    fd.append('document_type',   docType)
    fd.append('document_number', docNumber)
    fd.append('country',         country)
    fd.append('document_file', { uri: file.uri, type: file.type, name: file.name } as any)

    setLoading(true)
    try {
      await api.post('/kyc/submit', fd, { headers: { 'Content-Type': 'multipart/form-data' }})
      const me = await api.get('/auth/me')
      updateUser(me.data.data)
      Alert.alert('Submitted!', 'KYC submitted. Usually verified within 24 hours.', [
        { text: 'OK', onPress: () => navigation.goBack() }
      ])
    } catch (err: any) {
      Alert.alert('Error', err.response?.data?.message || 'Submission failed.')
    } finally {
      setLoading(false)
    }
  }

  if (user?.kyc_status === 'approved') {
    return (
      <View style={{ flex: 1, backgroundColor: colors.dark, justifyContent: 'center', alignItems: 'center', padding: spacing.lg }}>
        <Text style={{ fontSize: 64 }}>✅</Text>
        <Text style={{ color: colors.white, fontSize: fontSize.xl, fontWeight: '700', marginTop: spacing.md }}>KYC Verified!</Text>
        <Text style={{ color: colors.muted, marginTop: spacing.sm, textAlign: 'center' }}>Your identity is verified. You can now book and pay.</Text>
        <TouchableOpacity onPress={() => navigation.goBack()} style={{ backgroundColor: colors.amber, borderRadius: 12, paddingVertical: 14, paddingHorizontal: 32, marginTop: spacing.xl }}>
          <Text style={{ color: colors.white, fontWeight: '700', fontSize: fontSize.md }}>Go Back</Text>
        </TouchableOpacity>
      </View>
    )
  }

  return (
    <ScrollView style={{ flex: 1, backgroundColor: colors.dark }}>
      <View style={{ backgroundColor: colors.surface, borderBottomWidth: 1, borderBottomColor: colors.border, padding: spacing.lg, paddingTop: 60, flexDirection: 'row', gap: spacing.md, alignItems: 'center' }}>
        <Text style={{ fontSize: 28 }}>🛡️</Text>
        <View>
          <Text style={{ fontSize: fontSize.xl, fontWeight: '700', color: colors.white }}>KYC Verification</Text>
          <Text style={{ color: colors.muted, fontSize: fontSize.sm }}>Required to book and make payments</Text>
        </View>
      </View>

      <View style={{ padding: spacing.lg }}>
        <Text style={{ color: colors.muted, fontSize: fontSize.sm, marginBottom: 4 }}>Document Type</Text>
        {['national_id','passport','drivers_license','business_reg'].map(t => (
          <TouchableOpacity key={t} onPress={() => setDocType(t)}
            style={{ flexDirection: 'row', alignItems: 'center', gap: spacing.md, backgroundColor: colors.surface, borderRadius: 10, padding: spacing.md, marginBottom: spacing.sm, borderWidth: 1, borderColor: docType === t ? colors.amber : colors.border }}>
            <View style={{ width: 20, height: 20, borderRadius: 10, borderWidth: 2, borderColor: docType === t ? colors.amber : colors.muted, backgroundColor: docType === t ? colors.amber : 'transparent' }} />
            <Text style={{ color: colors.white, textTransform: 'capitalize' }}>{t.replace(/_/g,' ')}</Text>
          </TouchableOpacity>
        ))}

        <Text style={{ color: colors.muted, fontSize: fontSize.sm, marginBottom: 4, marginTop: spacing.md }}>Document Number</Text>
        <View style={{ backgroundColor: colors.surface, borderWidth: 1, borderColor: colors.border, borderRadius: 10, paddingHorizontal: 16, paddingVertical: 12, marginBottom: spacing.md }}>
          <Text style={{ color: file ? colors.white : colors.muted, fontSize: fontSize.md }}>{docNumber || 'Enter document number'}</Text>
        </View>

        <TouchableOpacity onPress={pickFile}
          style={{ backgroundColor: colors.surface, borderWidth: 2, borderColor: file ? colors.amber : colors.border, borderStyle: 'dashed', borderRadius: 12, padding: spacing.xl, alignItems: 'center', marginBottom: spacing.lg }}>
          <Text style={{ fontSize: 32, marginBottom: spacing.sm }}>📄</Text>
          <Text style={{ color: file ? colors.amber : colors.muted, fontSize: fontSize.md }}>{file ? file.name : 'Tap to upload document'}</Text>
          <Text style={{ color: colors.muted, fontSize: fontSize.xs, marginTop: 4 }}>JPG, PNG or PDF • Max 5MB</Text>
        </TouchableOpacity>

        <TouchableOpacity onPress={handleSubmit} disabled={loading}
          style={{ backgroundColor: colors.amber, borderRadius: 12, paddingVertical: 16, alignItems: 'center', opacity: loading ? 0.6 : 1 }}>
          {loading ? <ActivityIndicator color={colors.white} /> : <Text style={{ color: colors.white, fontWeight: '700', fontSize: fontSize.md }}>Submit KYC</Text>}
        </TouchableOpacity>
      </View>
    </ScrollView>
  )
}
