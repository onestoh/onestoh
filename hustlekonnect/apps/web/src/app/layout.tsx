import type { Metadata } from 'next'
import { Inter } from 'next/font/google'
import { Toaster } from 'react-hot-toast'
import './globals.css'

const inter = Inter({ subsets: ['latin'] })

export const metadata: Metadata = {
  title: 'HustleKonnect — Pan-African Vehicle & Machinery Marketplace',
  description: 'Rent or buy vehicles and heavy machinery across Africa. Available in Kenya, Uganda, Tanzania, Nigeria, Ghana, and South Africa.',
  keywords: 'vehicle rental, machinery rental, Africa, Kenya, Uganda, Tanzania',
}

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en">
      <body className={`${inter.className} bg-brand-dark text-white min-h-screen`}>
        {children}
        <Toaster
          position="top-right"
          toastOptions={{
            style: { background: '#141D2B', color: '#fff', border: '1px solid #1E2A3B' },
            success: { iconTheme: { primary: '#E8922A', secondary: '#fff' } },
          }}
        />
      </body>
    </html>
  )
}
