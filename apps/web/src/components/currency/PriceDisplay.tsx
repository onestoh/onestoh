"use client";

import { useEffect, useState } from "react";
import { CURRENCIES, usePreferredCurrency } from "./CurrencySelector";

// Approximate exchange rates vs KES
const EXCHANGE_RATES: Record<string, number> = {
  KES: 1,
  UGX: 28,
  TZS: 22.5,
  USD: 0.0078,
  GBP: 0.0062,
  EUR: 0.0071,
};

function formatAmount(amount: number, code: string, symbol: string): string {
  if (["USD", "GBP", "EUR"].includes(code)) {
    return `${symbol}${amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  }
  return `${code} ${Math.round(amount).toLocaleString()}`;
}

export interface PriceDisplayProps {
  kesAmount: number;
  showOriginal?: boolean;
  className?: string;
  size?: "sm" | "md" | "lg";
}

export default function PriceDisplay({ kesAmount, showOriginal = false, className = "", size = "md" }: PriceDisplayProps) {
  const currency = usePreferredCurrency();
  const [rates, setRates] = useState<Record<string, number>>(EXCHANGE_RATES);

  // Optionally refresh rates from API
  useEffect(() => {
    const load = async () => {
      try {
        const res = await fetch("/api/v1/currencies/rates");
        if (res.ok) {
          const data = await res.json();
          if (data.rates) setRates({ KES: 1, ...data.rates });
        }
      } catch { /* use defaults */ }
    };
    load();
  }, []);

  const rate = rates[currency] ?? 1;
  const converted = kesAmount * rate;
  const currencyObj = CURRENCIES.find((c) => c.code === currency) ?? CURRENCIES[0];
  const sizeClasses = { sm: "text-sm", md: "text-base", lg: "text-xl font-bold" }[size];

  return (
    <span className={`${className}`}>
      <span className={`font-semibold text-white ${sizeClasses}`}>
        {formatAmount(converted, currencyObj.code, currencyObj.symbol)}
      </span>
      {showOriginal && currency !== "KES" && (
        <span className="text-xs text-[#7088A8] ml-1">
          (KES {Math.round(kesAmount).toLocaleString()})
        </span>
      )}
    </span>
  );
}
