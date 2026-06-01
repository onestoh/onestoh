"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";

interface Tenant {
  id: string;
  name: string;
  slug: string;
  plan: "starter" | "growth" | "enterprise";
  country: string;
  currency: string;
  mrr: number;
  activeBookings: number;
  userCount: number;
  status: "active" | "suspended" | "pending";
  createdAt: string;
}

interface CreateTenantForm {
  name: string;
  slug: string;
  plan: "starter" | "growth" | "enterprise";
  country: string;
  currency: string;
  timezone: string;
  adminEmail: string;
  adminName: string;
}

const PLAN_STYLES: Record<string, string> = {
  starter: "bg-[#1e2d42] text-[#7088A8]",
  growth: "bg-blue-500/20 text-blue-300",
  enterprise: "bg-[#E8922A]/20 text-[#E8922A]",
};

const STATUS_STYLES: Record<string, string> = {
  active: "bg-[#2ECC8A]/20 text-[#2ECC8A]",
  suspended: "bg-[#E05252]/20 text-[#E05252]",
  pending: "bg-amber-500/20 text-amber-400",
};

const COUNTRIES = [
  { code: "KE", name: "Kenya", currency: "KES", timezone: "Africa/Nairobi" },
  { code: "UG", name: "Uganda", currency: "UGX", timezone: "Africa/Kampala" },
  { code: "TZ", name: "Tanzania", currency: "TZS", timezone: "Africa/Dar_es_Salaam" },
  { code: "NG", name: "Nigeria", currency: "NGN", timezone: "Africa/Lagos" },
  { code: "GH", name: "Ghana", currency: "GHS", timezone: "Africa/Accra" },
];

const INITIAL_FORM: CreateTenantForm = {
  name: "", slug: "", plan: "starter", country: "KE",
  currency: "KES", timezone: "Africa/Nairobi", adminEmail: "", adminName: "",
};

function CreateTenantModal({ onClose, onCreate }: {
  onClose: () => void;
  onCreate: (form: CreateTenantForm) => Promise<void>;
}) {
  const [form, setForm] = useState<CreateTenantForm>(INITIAL_FORM);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const update = (k: keyof CreateTenantForm, v: string) => setForm((p) => ({ ...p, [k]: v }));

  const handleCountryChange = (code: string) => {
    const c = COUNTRIES.find((c) => c.code === code);
    setForm((p) => ({ ...p, country: code, currency: c?.currency ?? p.currency, timezone: c?.timezone ?? p.timezone }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true); setError(null);
    try { await onCreate(form); onClose(); }
    catch (err: any) { setError(err.message); }
    finally { setLoading(false); }
  };

  const inputCls = "w-full bg-[#080C12] border border-[#1e2d42] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#E8922A]";

  return (
    <div className="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto">
      <div className="bg-[#141D2B] rounded-2xl border border-[#1e2d42] w-full max-w-lg my-4">
        <div className="flex items-center justify-between p-5 border-b border-[#1e2d42]">
          <h2 className="font-semibold text-white">Create New Tenant</h2>
          <button onClick={onClose} className="text-[#7088A8] hover:text-white text-xl">×</button>
        </div>
        <form onSubmit={handleSubmit} className="p-5 space-y-4">
          {error && <div className="bg-[#E05252]/10 border border-[#E05252]/30 rounded-xl p-3 text-[#E05252] text-sm">{error}</div>}
          <div className="grid grid-cols-2 gap-3">
            <div className="col-span-2">
              <label className="block text-xs text-[#7088A8] mb-1">Tenant Name</label>
              <input className={inputCls} value={form.name} onChange={(e) => update("name", e.target.value)} placeholder="e.g. YardOS Uganda" required />
            </div>
            <div>
              <label className="block text-xs text-[#7088A8] mb-1">Slug</label>
              <input className={inputCls} value={form.slug} onChange={(e) => update("slug", e.target.value.toLowerCase().replace(/[^a-z0-9-]/g, "-"))} placeholder="yardos-ug" required />
            </div>
            <div>
              <label className="block text-xs text-[#7088A8] mb-1">Plan</label>
              <select className={inputCls} value={form.plan} onChange={(e) => update("plan", e.target.value as CreateTenantForm["plan"])}>
                <option value="starter">Starter</option>
                <option value="growth">Growth</option>
                <option value="enterprise">Enterprise</option>
              </select>
            </div>
            <div>
              <label className="block text-xs text-[#7088A8] mb-1">Country</label>
              <select className={inputCls} value={form.country} onChange={(e) => handleCountryChange(e.target.value)}>
                {COUNTRIES.map((c) => <option key={c.code} value={c.code}>{c.name}</option>)}
              </select>
            </div>
            <div>
              <label className="block text-xs text-[#7088A8] mb-1">Currency</label>
              <input className={inputCls} value={form.currency} onChange={(e) => update("currency", e.target.value)} />
            </div>
            <div className="col-span-2">
              <label className="block text-xs text-[#7088A8] mb-1">Timezone</label>
              <input className={inputCls} value={form.timezone} onChange={(e) => update("timezone", e.target.value)} />
            </div>
            <div>
              <label className="block text-xs text-[#7088A8] mb-1">Admin Name</label>
              <input className={inputCls} value={form.adminName} onChange={(e) => update("adminName", e.target.value)} placeholder="Jane Doe" required />
            </div>
            <div>
              <label className="block text-xs text-[#7088A8] mb-1">Admin Email</label>
              <input type="email" className={inputCls} value={form.adminEmail} onChange={(e) => update("adminEmail", e.target.value)} placeholder="admin@tenant.com" required />
            </div>
          </div>
          <button type="submit" disabled={loading}
            className="w-full bg-[#E8922A] text-black font-semibold py-3 rounded-xl hover:bg-amber-400 transition disabled:opacity-50">
            {loading ? "Creating…" : "Create Tenant"}
          </button>
        </form>
      </div>
    </div>
  );
}

export default function TenantsPage() {
  const router = useRouter();
  const [tenants, setTenants] = useState<Tenant[]>([]);
  const [loading, setLoading] = useState(true);
  const [showCreate, setShowCreate] = useState(false);

  useEffect(() => {
    const load = async () => {
      try {
        const res = await fetch("/api/v1/admin/tenants");
        const data = await res.json();
        setTenants(data.tenants ?? []);
      } catch { /* ignore */ } finally { setLoading(false); }
    };
    load();
  }, []);

  const handleCreate = async (form: CreateTenantForm) => {
    const res = await fetch("/api/v1/admin/tenants", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(form),
    });
    if (!res.ok) {
      const d = await res.json();
      throw new Error(d.message ?? "Failed to create tenant");
    }
    const data = await res.json();
    setTenants((prev) => [data.tenant, ...prev]);
  };

  return (
    <div className="min-h-screen bg-[#080C12] p-6">
      <div className="max-w-7xl mx-auto space-y-8">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-white">Tenant Management</h1>
            <p className="text-[#7088A8] mt-1">Manage YardOS white-label tenants</p>
          </div>
          <button onClick={() => setShowCreate(true)}
            className="bg-[#E8922A] text-black font-semibold px-5 py-2.5 rounded-xl hover:bg-amber-400 transition">
            + Create New Tenant
          </button>
        </div>

        <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b border-[#1e2d42]">
                {["Tenant", "Plan", "Country", "MRR", "Active Bookings", "Users", "Status", ""].map((h) => (
                  <th key={h} className={`p-4 text-[#7088A8] font-medium ${h === "" ? "" : "text-left"}`}>{h}</th>
                ))}
              </tr>
            </thead>
            <tbody>
              {loading ? (
                [...Array(5)].map((_, i) => (
                  <tr key={i} className="border-b border-[#1e2d42]">
                    {[...Array(8)].map((__, j) => (
                      <td key={j} className="p-4"><div className="h-4 bg-[#1e2d42] rounded animate-pulse" /></td>
                    ))}
                  </tr>
                ))
              ) : tenants.length === 0 ? (
                <tr><td colSpan={8} className="p-8 text-center text-[#7088A8]">No tenants yet.</td></tr>
              ) : (
                tenants.map((t) => (
                  <tr key={t.id} className="border-b border-[#1e2d42] hover:bg-[#1e2d42]/30 transition cursor-pointer" onClick={() => router.push(`/dashboard/superadmin/tenants/${t.id}`)}>
                    <td className="p-4">
                      <p className="text-white font-medium">{t.name}</p>
                      <p className="text-xs text-[#7088A8]">{t.slug}</p>
                    </td>
                    <td className="p-4">
                      <span className={`text-xs px-2 py-0.5 rounded-full font-medium capitalize ${PLAN_STYLES[t.plan]}`}>{t.plan}</span>
                    </td>
                    <td className="p-4 text-[#7088A8]">{t.country}</td>
                    <td className="p-4 text-white">{t.currency} {t.mrr.toLocaleString()}</td>
                    <td className="p-4 text-center text-white">{t.activeBookings}</td>
                    <td className="p-4 text-center text-white">{t.userCount.toLocaleString()}</td>
                    <td className="p-4">
                      <span className={`text-xs px-2 py-0.5 rounded-full font-medium capitalize ${STATUS_STYLES[t.status]}`}>{t.status}</span>
                    </td>
                    <td className="p-4 text-right">
                      <button className="text-xs text-[#7088A8] hover:text-white transition" onClick={(e) => { e.stopPropagation(); router.push(`/dashboard/superadmin/tenants/${t.id}`); }}>
                        View →
                      </button>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>

      {showCreate && (
        <CreateTenantModal onClose={() => setShowCreate(false)} onCreate={handleCreate} />
      )}
    </div>
  );
}
