import type { Metadata } from 'next'
import './globals.css'
import { Toaster } from 'react-hot-toast'

export const metadata: Metadata = {
  title: 'TheOnlineYard — Rent & Buy Vehicles and Heavy Machinery',
  description: "Kenya's automated marketplace for vehicle and heavy machinery rental and sales. M-Pesa payments, live availability, escrow-protected.",
  keywords: 'car hire Kenya, vehicle rental Nairobi, heavy equipment hire, machinery rental, M-Pesa car hire',
}

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en">
      <head>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossOrigin="anonymous" />
        <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Outfit:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
      </head>
      <body className="bg-brand-black text-brand-text font-sans antialiased">
        <Toaster position="top-right" toastOptions={{ style: { background: '#141D2B', color: '#DCE5F2', border: '1px solid rgba(232,146,42,0.2)' } }} />
        {children}
      </body>
    </html>
  )
}
