import React, { useState } from 'react';
import {
  View,
  Text,
  ScrollView,
  TouchableOpacity,
  StyleSheet,
  Switch,
  Alert,
  ActivityIndicator,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { RouteProp } from '@react-navigation/native';
import { MarketplaceStackParamList } from '../../navigation/AppNavigator';
import { bookingsApi } from '../../lib/api';
import PriceRow from '../../components/PriceRow';

type Props = {
  navigation: NativeStackNavigationProp<MarketplaceStackParamList, 'Booking'>;
  route: RouteProp<MarketplaceStackParamList, 'Booking'>;
};

type RentalType = 'self_drive' | 'chauffeur';

export default function BookingScreen({ navigation, route }: Props) {
  const { assetId } = route.params;
  const [rentalType, setRentalType] = useState<RentalType>('self_drive');
  const [cdwEnabled, setCdwEnabled] = useState(false);
  const [tplEnabled, setTplEnabled] = useState(true);
  const [fromDate] = useState('2024-07-15');
  const [toDate] = useState('2024-07-17');
  const [loading, setLoading] = useState(false);

  const DAYS = 3;
  const DAILY_RATE = 15000;
  const CHAUFFEUR_DAILY = 3000;
  const CDW_DAILY = 500;
  const TPL_DAILY = 300;
  const PLATFORM_PCT = 0.07;

  const baseRate = DAILY_RATE * DAYS + (rentalType === 'chauffeur' ? CHAUFFEUR_DAILY * DAYS : 0);
  const insuranceCost = (cdwEnabled ? CDW_DAILY * DAYS : 0) + (tplEnabled ? TPL_DAILY * DAYS : 0);
  const platformFee = Math.round(baseRate * PLATFORM_PCT);
  const total = baseRate + insuranceCost + platformFee;

  const handlePay = async () => {
    setLoading(true);
    try {
      const res = await bookingsApi.create({
        assetId,
        fromDate,
        toDate,
        rentalType,
        insuranceProducts: [
          ...(cdwEnabled ? ['cdw'] : []),
          ...(tplEnabled ? ['tpl'] : []),
        ],
      });
      navigation.navigate('MpesaPayment', {
        bookingId: res.data.bookingId,
        phone: '+254700000000',
        amountKES: total,
      });
    } catch {
      Alert.alert('Error', 'Failed to create booking. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      <ScrollView contentContainerStyle={styles.content}>
        {/* Dates */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Rental Period</Text>
          <View style={styles.dateRow}>
            <View style={styles.dateBox}>
              <Text style={styles.dateLabel}>Pick-up</Text>
              <Text style={styles.dateValue}>{fromDate}</Text>
            </View>
            <Text style={styles.dateSep}>→</Text>
            <View style={styles.dateBox}>
              <Text style={styles.dateLabel}>Return</Text>
              <Text style={styles.dateValue}>{toDate}</Text>
            </View>
          </View>
          <Text style={styles.duration}>{DAYS} days</Text>
        </View>

        {/* Rental Type */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Rental Type</Text>
          {(['self_drive', 'chauffeur'] as RentalType[]).map((rt) => (
            <TouchableOpacity
              key={rt}
              style={[styles.radioRow, rentalType === rt && styles.radioRowActive]}
              onPress={() => setRentalType(rt)}
            >
              <View style={[styles.radio, rentalType === rt && styles.radioActive]}>
                {rentalType === rt && <View style={styles.radioDot} />}
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.radioLabel}>
                  {rt === 'self_drive' ? 'Self Drive' : 'With Chauffeur'}
                </Text>
                {rt === 'chauffeur' && (
                  <Text style={styles.radioSubLabel}>+KES {CHAUFFEUR_DAILY.toLocaleString()}/day</Text>
                )}
              </View>
            </TouchableOpacity>
          ))}
        </View>

        {/* Insurance Add-ons */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Insurance Add-ons</Text>
          <View style={styles.insuranceRow}>
            <View style={{ flex: 1 }}>
              <Text style={styles.insuranceLabel}>Collision Damage Waiver (CDW)</Text>
              <Text style={styles.insurancePrice}>KES {CDW_DAILY}/day</Text>
            </View>
            <Switch
              value={cdwEnabled}
              onValueChange={setCdwEnabled}
              trackColor={{ false: '#1e2d40', true: '#E8922A' }}
              thumbColor="#ffffff"
            />
          </View>
          <View style={styles.insuranceRow}>
            <View style={{ flex: 1 }}>
              <Text style={styles.insuranceLabel}>Third Party Liability (TPL)</Text>
              <Text style={styles.insurancePrice}>KES {TPL_DAILY}/day</Text>
            </View>
            <Switch
              value={tplEnabled}
              onValueChange={setTplEnabled}
              trackColor={{ false: '#1e2d40', true: '#E8922A' }}
              thumbColor="#ffffff"
            />
          </View>
        </View>

        {/* Price Breakdown */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Price Breakdown</Text>
          <PriceRow label={`Daily rate × ${DAYS} days`} value={`KES ${(DAILY_RATE * DAYS).toLocaleString()}`} />
          {rentalType === 'chauffeur' && (
            <PriceRow label={`Chauffeur × ${DAYS} days`} value={`KES ${(CHAUFFEUR_DAILY * DAYS).toLocaleString()}`} />
          )}
          {cdwEnabled && <PriceRow label={`CDW × ${DAYS} days`} value={`KES ${(CDW_DAILY * DAYS).toLocaleString()}`} />}
          {tplEnabled && <PriceRow label={`TPL × ${DAYS} days`} value={`KES ${(TPL_DAILY * DAYS).toLocaleString()}`} />}
          <PriceRow label="Platform fee (7%)" value={`KES ${platformFee.toLocaleString()}`} />
          <PriceRow label="Total" value={`KES ${total.toLocaleString()}`} bold />
        </View>
      </ScrollView>

      <View style={styles.ctaBar}>
        <View>
          <Text style={styles.totalLabel}>Total</Text>
          <Text style={styles.totalValue}>KES {total.toLocaleString()}</Text>
        </View>
        <TouchableOpacity
          style={[styles.payBtn, loading && styles.disabledBtn]}
          onPress={handlePay}
          disabled={loading}
        >
          {loading ? <ActivityIndicator color="#fff" /> : <Text style={styles.payBtnText}>Pay with M-Pesa</Text>}
        </TouchableOpacity>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#080C12' },
  content: { padding: 16, gap: 12, paddingBottom: 16 },
  card: { backgroundColor: '#141D2B', borderRadius: 14, padding: 16 },
  cardTitle: { fontSize: 14, fontWeight: '700', color: '#d1d5db', marginBottom: 12 },
  dateRow: { flexDirection: 'row', alignItems: 'center', gap: 12 },
  dateBox: { flex: 1 },
  dateLabel: { fontSize: 11, color: '#9ca3af' },
  dateValue: { fontSize: 15, fontWeight: '700', color: '#ffffff', marginTop: 2 },
  dateSep: { fontSize: 18, color: '#6b7280' },
  duration: { marginTop: 8, fontSize: 13, color: '#E8922A', fontWeight: '600' },
  radioRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
    padding: 12,
    borderRadius: 10,
    borderWidth: 1.5,
    borderColor: '#1e2d40',
    marginBottom: 8,
  },
  radioRowActive: { borderColor: '#E8922A', backgroundColor: '#2a1a08' },
  radio: { width: 20, height: 20, borderRadius: 10, borderWidth: 2, borderColor: '#4b5563', alignItems: 'center', justifyContent: 'center' },
  radioActive: { borderColor: '#E8922A' },
  radioDot: { width: 10, height: 10, borderRadius: 5, backgroundColor: '#E8922A' },
  radioLabel: { fontSize: 14, fontWeight: '600', color: '#e5e7eb' },
  radioSubLabel: { fontSize: 12, color: '#E8922A', marginTop: 1 },
  insuranceRow: { flexDirection: 'row', alignItems: 'center', paddingVertical: 10, borderBottomWidth: 1, borderBottomColor: '#1e2d40' },
  insuranceLabel: { fontSize: 14, color: '#e5e7eb', fontWeight: '600' },
  insurancePrice: { fontSize: 12, color: '#9ca3af', marginTop: 2 },
  ctaBar: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    padding: 16,
    backgroundColor: '#141D2B',
    borderTopWidth: 1,
    borderTopColor: '#1e2d40',
  },
  totalLabel: { fontSize: 12, color: '#9ca3af' },
  totalValue: { fontSize: 20, fontWeight: '800', color: '#E8922A' },
  payBtn: { backgroundColor: '#E8922A', paddingHorizontal: 20, paddingVertical: 14, borderRadius: 12 },
  disabledBtn: { opacity: 0.6 },
  payBtnText: { fontSize: 14, fontWeight: '700', color: '#ffffff' },
});
