"use client";

import { useEffect, useState } from "react";
import { useParams, useRouter } from "next/navigation";
import TenantBrandingPreview from "@/components/tenant/TenantBrandingPreview";

interface Gateway {
  id: string;
  name: string;
  country: string;
  configured: boolean;
  enabled: boolean;
}

interface TenantDetail {
  id: string;
  name: string;
  slug: string;
  plan: string;
  country: string;
  currency: string;
  timezone: string;
  primaryColor: string;
  logoUrl: string | null;
  platformFeePercent: number;
  referralCommissionPercent: number;
  perTransactionFee: number;
  monthlyFee: number;
  currentPeriodGmv: number;
  revenueDue: number;
  status: "active" | "suspended";
  stats: {
    users: number;
    assets: number;
    bookings30d: number;
    gmv30d: number;
    disputesOpen: number;
  };
  gateways: Gateway[];
}

const GATEWAY_COUNTRIES: Record<string, string> = {
  mpesa: "Kenya",
  mtn_momo: "Uganda",
  tigo_pesa: "Tanzania",
  stripe: "International",
  flutterwave: "Africa",
};

const GATEWAY_NAMES: Record<string, string> = {
  mpesa: "M-Pesa",
  mtn_momo: "MTN Mobile Money",
  tigo_pesa: "Tigo Pesa",
  stripe: "Stripe",
  flutterwave: "Flutterwave",
};

function GatewayCredentialsModal({ gateway, onClose, onSave }: {
  gateway: Gateway;
  onClose: () => void;
  onSave: (id: string, credentials: Record<string, string>) => Promise<void>;
}) {
  const [fields, setFields] = useState<Record<string, string>>({});
  const [loading, setLoading] = useState(false);
  const [saved, setSaved] = useState(false);

  const gatewayFields: Record<string, { key: string; label: string }[]> = {
    mpesa: [{ key: "consumer_key", label: "Consumer Key" }, { key: "consumer_secret", label: "Consumer Secret" }, { key: "shortcode", label: "Shortcode" }, { key: "passkey", label: "Passkey" }],
    mtn_momo: [{ key: "subscription_key", label: "Subscription Key" }, { key: "api_user", label: "API User UUID" }, { key: "api_key", label: "API Key" }],
    tigo_pesa: [{ key: "username", label: "Username" }, { key: "password", label: "Password" }, { key: "biller_code", label: "Biller Code" }],
    stripe: [{ key: "publishable_key", label: "Publishable Key" }, { key: "secret_key", label: "Secret Key" }, { key: "webhook_secret", label: "Webhook Secret" }],
    flutterwave: [{ key: "public_key", label: "Public Key" }, { key: "secret_key", label: "Secret Key" }, { key: "encryption_key", label: "Encryption Key" }],
  };

  const flds = gatewayFields[gateway.id] ?? [];

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    await onSave(gateway.id, fields);
    setSaved(true);
    setLoading(false);
    setTimeout(onClose, 1200);
  };

  return (
    <div className="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
      <div className="bg-[#141D2B] rounded-2xl border border-[#1e2d42] w-full max-w-md">
        <div className="flex items-center justify-between p-5 border-b border-[#1e2d42]">
          <h2 className="font-semibold text-white">Configure {gateway.name}</h2>
          <button onClick={onClose} className="text-[#7088A8] hover:text-white text-xl">×</button>
        </div>
        {saved ? (
          <div className="p-8 text-center">
            <div className="text-3xl mb-3">✓</div>
            <p className="text-[#2ECC8A] font-semibold">Credentials saved securely</p>
          </div>
        ) : (
          <form onSubmit={handleSave} className="p-5 space-y-3">
            <p className="text-xs text-[#7088A8]">Credentials are encrypted at rest. Existing values are shown as ***.</p>
            {flds.map((f) => (
              <div key={f.key}>
                <label className="block text-xs text-[#7088A8] mb-1">{f.label}</label>
                <input
                  className="w-full bg-[#080C12] border border-[#1e2d42] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#E8922A] font-mono placeholder:text-[#3a4e64]"
                  placeholder={gateway.configured ? "***" : "Enter value"}
                  value={fields[f.key] ?? ""}
                  onChange={(e) => setFields((p) => ({ ...p, [f.key]: e.target.value }))}
                />
              </div>
            ))}
            <button type="submit" disabled={loading}
              className="w-full bg-[#E8922A] text-black font-semibold py-3 rounded-xl hover:bg-amber-400 transition disabled:opacity-50">
              {loading ? "Saving…" : "Save Credentials"}
            </button>
          </form>
        )}
      </div>
    </div>
  );
}

export default function TenantDetailPage() {
  const { tenantId } = useParams<{ tenantId: string }>();
  const router = useRouter();
  const [tenant, setTenant] = useState<TenantDetail | null>(null);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [configGateway, setConfigGateway] = useState<Gateway | null>(null);
  const [suspendConfirm, setSuspendConfirm] = useState(false);

  // Editable branding state
  const [primaryColor, setPrimaryColor] = useState("#E8922A");
  const [logoUrl, setLogoUrl] = useState("");

  useEffect(() => {
    const load = async () => {
      try {
        const res = await fetch(`/api/v1/admin/tenants/${tenantId}`);
        const data = await res.json();
        setTenant(data.tenant);
        setPrimaryColor(data.tenant.primaryColor ?? "#E8922A");
        setLogoUrl(data.tenant.logoUrl ?? "");
      } catch { /* ignore */ } finally { setLoading(false); }
    };
    load();
  }, [tenantId]);

  const saveBranding = async () => {
    setSaving(true);
    try {
      await fetch(`/api/v1/admin/tenants/${tenantId}/branding`, {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ primaryColor, logoUrl }),
      });
      setTenant((p) => p ? { ...p, primaryColor, logoUrl } : p);
    } catch { /* ignore */ } finally { setSaving(false); }
  };

  const saveFees = async (fees: { platformFeePercent: number; referralCommissionPercent: number; perTransactionFee: number }) => {
    await fetch(`/api/v1/admin/tenants/${tenantId}/fees`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(fees),
    });
  };

  const saveGatewayCredentials = async (gatewayId: string, credentials: Record<string, string>) => {
    await fetch(`/api/v1/admin/tenants/${tenantId}/gateways/${gatewayId}`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ credentials }),
    });
    setTenant((p) => p ? {
      ...p,
      gateways: p.gateways.map((g) => g.id === gatewayId ? { ...g, configured: true } : g),
    } : p);
  };

  const toggleSuspend = async () => {
    const action = tenant?.status === "active" ? "suspend" : "reactivate";
    await fetch(`/api/v1/admin/tenants/${tenantId}/${action}`, { method: "POST" });
    setTenant((p) => p ? { ...p, status: action === "suspend" ? "suspended" : "active" } : p);
    setSuspendConfirm(false);
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-[#080C12] p-6 animate-pulse">
        <div className="max-w-5xl mx-auto space-y-6">
          <div className="h-8 bg-[#141D2B] rounded w-64" />
          <div className="grid grid-cols-5 gap-4">
            {[...Array(5)].map((_, i) => <div key={i} className="h-24 bg-[#141D2B] rounded-xl" />)}
          </div>
          <div className="h-64 bg-[#141D2B] rounded-xl" />
        </div>
      </div>
    );
  }

  if (!tenant) {
    return (
      <div className="min-h-screen bg-[#080C12] flex items-center justify-center">
        <div className="text-center">
          <p className="text-white font-semibold">Tenant not found</p>
          <button onClick={() => router.back()} className="text-[#E8922A] text-sm mt-2 hover:underline">Go back</button>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-[#080C12] p-6">
      <div className="max-w-5xl mx-auto space-y-8">
        {/* Header */}
        <div className="flex items-start justify-between">
          <div>
            <button onClick={() => router.back()} className="text-xs text-[#7088A8] hover:text-white mb-2 flex items-center gap-1">← Tenants</button>
            <h1 className="text-2xl font-bold text-white">{tenant.name}</h1>
            <p className="text-[#7088A8] text-sm mt-0.5">{tenant.slug} · {tenant.country} · {tenant.currency}</p>
          </div>
          <button
            onClick={() => setSuspendConfirm(true)}
            className={`px-4 py-2.5 rounded-xl text-sm font-semibold transition ${
              tenant.status === "active"
                ? "border border-[#E05252] text-[#E05252] hover:bg-[#E05252]/10"
                : "bg-[#2ECC8A] text-black hover:bg-green-400"
            }`}
          >
            {tenant.status === "active" ? "Suspend Tenant" : "Reactivate Tenant"}
          </button>
        </div>

        {/* Stats */}
        <div className="grid grid-cols-2 md:grid-cols-5 gap-3">
          {[
            { label: "Users", value: tenant.stats.users.toLocaleString() },
            { label: "Assets", value: tenant.stats.assets.toLocaleString() },
            { label: "Bookings (30d)", value: tenant.stats.bookings30d.toLocaleString() },
            { label: "GMV (30d)", value: `${tenant.currency} ${tenant.stats.gmv30d.toLocaleString()}` },
            { label: "Open Disputes", value: tenant.stats.disputesOpen.toString(), highlight: tenant.stats.disputesOpen > 0 },
          ].map((s) => (
            <div key={s.label} className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-4 text-center">
              <p className={`text-xl font-bold ${s.highlight ? "text-[#E8922A]" : "text-white"}`}>{s.value}</p>
              <p className="text-xs text-[#7088A8] mt-0.5">{s.label}</p>
            </div>
          ))}
        </div>

        {/* Branding */}
        <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-6">
          <h2 className="text-white font-semibold mb-4">Branding</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="space-y-4">
              <div>
                <label className="block text-xs text-[#7088A8] mb-1.5">Primary Colour</label>
                <div className="flex gap-3 items-center">
                  <input type="color" value={primaryColor} onChange={(e) => setPrimaryColor(e.target.value)}
                    className="w-12 h-10 rounded-lg border border-[#1e2d42] bg-transparent cursor-pointer" />
                  <input
                    className="flex-1 bg-[#080C12] border border-[#1e2d42] rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:border-[#E8922A] font-mono"
                    value={primaryColor} onChange={(e) => setPrimaryColor(e.target.value)}
                  />
                </div>
              </div>
              <div>
                <label className="block text-xs text-[#7088A8] mb-1.5">Logo URL</label>
                <input
                  className="w-full bg-[#080C12] border border-[#1e2d42] rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:border-[#E8922A]"
                  value={logoUrl} onChange={(e) => setLogoUrl(e.target.value)}
                  placeholder="https://cdn.example.com/logo.png"
                />
              </div>
              <button onClick={saveBranding} disabled={saving}
                className="bg-[#E8922A] text-black text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-amber-400 transition disabled:opacity-50">
                {saving ? "Saving…" : "Save Branding"}
              </button>
            </div>
            <div>
              <p className="text-xs text-[#7088A8] mb-2">Live Preview</p>
              <TenantBrandingPreview primaryColor={primaryColor} logoUrl={logoUrl || undefined} tenantName={tenant.name} />
            </div>
          </div>
        </div>

        {/* Payment Gateways */}
        <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-6">
          <h2 className="text-white font-semibold mb-4">Payment Gateways</h2>
          <div className="space-y-3">
            {(tenant.gateways ?? []).map((g) => (
              <div key={g.id} className="flex items-center justify-between p-4 bg-[#1e2d42]/50 rounded-xl">
                <div className="flex items-center gap-3">
                  <div className="w-9 h-9 rounded-xl bg-[#080C12] flex items-center justify-center text-lg">
                    {g.id === "mpesa" ? "🇰🇪" : g.id === "mtn_momo" ? "🇺🇬" : g.id === "tigo_pesa" ? "🇹🇿" : g.id === "stripe" ? "💳" : "🇬🇧"}
                  </div>
                  <div>
                    <p className="text-sm text-white">{GATEWAY_NAMES[g.id] ?? g.name}</p>
                    <p className="text-xs text-[#7088A8]">{GATEWAY_COUNTRIES[g.id] ?? g.country}</p>
                  </div>
                </div>
                <div className="flex items-center gap-3">
                  <span className={`text-xs px-2 py-0.5 rounded-full ${
                    g.configured ? "bg-[#2ECC8A]/20 text-[#2ECC8A]" : "bg-[#E05252]/20 text-[#E05252]"
                  }`}>
                    {g.configured ? "Configured" : "Not Configured"}
                  </span>
                  <button onClick={() => setConfigGateway(g)}
                    className="text-sm text-[#E8922A] hover:underline">Configure</button>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Fee Structure */}
        <FeeStructurePanel tenant={tenant} onSave={saveFees} />

        {/* Billing */}
        <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-6">
          <h2 className="text-white font-semibold mb-4">Billing</h2>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
              <p className="text-xs text-[#7088A8]">Plan</p>
              <p className="text-white font-semibold capitalize mt-1">{tenant.plan}</p>
            </div>
            <div>
              <p className="text-xs text-[#7088A8]">Monthly Fee</p>
              <p className="text-white font-semibold mt-1">{tenant.currency} {tenant.monthlyFee.toLocaleString()}</p>
            </div>
            <div>
              <p className="text-xs text-[#7088A8]">Period GMV</p>
              <p className="text-white font-semibold mt-1">{tenant.currency} {tenant.currentPeriodGmv.toLocaleString()}</p>
            </div>
            <div>
              <p className="text-xs text-[#7088A8]">Revenue Due</p>
              <p className="text-[#E8922A] font-semibold mt-1">{tenant.currency} {tenant.revenueDue.toLocaleString()}</p>
            </div>
          </div>
        </div>
      </div>

      {configGateway && (
        <GatewayCredentialsModal
          gateway={configGateway}
          onClose={() => setConfigGateway(null)}
          onSave={saveGatewayCredentials}
        />
      )}

      {suspendConfirm && (
        <div className="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
          <div className="bg-[#141D2B] rounded-2xl border border-[#1e2d42] w-full max-w-sm p-6 text-center">
            <p className="text-white font-semibold text-lg mb-2">
              {tenant.status === "active" ? "Suspend" : "Reactivate"} {tenant.name}?
            </p>
            <p className="text-[#7088A8] text-sm mb-6">
              {tenant.status === "active"
                ? "This will prevent all users of this tenant from accessing the platform."
                : "This will restore access for all users of this tenant."}
            </p>
            <div className="flex gap-3">
              <button onClick={() => setSuspendConfirm(false)}
                className="flex-1 border border-[#1e2d42] text-white text-sm font-semibold py-3 rounded-xl hover:bg-[#1e2d42] transition">Cancel</button>
              <button onClick={toggleSuspend}
                className={`flex-1 text-sm font-semibold py-3 rounded-xl transition ${
                  tenant.status === "active" ? "bg-[#E05252] text-white hover:bg-red-500" : "bg-[#2ECC8A] text-black hover:bg-green-400"
                }`}>
                Confirm
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

function FeeStructurePanel({ tenant, onSave }: {
  tenant: TenantDetail;
  onSave: (fees: { platformFeePercent: number; referralCommissionPercent: number; perTransactionFee: number }) => Promise<void>;
}) {
  const [platformFee, setPlatformFee] = useState(tenant.platformFeePercent.toString());
  const [referralFee, setReferralFee] = useState(tenant.referralCommissionPercent.toString());
  const [txFee, setTxFee] = useState(tenant.perTransactionFee.toString());
  const [saving, setSaving] = useState(false);
  const [saved, setSaved] = useState(false);

  const handleSave = async () => {
    setSaving(true);
    await onSave({
      platformFeePercent: parseFloat(platformFee),
      referralCommissionPercent: parseFloat(referralFee),
      perTransactionFee: parseFloat(txFee),
    });
    setSaving(false); setSaved(true);
    setTimeout(() => setSaved(false), 2000);
  };

  const inputCls = "w-full bg-[#080C12] border border-[#1e2d42] rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:border-[#E8922A]";

  return (
    <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-6">
      <h2 className="text-white font-semibold mb-4">Fee Structure</h2>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label className="block text-xs text-[#7088A8] mb-1.5">Platform Fee (%)</label>
          <input className={inputCls} type="number" step="0.1" min="0" max="100" value={platformFee} onChange={(e) => setPlatformFee(e.target.value)} />
        </div>
        <div>
          <label className="block text-xs text-[#7088A8] mb-1.5">Referral Commission (%)</label>
          <input className={inputCls} type="number" step="0.1" min="0" max="100" value={referralFee} onChange={(e) => setReferralFee(e.target.value)} />
        </div>
        <div>
          <label className="block text-xs text-[#7088A8] mb-1.5">Per-Transaction Fee ({tenant.currency})</label>
          <input className={inputCls} type="number" step="1" min="0" value={txFee} onChange={(e) => setTxFee(e.target.value)} />
        </div>
      </div>
      <button onClick={handleSave} disabled={saving}
        className="mt-4 bg-[#E8922A] text-black text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-amber-400 transition disabled:opacity-50">
        {saved ? "✓ Saved" : saving ? "Saving…" : "Save Fees"}
      </button>
    </div>
  );
}
