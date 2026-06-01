'use client'
import { useEffect } from 'react'
import { X } from 'lucide-react'
import clsx from 'clsx'

interface ModalProps {
  open: boolean
  onClose: () => void
  title?: string
  children: React.ReactNode
  size?: 'sm' | 'md' | 'lg' | 'xl'
}

const sizeMap = {
  sm: 'max-w-md',
  md: 'max-w-lg',
  lg: 'max-w-2xl',
  xl: 'max-w-4xl',
}

export function Modal({ open, onClose, title, children, size = 'md' }: ModalProps) {
  useEffect(() => {
    if (open) {
      document.body.style.overflow = 'hidden'
    } else {
      document.body.style.overflow = ''
    }
    return () => { document.body.style.overflow = '' }
  }, [open])

  if (!open) return null

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div
        className="absolute inset-0 bg-black/70 backdrop-blur-sm"
        onClick={onClose}
      />
      <div
        className={clsx(
          'relative w-full bg-[#141D2B] border border-white/10 rounded-2xl shadow-2xl',
          sizeMap[size]
        )}
      >
        {title && (
          <div className="flex items-center justify-between px-6 py-4 border-b border-white/5">
            <h2 className="font-semibold text-lg text-[#DCE5F2]">{title}</h2>
            <button
              onClick={onClose}
              className="p-1 rounded-lg text-[#7088A8] hover:text-[#DCE5F2] hover:bg-white/5 transition-colors"
            >
              <X className="w-5 h-5" />
            </button>
          </div>
        )}
        <div className="p-6">{children}</div>
      </div>
    </div>
  )
}
