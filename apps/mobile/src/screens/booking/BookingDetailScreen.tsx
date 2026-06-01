import React, { useState, useEffect } from 'react';
import {
  View, Text, ScrollView, StyleSheet, TouchableOpacity, Alert, ActivityIndicator, Image,
} from 'react-native';
import { launchCamera, launchImageLibrary } from 'react-native-image-picker';
import { RouteProp } from '@react-navigation/native';
import { BookingsStackParamList } from '../../navigation/AppNavigator';
import { bookingsApi } from '../../lib/api';
import { Booking } from '../../lib/types';
import StatusBadge from '../../components/StatusBadge';
import PriceRow from '../../components/PriceRow';

type Props = {
  route: RouteProp<BookingsStackParamList, 'BookingDetail'>;
};

export default function BookingDetailScreen({ route }: Props) {
  const { bookingId } = route.params;
  const [booking, setBooking] = useState<Booking | null>(null);
  const [loading, setLoading] = useState(true);
  const [uploadedPhotos, setUploadedPhotos] = useState<{ uri: string; type: 'pre' | 'post' }[]>([]);
  const [showDisputeModal, setShowDisputeModal] = useState(false);

  useEffect(() => {
    bookingsApi.detail(bookingId).then((res) => {
      setBooking(res.data);
    }).finally(() => setLoading(false));
  }, [bookingId]);

  const handleUploadPhoto = (photoType: 'pre' | 'post') => {
    Alert.alert('Upload Photo', 'Choose source', [
      {
        text: 'Camera',
        onPress: () => launchCamera({ mediaType: 'photo', quality: 0.8 }, (res) => {
          if (res.assets?.[0]?.uri) {
            const uri = res.assets[0].uri!;
            setUploadedPhotos((prev) => [...prev, { uri, type: photoType }]);
            const fd = new FormData();
            fd.append('photo', { uri, type: 'image/jpeg', name: 'photo.jpg' } as unknown as Blob);
            bookingsApi.uploadPhoto(bookingId, photoType, fd).catch(() => {});
          }
        }),
      },
      {
        text: 'Gallery',
        onPress: () => launchImageLibrary({ mediaType: 'photo' }, (res) => {
          if (res.assets?.[0]?.uri) {
            const uri = res.assets[0].uri!;
            setUploadedPhotos((prev) => [...prev, { uri, type: photoType }]);
          }
        }),
      },
      { text: 'Cancel', style: 'cancel' },
    ]);
  };

  if (loading) {
    return <View style={styles.centered}><ActivityIndicator size="large" color="#E8922A" /></View>;
  }

  if (!booking) {
    return <View style={styles.centered}><Text style={styles.errorText}>Booking not found.</Text></View>;
  }

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.content}>
      {/* Header */}
      <View style={styles.card}>
        <View style={styles.headerRow}>
          <Text style={styles.assetName}>{booking.assetName}</Text>
          <StatusBadge status={booking.status} />
        </View>
        <Text style={styles.refText}>Ref: {booking.id}</Text>
      </View>

      {/* Details */}
      <View style={styles.card}>
        <Text style={styles.sectionTitle}>Booking Details</Text>
        <PriceRow label="Pick-up" value={booking.fromDate} />
        <PriceRow label="Return" value={booking.toDate} />
        <PriceRow label="Type" value={booking.rentalType === 'self_drive' ? 'Self Drive' : 'Chauffeur'} />
        <PriceRow label="Pickup Location" value={booking.pickupLocation} />
        <PriceRow label="Total" value={`KES ${booking.totalKES.toLocaleString()}`} bold />
      </View>

      {/* Driver Info */}
      {booking.driverName && (
        <View style={styles.card}>
          <Text style={styles.sectionTitle}>Assigned Driver</Text>
          <View style={styles.driverRow}>
            <View style={styles.driverAvatar}>
              <Text style={styles.driverAvatarText}>{booking.driverName.charAt(0)}</Text>
            </View>
            <View style={{ flex: 1 }}>
              <Text style={styles.driverName}>{booking.driverName}</Text>
            </View>
            <TouchableOpacity style={styles.callBtn}>
              <Text style={styles.callBtnText}>📞 Call</Text>
            </TouchableOpacity>
          </View>
        </View>
      )}

      {/* Photos */}
      <View style={styles.card}>
        <Text style={styles.sectionTitle}>Condition Photos</Text>
        <View style={styles.photoRow}>
          <TouchableOpacity style={styles.photoBtn} onPress={() => handleUploadPhoto('pre')}>
            <Text style={styles.photoBtnIcon}>📷</Text>
            <Text style={styles.photoBtnText}>Pre-rental</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.photoBtn} onPress={() => handleUploadPhoto('post')}>
            <Text style={styles.photoBtnIcon}>📷</Text>
            <Text style={styles.photoBtnText}>Post-rental</Text>
          </TouchableOpacity>
        </View>
        {uploadedPhotos.length > 0 && (
          <View style={styles.photoGrid}>
            {uploadedPhotos.map((p, i) => (
              <Image key={i} source={{ uri: p.uri }} style={styles.uploadedPhoto} />
            ))}
          </View>
        )}
      </View>

      {/* Dispute */}
      {booking.status === 'completed' && (
        <TouchableOpacity
          style={styles.disputeBtn}
          onPress={() => setShowDisputeModal(true)}
        >
          <Text style={styles.disputeBtnText}>Raise a Dispute</Text>
        </TouchableOpacity>
      )}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#080C12' },
  content: { padding: 16, gap: 12, paddingBottom: 32 },
  centered: { flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: '#080C12' },
  errorText: { color: '#9ca3af', fontSize: 15 },
  card: { backgroundColor: '#141D2B', borderRadius: 14, padding: 16 },
  headerRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start' },
  assetName: { flex: 1, fontSize: 17, fontWeight: '700', color: '#ffffff', marginRight: 8 },
  refText: { fontSize: 11, color: '#6b7280', marginTop: 4, fontFamily: 'monospace' },
  sectionTitle: { fontSize: 13, fontWeight: '700', color: '#9ca3af', marginBottom: 10, textTransform: 'uppercase', letterSpacing: 0.5 },
  driverRow: { flexDirection: 'row', alignItems: 'center', gap: 12 },
  driverAvatar: { width: 40, height: 40, borderRadius: 20, backgroundColor: '#E8922A', alignItems: 'center', justifyContent: 'center' },
  driverAvatarText: { fontSize: 18, fontWeight: '700', color: '#ffffff' },
  driverName: { fontSize: 14, fontWeight: '600', color: '#e5e7eb' },
  callBtn: { backgroundColor: '#1e2d40', paddingHorizontal: 12, paddingVertical: 8, borderRadius: 8 },
  callBtnText: { fontSize: 13, color: '#E8922A', fontWeight: '600' },
  photoRow: { flexDirection: 'row', gap: 10 },
  photoBtn: { flex: 1, borderWidth: 1.5, borderColor: '#1e2d40', borderStyle: 'dashed', borderRadius: 10, paddingVertical: 16, alignItems: 'center', gap: 4 },
  photoBtnIcon: { fontSize: 24 },
  photoBtnText: { fontSize: 12, color: '#9ca3af' },
  photoGrid: { flexDirection: 'row', flexWrap: 'wrap', gap: 8, marginTop: 10 },
  uploadedPhoto: { width: 72, height: 72, borderRadius: 8 },
  disputeBtn: { backgroundColor: '#1e2d40', borderRadius: 12, paddingVertical: 14, alignItems: 'center', borderWidth: 1, borderColor: '#dc2626' },
  disputeBtnText: { color: '#dc2626', fontSize: 14, fontWeight: '700' },
});
