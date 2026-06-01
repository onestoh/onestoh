import React, { useState, useRef, useEffect } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  ActivityIndicator,
  Alert,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { RouteProp } from '@react-navigation/native';
import { AuthStackParamList } from '../../navigation/AppNavigator';
import { authApi } from '../../lib/api';
import { useAuthStore } from '../../lib/store';
import { User } from '../../lib/types';

type Props = {
  navigation: NativeStackNavigationProp<AuthStackParamList, 'OTPVerify'>;
  route: RouteProp<AuthStackParamList, 'OTPVerify'>;
};

const OTP_LENGTH = 6;

export default function OTPScreen({ navigation, route }: Props) {
  const { phone } = route.params;
  const [otp, setOtp] = useState<string[]>(Array(OTP_LENGTH).fill(''));
  const [loading, setLoading] = useState(false);
  const [resendCountdown, setResendCountdown] = useState(30);
  const inputRefs = useRef<(TextInput | null)[]>([]);
  const login = useAuthStore((s) => s.login);

  useEffect(() => {
    const timer = setInterval(() => {
      setResendCountdown((c) => (c > 0 ? c - 1 : 0));
    }, 1000);
    return () => clearInterval(timer);
  }, []);

  const handleChange = (text: string, index: number) => {
    const cleaned = text.replace(/[^0-9]/g, '').slice(-1);
    const newOtp = [...otp];
    newOtp[index] = cleaned;
    setOtp(newOtp);

    if (cleaned && index < OTP_LENGTH - 1) {
      inputRefs.current[index + 1]?.focus();
    }

    // Auto-submit when all digits filled
    if (cleaned && index === OTP_LENGTH - 1 && newOtp.every((d) => d)) {
      handleVerify(newOtp.join(''));
    }
  };

  const handleKeyPress = (key: string, index: number) => {
    if (key === 'Backspace' && !otp[index] && index > 0) {
      inputRefs.current[index - 1]?.focus();
    }
  };

  const handleVerify = async (code?: string) => {
    const otpCode = code ?? otp.join('');
    if (otpCode.length < OTP_LENGTH) {
      Alert.alert('Error', 'Please enter the complete 6-digit code.');
      return;
    }
    setLoading(true);
    try {
      const res = await authApi.verifyOtp({ phone, otp: otpCode });
      if (res.data.kycRequired) {
        navigation.navigate('KYC');
      } else {
        login(res.data.token, res.data.user as User);
      }
    } catch {
      Alert.alert('Invalid Code', 'The OTP you entered is incorrect or has expired.');
      setOtp(Array(OTP_LENGTH).fill(''));
      inputRefs.current[0]?.focus();
    } finally {
      setLoading(false);
    }
  };

  const handleResend = async () => {
    if (resendCountdown > 0) return;
    try {
      await authApi.resendOtp({ phone });
      setResendCountdown(30);
      Alert.alert('Sent', 'A new OTP has been sent to your phone.');
    } catch {
      Alert.alert('Error', 'Failed to resend OTP. Please try again.');
    }
  };

  return (
    <View style={styles.container}>
      <View style={styles.inner}>
        <Text style={styles.title}>Verify Your Phone</Text>
        <Text style={styles.subtitle}>
          Enter the 6-digit code sent to{' '}
          <Text style={styles.phone}>{phone}</Text>
        </Text>

        <View style={styles.otpRow}>
          {otp.map((digit, i) => (
            <TextInput
              key={i}
              ref={(ref) => { inputRefs.current[i] = ref; }}
              style={[styles.otpBox, digit ? styles.otpBoxFilled : null]}
              value={digit}
              onChangeText={(t) => handleChange(t, i)}
              onKeyPress={({ nativeEvent }) => handleKeyPress(nativeEvent.key, i)}
              keyboardType="number-pad"
              maxLength={1}
              selectTextOnFocus
            />
          ))}
        </View>

        <TouchableOpacity
          style={[styles.verifyBtn, loading && styles.disabledBtn]}
          onPress={() => handleVerify()}
          disabled={loading}
        >
          {loading ? <ActivityIndicator color="#fff" /> : <Text style={styles.verifyBtnText}>Verify</Text>}
        </TouchableOpacity>

        <TouchableOpacity
          style={styles.resendBtn}
          onPress={handleResend}
          disabled={resendCountdown > 0}
        >
          <Text style={[styles.resendText, resendCountdown > 0 && styles.resendDisabled]}>
            {resendCountdown > 0 ? `Resend in ${resendCountdown}s` : 'Resend OTP'}
          </Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#080C12', justifyContent: 'center', padding: 24 },
  inner: { backgroundColor: '#141D2B', borderRadius: 16, padding: 28, alignItems: 'center' },
  title: { fontSize: 22, fontWeight: '700', color: '#ffffff', marginBottom: 10 },
  subtitle: { fontSize: 14, color: '#9ca3af', textAlign: 'center', marginBottom: 32, lineHeight: 20 },
  phone: { color: '#E8922A', fontWeight: '600' },
  otpRow: { flexDirection: 'row', gap: 10, marginBottom: 32 },
  otpBox: {
    width: 46,
    height: 54,
    backgroundColor: '#0f1621',
    borderWidth: 2,
    borderColor: '#1e2d40',
    borderRadius: 10,
    textAlign: 'center',
    fontSize: 22,
    fontWeight: '700',
    color: '#ffffff',
  },
  otpBoxFilled: { borderColor: '#E8922A' },
  verifyBtn: {
    backgroundColor: '#E8922A',
    borderRadius: 12,
    paddingVertical: 14,
    paddingHorizontal: 48,
    alignItems: 'center',
    marginBottom: 16,
    width: '100%',
  },
  disabledBtn: { opacity: 0.6 },
  verifyBtnText: { fontSize: 16, fontWeight: '700', color: '#ffffff' },
  resendBtn: { paddingVertical: 8 },
  resendText: { fontSize: 14, color: '#E8922A', fontWeight: '500' },
  resendDisabled: { color: '#4b5563' },
});
