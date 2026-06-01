import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet, ScrollView, Alert, ActivityIndicator, Image,
} from 'react-native';
import { launchImageLibrary } from 'react-native-image-picker';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { OwnerStackParamList } from '../../navigation/AppNavigator';
import { assetsApi } from '../../lib/api';

type Props = {
  navigation: NativeStackNavigationProp<OwnerStackParamList, 'AddListing'>;
};

const STEPS = ['Basic Info', 'Pricing', 'Photos', 'Review'];
const CATEGORIES = ['Car', 'SUV', 'Truck', 'Bus', 'Machinery', 'Boat'];

export default function AddListingScreen({ navigation }: Props) {
  const [step, setStep] = useState(0);
  const [form, setForm] = useState({
    name: '', make: '', model: '', year: '', category: 'Car', description: '', location: '',
    dailyRateKES: '', weeklyRateKES: '', monthlyRateKES: '',
    photos: [] as string[],
  });
  const [loading, setLoading] = useState(false);

  const update = (key: string, value: string) => setForm((f) => ({ ...f, [key]: value }));

  const pickPhotos = () => {
    launchImageLibrary({ mediaType: 'photo', selectionLimit: 6 }, (res) => {
      if (res.assets) {
        setForm((f) => ({ ...f, photos: [...f.photos, ...res.assets!.map((a) => a.uri!)] }));
      }
    });
  };

  const handleSubmit = async () => {
    setLoading(true);
    try {
      const fd = new FormData();
      Object.entries(form).forEach(([k, v]) => {
        if (k !== 'photos') fd.append(k, String(v));
      });
      form.photos.forEach((uri, i) => {
        fd.append('photos', { uri, type: 'image/jpeg', name: `photo_${i}.jpg` } as unknown as Blob);
      });
      await assetsApi.create(fd);
      Alert.alert('Success', 'Listing submitted for review!', [
        { text: 'OK', onPress: () => navigation.goBack() },
      ]);
    } catch {
      Alert.alert('Error', 'Failed to create listing.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      {/* Progress */}
      <View style={styles.progress}>
        {STEPS.map((s, i) => (
          <View key={s} style={styles.stepItem}>
            <View style={[styles.stepDot, i <= step && styles.stepDotActive]}>
              <Text style={styles.stepDotText}>{i + 1}</Text>
            </View>
            <Text style={[styles.stepLabel, i === step && styles.stepLabelActive]}>{s}</Text>
          </View>
        ))}
      </View>

      <ScrollView contentContainerStyle={styles.content} keyboardShouldPersistTaps="handled">
        {step === 0 && (
          <>
            <Text style={styles.sectionTitle}>Basic Information</Text>
            {[['Listing Title', 'name'], ['Make', 'make'], ['Model', 'model'], ['Year', 'year'], ['Location', 'location']].map(([label, key]) => (
              <View key={key} style={styles.field}>
                <Text style={styles.label}>{label}</Text>
                <TextInput
                  style={styles.input}
                  value={form[key as keyof typeof form] as string}
                  onChangeText={(t) => update(key, t)}
                  placeholder={label}
                  placeholderTextColor="#4b5563"
                  keyboardType={key === 'year' ? 'number-pad' : 'default'}
                />
              </View>
            ))}
            <View style={styles.field}>
              <Text style={styles.label}>Category</Text>
              <View style={styles.categoryRow}>
                {CATEGORIES.map((cat) => (
                  <TouchableOpacity
                    key={cat}
                    style={[styles.catBtn, form.category === cat && styles.catBtnActive]}
                    onPress={() => update('category', cat)}
                  >
                    <Text style={[styles.catBtnText, form.category === cat && styles.catBtnTextActive]}>{cat}</Text>
                  </TouchableOpacity>
                ))}
              </View>
            </View>
            <View style={styles.field}>
              <Text style={styles.label}>Description</Text>
              <TextInput
                style={[styles.input, { height: 100, textAlignVertical: 'top' }]}
                value={form.description}
                onChangeText={(t) => update('description', t)}
                placeholder="Describe your asset..."
                placeholderTextColor="#4b5563"
                multiline
              />
            </View>
          </>
        )}

        {step === 1 && (
          <>
            <Text style={styles.sectionTitle}>Pricing (KES)</Text>
            {[['Daily Rate', 'dailyRateKES'], ['Weekly Rate', 'weeklyRateKES'], ['Monthly Rate', 'monthlyRateKES']].map(([label, key]) => (
              <View key={key} style={styles.field}>
                <Text style={styles.label}>{label}</Text>
                <TextInput
                  style={styles.input}
                  value={form[key as keyof typeof form] as string}
                  onChangeText={(t) => update(key, t)}
                  placeholder="0"
                  placeholderTextColor="#4b5563"
                  keyboardType="number-pad"
                />
              </View>
            ))}
          </>
        )}

        {step === 2 && (
          <>
            <Text style={styles.sectionTitle}>Photos (up to 6)</Text>
            <View style={styles.photoGrid}>
              {form.photos.map((uri, i) => (
                <Image key={i} source={{ uri }} style={styles.photoThumb} />
              ))}
              {form.photos.length < 6 && (
                <TouchableOpacity style={styles.addPhotoBtn} onPress={pickPhotos}>
                  <Text style={styles.addPhotoBtnText}>+</Text>
                </TouchableOpacity>
              )}
            </View>
          </>
        )}

        {step === 3 && (
          <>
            <Text style={styles.sectionTitle}>Review & Submit</Text>
            <View style={styles.reviewCard}>
              {Object.entries(form).filter(([k]) => k !== 'photos').map(([k, v]) => (
                <View key={k} style={styles.reviewRow}>
                  <Text style={styles.reviewKey}>{k}</Text>
                  <Text style={styles.reviewVal}>{String(v) || '—'}</Text>
                </View>
              ))}
              <Text style={styles.reviewKey}>Photos</Text>
              <Text style={styles.reviewVal}>{form.photos.length} uploaded</Text>
            </View>
          </>
        )}
      </ScrollView>

      {/* Navigation Buttons */}
      <View style={styles.navRow}>
        {step > 0 && (
          <TouchableOpacity style={styles.backBtn} onPress={() => setStep((s) => s - 1)}>
            <Text style={styles.backBtnText}>← Back</Text>
          </TouchableOpacity>
        )}
        {step < STEPS.length - 1 ? (
          <TouchableOpacity style={styles.nextBtn} onPress={() => setStep((s) => s + 1)}>
            <Text style={styles.nextBtnText}>Next →</Text>
          </TouchableOpacity>
        ) : (
          <TouchableOpacity
            style={[styles.nextBtn, loading && { opacity: 0.6 }]}
            onPress={handleSubmit}
            disabled={loading}
          >
            {loading ? <ActivityIndicator color="#fff" /> : <Text style={styles.nextBtnText}>Submit Listing</Text>}
          </TouchableOpacity>
        )}
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#080C12' },
  progress: { flexDirection: 'row', justifyContent: 'space-around', padding: 16, backgroundColor: '#141D2B' },
  stepItem: { alignItems: 'center', gap: 4 },
  stepDot: { width: 28, height: 28, borderRadius: 14, backgroundColor: '#1e2d40', alignItems: 'center', justifyContent: 'center' },
  stepDotActive: { backgroundColor: '#E8922A' },
  stepDotText: { color: '#ffffff', fontSize: 12, fontWeight: '700' },
  stepLabel: { fontSize: 10, color: '#6b7280' },
  stepLabelActive: { color: '#E8922A', fontWeight: '700' },
  content: { padding: 16, paddingBottom: 20 },
  sectionTitle: { fontSize: 17, fontWeight: '700', color: '#ffffff', marginBottom: 16 },
  field: { marginBottom: 14 },
  label: { fontSize: 13, fontWeight: '600', color: '#9ca3af', marginBottom: 6 },
  input: { backgroundColor: '#141D2B', borderWidth: 1, borderColor: '#1e2d40', borderRadius: 10, paddingHorizontal: 14, paddingVertical: 12, fontSize: 15, color: '#ffffff' },
  categoryRow: { flexDirection: 'row', flexWrap: 'wrap', gap: 8 },
  catBtn: { paddingHorizontal: 14, paddingVertical: 8, backgroundColor: '#141D2B', borderRadius: 20, borderWidth: 1, borderColor: '#1e2d40' },
  catBtnActive: { backgroundColor: '#E8922A', borderColor: '#E8922A' },
  catBtnText: { fontSize: 13, color: '#9ca3af', fontWeight: '500' },
  catBtnTextActive: { color: '#ffffff', fontWeight: '700' },
  photoGrid: { flexDirection: 'row', flexWrap: 'wrap', gap: 10 },
  photoThumb: { width: 100, height: 80, borderRadius: 10, backgroundColor: '#141D2B' },
  addPhotoBtn: { width: 100, height: 80, borderRadius: 10, borderWidth: 1.5, borderColor: '#1e2d40', borderStyle: 'dashed', alignItems: 'center', justifyContent: 'center' },
  addPhotoBtnText: { fontSize: 28, color: '#4b5563' },
  reviewCard: { backgroundColor: '#141D2B', borderRadius: 14, padding: 16, gap: 8 },
  reviewRow: { flexDirection: 'row', justifyContent: 'space-between' },
  reviewKey: { fontSize: 12, color: '#9ca3af', textTransform: 'capitalize' },
  reviewVal: { fontSize: 13, color: '#e5e7eb', fontWeight: '500', textAlign: 'right', flex: 1, marginLeft: 8 },
  navRow: { flexDirection: 'row', padding: 16, gap: 10, borderTopWidth: 1, borderTopColor: '#1e2d40', backgroundColor: '#141D2B' },
  backBtn: { flex: 1, paddingVertical: 13, backgroundColor: '#1e2d40', borderRadius: 10, alignItems: 'center' },
  backBtnText: { color: '#9ca3af', fontWeight: '600', fontSize: 14 },
  nextBtn: { flex: 2, paddingVertical: 13, backgroundColor: '#E8922A', borderRadius: 10, alignItems: 'center' },
  nextBtnText: { color: '#ffffff', fontWeight: '700', fontSize: 14 },
});
