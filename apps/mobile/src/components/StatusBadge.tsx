import React from 'react';
import { View, Text, StyleSheet } from 'react-native';

type Status = string;

interface StatusBadgeProps {
  status: Status;
  small?: boolean;
}

function getColors(status: string): { bg: string; text: string } {
  const s = status.toLowerCase();
  if (s === 'active' || s === 'confirmed' || s === 'approved' || s === 'completed') {
    return { bg: '#052e16', text: '#4ade80' };
  }
  if (s === 'pending' || s === 'under_review') {
    return { bg: '#422006', text: '#fb923c' };
  }
  if (s === 'cancelled' || s === 'rejected' || s === 'failed' || s === 'off-road') {
    return { bg: '#450a0a', text: '#f87171' };
  }
  if (s === 'disputed' || s === 'appealed') {
    return { bg: '#2e1065', text: '#c084fc' };
  }
  if (s === 'maintenance') {
    return { bg: '#422006', text: '#fbbf24' };
  }
  if (s === 'inactive') {
    return { bg: '#111827', text: '#6b7280' };
  }
  return { bg: '#1f2937', text: '#9ca3af' };
}

export default function StatusBadge({ status, small = false }: StatusBadgeProps) {
  const { bg, text } = getColors(status);
  const displayText = status
    .replace(/_/g, ' ')
    .replace(/\b\w/g, (l) => l.toUpperCase());

  return (
    <View style={[styles.badge, { backgroundColor: bg }, small && styles.small]}>
      <Text style={[styles.text, { color: text }, small && styles.smallText]}>
        {displayText}
      </Text>
    </View>
  );
}

const styles = StyleSheet.create({
  badge: { paddingHorizontal: 10, paddingVertical: 4, borderRadius: 20 },
  small: { paddingHorizontal: 8, paddingVertical: 3 },
  text: { fontSize: 13, fontWeight: '600' },
  smallText: { fontSize: 11 },
});
