import React, { useState } from 'react';
import {
  View, Text, StyleSheet, TouchableOpacity, Image, Alert, FlatList,
} from 'react-native';
import { launchCamera, launchImageLibrary } from 'react-native-image-picker';

interface PhotoUploaderProps {
  maxPhotos?: number;
  onPhotosChange?: (uris: string[]) => void;
}

export default function PhotoUploader({ maxPhotos = 6, onPhotosChange }: PhotoUploaderProps) {
  const [photos, setPhotos] = useState<string[]>([]);

  const addPhoto = (uri: string) => {
    setPhotos((prev) => {
      const next = [...prev, uri];
      onPhotosChange?.(next);
      return next;
    });
  };

  const removePhoto = (uri: string) => {
    setPhotos((prev) => {
      const next = prev.filter((p) => p !== uri);
      onPhotosChange?.(next);
      return next;
    });
  };

  const handleAdd = () => {
    Alert.alert('Add Photo', 'Choose source', [
      {
        text: 'Camera',
        onPress: () =>
          launchCamera({ mediaType: 'photo', quality: 0.8 }, (res) => {
            if (res.assets?.[0]?.uri) addPhoto(res.assets[0].uri!);
          }),
      },
      {
        text: 'Gallery',
        onPress: () =>
          launchImageLibrary(
            { mediaType: 'photo', selectionLimit: maxPhotos - photos.length },
            (res) => {
              res.assets?.forEach((a) => { if (a.uri) addPhoto(a.uri); });
            },
          ),
      },
      { text: 'Cancel', style: 'cancel' },
    ]);
  };

  return (
    <View style={styles.container}>
      <FlatList
        data={[
          ...photos,
          ...(photos.length < maxPhotos ? ['__add__'] : []),
        ]}
        keyExtractor={(item, i) => `${item}-${i}`}
        horizontal
        showsHorizontalScrollIndicator={false}
        contentContainerStyle={styles.list}
        renderItem={({ item }) => {
          if (item === '__add__') {
            return (
              <TouchableOpacity style={styles.addBtn} onPress={handleAdd}>
                <Text style={styles.addIcon}>+</Text>
                <Text style={styles.addText}>Add</Text>
              </TouchableOpacity>
            );
          }
          return (
            <View style={styles.photoItem}>
              <Image source={{ uri: item }} style={styles.photo} />
              <TouchableOpacity style={styles.removeBtn} onPress={() => removePhoto(item)}>
                <Text style={styles.removeBtnText}>×</Text>
              </TouchableOpacity>
            </View>
          );
        }}
      />
      <Text style={styles.counter}>{photos.length}/{maxPhotos} photos</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { gap: 8 },
  list: { gap: 10, padding: 2 },
  photoItem: { position: 'relative' },
  photo: { width: 88, height: 88, borderRadius: 10 },
  removeBtn: {
    position: 'absolute', top: -6, right: -6,
    width: 22, height: 22, borderRadius: 11,
    backgroundColor: '#dc2626',
    alignItems: 'center', justifyContent: 'center',
  },
  removeBtnText: { color: '#ffffff', fontSize: 14, fontWeight: '700', lineHeight: 16 },
  addBtn: {
    width: 88, height: 88, borderRadius: 10,
    borderWidth: 1.5, borderColor: '#1e2d40', borderStyle: 'dashed',
    alignItems: 'center', justifyContent: 'center', backgroundColor: '#141D2B',
  },
  addIcon: { fontSize: 24, color: '#4b5563' },
  addText: { fontSize: 11, color: '#6b7280' },
  counter: { fontSize: 12, color: '#6b7280', textAlign: 'right' },
});
