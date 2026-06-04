import React, { useEffect } from 'react'
import { NavigationContainer } from '@react-navigation/native'
import { createStackNavigator } from '@react-navigation/stack'
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs'
import { ActivityIndicator, View } from 'react-native'
import { useAuthStore } from '@/store/auth'
import { colors } from '@/theme'

// Auth screens
import LoginScreen      from '@/screens/auth/LoginScreen'
import RegisterScreen   from '@/screens/auth/RegisterScreen'
import VerifyOtpScreen  from '@/screens/auth/VerifyOtpScreen'

// App screens
import HomeScreen       from '@/screens/HomeScreen'
import MarketplaceScreen from '@/screens/MarketplaceScreen'
import AssetDetailScreen from '@/screens/AssetDetailScreen'
import BookingsScreen   from '@/screens/BookingsScreen'
import NotificationsScreen from '@/screens/NotificationsScreen'
import ProfileScreen    from '@/screens/ProfileScreen'
import KycScreen        from '@/screens/KycScreen'

const Stack = createStackNavigator()
const Tab   = createBottomTabNavigator()

function TabIcon({ name, focused }: { name: string; focused: boolean }) {
  const icons: Record<string, string> = { Home: '🏠', Browse: '🔍', Bookings: '📋', Alerts: '🔔', Profile: '👤' }
  return <View style={{ opacity: focused ? 1 : 0.5 }}>{/* Icon placeholder */}</View>
}

function MainTabs() {
  return (
    <Tab.Navigator screenOptions={{
      headerShown: false,
      tabBarStyle: { backgroundColor: colors.surface, borderTopColor: colors.border },
      tabBarActiveTintColor: colors.amber,
      tabBarInactiveTintColor: colors.muted,
    }}>
      <Tab.Screen name="Home"     component={HomeScreen}          options={{ tabBarLabel: 'Home' }} />
      <Tab.Screen name="Browse"   component={MarketplaceScreen}   options={{ tabBarLabel: 'Browse' }} />
      <Tab.Screen name="Bookings" component={BookingsScreen}      options={{ tabBarLabel: 'Bookings' }} />
      <Tab.Screen name="Alerts"   component={NotificationsScreen} options={{ tabBarLabel: 'Alerts' }} />
      <Tab.Screen name="Profile"  component={ProfileScreen}       options={{ tabBarLabel: 'Profile' }} />
    </Tab.Navigator>
  )
}

export default function AppNavigator() {
  const { isAuthenticated, hydrate } = useAuthStore()
  const [ready, setReady] = React.useState(false)

  useEffect(() => {
    hydrate().finally(() => setReady(true))
  }, [])

  if (!ready) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: colors.dark }}>
        <ActivityIndicator color={colors.amber} size="large" />
      </View>
    )
  }

  return (
    <NavigationContainer>
      <Stack.Navigator screenOptions={{ headerShown: false }}>
        {isAuthenticated ? (
          <>
            <Stack.Screen name="Main"        component={MainTabs} />
            <Stack.Screen name="AssetDetail" component={AssetDetailScreen} />
            <Stack.Screen name="Kyc"         component={KycScreen} />
          </>
        ) : (
          <>
            <Stack.Screen name="Login"     component={LoginScreen} />
            <Stack.Screen name="Register"  component={RegisterScreen} />
            <Stack.Screen name="VerifyOtp" component={VerifyOtpScreen} />
          </>
        )}
      </Stack.Navigator>
    </NavigationContainer>
  )
}
