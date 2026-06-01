import React, { useState } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  ScrollView,
  Image,
  Alert,
  ActivityIndicator,
} from 'react-native';
import { launchCamera, launchImageLibrary, Asset as ImageAsset } from 'react-native-image-picker';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { AuthStackParamList } from '../../navigation/AppNavigator';
import { kycApi } from '../../lib/api';
import { useAuthStore } from '../../lib/store';

type Props = {
  navigation: NativeStackNavigationProp<AuthStackParamList, 'KYC'>;
};

interface DocSlot {
  key: string;
  label: string;
  required: boolean;
  uri?: string;
}

export default function KYCScreen({ navigation: _navigation }: Props) {
  const [docs, setDocs] = useState<DocSlot[]>([
    { key: 'national_id_front', label: 'National ID (Front)', required: true },
    { key: 'national_id_back', label: 'National ID (Back)', required: true },
    { key: 'selfie', label: 'Selfie with ID', required: true },
    { key: 'driving_licence', label: "Driver's Licence (if applicable)", required: false },
  ]);
  const [loading, setLoading] = useState(false);
  const updateUser = useAuthStore((s) => s.updateUser);

  const pickDocument = (key: string, fromCamera: boolean) => {
    const picker = fromCamera ? launchCamera : launchImageLibrary;
    picker(
      { mediaType: 'photo', quality: 0.8, maxWidth: 1600, maxHeight: 1600 },
      (response) => {
        if (response.assets?.[0]) {
          const asset: ImageAsset = response.assets[0];
          setDocs((prev) =>
            prev.map((d) => (d.key === key ? { ...d, uri: asset.uri } : d)),
          );
        }
      },
    );
  };

  const showPicker = (key: string) => {
    Alert.alert('Upload Document', 'Choose source', [
      { text: 'Camera', onPress: () => pickDocument(key, true) },
      { text: 'Gallery', onPress: () => pickDocument(key, false) },
      { text: 'Cancel', style: 'cancel' },
    ]);
  };

  const handleSubmit = async () => {
    const missing = docs.filter((d) => d.required && !d.uri);
    if (missing.length > 0) {
      Alert.alert('Missing Documents', `Please upload: ${missing.map((d) => d.label).join(', ')}`);
      return;
    }
    setLoading(true);
    try {
      const formData = new FormData();
      docs.forEach((doc) => {
        if (doc.uri) {
          formData.append(doc.key, {
            uri: doc.uri,
            type: 'image/jpeg',
            name: `${doc.key}.jpg`,
          } as unknown as Blob);
        }
      });
      await kycApi.upload(formData);
      updateUser({ kycStatus: 'pending' });
      Alert.alert(
        'Submitted',
        'Your documents have been submitted for review. You will be notified within 24 hours.',
        [{ text: 'OK' }],
      );
    } catch {
      Alert.alert('Error', 'Failed to upload documents. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.content}>
      <Text style={styles.title}>Identity Verification</Text>
      <Text style={styles.subtitle}>
        To comply with KYC requirements, please upload the following documents. Your data is
        encrypted and kept secure.
      </Text>

      <View style={styles.docList}>
        {docs.map((doc) => (
          <View key={doc.key} style={styles.docCard}>
            <View style={styles.docInfo}>
              <Text style={styles.docLabel}>{doc.label}</Text>
              {!doc.required && <Text style={styles.optional}>Optional</Text>}
            </View>
            {doc.uri ? (
              <View style={styles.previewRow}>
                <Image source={{ uri: doc.uri }} style={styles.preview} />
                <TouchableOpacity onPress={() => showPicker(doc.key)} style={styles.changeBtn}>
                  <Text style={styles.changeBtnText}>Change</Text>
                </TouchableOpacity>
              </View>
            ) : (
              <TouchableOpacity style={styles.uploadBtn} onPress={() => showPicker(doc.key)}>
                <Text style={styles.uploadIcon}>+</Text>
                <Text style={styles.uploadText}>Tap to upload</Text>
              </TouchableOpacity>
            )}
          </View>
        ))}
      </View>

      <TouchableOpacity
        style={[styles.submitBtn, loading && styles.disabledBtn]}
        onPress={handleSubmit}
        disabled={loading}
      >
        {loading ? <ActivityIndicator color="#fff" /> : <Text style={styles.submitBtnText}>Submit for Review</Text>}
      </TouchableOpacity>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#080C12' },
  content: { padding: 24, paddingBottom: 48 },
  title: { fontSize: 22, fontWeight: '700', color: '#ffffff', marginBottom: 8 },
  subtitle: { fontSize: 13, color: '#9ca3af', marginBottom: 24, lineHeight: 20 },
  docList: { gap: 12, marginBottom: 32 },
  docCard: { backgroundColor: '#141D2B', borderRadius: 12, padding: 16 },
  docInfo: { flexDirection: 'row', alignItems: 'center', gap: 8, marginBottom: 12 },
  docLabel: { fontSize: 14, fontWeight: '600', color: '#e5e7eb', flex: 1 },
  optional: { fontSize: 11, color: '#6b7280', backgroundColor: '#1e2d40', paddingHorizontal: 8, paddingVertical: 2, borderRadius: 10 },
  previewRow: { flexDirection: 'row', alignItems: 'center', gap: 12 },
  preview: { width: 80, height: 56, borderRadius: 8, backgroundColor: '#0f1621' },
  changeBtn: { paddingHorizontal: 14, paddingVertical: 7, borderWidth: 1, borderColor: '#E8922A', borderRadius: 8 },
  changeBtnText: { color: '#E8922A', fontSize: 13, fontWeight: '600' },
  uploadBtn: {
    borderWidth: 1.5,
    borderColor: '#1e2d40',
    borderStyle: 'dashed',
    borderRadius: 10,
    paddingVertical: 24,
    alignItems: 'center',
    gap: 6,
  },
  uploadIcon: { fontSize: 28, color: '#4b5563' },
  uploadText: { fontSize: 13, color: '#6b7280' },
  submitBtn: {
    backgroundColor: '#E8922A',
    borderRadius: 14,
    paddingVertical: 16,
    alignItems: 'center',
  },
  disabledBtn: { opacity: 0.6 },
  submitBtnText: { fontSize: 16, fontWeight: '700', color: '#ffffff' },
});
