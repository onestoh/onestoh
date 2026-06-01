import Link from 'next/link'

export function Footer() {
  return (
    <footer className="bg-[#0B1018] border-t border-white/5 mt-auto">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 py-12">
        <div className="grid grid-cols-2 md:grid-cols-4 gap-8 mb-10">
          <div className="col-span-2 md:col-span-1">
            <span className="text-xl font-serif font-semibold text-[#E8922A]">TheOnlineYard</span>
            <p className="mt-3 text-sm text-[#7088A8] leading-relaxed">
              Kenya&apos;s automated marketplace for vehicle and heavy machinery rental. M-Pesa powered, escrow-protected.
            </p>
            <div className="flex gap-3 mt-4">
              <span className="badge-green">KES</span>
              <span className="badge-amber">M-PESA</span>
              <span className="badge-blue">TRUSTED</span>
            </div>
          </div>
          <div>
            <h4 className="text-xs font-mono tracking-widest uppercase text-[#E8922A] mb-4">Marketplace</h4>
            <ul className="space-y-2">
              {[
                ['Browse Listings', '/'],
                ['SUVs & 4x4', '/listings?category=suv_4x4'],
                ['Heavy Trucks', '/listings?category=heavy_truck'],
                ['Excavators', '/listings?category=excavator'],
                ['Generators', '/listings?category=generator'],
              ].map(([label, href]) => (
                <li key={href}>
                  <Link href={href} className="text-sm text-[#7088A8] hover:text-[#DCE5F2]">{label}</Link>
                </li>
              ))}
            </ul>
          </div>
          <div>
            <h4 className="text-xs font-mono tracking-widest uppercase text-[#E8922A] mb-4">For Owners</h4>
            <ul className="space-y-2">
              {[
                ['List Your Asset', '/register?role=individual_owner'],
                ['Yard Registration', '/register?role=yard_owner'],
                ['Become a Broker', '/register?role=broker'],
                ['Owner Dashboard', '/owner'],
                ['Earnings', '/owner/earnings'],
              ].map(([label, href]) => (
                <li key={href}>
                  <Link href={href} className="text-sm text-[#7088A8] hover:text-[#DCE5F2]">{label}</Link>
                </li>
              ))}
            </ul>
          </div>
          <div>
            <h4 className="text-xs font-mono tracking-widest uppercase text-[#E8922A] mb-4">Support</h4>
            <ul className="space-y-2">
              {[
                ['How It Works', '/how-it-works'],
                ['Safety & Trust', '/trust'],
                ['M-Pesa Payments', '/payments'],
                ['Terms of Service', '/terms'],
                ['Privacy Policy', '/privacy'],
                ['Contact Us', '/contact'],
              ].map(([label, href]) => (
                <li key={href}>
                  <Link href={href} className="text-sm text-[#7088A8] hover:text-[#DCE5F2]">{label}</Link>
                </li>
              ))}
            </ul>
          </div>
        </div>
        <div className="border-t border-white/5 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
          <p className="text-xs text-[#7088A8] font-mono">
            &copy; {new Date().getFullYear()} TheOnlineYard Ltd. Nairobi, Kenya. All rights reserved.
          </p>
          <p className="text-xs text-[#7088A8]">
            Regulated marketplace &bull; Escrow-protected transactions &bull; KYC verified
          </p>
        </div>
      </div>
    </footer>
  )
}
