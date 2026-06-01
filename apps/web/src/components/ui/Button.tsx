import clsx from 'clsx'
import { Loader2 } from 'lucide-react'

type ButtonVariant = 'primary' | 'outline' | 'ghost' | 'danger'
type ButtonSize = 'sm' | 'md' | 'lg'

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: ButtonVariant
  size?: ButtonSize
  loading?: boolean
  children: React.ReactNode
}

const variantMap: Record<ButtonVariant, string> = {
  primary: 'bg-[#E8922A] text-[#080C12] font-semibold hover:bg-[#F5B050]',
  outline: 'border border-[#E8922A] text-[#E8922A] hover:bg-[#E8922A] hover:text-[#080C12]',
  ghost: 'text-[#DCE5F2] hover:bg-white/5',
  danger: 'bg-[#E05252]/10 text-[#E05252] border border-[#E05252]/30 hover:bg-[#E05252] hover:text-white',
}

const sizeMap: Record<ButtonSize, string> = {
  sm: 'px-3 py-1.5 text-sm',
  md: 'px-6 py-2.5',
  lg: 'px-8 py-3 text-lg',
}

export function Button({
  variant = 'primary',
  size = 'md',
  loading = false,
  disabled,
  children,
  className,
  ...props
}: ButtonProps) {
  return (
    <button
      disabled={disabled || loading}
      className={clsx(
        'inline-flex items-center justify-center gap-2 rounded-lg transition-colors font-medium',
        variantMap[variant],
        sizeMap[size],
        (disabled || loading) && 'opacity-50 cursor-not-allowed',
        className
      )}
      {...props}
    >
      {loading && <Loader2 className="w-4 h-4 animate-spin" />}
      {children}
    </button>
  )
}
