import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  ScrollView,
  ActivityIndicator,
  Alert,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { AuthStackParamList } from '../../navigation/AppNavigator';
import { authApi } from '../../lib/api';
import { UserRole } from '../../lib/types';

type Props = {
  navigation: NativeStackNavigationProp<AuthStackParamList, 'Register'>;
};

const ROLES: { value: UserRole; label: string; description: string }[] = [
  { value: 'client', label: 'Renter', description: 'I want to hire vehicles or equipment' },
  { value: 'owner', label: 'Asset Owner', description: 'I want to list and rent out my assets' },
  { value: 'broker', label: 'Broker', description: 'I facilitate bookings between clients and owners' },
];

export default function RegisterScreen({ navigation }: Props) {
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [phone, setPhone] = useState('');
  const [password, setPassword] = useState('');
  const [role, setRole] = useState<UserRole>('client');
  const [loading, setLoading] = useState(false);

  const handleRegister = async () => {
    if (!name || !email || !phone || !password) {
      Alert.alert('Error', 'Please fill in all fields.');
      return;
    }
    setLoading(true);
    try {
      await authApi.register({ name, email, phone, password, role });
      navigation.navigate('OTPVerify', { phone });
    } catch (err: unknown) {
      const message =
        (err as { response?: { data?: { message?: string } } })?.response?.data?.message ??
        'Registration failed. Please try again.';
      Alert.alert('Registration Failed', message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.content} keyboardShouldPersistTaps="handled">
      <View style={styles.header}>
        <Text style={styles.logo}>TheOnlineYard</Text>
        <Text style={styles.subtitle}>Create your account</Text>
      </View>

      <View style={styles.form}>
        <Text style={styles.sectionTitle}>I am a...</Text>
        <View style={styles.roleGroup}>
          {ROLES.map((r) => (
            <TouchableOpacity
              key={r.value}
              style={[styles.roleCard, role === r.value && styles.roleCardActive]}
              onPress={() => setRole(r.value)}
            >
              <Text style={[styles.roleLabel, role === r.value && styles.roleLabelActive]}>{r.label}</Text>
              <Text style={styles.roleDesc}>{r.description}</Text>
            </TouchableOpacity>
          ))}
        </View>

        {[['Full Name', name, setName, 'default'], ['Email Address', email, setEmail, 'email-address'], ['Phone (+254...)', phone, setPhone, 'phone-pad']].map(
          ([label, value, setter, keyboardType]) => (
            <View style={styles.field} key={label as string}>
              <Text style={styles.label}>{label as string}</Text>
              <TextInput
                style={styles.input}
                value={value as string}
                onChangeText={setter as (t: string) => void}
                placeholder={label as string}
                placeholderTextColor="#4b5563"
                keyboardType={keyboardType as 'default'}
                autoCapitalize={keyboardType === 'email-address' ? 'none' : 'words'}
              />
            </View>
          ),
        )}

        <View style={styles.field}>
          <Text style={styles.label}>Password</Text>
          <TextInput
            style={styles.input}
            value={password}
            onChangeText={setPassword}
            placeholder="Create a strong password"
            placeholderTextColor="#4b5563"
            secureTextEntry
          />
        </View>

        <TouchableOpacity
          style={[styles.primaryBtn, loading && styles.disabledBtn]}
          onPress={handleRegister}
          disabled={loading}
        >
          {loading ? <ActivityIndicator color="#fff" /> : <Text style={styles.primaryBtnText}>Create Account</Text>}
        </TouchableOpacity>

        <TouchableOpacity style={styles.loginLink} onPress={() => navigation.navigate('Login')}>
          <Text style={styles.loginLinkText}>Already have an account? Log In</Text>
        </TouchableOpacity>
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#080C12' },
  content: { flexGrow: 1, padding: 24 },
  header: { alignItems: 'center', paddingVertical: 32 },
  logo: { fontSize: 26, fontWeight: '800', color: '#E8922A' },
  subtitle: { fontSize: 14, color: '#9ca3af', marginTop: 4 },
  form: { backgroundColor: '#141D2B', borderRadius: 16, padding: 20, marginBottom: 24 },
  sectionTitle: { fontSize: 15, fontWeight: '600', color: '#d1d5db', marginBottom: 12 },
  roleGroup: { gap: 8, marginBottom: 20 },
  roleCard: {
    borderWidth: 1.5,
    borderColor: '#1e2d40',
    borderRadius: 12,
    padding: 12,
    backgroundColor: '#0f1621',
  },
  roleCardActive: { borderColor: '#E8922A', backgroundColor: '#2a1a08' },
  roleLabel: { fontSize: 14, fontWeight: '700', color: '#9ca3af' },
  roleLabelActive: { color: '#E8922A' },
  roleDesc: { fontSize: 12, color: '#6b7280', marginTop: 2 },
  field: { marginBottom: 14 },
  label: { fontSize: 13, fontWeight: '600', color: '#9ca3af', marginBottom: 6 },
  input: {
    backgroundColor: '#0f1621',
    borderWidth: 1,
    borderColor: '#1e2d40',
    borderRadius: 10,
    paddingHorizontal: 14,
    paddingVertical: 12,
    fontSize: 15,
    color: '#ffffff',
  },
  primaryBtn: {
    backgroundColor: '#E8922A',
    borderRadius: 12,
    paddingVertical: 14,
    alignItems: 'center',
    marginTop: 8,
    marginBottom: 12,
  },
  disabledBtn: { opacity: 0.6 },
  primaryBtnText: { fontSize: 16, fontWeight: '700', color: '#ffffff' },
  loginLink: { alignItems: 'center' },
  loginLinkText: { fontSize: 13, color: '#6b7280' },
});
