import React, { useState, useEffect } from 'react';
import {
  View, Text, FlatList, StyleSheet, TouchableOpacity, Modal, TextInput, Alert, ActivityIndicator, RefreshControl,
} from 'react-native';
import { walletApi } from '../../lib/api';
import { Transaction } from '../../lib/types';

interface WalletBalance {
  mainBalanceKES: number;
  pendingKES: number;
}

export default function WalletScreen() {
  const [balance, setBalance] = useState<WalletBalance>({ mainBalanceKES: 0, pendingKES: 0 });
  const [transactions, setTransactions] = useState<Transaction[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [showPayout, setShowPayout] = useState(false);
  const [payoutAmount, setPayoutAmount] = useState('');
  const [accountNumber, setAccountNumber] = useState('');
  const [accountName, setAccountName] = useState('');
  const [payoutLoading, setPayoutLoading] = useState(false);

  const fetchData = async () => {
    try {
      const [balRes, txRes] = await Promise.all([
        walletApi.balance(),
        walletApi.transactions({ limit: 30 }),
      ]);
      setBalance(balRes.data);
      setTransactions(txRes.data.transactions ?? []);
    } catch {
      // silent
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => { fetchData(); }, []);

  const handlePayout = async () => {
    if (!payoutAmount || !accountNumber || !accountName) {
      Alert.alert('Error', 'Please fill in all payout fields.');
      return;
    }
    setPayoutLoading(true);
    try {
      await walletApi.requestPayout({
        amountKES: Number(payoutAmount),
        accountNumber,
        accountName,
      });
      Alert.alert('Success', 'Payout request submitted. Processing within 1 business day.');
      setShowPayout(false);
    } catch {
      Alert.alert('Error', 'Payout request failed. Please try again.');
    } finally {
      setPayoutLoading(false);
    }
  };

  const renderTransaction = ({ item }: { item: Transaction }) => (
    <View style={styles.txRow}>
      <View style={[styles.txIcon, item.type === 'credit' ? styles.txIconCredit : styles.txIconDebit]}>
        <Text style={styles.txIconText}>{item.type === 'credit' ? '↓' : '↑'}</Text>
      </View>
      <View style={{ flex: 1 }}>
        <Text style={styles.txDesc}>{item.description}</Text>
        <Text style={styles.txRef}>{item.reference} · {item.createdAt}</Text>
      </View>
      <Text style={[styles.txAmount, item.type === 'credit' ? styles.txCredit : styles.txDebit]}>
        {item.type === 'credit' ? '+' : '-'}KES {item.amountKES.toLocaleString()}
      </Text>
    </View>
  );

  if (loading) {
    return <View style={styles.centered}><ActivityIndicator size="large" color="#E8922A" /></View>;
  }

  return (
    <View style={styles.container}>
      {/* Balance Card */}
      <View style={styles.balanceCard}>
        <Text style={styles.balanceLabel}>Available Balance</Text>
        <Text style={styles.balanceValue}>KES {balance.mainBalanceKES.toLocaleString()}</Text>
        {balance.pendingKES > 0 && (
          <Text style={styles.pendingText}>KES {balance.pendingKES.toLocaleString()} pending</Text>
        )}
        <TouchableOpacity style={styles.payoutBtn} onPress={() => setShowPayout(true)}>
          <Text style={styles.payoutBtnText}>Request Payout</Text>
        </TouchableOpacity>
      </View>

      {/* Transaction History */}
      <Text style={styles.sectionTitle}>Transaction History</Text>
      <FlatList
        data={transactions}
        keyExtractor={(item) => item.id}
        renderItem={renderTransaction}
        contentContainerStyle={styles.txList}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); fetchData(); }} tintColor="#E8922A" />
        }
        ListEmptyComponent={<Text style={styles.emptyText}>No transactions yet.</Text>}
      />

      {/* Payout Modal */}
      <Modal visible={showPayout} transparent animationType="slide">
        <View style={styles.modalOverlay}>
          <View style={styles.modalCard}>
            <Text style={styles.modalTitle}>Request Payout</Text>
            <Text style={styles.modalLabel}>Amount (KES)</Text>
            <TextInput
              style={styles.modalInput}
              value={payoutAmount}
              onChangeText={setPayoutAmount}
              keyboardType="number-pad"
              placeholder="e.g. 10000"
              placeholderTextColor="#4b5563"
            />
            <Text style={styles.modalLabel}>M-Pesa Number</Text>
            <TextInput
              style={styles.modalInput}
              value={accountNumber}
              onChangeText={setAccountNumber}
              keyboardType="phone-pad"
              placeholder="+254 7XX XXX XXX"
              placeholderTextColor="#4b5563"
            />
            <Text style={styles.modalLabel}>Account Name</Text>
            <TextInput
              style={styles.modalInput}
              value={accountName}
              onChangeText={setAccountName}
              placeholder="Name on M-Pesa"
              placeholderTextColor="#4b5563"
            />
            <View style={styles.modalBtns}>
              <TouchableOpacity style={styles.cancelBtn} onPress={() => setShowPayout(false)}>
                <Text style={styles.cancelBtnText}>Cancel</Text>
              </TouchableOpacity>
              <TouchableOpacity
                style={[styles.confirmBtn, payoutLoading && { opacity: 0.6 }]}
                onPress={handlePayout}
                disabled={payoutLoading}
              >
                {payoutLoading ? <ActivityIndicator color="#fff" /> : <Text style={styles.confirmBtnText}>Submit</Text>}
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#080C12' },
  centered: { flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: '#080C12' },
  balanceCard: { margin: 16, backgroundColor: '#141D2B', borderRadius: 16, padding: 24, alignItems: 'center' },
  balanceLabel: { fontSize: 13, color: '#9ca3af' },
  balanceValue: { fontSize: 36, fontWeight: '800', color: '#E8922A', marginTop: 4 },
  pendingText: { fontSize: 13, color: '#6b7280', marginTop: 4 },
  payoutBtn: { marginTop: 16, backgroundColor: '#E8922A', paddingHorizontal: 24, paddingVertical: 12, borderRadius: 10 },
  payoutBtnText: { fontSize: 14, fontWeight: '700', color: '#ffffff' },
  sectionTitle: { fontSize: 14, fontWeight: '700', color: '#9ca3af', paddingHorizontal: 16, marginBottom: 8, textTransform: 'uppercase', letterSpacing: 0.5 },
  txList: { paddingHorizontal: 16, gap: 2 },
  txRow: { flexDirection: 'row', alignItems: 'center', gap: 12, paddingVertical: 12, borderBottomWidth: 1, borderBottomColor: '#1e2d40' },
  txIcon: { width: 36, height: 36, borderRadius: 18, alignItems: 'center', justifyContent: 'center' },
  txIconCredit: { backgroundColor: '#052e16' },
  txIconDebit: { backgroundColor: '#450a0a' },
  txIconText: { fontSize: 16, fontWeight: '700', color: '#ffffff' },
  txDesc: { fontSize: 14, color: '#e5e7eb', fontWeight: '500' },
  txRef: { fontSize: 11, color: '#6b7280', marginTop: 2 },
  txAmount: { fontSize: 14, fontWeight: '700' },
  txCredit: { color: '#4ade80' },
  txDebit: { color: '#f87171' },
  emptyText: { color: '#6b7280', textAlign: 'center', paddingTop: 40, fontSize: 14 },
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.7)', justifyContent: 'flex-end' },
  modalCard: { backgroundColor: '#141D2B', borderTopLeftRadius: 20, borderTopRightRadius: 20, padding: 24 },
  modalTitle: { fontSize: 18, fontWeight: '700', color: '#ffffff', marginBottom: 20 },
  modalLabel: { fontSize: 13, color: '#9ca3af', marginBottom: 6 },
  modalInput: { backgroundColor: '#0f1621', borderWidth: 1, borderColor: '#1e2d40', borderRadius: 10, paddingHorizontal: 14, paddingVertical: 12, fontSize: 15, color: '#ffffff', marginBottom: 14 },
  modalBtns: { flexDirection: 'row', gap: 10, marginTop: 4 },
  cancelBtn: { flex: 1, paddingVertical: 13, backgroundColor: '#1e2d40', borderRadius: 10, alignItems: 'center' },
  cancelBtnText: { color: '#9ca3af', fontWeight: '600' },
  confirmBtn: { flex: 1, paddingVertical: 13, backgroundColor: '#E8922A', borderRadius: 10, alignItems: 'center' },
  confirmBtnText: { color: '#ffffff', fontWeight: '700' },
});
