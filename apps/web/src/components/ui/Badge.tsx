import clsx from 'clsx'

type BadgeVariant = 'green' | 'amber' | 'red' | 'blue' | 'muted'

interface BadgeProps {
  variant?: BadgeVariant
  children: React.ReactNode
  className?: string
}

const variantMap: Record<BadgeVariant, string> = {
  green: 'bg-green-500/10 text-[#2ECC8A]',
  amber: 'bg-amber-500/10 text-[#E8922A]',
  red: 'bg-red-500/10 text-[#E05252]',
  blue: 'bg-blue-500/10 text-[#4A9FE0]',
  muted: 'bg-white/5 text-[#7088A8]',
}

export function Badge({ variant = 'muted', children, className }: BadgeProps) {
  return (
    <span
      className={clsx(
        'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-mono',
        variantMap[variant],
        className
      )}
    >
      {children}
    </span>
  )
}
