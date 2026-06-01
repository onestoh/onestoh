import Link from 'next/link'

export default function AuthLayout({ children }: { children: React.ReactNode }) {
  return (
    <div className="min-h-screen bg-[#080C12] flex flex-col">
      <header className="px-6 py-4 border-b border-white/5">
        <Link href="/" className="text-xl font-serif font-semibold text-[#E8922A]">
          TheOnlineYard
        </Link>
      </header>
      <main className="flex-1 flex items-center justify-center p-6">
        <div className="w-full max-w-md">{children}</div>
      </main>
      <footer className="py-4 text-center text-xs text-[#7088A8]">
        &copy; {new Date().getFullYear()} TheOnlineYard Ltd. Kenya
      </footer>
    </div>
  )
}
