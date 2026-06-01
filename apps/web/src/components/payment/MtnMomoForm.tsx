"use client";

import { useEffect, useRef, useState } from "react";

interface MtnMomoFormProps {
  amountUgx: number;
  bookingId?: string;
  onSuccess?: (transactionId: string) => void;
  onError?: (error: string) => void;
}

type Status = "idle" | "sending" | "pending" | "confirmed" | "failed";

function formatPhone(v: string) {
  const digits = v.replace(/\D/g, "").slice(0, 9);
  if (digits.length <= 3) return digits;
  if (digits.length <= 6) return `${digits.slice(0, 3)} ${digits.slice(3)}`;
  return `${digits.slice(0, 3)} ${digits.slice(3, 6)} ${digits.slice(6)}`;
}

export default function MtnMomoForm({ amountUgx, bookingId, onSuccess, onError }: MtnMomoFormProps) {
  const [phone, setPhone] = useState("");
  const [status, setStatus] = useState<Status>("idle");
  const [errorMsg, setErrorMsg] = useState<string | null>(null);
  const [transactionId, setTransactionId] = useState<string | null>(null);
  const [dots, setDots] = useState(".");
  const pollRef = useRef<ReturnType<typeof setInterval> | null>(null);
  const dotRef = useRef<ReturnType<typeof setInterval> | null>(null);

  useEffect(() => {
    if (status === "pending") {
      dotRef.current = setInterval(() => setDots((d) => d.length >= 3 ? "." : d + "."), 500);
    } else {
      if (dotRef.current) clearInterval(dotRef.current);
    }
    return () => { if (dotRef.current) clearInterval(dotRef.current); };
  }, [status]);

  useEffect(() => {
    return () => { if (pollRef.current) clearInterval(pollRef.current); };
  }, []);

  const startPolling = (txId: string) => {
    let attempts = 0;
    pollRef.current = setInterval(async () => {
      attempts++;
      if (attempts > 24) { // ~2 min timeout
        clearInterval(pollRef.current!);
        setStatus("failed");
        setErrorMsg("Payment timed out. Please try again.");
        return;
      }
      try {
        const res = await fetch(`/api/v1/payments/mtn-momo/status/${txId}`);
        const data = await res.json();
        if (data.status === "SUCCESSFUL") {
          clearInterval(pollRef.current!);
          setStatus("confirmed");
          onSuccess?.(txId);
        } else if (data.status === "FAILED" || data.status === "REJECTED") {
          clearInterval(pollRef.current!);
          setStatus("failed");
          setErrorMsg(data.message ?? "Payment failed. Please try again.");
        }
      } catch { /* retry */ }
    }, 5000);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setStatus("sending");
    setErrorMsg(null);
    const rawPhone = "+256" + phone.replace(/\s/g, "");
    try {
      const res = await fetch("/api/v1/payments/mtn-momo/request", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ phone: rawPhone, amountUgx, bookingId }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message ?? "Failed to initiate payment");
      setTransactionId(data.transactionId);
      setStatus("pending");
      startPolling(data.transactionId);
    } catch (err: any) {
      setStatus("failed");
      setErrorMsg(err.message);
      onError?.(err.message);
    }
  };

  const reset = () => { setStatus("idle"); setErrorMsg(null); setTransactionId(null); };

  if (status === "confirmed") {
    return (
      <div className="bg-[#141D2B] rounded-2xl border border-[#2ECC8A]/30 p-8 text-center">
        <div className="w-14 h-14 rounded-full bg-[#2ECC8A]/20 flex items-center justify-center text-2xl mx-auto mb-4">✓</div>
        <h3 className="text-white font-semibold text-lg">Payment Confirmed</h3>
        <p className="text-[#7088A8] text-sm mt-1">UGX {amountUgx.toLocaleString()} received via MTN MoMo</p>
        {transactionId && <p className="text-xs text-[#7088A8] mt-2">Ref: {transactionId}</p>}
      </div>
    );
  }

  if (status === "pending") {
    return (
      <div className="bg-[#141D2B] rounded-2xl border border-amber-500/30 p-8 text-center">
        <div className="w-14 h-14 rounded-full bg-amber-500/10 flex items-center justify-center mx-auto mb-4">
          <div className="w-8 h-8 border-3 border-amber-500/30 border-t-amber-400 rounded-full animate-spin" style={{ borderWidth: 3 }} />
        </div>
        <h3 className="text-white font-semibold">Awaiting Payment{dots}</h3>
        <p className="text-[#7088A8] text-sm mt-2">
          A payment prompt of <strong className="text-white">UGX {amountUgx.toLocaleString()}</strong> has been sent to <strong className="text-white">0{phone.replace(/\s/g, "")}</strong>
        </p>
        <p className="text-xs text-[#7088A8] mt-2">Enter your MTN MoMo PIN to confirm</p>
        <div className="mt-4 flex items-center justify-center gap-2 text-xs text-[#7088A8]">
          <span className="w-2 h-2 rounded-full bg-amber-400 animate-pulse" />
          Pending
        </div>
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit} className="bg-[#141D2B] rounded-2xl border border-[#1e2d42] p-6 space-y-4">
      <div className="flex items-center gap-3">
        <div className="w-10 h-10 rounded-xl bg-yellow-400/10 flex items-center justify-center">
          <span className="text-xl">🟡</span>
        </div>
        <div>
          <h3 className="text-white font-semibold">MTN Mobile Money</h3>
          <p className="text-xs text-[#7088A8]">Uganda · UGX</p>
        </div>
      </div>

      <div className="bg-[#1e2d42]/60 rounded-xl p-3">
        <p className="text-sm text-white font-semibold">UGX {amountUgx.toLocaleString()}</p>
        <p className="text-xs text-[#7088A8] mt-0.5">Amount to pay</p>
      </div>

      <div>
        <label className="block text-sm text-[#7088A8] mb-1.5">MTN MoMo Phone Number</label>
        <div className="flex">
          <span className="bg-[#1e2d42] border border-[#1e2d42] rounded-l-xl px-3 py-3 text-sm text-[#7088A8] border-r-0 flex items-center gap-1">
            <span>🇺🇬</span> +256
          </span>
          <input
            className="flex-1 bg-[#080C12] border border-[#1e2d42] rounded-r-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-[#E8922A] placeholder:text-[#3a4e64] font-mono"
            value={phone}
            onChange={(e) => setPhone(formatPhone(e.target.value))}
            placeholder="7XX XXX XXX"
            required
            inputMode="numeric"
          />
        </div>
        <p className="text-xs text-[#7088A8] mt-1">Enter your MTN number registered for Mobile Money</p>
      </div>

      {status === "failed" && errorMsg && (
        <div className="bg-[#E05252]/10 border border-[#E05252]/30 rounded-xl p-3 text-[#E05252] text-sm flex items-center justify-between gap-2">
          <span>{errorMsg}</span>
          <button type="button" onClick={reset} className="text-xs underline shrink-0">Try again</button>
        </div>
      )}

      <button type="submit" disabled={status === "sending"}
        className="w-full bg-yellow-400 text-black font-semibold py-3 rounded-xl hover:bg-yellow-300 transition disabled:opacity-50 flex items-center justify-center gap-2">
        {status === "sending" ? (
          <><span className="w-4 h-4 border-2 border-black/30 border-t-black rounded-full animate-spin" /> Sending request…</>
        ) : (
          "🟡 Pay with MTN MoMo"
        )}
      </button>
    </form>
  );
}
