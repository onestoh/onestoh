"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";

interface FormData {
  firstName: string;
  lastName: string;
  email: string;
  phone: string;
  password: string;
  confirmPassword: string;
  idType: "national_id" | "passport";
  idNumber: string;
  paymentPrimary: "mtn_momo" | "airtel_money";
  referralCode: string;
}

const INITIAL: FormData = {
  firstName: "", lastName: "", email: "", phone: "",
  password: "", confirmPassword: "",
  idType: "national_id", idNumber: "",
  paymentPrimary: "mtn_momo", referralCode: "",
};

function Field({ label, children }: { label: string; children: React.ReactNode }) {
  return (
    <div>
      <label className="block text-sm text-[#7088A8] mb-1.5">{label}</label>
      {children}
    </div>
  );
}

const inputCls = "w-full bg-[#080C12] border border-[#1e2d42] rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-[#E8922A] placeholder:text-[#3a4e64]";

export default function UgandaRegisterPage() {
  const router = useRouter();
  const [form, setForm] = useState<FormData>(INITIAL);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [step, setStep] = useState(1);

  const update = (key: keyof FormData, value: string) =>
    setForm((prev) => ({ ...prev, [key]: value }));

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (form.password !== form.confirmPassword) { setError("Passwords do not match"); return; }
    if (step < 3) { setStep(step + 1); return; }
    setLoading(true);
    setError(null);
    try {
      const res = await fetch("/api/v1/auth/register", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ ...form, country: "UG", currency: "UGX", phone: form.phone }),
      });
      if (!res.ok) {
        const d = await res.json();
        throw new Error(d.message ?? "Registration failed");
      }
      router.push("/verify-email");
    } catch (err: any) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-[#080C12] flex items-center justify-center p-6">
      <div className="w-full max-w-md">
        {/* Header */}
        <div className="text-center mb-8">
          <div className="flex items-center justify-center gap-2 mb-4">
            <span className="text-3xl">🇺🇬</span>
            <h1 className="text-2xl font-bold text-white">Join TheOnlineYard</h1>
          </div>
          <p className="text-[#7088A8] text-sm">Uganda — UGX account</p>
          {/* Steps */}
          <div className="flex items-center justify-center gap-2 mt-4">
            {[1, 2, 3].map((s) => (
              <div key={s} className={`w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition ${
                s < step ? "bg-[#2ECC8A] text-black" : s === step ? "bg-[#E8922A] text-black" : "bg-[#1e2d42] text-[#7088A8]"
              }`}>{s < step ? "✓" : s}</div>
            ))}
          </div>
        </div>

        <form onSubmit={handleSubmit} className="bg-[#141D2B] rounded-2xl border border-[#1e2d42] p-6 space-y-4">
          {error && <div className="bg-[#E05252]/10 border border-[#E05252]/30 rounded-xl p-3 text-[#E05252] text-sm">{error}</div>}

          {step === 1 && (
            <>
              <h2 className="text-white font-semibold">Personal Details</h2>
              <div className="grid grid-cols-2 gap-3">
                <Field label="First Name">
                  <input className={inputCls} value={form.firstName} onChange={(e) => update("firstName", e.target.value)} placeholder="John" required />
                </Field>
                <Field label="Last Name">
                  <input className={inputCls} value={form.lastName} onChange={(e) => update("lastName", e.target.value)} placeholder="Doe" required />
                </Field>
              </div>
              <Field label="Email">
                <input type="email" className={inputCls} value={form.email} onChange={(e) => update("email", e.target.value)} placeholder="john@example.com" required />
              </Field>
              <Field label="Phone Number (+256)">
                <div className="flex">
                  <span className="bg-[#1e2d42] border border-[#1e2d42] rounded-l-xl px-3 py-3 text-sm text-[#7088A8] border-r-0">+256</span>
                  <input
                    className={`${inputCls} rounded-l-none`}
                    value={form.phone}
                    onChange={(e) => update("phone", "+256" + e.target.value.replace(/^\+256/, "").replace(/\D/g, ""))}
                    placeholder="7XX XXX XXX"
                    required
                  />
                </div>
              </Field>
              <Field label="Password">
                <input type="password" className={inputCls} value={form.password} onChange={(e) => update("password", e.target.value)} placeholder="Min. 8 characters" required minLength={8} />
              </Field>
              <Field label="Confirm Password">
                <input type="password" className={inputCls} value={form.confirmPassword} onChange={(e) => update("confirmPassword", e.target.value)} placeholder="Repeat password" required />
              </Field>
            </>
          )}

          {step === 2 && (
            <>
              <h2 className="text-white font-semibold">Identity Verification</h2>
              <Field label="ID Document Type">
                <select className={inputCls} value={form.idType} onChange={(e) => update("idType", e.target.value as "national_id" | "passport")}>
                  <option value="national_id">Ugandan National ID</option>
                  <option value="passport">Passport</option>
                </select>
              </Field>
              <Field label={form.idType === "national_id" ? "National ID Number" : "Passport Number"}>
                <input className={inputCls} value={form.idNumber} onChange={(e) => update("idNumber", e.target.value)} placeholder={form.idType === "national_id" ? "CM9XXXXXX" : "A00000000"} required />
              </Field>
              <div className="bg-[#1e2d42]/50 rounded-xl p-3">
                <p className="text-xs text-[#7088A8]">Your ID details are encrypted and used only for identity verification as required by Ugandan financial regulations.</p>
              </div>
            </>
          )}

          {step === 3 && (
            <>
              <h2 className="text-white font-semibold">Payment Methods</h2>
              <p className="text-xs text-[#7088A8]">Your default currency is set to <strong className="text-white">UGX</strong>. You can change this in settings.</p>
              <Field label="Primary Payment Method">
                <div className="space-y-2">
                  {(["mtn_momo", "airtel_money"] as const).map((m) => (
                    <label key={m} className={`flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition ${
                      form.paymentPrimary === m ? "border-[#E8922A] bg-[#E8922A]/5" : "border-[#1e2d42]"
                    }`}>
                      <input type="radio" name="paymentPrimary" value={m} checked={form.paymentPrimary === m}
                        onChange={() => update("paymentPrimary", m)} className="accent-[#E8922A]" />
                      <span className="text-sm text-white">{m === "mtn_momo" ? "🟡 MTN Mobile Money" : "🔴 Airtel Money"}</span>
                    </label>
                  ))}
                </div>
              </Field>
              <Field label="Referral Code (optional)">
                <input className={inputCls} value={form.referralCode} onChange={(e) => update("referralCode", e.target.value)} placeholder="e.g. YARD2024" />
              </Field>
            </>
          )}

          <div className="flex gap-3 pt-2">
            {step > 1 && (
              <button type="button" onClick={() => setStep(step - 1)}
                className="flex-1 border border-[#1e2d42] text-white text-sm font-semibold py-3 rounded-xl hover:bg-[#1e2d42] transition">
                Back
              </button>
            )}
            <button type="submit" disabled={loading}
              className="flex-1 bg-[#E8922A] text-black text-sm font-semibold py-3 rounded-xl hover:bg-amber-400 transition disabled:opacity-50">
              {loading ? "Creating account…" : step < 3 ? "Continue" : "Create Account"}
            </button>
          </div>

          <p className="text-center text-xs text-[#7088A8]">
            Already have an account? <a href="/login" className="text-[#E8922A] hover:underline">Sign in</a>
          </p>
        </form>
      </div>
    </div>
  );
}
