import clsx from 'clsx'
import { forwardRef } from 'react'

interface InputProps extends React.InputHTMLAttributes<HTMLInputElement> {
  label?: string
  error?: string
  hint?: string
}

export const Input = forwardRef<HTMLInputElement, InputProps>(function Input(
  { label, error, hint, className, ...props },
  ref
) {
  return (
    <div className="w-full">
      {label && (
        <label className="block text-xs font-mono tracking-widest uppercase text-[#E8922A] mb-2">
          {label}
        </label>
      )}
      <input
        ref={ref}
        className={clsx(
          'w-full bg-[#141D2B] border rounded-lg px-4 py-2.5 text-[#DCE5F2] placeholder:text-[#7088A8] focus:outline-none transition-colors',
          error
            ? 'border-[#E05252]/50 focus:border-[#E05252]'
            : 'border-white/10 focus:border-[#E8922A]/50',
          className
        )}
        {...props}
      />
      {error && <p className="mt-1.5 text-xs text-[#E05252]">{error}</p>}
      {hint && !error && <p className="mt-1.5 text-xs text-[#7088A8]">{hint}</p>}
    </div>
  )
})
