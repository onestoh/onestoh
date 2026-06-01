"use client";

import { useEffect, useRef, useState } from "react";

export interface Currency {
  code: string;
  symbol: string;
  flag: string;
  name: string;
}

export const CURRENCIES: Currency[] = [
  { code: "KES", symbol: "KES", flag: "🇰🇪", name: "Kenyan Shilling" },
  { code: "UGX", symbol: "UGX", flag: "🇺🇬", name: "Ugandan Shilling" },
  { code: "TZS", symbol: "TZS", flag: "🇹🇿", name: "Tanzanian Shilling" },
  { code: "USD", symbol: "$", flag: "🇺🇸", name: "US Dollar" },
  { code: "GBP", symbol: "£", flag: "🇬🇧", name: "British Pound" },
  { code: "EUR", symbol: "€", flag: "🇪🇺", name: "Euro" },
];

// Simple Zustand-free store using a custom event pattern
const CURRENCY_EVENT = "yardos:currency:change";

export function getPreferredCurrency(): string {
  if (typeof window === "undefined") return "KES";
  return localStorage.getItem("yardos_currency") ?? "KES";
}

export function setPreferredCurrencyLocal(code: string) {
  localStorage.setItem("yardos_currency", code);
  window.dispatchEvent(new CustomEvent(CURRENCY_EVENT, { detail: code }));
}

export function usePreferredCurrency() {
  const [currency, setCurrency] = useState<string>("KES");
  useEffect(() => {
    setCurrency(getPreferredCurrency());
    const handler = (e: Event) => setCurrency((e as CustomEvent).detail);
    window.addEventListener(CURRENCY_EVENT, handler);
    return () => window.removeEventListener(CURRENCY_EVENT, handler);
  }, []);
  return currency;
}

export interface CurrencySelectorProps {
  compact?: boolean;
  className?: string;
}

export default function CurrencySelector({ compact = false, className = "" }: CurrencySelectorProps) {
  const [selected, setSelected] = useState<string>("KES");
  const [open, setOpen] = useState(false);
  const ref = useRef<HTMLDivElement>(null);

  useEffect(() => {
    setSelected(getPreferredCurrency());
  }, []);

  useEffect(() => {
    const close = (e: MouseEvent) => { if (ref.current && !ref.current.contains(e.target as Node)) setOpen(false); };
    document.addEventListener("mousedown", close);
    return () => document.removeEventListener("mousedown", close);
  }, []);

  const current = CURRENCIES.find((c) => c.code === selected) ?? CURRENCIES[0];

  const handleSelect = async (code: string) => {
    setSelected(code);
    setPreferredCurrencyLocal(code);
    setOpen(false);
    try {
      await fetch("/api/v1/currencies/set-preferred", {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ currency: code }),
      });
    } catch { /* non-critical */ }
  };

  return (
    <div ref={ref} className={`relative ${className}`}>
      <button
        onClick={() => setOpen(!open)}
        className="flex items-center gap-2 bg-[#1e2d42] hover:bg-[#243447] border border-[#1e2d42] rounded-xl px-3 py-2 text-sm text-white transition"
      >
        <span>{current.flag}</span>
        <span className="font-medium">{current.code}</span>
        {!compact && <span className="text-[#7088A8]">{current.symbol}</span>}
        <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor" className="text-[#7088A8] ml-1">
          <path d="M6 8L1 3h10z" />
        </svg>
      </button>

      {open && (
        <div className="absolute right-0 mt-1 w-52 bg-[#141D2B] border border-[#1e2d42] rounded-xl shadow-xl overflow-hidden z-50">
          {CURRENCIES.map((c) => (
            <button
              key={c.code}
              onClick={() => handleSelect(c.code)}
              className={`w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-[#1e2d42] transition ${
                c.code === selected ? "text-[#E8922A]" : "text-white"
              }`}
            >
              <span className="text-base">{c.flag}</span>
              <span className="font-medium">{c.code}</span>
              <span className="text-[#7088A8] ml-auto">{c.symbol}</span>
            </button>
          ))}
        </div>
      )}
    </div>
  );
}
