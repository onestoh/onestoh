import React from 'react';
import { View, Text, StyleSheet } from 'react-native';

interface PriceRowProps {
  label: string;
  value: string;
  bold?: boolean;
  accent?: boolean;
}

export default function PriceRow({ label, value, bold = false, accent = false }: PriceRowProps) {
  return (
    <View style={styles.row}>
      <Text style={[styles.label, bold && styles.bold]}>{label}</Text>
      <Text style={[styles.value, bold && styles.bold, accent && styles.accent]}>{value}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  row: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingVertical: 5 },
  label: { fontSize: 14, color: '#9ca3af' },
  value: { fontSize: 14, color: '#e5e7eb', fontWeight: '500' },
  bold: { fontWeight: '700', color: '#ffffff', fontSize: 15 },
  accent: { color: '#E8922A' },
});
