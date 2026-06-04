import Link from 'next/link'
import { Search, Shield, Zap, Globe } from 'lucide-react'

export default function HomePage() {
  const countries = [
    { code: 'KE', name: 'Kenya', flag: '🇰🇪', currency: 'KES' },
    { code: 'UG', name: 'Uganda', flag: '🇺🇬', currency: 'UGX' },
    { code: 'TZ', name: 'Tanzania', flag: '🇹🇿', currency: 'TZS' },
    { code: 'NG', name: 'Nigeria', flag: '🇳🇬', currency: 'NGN' },
    { code: 'GH', name: 'Ghana', flag: '🇬🇭', currency: 'GHS' },
    { code: 'ZA', name: 'South Africa', flag: '🇿🇦', currency: 'ZAR' },
  ]

  return (
    <main className="min-h-screen">
      {/* Hero */}
      <section className="relative bg-brand-surface border-b border-brand-border">
        <nav className="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
          <div className="flex items-center gap-2">
            <span className="text-2xl font-bold">
              <span className="text-brand-amber">Hustle</span>Konnect
            </span>
          </div>
          <div className="flex items-center gap-4">
            <Link href="/marketplace" className="text-brand-muted hover:text-white transition-colors">Browse</Link>
            <Link href="/login" className="btn-ghost text-sm">Log In</Link>
            <Link href="/register" className="btn-primary text-sm">Get Started</Link>
          </div>
        </nav>

        <div className="max-w-7xl mx-auto px-4 py-20 text-center">
          <h1 className="text-5xl font-bold mb-6">
            Africa's #1 Vehicle &<br/>
            <span className="text-brand-amber">Machinery Marketplace</span>
          </h1>
          <p className="text-xl text-brand-muted mb-10 max-w-2xl mx-auto">
            Rent or buy verified vehicles and heavy equipment across 6 African markets.
            M-Pesa, MTN MoMo, Paystack — pay the way you know.
          </p>

          {/* Search bar */}
          <div className="max-w-2xl mx-auto bg-brand-dark border border-brand-border rounded-2xl p-4 flex gap-3">
            <select className="input flex-1">
              <option value="">Select Country</option>
              {countries.map(c => <option key={c.code} value={c.code}>{c.flag} {c.name}</option>)}
            </select>
            <select className="input flex-1">
              <option value="">Asset Type</option>
              <option value="car">Car</option>
              <option value="truck">Truck</option>
              <option value="excavator">Excavator</option>
              <option value="crane">Crane</option>
              <option value="bus">Bus</option>
            </select>
            <Link href="/marketplace" className="btn-primary flex items-center gap-2 whitespace-nowrap">
              <Search size={18} /> Search
            </Link>
          </div>
        </div>
      </section>

      {/* Markets */}
      <section className="max-w-7xl mx-auto px-4 py-16">
        <h2 className="text-3xl font-bold text-center mb-10">Available Markets</h2>
        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
          {countries.map(c => (
            <Link key={c.code} href={`/marketplace?country=${c.code}`}
              className="card text-center hover:border-brand-amber transition-colors cursor-pointer">
              <div className="text-4xl mb-2">{c.flag}</div>
              <div className="font-semibold">{c.name}</div>
              <div className="text-xs text-brand-muted mt-1">{c.currency}</div>
            </Link>
          ))}
        </div>
      </section>

      {/* Features */}
      <section className="bg-brand-surface border-y border-brand-border py-16">
        <div className="max-w-7xl mx-auto px-4">
          <h2 className="text-3xl font-bold text-center mb-10">Why HustleKonnect?</h2>
          <div className="grid md:grid-cols-3 gap-8">
            {[
              { icon: Shield, title: 'Fully Secured', desc: 'KYC verification, escrow payments, and end-to-end encryption protect every transaction.' },
              { icon: Zap, title: 'Zero Human Wait', desc: 'Automated booking lifecycle: confirmed, active, completed — all without manual intervention.' },
              { icon: Globe, title: 'Pan-African Payments', desc: 'M-Pesa, MTN MoMo, Paystack, Stripe — pay with your preferred local method.' },
            ].map(({ icon: Icon, title, desc }) => (
              <div key={title} className="card">
                <Icon size={36} className="text-brand-amber mb-4" />
                <h3 className="text-xl font-semibold mb-2">{title}</h3>
                <p className="text-brand-muted">{desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="max-w-7xl mx-auto px-4 py-20 text-center">
        <h2 className="text-4xl font-bold mb-4">Ready to Hustle?</h2>
        <p className="text-brand-muted text-lg mb-8">Join thousands of Africans already using HustleKonnect.</p>
        <div className="flex justify-center gap-4">
          <Link href="/register?role=client" className="btn-primary text-lg px-8">Rent a Vehicle</Link>
          <Link href="/register?role=owner" className="btn-ghost text-lg px-8">List Your Assets</Link>
        </div>
      </section>

      <footer className="bg-brand-surface border-t border-brand-border py-8 text-center text-brand-muted text-sm">
        © {new Date().getFullYear()} HustleKonnect. Built for Africa, by Africans.
      </footer>
    </main>
  )
}
