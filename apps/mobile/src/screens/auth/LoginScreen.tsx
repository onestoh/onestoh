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
import { useAuthStore } from '../../lib/store';
import { User } from '../../lib/types';

type Props = {
  navigation: NativeStackNavigationProp<AuthStackParamList, 'Login'>;
};

export default function LoginScreen({ navigation }: Props) {
  const [identifier, setIdentifier] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const login = useAuthStore((s) => s.login);

  const handleLogin = async () => {
    if (!identifier.trim() || !password.trim()) {
      Alert.alert('Error', 'Please enter your email/phone and password.');
      return;
    }
    setLoading(true);
    try {
      const res = await authApi.login({ identifier, password });
      login(res.data.token, res.data.user as User);
    } catch (err: unknown) {
      const message =
        (err as { response?: { data?: { message?: string } } })?.response?.data?.message ??
        'Login failed. Please check your credentials.';
      Alert.alert('Login Failed', message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.content} keyboardShouldPersistTaps="handled">
      <View style={styles.header}>
        <Text style={styles.logo}>TheOnlineYard</Text>
        <Text style={styles.tagline}>Kenya&apos;s Asset Hire Marketplace</Text>
      </View>

      <View style={styles.form}>
        <Text style={styles.title}>Welcome Back</Text>

        <View style={styles.field}>
          <Text style={styles.label}>Phone or Email</Text>
          <TextInput
            style={styles.input}
            value={identifier}
            onChangeText={setIdentifier}
            placeholder="+254 700 000 000"
            placeholderTextColor="#4b5563"
            autoCapitalize="none"
            keyboardType="email-address"
          />
        </View>

        <View style={styles.field}>
          <Text style={styles.label}>Password</Text>
          <TextInput
            style={styles.input}
            value={password}
            onChangeText={setPassword}
            placeholder="••••••••"
            placeholderTextColor="#4b5563"
            secureTextEntry
          />
        </View>

        <TouchableOpacity style={styles.forgotBtn}>
          <Text style={styles.forgotText}>Forgot password?</Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={[styles.primaryBtn, loading && styles.disabledBtn]}
          onPress={handleLogin}
          disabled={loading}
        >
          {loading ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.primaryBtnText}>Log In</Text>
          )}
        </TouchableOpacity>

        <TouchableOpacity
          style={styles.secondaryBtn}
          onPress={() => navigation.navigate('Register')}
        >
          <Text style={styles.secondaryBtnText}>Don&apos;t have an account? Register</Text>
        </TouchableOpacity>
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#080C12' },
  content: { flexGrow: 1, padding: 24, justifyContent: 'center' },
  header: { alignItems: 'center', marginBottom: 40 },
  logo: { fontSize: 28, fontWeight: '800', color: '#E8922A' },
  tagline: { fontSize: 13, color: '#6b7280', marginTop: 4 },
  form: { backgroundColor: '#141D2B', borderRadius: 16, padding: 24 },
  title: { fontSize: 22, fontWeight: '700', color: '#ffffff', marginBottom: 24 },
  field: { marginBottom: 16 },
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
  forgotBtn: { alignSelf: 'flex-end', marginBottom: 20 },
  forgotText: { fontSize: 13, color: '#E8922A' },
  primaryBtn: {
    backgroundColor: '#E8922A',
    borderRadius: 12,
    paddingVertical: 14,
    alignItems: 'center',
    marginBottom: 12,
  },
  disabledBtn: { opacity: 0.6 },
  primaryBtnText: { fontSize: 16, fontWeight: '700', color: '#ffffff' },
  secondaryBtn: { alignItems: 'center', paddingVertical: 8 },
  secondaryBtnText: { fontSize: 14, color: '#6b7280' },
});
