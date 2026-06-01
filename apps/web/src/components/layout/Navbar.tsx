'use client'
import { useState } from 'react'
import Link from 'next/link'
import { useAuthStore } from '@/lib/store'
import {
  Menu, X, Bell, Wallet, ChevronDown, LogOut, User, LayoutDashboard
} from 'lucide-react'
import clsx from 'clsx'

const NAV_LINKS = [
  { href: '/', label: 'Marketplace' },
  { href: '/listings?category=suv_4x4', label: 'SUVs & 4x4' },
  { href: '/listings?category=heavy_truck', label: 'Heavy Trucks' },
  { href: '/listings?category=excavator', label: 'Excavators' },
  { href: '/listings?category=generator', label: 'Generators' },
]

export function Navbar() {
  const { user, logout } = useAuthStore()
  const [menuOpen, setMenuOpen] = useState(false)
  const [profileOpen, setProfileOpen] = useState(false)

  const dashboardHref =
    user?.role === 'super_admin' ? '/admin'
    : user?.role === 'yard_owner' || user?.role === 'individual_owner' ? '/owner'
    : user?.role === 'broker' ? '/broker'
    : '/client'

  return (
    <header className="sticky top-0 z-40 bg-[#0B1018]/80 backdrop-blur-md border-b border-white/5">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
        {/* Logo */}
        <Link href="/" className="flex items-center gap-2">
          <span className="text-xl font-serif font-semibold text-[#E8922A]">TheOnlineYard</span>
          <span className="hidden sm:inline text-xs font-mono text-[#7088A8] tracking-widest">KENYA</span>
        </Link>

        {/* Desktop nav */}
        <nav className="hidden md:flex items-center gap-6">
          {NAV_LINKS.map((l) => (
            <Link
              key={l.href}
              href={l.href}
              className="text-sm text-[#7088A8] hover:text-[#DCE5F2] transition-colors"
            >
              {l.label}
            </Link>
          ))}
        </nav>

        {/* Right side */}
        <div className="flex items-center gap-3">
          {user ? (
            <>
              <button className="p-2 rounded-lg text-[#7088A8] hover:text-[#DCE5F2] hover:bg-white/5 transition-colors relative">
                <Bell className="w-5 h-5" />
                <span className="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#E8922A]" />
              </button>
              <div className="relative">
                <button
                  onClick={() => setProfileOpen(!profileOpen)}
                  className="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-white/5 transition-colors"
                >
                  <div className="w-8 h-8 rounded-full bg-[#E8922A]/20 flex items-center justify-center text-[#E8922A] font-semibold text-sm">
                    {user.name[0].toUpperCase()}
                  </div>
                  <span className="hidden sm:block text-sm text-[#DCE5F2]">{user.name.split(' ')[0]}</span>
                  <ChevronDown className="w-4 h-4 text-[#7088A8]" />
                </button>
                {profileOpen && (
                  <div className="absolute right-0 mt-2 w-52 bg-[#141D2B] border border-white/10 rounded-xl shadow-xl overflow-hidden">
                    <div className="px-4 py-3 border-b border-white/5">
                      <p className="text-sm font-medium text-[#DCE5F2]">{user.name}</p>
                      <p className="text-xs text-[#7088A8] font-mono">{user.role.replace('_', ' ')}</p>
                    </div>
                    <div className="py-1">
                      <Link
                        href={dashboardHref}
                        onClick={() => setProfileOpen(false)}
                        className="flex items-center gap-2 px-4 py-2 text-sm text-[#DCE5F2] hover:bg-white/5"
                      >
                        <LayoutDashboard className="w-4 h-4" /> Dashboard
                      </Link>
                      <Link
                        href="/profile"
                        onClick={() => setProfileOpen(false)}
                        className="flex items-center gap-2 px-4 py-2 text-sm text-[#DCE5F2] hover:bg-white/5"
                      >
                        <User className="w-4 h-4" /> Profile
                      </Link>
                      <Link
                        href="/wallet"
                        onClick={() => setProfileOpen(false)}
                        className="flex items-center gap-2 px-4 py-2 text-sm text-[#DCE5F2] hover:bg-white/5"
                      >
                        <Wallet className="w-4 h-4" /> Wallet
                      </Link>
                      <button
                        onClick={() => { logout(); setProfileOpen(false) }}
                        className="w-full flex items-center gap-2 px-4 py-2 text-sm text-[#E05252] hover:bg-white/5"
                      >
                        <LogOut className="w-4 h-4" /> Sign Out
                      </button>
                    </div>
                  </div>
                )}
              </div>
            </>
          ) : (
            <div className="flex items-center gap-2">
              <Link href="/login" className="text-sm text-[#7088A8] hover:text-[#DCE5F2] px-3 py-2">
                Sign In
              </Link>
              <Link
                href="/register"
                className="bg-[#E8922A] text-[#080C12] font-semibold text-sm px-4 py-2 rounded-lg hover:bg-[#F5B050] transition-colors"
              >
                Get Started
              </Link>
            </div>
          )}

          <button
            className="md:hidden p-2 rounded-lg text-[#7088A8] hover:bg-white/5"
            onClick={() => setMenuOpen(!menuOpen)}
          >
            {menuOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
          </button>
        </div>
      </div>

      {/* Mobile menu */}
      {menuOpen && (
        <div className="md:hidden border-t border-white/5 bg-[#0B1018] px-4 py-3 space-y-1">
          {NAV_LINKS.map((l) => (
            <Link
              key={l.href}
              href={l.href}
              onClick={() => setMenuOpen(false)}
              className="block py-2 text-sm text-[#7088A8] hover:text-[#DCE5F2]"
            >
              {l.label}
            </Link>
          ))}
        </div>
      )}
    </header>
  )
}
