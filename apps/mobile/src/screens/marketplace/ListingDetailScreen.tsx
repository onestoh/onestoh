import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  ScrollView,
  TouchableOpacity,
  StyleSheet,
  FlatList,
  Image,
  Dimensions,
  ActivityIndicator,
} from 'react-native';
import { Calendar } from 'react-native-calendars';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { RouteProp } from '@react-navigation/native';
import { MarketplaceStackParamList } from '../../navigation/AppNavigator';
import { assetsApi } from '../../lib/api';
import { Asset } from '../../lib/types';
import PriceRow from '../../components/PriceRow';

type Props = {
  navigation: NativeStackNavigationProp<MarketplaceStackParamList, 'ListingDetail'>;
  route: RouteProp<MarketplaceStackParamList, 'ListingDetail'>;
};

type RateTab = 'hourly' | 'daily' | 'weekly' | 'monthly';

const { width: SCREEN_WIDTH } = Dimensions.get('window');

export default function ListingDetailScreen({ navigation, route }: Props) {
  const { assetId } = route.params;
  const [asset, setAsset] = useState<Asset | null>(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState<RateTab>('daily');
  const [selectedDates, setSelectedDates] = useState<Record<string, { selected: boolean; color: string }>>({});

  useEffect(() => {
    assetsApi.detail(assetId).then((res) => {
      setAsset(res.data);
    }).finally(() => setLoading(false));
  }, [assetId]);

  if (loading) {
    return (
      <View style={styles.centered}>
        <ActivityIndicator size="large" color="#E8922A" />
      </View>
    );
  }

  if (!asset) {
    return (
      <View style={styles.centered}>
        <Text style={styles.errorText}>Listing not found.</Text>
      </View>
    );
  }

  const rateValue = {
    hourly: asset.hourlyRateKES,
    daily: asset.dailyRateKES,
    weekly: asset.weeklyRateKES,
    monthly: asset.monthlyRateKES,
  }[activeTab];

  const PLATFORM_FEE = Math.round((rateValue ?? 0) * 0.07);

  const handleDayPress = (day: { dateString: string }) => {
    setSelectedDates((prev) => {
      const next = { ...prev };
      if (next[day.dateString]) {
        delete next[day.dateString];
      } else {
        next[day.dateString] = { selected: true, color: '#E8922A' };
      }
      return next;
    });
  };

  return (
    <View style={styles.container}>
      <ScrollView showsVerticalScrollIndicator={false}>
        {/* Image Carousel */}
        <FlatList
          data={asset.images.length ? asset.images : ['placeholder']}
          horizontal
          pagingEnabled
          showsHorizontalScrollIndicator={false}
          keyExtractor={(item, i) => `${item}-${i}`}
          renderItem={({ item }) => (
            <Image
              source={{ uri: item !== 'placeholder' ? item : undefined }}
              style={styles.image}
              defaultSource={require('../../assets/placeholder.png')}
            />
          )}
          style={{ width: SCREEN_WIDTH }}
        />

        <View style={styles.body}>
          {/* Title & Location */}
          <View style={styles.titleRow}>
            <View style={{ flex: 1 }}>
              <Text style={styles.name}>{asset.name}</Text>
              <Text style={styles.location}>{asset.location}</Text>
            </View>
            <View>
              <Text style={styles.rating}>⭐ {asset.rating.toFixed(1)}</Text>
              <Text style={styles.reviewCount}>({asset.reviewCount})</Text>
            </View>
          </View>

          {/* Specs */}
          <View style={styles.specsCard}>
            <Text style={styles.sectionTitle}>Specifications</Text>
            {Object.entries(asset.specs).map(([key, val]) => (
              <View key={key} style={styles.specRow}>
                <Text style={styles.specKey}>{key}</Text>
                <Text style={styles.specVal}>{String(val)}</Text>
              </View>
            ))}
          </View>

          {/* Rate Tabs */}
          <Text style={styles.sectionTitle}>Rates</Text>
          <View style={styles.tabRow}>
            {(['hourly', 'daily', 'weekly', 'monthly'] as RateTab[]).map((tab) => (
              <TouchableOpacity
                key={tab}
                style={[styles.tab, activeTab === tab && styles.tabActive]}
                onPress={() => setActiveTab(tab)}
              >
                <Text style={[styles.tabText, activeTab === tab && styles.tabTextActive]}>
                  {tab.charAt(0).toUpperCase() + tab.slice(1)}
                </Text>
              </TouchableOpacity>
            ))}
          </View>

          {/* Price Breakdown */}
          <View style={styles.priceCard}>
            <PriceRow label={`Base rate (${activeTab})`} value={`KES ${(rateValue ?? 0).toLocaleString()}`} />
            <PriceRow label="Platform fee (7%)" value={`KES ${PLATFORM_FEE.toLocaleString()}`} />
            <PriceRow
              label="Total"
              value={`KES ${((rateValue ?? 0) + PLATFORM_FEE).toLocaleString()}`}
              bold
            />
          </View>

          {/* Availability Calendar */}
          <Text style={styles.sectionTitle}>Availability</Text>
          <Calendar
            onDayPress={handleDayPress}
            markedDates={selectedDates}
            markingType="multi-dot"
            theme={{
              backgroundColor: '#141D2B',
              calendarBackground: '#141D2B',
              textSectionTitleColor: '#9ca3af',
              dayTextColor: '#ffffff',
              todayTextColor: '#E8922A',
              selectedDayTextColor: '#ffffff',
              selectedDayBackgroundColor: '#E8922A',
              arrowColor: '#E8922A',
              monthTextColor: '#ffffff',
              textDisabledColor: '#374151',
            }}
            style={styles.calendar}
          />
        </View>
      </ScrollView>

      {/* CTA */}
      <View style={styles.ctaBar}>
        <View>
          <Text style={styles.ctaPrice}>KES {(asset.dailyRateKES).toLocaleString()}</Text>
          <Text style={styles.ctaLabel}>per day</Text>
        </View>
        <TouchableOpacity
          style={styles.bookBtn}
          onPress={() => navigation.navigate('Booking', { assetId: asset.id })}
        >
          <Text style={styles.bookBtnText}>Book Now</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#080C12' },
  centered: { flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: '#080C12' },
  errorText: { color: '#9ca3af', fontSize: 15 },
  image: { width: SCREEN_WIDTH, height: 240, backgroundColor: '#141D2B' },
  body: { padding: 16, gap: 16 },
  titleRow: { flexDirection: 'row', gap: 12 },
  name: { fontSize: 20, fontWeight: '700', color: '#ffffff' },
  location: { fontSize: 13, color: '#9ca3af', marginTop: 2 },
  rating: { fontSize: 14, fontWeight: '700', color: '#E8922A' },
  reviewCount: { fontSize: 12, color: '#6b7280', textAlign: 'right' },
  sectionTitle: { fontSize: 15, fontWeight: '700', color: '#d1d5db', marginBottom: 8 },
  specsCard: { backgroundColor: '#141D2B', borderRadius: 12, padding: 14, gap: 8 },
  specRow: { flexDirection: 'row', justifyContent: 'space-between' },
  specKey: { fontSize: 13, color: '#9ca3af' },
  specVal: { fontSize: 13, color: '#e5e7eb', fontWeight: '600' },
  tabRow: { flexDirection: 'row', gap: 8 },
  tab: { flex: 1, paddingVertical: 8, backgroundColor: '#141D2B', borderRadius: 8, alignItems: 'center', borderWidth: 1, borderColor: '#1e2d40' },
  tabActive: { backgroundColor: '#E8922A', borderColor: '#E8922A' },
  tabText: { fontSize: 12, color: '#9ca3af', fontWeight: '600' },
  tabTextActive: { color: '#ffffff' },
  priceCard: { backgroundColor: '#141D2B', borderRadius: 12, padding: 14, gap: 6 },
  calendar: { borderRadius: 12, overflow: 'hidden' },
  ctaBar: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    padding: 16,
    backgroundColor: '#141D2B',
    borderTopWidth: 1,
    borderTopColor: '#1e2d40',
  },
  ctaPrice: { fontSize: 20, fontWeight: '800', color: '#E8922A' },
  ctaLabel: { fontSize: 12, color: '#9ca3af' },
  bookBtn: {
    backgroundColor: '#E8922A',
    paddingHorizontal: 32,
    paddingVertical: 14,
    borderRadius: 12,
  },
  bookBtnText: { fontSize: 15, fontWeight: '700', color: '#ffffff' },
});
