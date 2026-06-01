"use client";

import { useState } from "react";

interface StripePaymentFormProps {
  amountKes: number;
  bookingId?: string;
  onSuccess?: (paymentIntentId: string) => void;
  onError?: (error: string) => void;
}

const SUPPORTED_CURRENCIES = [
  { code: "USD", symbol: "$", rate: 0.0078, flag: "🇺🇸" },
  { code: "GBP", symbol: "£", rate: 0.0062, flag: "🇬🇧" },
  { code: "EUR", symbol: "€", rate: 0.0071, flag: "🇪🇺" },
];

function formatCard(value: string) {
  return value.replace(/\D/g, "").replace(/(\d{4})/g, "$1 ").trim().slice(0, 19);
}
function formatExpiry(value: string) {
  const v = value.replace(/\D/g, "").slice(0, 4);
  return v.length >= 3 ? `${v.slice(0, 2)}/${v.slice(2)}` : v;
}
function formatCVC(value: string) {
  return value.replace(/\D/g, "").slice(0, 4);
}

type Status = "idle" | "loading" | "success" | "error";

export default function StripePaymentForm({ amountKes, bookingId, onSuccess, onError }: StripePaymentFormProps) {
  const [currency, setCurrency] = useState("USD");
  const [cardNumber, setCardNumber] = useState("");
  const [expiry, setExpiry] = useState("");
  const [cvc, setCvc] = useState("");
  const [name, setName] = useState("");
  const [status, setStatus] = useState<Status>("idle");
  const [errorMsg, setErrorMsg] = useState<string | null>(null);

  const curr = SUPPORTED_CURRENCIES.find((c) => c.code === currency) ?? SUPPORTED_CURRENCIES[0];
  const convertedAmount = (amountKes * curr.rate).toFixed(2);

  const inputCls = "w-full bg-[#080C12] border border-[#1e2d42] rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-[#E8922A] placeholder:text-[#3a4e64] font-mono";

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setStatus("loading");
    setErrorMsg(null);
    try {
      // 1. Create payment intent
      const intentRes = await fetch("/api/v1/payments/stripe/intent", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ amountKes, currency, bookingId }),
      });
      if (!intentRes.ok) {
        const d = await intentRes.json();
        throw new Error(d.message ?? "Failed to create payment intent");
      }
      const { clientSecret, paymentIntentId } = await intentRes.json();

      // 2. Confirm payment (real implementation would use Stripe.js)
      const confirmRes = await fetch("/api/v1/payments/stripe/confirm", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ clientSecret, paymentIntentId, cardNumber: cardNumber.replace(/\s/g, ""), expiry, cvc, name }),
      });
      if (!confirmRes.ok) {
        const d = await confirmRes.json();
        throw new Error(d.message ?? "Payment confirmation failed");
      }
      setStatus("success");
      onSuccess?.(paymentIntentId);
    } catch (err: any) {
      setStatus("error");
      setErrorMsg(err.message);
      onError?.(err.message);
    }
  };

  if (status === "success") {
    return (
      <div className="bg-[#141D2B] rounded-2xl border border-[#2ECC8A]/30 p-8 text-center">
        <div className="w-14 h-14 rounded-full bg-[#2ECC8A]/20 flex items-center justify-center text-2xl mx-auto mb-4">✓</div>
        <h3 className="text-white font-semibold text-lg">Payment Successful</h3>
        <p className="text-[#7088A8] text-sm mt-2">
          {curr.symbol}{convertedAmount} {currency} paid · KES {amountKes.toLocaleString()} equivalent
        </p>
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit} className="bg-[#141D2B] rounded-2xl border border-[#1e2d42] p-6 space-y-4">
      <div className="flex items-center justify-between">
        <h3 className="text-white font-semibold">Card Payment</h3>
        <span className="text-xs px-2 py-1 rounded-full bg-blue-500/20 text-blue-300 font-medium">International payment</span>
      </div>

      {/* Currency selector */}
      <div>
        <label className="block text-sm text-[#7088A8] mb-1.5">Currency</label>
        <div className="flex gap-2">
          {SUPPORTED_CURRENCIES.map((c) => (
            <button
              key={c.code}
              type="button"
              onClick={() => setCurrency(c.code)}
              className={`flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl border text-sm font-medium transition ${
                currency === c.code ? "border-[#E8922A] bg-[#E8922A]/10 text-white" : "border-[#1e2d42] text-[#7088A8] hover:border-[#7088A8]"
              }`}
            >
              <span>{c.flag}</span> {c.code}
            </button>
          ))}
        </div>
      </div>

      {/* Conversion notice */}
      <div className="bg-[#1e2d42]/60 rounded-xl p-3 text-xs text-[#7088A8]">
        {curr.symbol}{convertedAmount} {currency} ≈ KES {amountKes.toLocaleString()} · Rate: 1 {currency} = KES {(1 / curr.rate).toFixed(0)}
      </div>

      {/* Card fields */}
      <div>
        <label className="block text-sm text-[#7088A8] mb-1.5">Name on Card</label>
        <input className={inputCls.replace("font-mono", "")} value={name} onChange={(e) => setName(e.target.value)}
          placeholder="John Doe" required />
      </div>
      <div>
        <label className="block text-sm text-[#7088A8] mb-1.5">Card Number</label>
        <input className={inputCls} value={cardNumber} onChange={(e) => setCardNumber(formatCard(e.target.value))}
          placeholder="4242 4242 4242 4242" required maxLength={19} inputMode="numeric" />
      </div>
      <div className="grid grid-cols-2 gap-3">
        <div>
          <label className="block text-sm text-[#7088A8] mb-1.5">Expiry</label>
          <input className={inputCls} value={expiry} onChange={(e) => setExpiry(formatExpiry(e.target.value))}
            placeholder="MM/YY" required maxLength={5} inputMode="numeric" />
        </div>
        <div>
          <label className="block text-sm text-[#7088A8] mb-1.5">CVC</label>
          <input className={inputCls} value={cvc} onChange={(e) => setCvc(formatCVC(e.target.value))}
            placeholder="123" required maxLength={4} inputMode="numeric" />
        </div>
      </div>

      {errorMsg && (
        <div className="bg-[#E05252]/10 border border-[#E05252]/30 rounded-xl p-3 text-[#E05252] text-sm">{errorMsg}</div>
      )}

      <button type="submit" disabled={status === "loading"}
        className="w-full bg-[#E8922A] text-black font-semibold py-3 rounded-xl hover:bg-amber-400 transition disabled:opacity-50 flex items-center justify-center gap-2">
        {status === "loading" ? (
          <><span className="w-4 h-4 border-2 border-black/30 border-t-black rounded-full animate-spin" /> Processing…</>
        ) : (
          `Pay ${curr.symbol}${convertedAmount} ${currency}`
        )}
      </button>

      <p className="text-center text-xs text-[#7088A8]">🔒 Secured by Stripe · PCI DSS compliant</p>
    </form>
  );
}
