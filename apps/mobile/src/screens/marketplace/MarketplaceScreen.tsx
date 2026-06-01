import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  FlatList,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  RefreshControl,
  ActivityIndicator,
  ScrollView,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { MarketplaceStackParamList } from '../../navigation/AppNavigator';
import { assetsApi } from '../../lib/api';
import { Asset } from '../../lib/types';
import ListingCard from '../../components/ListingCard';

type Props = {
  navigation: NativeStackNavigationProp<MarketplaceStackParamList, 'MarketplaceHome'>;
};

const CATEGORIES = ['All', 'Cars', 'SUVs', 'Trucks', 'Buses', 'Machinery', 'Boats'];

export default function MarketplaceScreen({ navigation }: Props) {
  const [assets, setAssets] = useState<Asset[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [search, setSearch] = useState('');
  const [category, setCategory] = useState('All');
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const [loadingMore, setLoadingMore] = useState(false);

  const fetchAssets = useCallback(
    async (reset = false) => {
      const currentPage = reset ? 1 : page;
      if (!reset) setLoadingMore(true);
      try {
        const res = await assetsApi.list({
          search: search || undefined,
          category: category !== 'All' ? category : undefined,
          page: currentPage,
          limit: 20,
        });
        const newAssets: Asset[] = res.data.assets ?? [];
        if (reset) {
          setAssets(newAssets);
          setPage(2);
        } else {
          setAssets((prev) => [...prev, ...newAssets]);
          setPage((p) => p + 1);
        }
        setHasMore(newAssets.length === 20);
      } catch {
        // silently fail for demo
      } finally {
        setLoading(false);
        setRefreshing(false);
        setLoadingMore(false);
      }
    },
    [search, category, page],
  );

  useEffect(() => {
    setLoading(true);
    fetchAssets(true);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [search, category]);

  const onRefresh = () => {
    setRefreshing(true);
    fetchAssets(true);
  };

  const onEndReached = () => {
    if (!loadingMore && hasMore) fetchAssets();
  };

  const renderFooter = () =>
    loadingMore ? <ActivityIndicator color="#E8922A" style={{ padding: 16 }} /> : null;

  return (
    <View style={styles.container}>
      {/* Search */}
      <View style={styles.searchRow}>
        <TextInput
          style={styles.searchInput}
          placeholder="Search vehicles, equipment..."
          placeholderTextColor="#4b5563"
          value={search}
          onChangeText={setSearch}
          returnKeyType="search"
        />
      </View>

      {/* Category Filter */}
      <ScrollView
        horizontal
        showsHorizontalScrollIndicator={false}
        style={styles.categoryScroll}
        contentContainerStyle={styles.categoryContent}
      >
        {CATEGORIES.map((cat) => (
          <TouchableOpacity
            key={cat}
            style={[styles.categoryPill, category === cat && styles.categoryPillActive]}
            onPress={() => setCategory(cat)}
          >
            <Text style={[styles.categoryText, category === cat && styles.categoryTextActive]}>
              {cat}
            </Text>
          </TouchableOpacity>
        ))}
      </ScrollView>

      {/* Listing Grid */}
      {loading ? (
        <View style={styles.centered}>
          <ActivityIndicator size="large" color="#E8922A" />
        </View>
      ) : (
        <FlatList
          data={assets}
          keyExtractor={(item) => item.id}
          renderItem={({ item }) => (
            <ListingCard
              asset={item}
              onPress={() => navigation.navigate('ListingDetail', { assetId: item.id })}
            />
          )}
          numColumns={1}
          contentContainerStyle={styles.listContent}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor="#E8922A" />
          }
          onEndReached={onEndReached}
          onEndReachedThreshold={0.4}
          ListFooterComponent={renderFooter}
          ListEmptyComponent={
            <View style={styles.empty}>
              <Text style={styles.emptyText}>No listings found</Text>
            </View>
          }
        />
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#080C12' },
  searchRow: { paddingHorizontal: 16, paddingTop: 12, paddingBottom: 8 },
  searchInput: {
    backgroundColor: '#141D2B',
    borderRadius: 12,
    paddingHorizontal: 14,
    paddingVertical: 11,
    fontSize: 14,
    color: '#ffffff',
    borderWidth: 1,
    borderColor: '#1e2d40',
  },
  categoryScroll: { maxHeight: 48 },
  categoryContent: { paddingHorizontal: 16, gap: 8, alignItems: 'center' },
  categoryPill: {
    paddingHorizontal: 16,
    paddingVertical: 7,
    backgroundColor: '#141D2B',
    borderRadius: 20,
    borderWidth: 1,
    borderColor: '#1e2d40',
  },
  categoryPillActive: { backgroundColor: '#E8922A', borderColor: '#E8922A' },
  categoryText: { fontSize: 13, color: '#9ca3af', fontWeight: '500' },
  categoryTextActive: { color: '#ffffff', fontWeight: '700' },
  listContent: { padding: 16, gap: 12 },
  centered: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  empty: { paddingTop: 60, alignItems: 'center' },
  emptyText: { color: '#6b7280', fontSize: 15 },
});
