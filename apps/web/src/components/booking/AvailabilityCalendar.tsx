'use client'
import { useState, useEffect } from 'react'
import { ChevronLeft, ChevronRight } from 'lucide-react'
import {
  format, startOfMonth, endOfMonth, eachDayOfInterval,
  isSameDay, isBefore, startOfDay, addMonths, subMonths, getDay
} from 'date-fns'
import clsx from 'clsx'

interface AvailabilityCalendarProps {
  assetId: number
  onRangeSelect?: (start: Date, end: Date) => void
  bookedDates?: string[] // ISO date strings
}

export function AvailabilityCalendar({
  assetId,
  onRangeSelect,
  bookedDates = [],
}: AvailabilityCalendarProps) {
  const [currentMonth, setCurrentMonth] = useState(new Date())
  const [startDate, setStartDate] = useState<Date | null>(null)
  const [endDate, setEndDate] = useState<Date | null>(null)
  const [hoverDate, setHoverDate] = useState<Date | null>(null)

  const today = startOfDay(new Date())
  const monthStart = startOfMonth(currentMonth)
  const monthEnd = endOfMonth(currentMonth)
  const days = eachDayOfInterval({ start: monthStart, end: monthEnd })

  // Pad start of month
  const startPad = getDay(monthStart)

  const isBooked = (date: Date) =>
    bookedDates.some((d) => isSameDay(new Date(d), date))

  const isPast = (date: Date) => isBefore(date, today)

  const isInRange = (date: Date) => {
    if (!startDate) return false
    const end = endDate || hoverDate
    if (!end) return false
    const [s, e] = startDate <= end ? [startDate, end] : [end, startDate]
    return date > s && date < e
  }

  const isStart = (date: Date) => startDate ? isSameDay(date, startDate) : false
  const isEnd = (date: Date) => endDate ? isSameDay(date, endDate) : false

  const handleDayClick = (date: Date) => {
    if (isPast(date) || isBooked(date)) return
    if (!startDate || (startDate && endDate)) {
      setStartDate(date)
      setEndDate(null)
    } else {
      if (isBefore(date, startDate)) {
        setStartDate(date)
        setEndDate(null)
      } else {
        setEndDate(date)
        if (onRangeSelect) onRangeSelect(startDate, date)
      }
    }
  }

  const WEEKDAYS = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']

  return (
    <div className="bg-[#141D2B] border border-white/5 rounded-xl p-4">
      {/* Header */}
      <div className="flex items-center justify-between mb-4">
        <button
          onClick={() => setCurrentMonth(subMonths(currentMonth, 1))}
          className="p-1.5 rounded-lg text-[#7088A8] hover:text-[#DCE5F2] hover:bg-white/5"
        >
          <ChevronLeft className="w-4 h-4" />
        </button>
        <h3 className="font-semibold text-[#DCE5F2]">
          {format(currentMonth, 'MMMM yyyy')}
        </h3>
        <button
          onClick={() => setCurrentMonth(addMonths(currentMonth, 1))}
          className="p-1.5 rounded-lg text-[#7088A8] hover:text-[#DCE5F2] hover:bg-white/5"
        >
          <ChevronRight className="w-4 h-4" />
        </button>
      </div>

      {/* Weekday headers */}
      <div className="grid grid-cols-7 mb-2">
        {WEEKDAYS.map((d) => (
          <div key={d} className="text-center text-xs font-mono text-[#7088A8] py-1">{d}</div>
        ))}
      </div>

      {/* Days grid */}
      <div className="grid grid-cols-7 gap-0.5">
        {Array.from({ length: startPad }).map((_, i) => (
          <div key={`pad-${i}`} />
        ))}
        {days.map((day) => {
          const past = isPast(day)
          const booked = isBooked(day)
          const inRange = isInRange(day)
          const start = isStart(day)
          const end = isEnd(day)
          const disabled = past || booked

          return (
            <button
              key={day.toISOString()}
              onClick={() => handleDayClick(day)}
              onMouseEnter={() => !disabled && setHoverDate(day)}
              onMouseLeave={() => setHoverDate(null)}
              disabled={disabled}
              className={clsx(
                'relative text-xs py-2 rounded-lg transition-colors text-center',
                disabled && 'opacity-30 cursor-not-allowed',
                !disabled && !start && !end && !inRange && 'hover:bg-white/5 text-[#7088A8] hover:text-[#DCE5F2]',
                inRange && 'bg-[#E8922A]/10 text-[#E8922A]',
                (start || end) && 'bg-[#E8922A] text-[#080C12] font-bold',
                booked && 'line-through'
              )}
            >
              {format(day, 'd')}
              {booked && (
                <span className="absolute bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-[#E05252]" />
              )}
            </button>
          )
        })}
      </div>

      {/* Legend */}
      <div className="flex items-center gap-4 mt-4 pt-4 border-t border-white/5">
        <div className="flex items-center gap-1.5">
          <span className="w-3 h-3 rounded-full bg-[#2ECC8A]" />
          <span className="text-xs text-[#7088A8]">Available</span>
        </div>
        <div className="flex items-center gap-1.5">
          <span className="w-3 h-3 rounded-full bg-[#E05252]" />
          <span className="text-xs text-[#7088A8]">Booked</span>
        </div>
        <div className="flex items-center gap-1.5">
          <span className="w-3 h-3 rounded bg-[#E8922A]" />
          <span className="text-xs text-[#7088A8]">Selected</span>
        </div>
      </div>

      {startDate && (
        <p className="mt-3 text-xs text-[#7088A8] font-mono">
          {endDate
            ? `${format(startDate, 'dd MMM')} → ${format(endDate, 'dd MMM yyyy')}`
            : `Start: ${format(startDate, 'dd MMM yyyy')} — select end date`}
        </p>
      )}
    </div>
  )
}
