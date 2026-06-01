import React, { useState, useEffect, useRef } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ActivityIndicator, Alert } from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { RouteProp } from '@react-navigation/native';
import { MarketplaceStackParamList } from '../../navigation/AppNavigator';
import { bookingsApi } from '../../lib/api';

type Props = {
  navigation: NativeStackNavigationProp<MarketplaceStackParamList, 'MpesaPayment'>;
  route: RouteProp<MarketplaceStackParamList, 'MpesaPayment'>;
};

const TIMEOUT_SECONDS = 15 * 60; // 15 minutes
const POLL_INTERVAL = 5000; // 5 seconds

export default function MpesaPaymentScreen({ navigation, route }: Props) {
  const { bookingId, phone, amountKES } = route.params;
  const [secondsLeft, setSecondsLeft] = useState(TIMEOUT_SECONDS);
  const [paymentStatus, setPaymentStatus] = useState<'pending' | 'success' | 'failed'>('pending');
  const pollingRef = useRef<ReturnType<typeof setInterval> | null>(null);
  const countdownRef = useRef<ReturnType<typeof setInterval> | null>(null);

  useEffect(() => {
    // Countdown
    countdownRef.current = setInterval(() => {
      setSecondsLeft((s) => {
        if (s <= 1) {
          clearInterval(countdownRef.current!);
          setPaymentStatus('failed');
          return 0;
        }
        return s - 1;
      });
    }, 1000);

    // Poll booking status
    pollingRef.current = setInterval(async () => {
      try {
        const res = await bookingsApi.pollPaymentStatus(bookingId);
        if (res.data.status === 'confirmed') {
          clearAll();
          setPaymentStatus('success');
        } else if (res.data.status === 'payment_failed') {
          clearAll();
          setPaymentStatus('failed');
        }
      } catch {
        // continue polling
      }
    }, POLL_INTERVAL);

    return clearAll;
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const clearAll = () => {
    if (pollingRef.current) clearInterval(pollingRef.current);
    if (countdownRef.current) clearInterval(countdownRef.current);
  };

  const formatTime = (s: number) => {
    const m = Math.floor(s / 60);
    const sec = s % 60;
    return `${String(m).padStart(2, '0')}:${String(sec).padStart(2, '0')}`;
  };

  if (paymentStatus === 'success') {
    return (
      <View style={styles.container}>
        <View style={styles.resultCard}>
          <Text style={styles.resultIcon}>✅</Text>
          <Text style={styles.resultTitle}>Payment Successful!</Text>
          <Text style={styles.resultSubtitle}>
            Your booking is confirmed. You will receive a confirmation SMS shortly.
          </Text>
          <TouchableOpacity
            style={styles.doneBtn}
            onPress={() => navigation.popToTop()}
          >
            <Text style={styles.doneBtnText}>View My Bookings</Text>
          </TouchableOpacity>
        </View>
      </View>
    );
  }

  if (paymentStatus === 'failed') {
    return (
      <View style={styles.container}>
        <View style={styles.resultCard}>
          <Text style={styles.resultIcon}>❌</Text>
          <Text style={styles.resultTitle}>Payment Failed</Text>
          <Text style={styles.resultSubtitle}>
            The payment was not completed. Please try again or contact support.
          </Text>
          <TouchableOpacity
            style={[styles.doneBtn, styles.retryBtn]}
            onPress={() => navigation.goBack()}
          >
            <Text style={styles.doneBtnText}>Try Again</Text>
          </TouchableOpacity>
        </View>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <View style={styles.card}>
        <Text style={styles.phoneIcon}>📱</Text>
        <Text style={styles.title}>Check Your Phone</Text>
        <Text style={styles.subtitle}>
          An M-Pesa STK Push has been sent to
        </Text>
        <Text style={styles.phone}>{phone}</Text>
        <Text style={styles.amountLabel}>Amount</Text>
        <Text style={styles.amount}>KES {amountKES.toLocaleString()}</Text>

        <View style={styles.timerBox}>
          <Text style={styles.timerLabel}>Time remaining</Text>
          <Text style={styles.timer}>{formatTime(secondsLeft)}</Text>
        </View>

        <ActivityIndicator size="large" color="#E8922A" style={{ marginTop: 24 }} />
        <Text style={styles.pollingText}>Checking payment status every 5 seconds...</Text>

        <TouchableOpacity
          style={styles.cancelBtn}
          onPress={() => {
            Alert.alert('Cancel Payment', 'Are you sure you want to cancel?', [
              { text: 'No', style: 'cancel' },
              {
                text: 'Yes, Cancel',
                style: 'destructive',
                onPress: () => {
                  clearAll();
                  navigation.goBack();
                },
              },
            ]);
          }}
        >
          <Text style={styles.cancelBtnText}>Cancel</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#080C12', justifyContent: 'center', padding: 24 },
  card: { backgroundColor: '#141D2B', borderRadius: 20, padding: 28, alignItems: 'center' },
  resultCard: { backgroundColor: '#141D2B', borderRadius: 20, padding: 32, alignItems: 'center' },
  phoneIcon: { fontSize: 56, marginBottom: 16 },
  title: { fontSize: 22, fontWeight: '700', color: '#ffffff', marginBottom: 8, textAlign: 'center' },
  subtitle: { fontSize: 14, color: '#9ca3af', textAlign: 'center' },
  phone: { fontSize: 18, fontWeight: '700', color: '#E8922A', marginTop: 4, marginBottom: 16 },
  amountLabel: { fontSize: 12, color: '#9ca3af' },
  amount: { fontSize: 26, fontWeight: '800', color: '#ffffff', marginBottom: 20 },
  timerBox: { backgroundColor: '#0f1621', borderRadius: 12, paddingHorizontal: 24, paddingVertical: 12, alignItems: 'center' },
  timerLabel: { fontSize: 11, color: '#9ca3af' },
  timer: { fontSize: 32, fontWeight: '800', color: '#E8922A', fontVariant: ['tabular-nums'] },
  pollingText: { fontSize: 12, color: '#6b7280', marginTop: 12 },
  cancelBtn: { marginTop: 24, paddingVertical: 10 },
  cancelBtnText: { color: '#9ca3af', fontSize: 14 },
  resultIcon: { fontSize: 60, marginBottom: 16 },
  resultTitle: { fontSize: 22, fontWeight: '700', color: '#ffffff', marginBottom: 10, textAlign: 'center' },
  resultSubtitle: { fontSize: 14, color: '#9ca3af', textAlign: 'center', lineHeight: 20, marginBottom: 28 },
  doneBtn: { backgroundColor: '#E8922A', borderRadius: 12, paddingVertical: 14, paddingHorizontal: 32 },
  retryBtn: { backgroundColor: '#dc2626' },
  doneBtnText: { fontSize: 15, fontWeight: '700', color: '#ffffff' },
});
