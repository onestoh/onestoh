'use client'
import Link from 'next/link'
import { usePathname } from 'next/navigation'
import { useAuthStore } from '@/lib/store'
import {
  LayoutDashboard, Calendar, Wallet, Settings, LogOut,
  Car, TrendingUp, Users, ShieldCheck
} from 'lucide-react'
import clsx from 'clsx'

function NavItem({ href, icon, label }: { href: string; icon: React.ReactNode; label: string }) {
  const pathname = usePathname()
  const active = pathname === href || pathname.startsWith(href + '/')
  return (
    <Link
      href={href}
      className={clsx(
        'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-colors',
        active
          ? 'bg-[#E8922A]/10 text-[#E8922A]'
          : 'text-[#7088A8] hover:text-[#DCE5F2] hover:bg-white/5'
      )}
    >
      {icon} {label}
    </Link>
  )
}

export default function DashboardLayout({ children }: { children: React.ReactNode }) {
  const { user, logout } = useAuthStore()

  const role = user?.role

  const navItems = [
    ...(role === 'client' ? [
      { href: '/client', icon: <LayoutDashboard className="w-4 h-4" />, label: 'Dashboard' },
      { href: '/client/bookings', icon: <Calendar className="w-4 h-4" />, label: 'My Bookings' },
      { href: '/client/wallet', icon: <Wallet className="w-4 h-4" />, label: 'Wallet' },
    ] : []),
    ...(role === 'yard_owner' || role === 'individual_owner' ? [
      { href: '/owner', icon: <LayoutDashboard className="w-4 h-4" />, label: 'Dashboard' },
      { href: '/owner/fleet', icon: <Car className="w-4 h-4" />, label: 'My Fleet' },
      { href: '/owner/bookings', icon: <Calendar className="w-4 h-4" />, label: 'Bookings' },
      { href: '/owner/earnings', icon: <TrendingUp className="w-4 h-4" />, label: 'Earnings' },
      { href: '/owner/wallet', icon: <Wallet className="w-4 h-4" />, label: 'Wallet' },
    ] : []),
    ...(role === 'broker' ? [
      { href: '/broker', icon: <LayoutDashboard className="w-4 h-4" />, label: 'Dashboard' },
      { href: '/broker/referrals', icon: <Users className="w-4 h-4" />, label: 'Referrals' },
      { href: '/broker/earnings', icon: <TrendingUp className="w-4 h-4" />, label: 'Earnings' },
      { href: '/broker/wallet', icon: <Wallet className="w-4 h-4" />, label: 'Wallet' },
    ] : []),
    ...(role === 'super_admin' ? [
      { href: '/admin', icon: <LayoutDashboard className="w-4 h-4" />, label: 'Overview' },
      { href: '/admin/kyc', icon: <ShieldCheck className="w-4 h-4" />, label: 'KYC Queue' },
      { href: '/admin/users', icon: <Users className="w-4 h-4" />, label: 'Users' },
    ] : []),
    { href: '/profile', icon: <Settings className="w-4 h-4" />, label: 'Settings' },
  ]

  return (
    <div className="min-h-screen flex bg-[#080C12]">
      {/* Sidebar */}
      <aside className="w-60 shrink-0 border-r border-white/5 flex flex-col bg-[#0B1018] sticky top-0 h-screen">
        <div className="px-6 py-5 border-b border-white/5">
          <Link href="/" className="text-lg font-serif font-semibold text-[#E8922A]">
            TheOnlineYard
          </Link>
        </div>

        {user && (
          <div className="px-4 py-4 border-b border-white/5">
            <div className="flex items-center gap-3">
              <div className="w-9 h-9 rounded-full bg-[#E8922A]/20 flex items-center justify-center text-[#E8922A] font-bold">
                {user.name[0].toUpperCase()}
              </div>
              <div className="min-w-0">
                <p className="text-sm font-medium text-[#DCE5F2] truncate">{user.name}</p>
                <p className="text-xs text-[#7088A8] capitalize">{user.role.replace(/_/g, ' ')}</p>
              </div>
            </div>
          </div>
        )}

        <nav className="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
          {navItems.map((item) => (
            <NavItem key={item.href} {...item} />
          ))}
        </nav>

        <div className="px-3 py-4 border-t border-white/5">
          <button
            onClick={logout}
            className="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-[#E05252] hover:bg-[#E05252]/10 transition-colors"
          >
            <LogOut className="w-4 h-4" /> Sign Out
          </button>
        </div>
      </aside>

      {/* Main */}
      <main className="flex-1 min-w-0 overflow-auto">
        <div className="max-w-5xl mx-auto px-6 py-8">{children}</div>
      </main>
    </div>
  )
}
