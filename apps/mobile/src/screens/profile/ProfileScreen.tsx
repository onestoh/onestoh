import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, Share, Alert } from 'react-native';
import { useAuthStore } from '../../lib/store';
import StatusBadge from '../../components/StatusBadge';

export default function ProfileScreen() {
  const { user, logout } = useAuthStore();

  const handleShare = async () => {
    try {
      await Share.share({
        message: `Join TheOnlineYard — Kenya's #1 asset hire platform! Use my referral link: https://app.theonlineyard.co.ke/ref/${user?.id}`,
      });
    } catch {
      // ignore
    }
  };

  const handleLogout = () => {
    Alert.alert('Logout', 'Are you sure you want to log out?', [
      { text: 'Cancel', style: 'cancel' },
      { text: 'Logout', style: 'destructive', onPress: logout },
    ]);
  };

  if (!user) return null;

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.content}>
      {/* User Card */}
      <View style={styles.userCard}>
        <View style={styles.avatar}>
          <Text style={styles.avatarText}>{user.name.charAt(0).toUpperCase()}</Text>
        </View>
        <View style={{ flex: 1 }}>
          <Text style={styles.userName}>{user.name}</Text>
          <Text style={styles.userEmail}>{user.email}</Text>
          <Text style={styles.userPhone}>{user.phone}</Text>
        </View>
        <View style={styles.kycBadge}>
          <StatusBadge status={user.kycStatus as 'pending' | 'approved' | 'rejected'} small />
          <Text style={styles.kycLabel}>KYC</Text>
        </View>
      </View>

      {/* Referral */}
      <View style={styles.referralCard}>
        <Text style={styles.sectionTitle}>Referral Program</Text>
        <Text style={styles.referralSubtitle}>Earn KES 500 for every friend who completes a booking</Text>
        <View style={styles.referralRow}>
          <Text style={styles.referralLink} numberOfLines={1}>
            app.theonlineyard.co.ke/ref/{user.id.slice(0, 8)}
          </Text>
          <TouchableOpacity style={styles.shareBtn} onPress={handleShare}>
            <Text style={styles.shareBtnText}>Share</Text>
          </TouchableOpacity>
        </View>
      </View>

      {/* Settings */}
      <View style={styles.settingsList}>
        {[
          { icon: '🔔', label: 'Notification Preferences' },
          { icon: '🔒', label: 'Account & Security' },
          { icon: '❓', label: 'Help & Support' },
          { icon: '📄', label: 'Terms & Privacy Policy' },
        ].map((item) => (
          <TouchableOpacity key={item.label} style={styles.settingsRow}>
            <Text style={styles.settingsIcon}>{item.icon}</Text>
            <Text style={styles.settingsLabel}>{item.label}</Text>
            <Text style={styles.settingsChevron}>›</Text>
          </TouchableOpacity>
        ))}
      </View>

      <TouchableOpacity style={styles.logoutBtn} onPress={handleLogout}>
        <Text style={styles.logoutBtnText}>Log Out</Text>
      </TouchableOpacity>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#080C12' },
  content: { padding: 16, gap: 16, paddingBottom: 40 },
  userCard: { backgroundColor: '#141D2B', borderRadius: 14, padding: 16, flexDirection: 'row', gap: 14, alignItems: 'center' },
  avatar: { width: 56, height: 56, borderRadius: 28, backgroundColor: '#E8922A', alignItems: 'center', justifyContent: 'center' },
  avatarText: { fontSize: 24, fontWeight: '800', color: '#ffffff' },
  userName: { fontSize: 17, fontWeight: '700', color: '#ffffff' },
  userEmail: { fontSize: 13, color: '#9ca3af', marginTop: 2 },
  userPhone: { fontSize: 13, color: '#9ca3af' },
  kycBadge: { alignItems: 'center', gap: 4 },
  kycLabel: { fontSize: 10, color: '#6b7280', textTransform: 'uppercase', letterSpacing: 0.5 },
  referralCard: { backgroundColor: '#141D2B', borderRadius: 14, padding: 16 },
  sectionTitle: { fontSize: 14, fontWeight: '700', color: '#e5e7eb', marginBottom: 4 },
  referralSubtitle: { fontSize: 12, color: '#9ca3af', marginBottom: 12 },
  referralRow: { flexDirection: 'row', alignItems: 'center', gap: 10 },
  referralLink: { flex: 1, backgroundColor: '#0f1621', borderRadius: 8, paddingHorizontal: 10, paddingVertical: 8, fontSize: 12, color: '#9ca3af', fontFamily: 'monospace' },
  shareBtn: { backgroundColor: '#E8922A', paddingHorizontal: 14, paddingVertical: 9, borderRadius: 8 },
  shareBtnText: { fontSize: 13, fontWeight: '700', color: '#ffffff' },
  settingsList: { backgroundColor: '#141D2B', borderRadius: 14, overflow: 'hidden' },
  settingsRow: { flexDirection: 'row', alignItems: 'center', paddingHorizontal: 16, paddingVertical: 14, borderBottomWidth: 1, borderBottomColor: '#1e2d40', gap: 12 },
  settingsIcon: { fontSize: 18 },
  settingsLabel: { flex: 1, fontSize: 15, color: '#e5e7eb' },
  settingsChevron: { fontSize: 20, color: '#4b5563' },
  logoutBtn: { backgroundColor: '#1e2d40', borderRadius: 12, paddingVertical: 14, alignItems: 'center', borderWidth: 1, borderColor: '#374151' },
  logoutBtnText: { fontSize: 15, fontWeight: '700', color: '#f87171' },
});
