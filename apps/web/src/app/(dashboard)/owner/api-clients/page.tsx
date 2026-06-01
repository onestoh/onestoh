"use client";

import { useEffect, useState } from "react";

interface ApiClient {
  id: string;
  name: string;
  clientId: string;
  scopes: string[];
  lastUsed: string | null;
  requestCount: number;
  status: "active" | "suspended";
  createdAt: string;
}

interface WebhookDelivery {
  id: string;
  event: string;
  status: "delivered" | "failed";
  timestamp: string;
  responseCode?: number;
}

const ALL_SCOPES = [
  { value: "listings:read", label: "Read Listings", desc: "List and retrieve listing details" },
  { value: "bookings:write", label: "Create Bookings", desc: "Create and manage bookings" },
  { value: "availability:read", label: "Read Availability", desc: "Check listing availability calendars" },
  { value: "analytics:read", label: "Read Analytics", desc: "Access revenue and performance data" },
];

function ScopeChip({ scope }: { scope: string }) {
  return (
    <span className="text-xs px-2 py-0.5 rounded-full bg-[#1e2d42] text-[#7088A8]">{scope}</span>
  );
}

function CreateClientModal({ onClose, onCreate }: { onClose: () => void; onCreate: (name: string, scopes: string[]) => Promise<{ clientId: string; clientSecret: string }>; }) {
  const [name, setName] = useState("");
  const [scopes, setScopes] = useState<string[]>(["listings:read"]);
  const [loading, setLoading] = useState(false);
  const [created, setCreated] = useState<{ clientId: string; clientSecret: string } | null>(null);
  const [copied, setCopied] = useState(false);

  const toggleScope = (v: string) =>
    setScopes((prev) => prev.includes(v) ? prev.filter((s) => s !== v) : [...prev, v]);

  const handleCreate = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    const result = await onCreate(name, scopes);
    setCreated(result);
    setLoading(false);
  };

  const copySecret = () => {
    if (created?.clientSecret) {
      navigator.clipboard.writeText(created.clientSecret);
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    }
  };

  return (
    <div className="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
      <div className="bg-[#141D2B] rounded-2xl border border-[#1e2d42] w-full max-w-md">
        <div className="flex items-center justify-between p-5 border-b border-[#1e2d42]">
          <h2 className="font-semibold text-white">{created ? "Client Created" : "Create API Client"}</h2>
          <button onClick={onClose} className="text-[#7088A8] hover:text-white text-xl">×</button>
        </div>
        <div className="p-5">
          {!created ? (
            <form onSubmit={handleCreate} className="space-y-4">
              <div>
                <label className="block text-sm text-[#7088A8] mb-1.5">Client Name</label>
                <input
                  className="w-full bg-[#080C12] border border-[#1e2d42] rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-[#E8922A]"
                  value={name} onChange={(e) => setName(e.target.value)}
                  placeholder="e.g. My Fleet App" required
                />
              </div>
              <div>
                <label className="block text-sm text-[#7088A8] mb-2">Scopes</label>
                <div className="space-y-2">
                  {ALL_SCOPES.map((s) => (
                    <label key={s.value} className={`flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition ${
                      scopes.includes(s.value) ? "border-[#E8922A] bg-[#E8922A]/5" : "border-[#1e2d42] hover:border-[#7088A8]"
                    }`}>
                      <input type="checkbox" checked={scopes.includes(s.value)} onChange={() => toggleScope(s.value)}
                        className="mt-0.5 accent-[#E8922A]" />
                      <div>
                        <p className="text-sm text-white">{s.label}</p>
                        <p className="text-xs text-[#7088A8]">{s.desc}</p>
                      </div>
                    </label>
                  ))}
                </div>
              </div>
              <button type="submit" disabled={loading || scopes.length === 0}
                className="w-full bg-[#E8922A] text-black font-semibold py-3 rounded-xl hover:bg-amber-400 transition disabled:opacity-50">
                {loading ? "Creating…" : "Create Client"}
              </button>
            </form>
          ) : (
            <div className="space-y-4">
              <div className="bg-amber-500/10 border border-amber-500/30 rounded-xl p-4">
                <p className="text-amber-400 text-sm font-medium mb-1">⚠️ Save this now — it won’t be shown again</p>
                <p className="text-xs text-[#7088A8]">Your client secret is shown once. Copy and store it securely.</p>
              </div>
              <div>
                <p className="text-xs text-[#7088A8] mb-1">Client ID</p>
                <p className="font-mono text-sm text-white bg-[#080C12] rounded-xl px-4 py-3 break-all">{created.clientId}</p>
              </div>
              <div>
                <p className="text-xs text-[#7088A8] mb-1">Client Secret</p>
                <div className="flex gap-2">
                  <p className="font-mono text-sm text-[#E8922A] bg-[#080C12] rounded-xl px-4 py-3 flex-1 break-all">{created.clientSecret}</p>
                  <button onClick={copySecret}
                    className="bg-[#1e2d42] text-white text-xs px-3 rounded-xl hover:bg-[#243447] transition shrink-0">
                    {copied ? "✓" : "Copy"}
                  </button>
                </div>
              </div>
              <button onClick={onClose}
                className="w-full border border-[#1e2d42] text-white font-semibold py-3 rounded-xl hover:bg-[#1e2d42] transition">
                Done
              </button>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

function WebhookDeliveriesTable({ clientId }: { clientId: string }) {
  const [deliveries, setDeliveries] = useState<WebhookDelivery[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const load = async () => {
      try {
        const res = await fetch(`/api/v1/api-clients/${clientId}/webhook-deliveries`);
        const data = await res.json();
        setDeliveries(data.deliveries ?? []);
      } catch { /* ignore */ } finally { setLoading(false); }
    };
    load();
  }, [clientId]);

  const retry = async (deliveryId: string) => {
    await fetch(`/api/v1/api-clients/${clientId}/webhook-deliveries/${deliveryId}/retry`, { method: "POST" });
  };

  return (
    <div className="mt-4">
      <p className="text-sm text-[#7088A8] mb-2">Webhook Deliveries</p>
      {loading ? (
        <div className="space-y-1">{[...Array(3)].map((_, i) => <div key={i} className="h-8 bg-[#1e2d42] rounded animate-pulse" />)}</div>
      ) : deliveries.length === 0 ? (
        <p className="text-xs text-[#7088A8]">No webhook deliveries yet.</p>
      ) : (
        <div className="space-y-1">
          {deliveries.slice(0, 5).map((d) => (
            <div key={d.id} className="flex items-center justify-between py-1.5 border-b border-[#1e2d42]">
              <code className="text-xs text-[#E8922A]">{d.event}</code>
              <div className="flex items-center gap-2">
                <span className={`text-xs px-2 py-0.5 rounded-full ${
                  d.status === "delivered" ? "bg-[#2ECC8A]/20 text-[#2ECC8A]" : "bg-[#E05252]/20 text-[#E05252]"
                }`}>{d.status}</span>
                <span className="text-xs text-[#7088A8]">{new Date(d.timestamp).toLocaleTimeString()}</span>
                {d.status === "failed" && (
                  <button onClick={() => retry(d.id)} className="text-xs text-amber-400 hover:underline">Retry</button>
                )}
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

export default function ApiClientsPage() {
  const [clients, setClients] = useState<ApiClient[]>([]);
  const [loading, setLoading] = useState(true);
  const [showCreate, setShowCreate] = useState(false);
  const [expandedId, setExpandedId] = useState<string | null>(null);
  const [rotateConfirmId, setRotateConfirmId] = useState<string | null>(null);

  useEffect(() => {
    const load = async () => {
      try {
        const res = await fetch("/api/v1/api-clients");
        const data = await res.json();
        setClients(data.clients ?? []);
      } catch { /* ignore */ } finally { setLoading(false); }
    };
    load();
  }, []);

  const handleCreate = async (name: string, scopes: string[]) => {
    const res = await fetch("/api/v1/api-clients", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ name, scopes }),
    });
    const data = await res.json();
    setClients((prev) => [data.client, ...prev]);
    return { clientId: data.client.clientId, clientSecret: data.clientSecret };
  };

  const handleRotate = async (id: string) => {
    await fetch(`/api/v1/api-clients/${id}/rotate-secret`, { method: "POST" });
    setRotateConfirmId(null);
  };

  const maskId = (id: string) => id.slice(0, 8) + "*".repeat(Math.max(0, id.length - 12)) + id.slice(-4);

  return (
    <div className="min-h-screen bg-[#080C12] p-6">
      <div className="max-w-5xl mx-auto space-y-8">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-white">Your API Clients</h1>
            <p className="text-[#7088A8] mt-1">Manage API access to your TheOnlineYard account</p>
          </div>
          <button onClick={() => setShowCreate(true)}
            className="bg-[#E8922A] text-black font-semibold px-5 py-2.5 rounded-xl hover:bg-amber-400 transition">
            + Create New Client
          </button>
        </div>

        {loading ? (
          <div className="space-y-3">{[...Array(3)].map((_, i) => <div key={i} className="h-20 bg-[#141D2B] rounded-xl animate-pulse" />)}</div>
        ) : clients.length === 0 ? (
          <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-12 text-center">
            <p className="text-4xl mb-4">🔑</p>
            <h2 className="text-white font-semibold">No API Clients Yet</h2>
            <p className="text-[#7088A8] text-sm mt-2 mb-6">Create an API client to start integrating your apps with TheOnlineYard.</p>
            <button onClick={() => setShowCreate(true)}
              className="bg-[#E8922A] text-black font-semibold px-6 py-3 rounded-xl hover:bg-amber-400 transition">
              Create First Client
            </button>
          </div>
        ) : (
          <div className="space-y-3">
            {clients.map((c) => (
              <div key={c.id} className="bg-[#141D2B] rounded-xl border border-[#1e2d42] overflow-hidden">
                <div className="flex items-center justify-between p-5 cursor-pointer" onClick={() => setExpandedId(expandedId === c.id ? null : c.id)}>
                  <div className="flex items-center gap-4">
                    <div className="w-9 h-9 rounded-xl bg-[#1e2d42] flex items-center justify-center">🔑</div>
                    <div>
                      <div className="flex items-center gap-2">
                        <p className="text-white font-medium">{c.name}</p>
                        <span className={`text-xs px-2 py-0.5 rounded-full ${
                          c.status === "active" ? "bg-[#2ECC8A]/20 text-[#2ECC8A]" : "bg-[#E05252]/20 text-[#E05252]"
                        }`}>{c.status}</span>
                      </div>
                      <p className="text-xs text-[#7088A8] font-mono mt-0.5">{maskId(c.clientId)}</p>
                    </div>
                  </div>
                  <div className="text-right">
                    <p className="text-sm text-white">{c.requestCount.toLocaleString()} requests</p>
                    <p className="text-xs text-[#7088A8]">{c.lastUsed ? `Last used ${new Date(c.lastUsed).toLocaleDateString()}` : "Never used"}</p>
                  </div>
                </div>

                {expandedId === c.id && (
                  <div className="px-5 pb-5 border-t border-[#1e2d42] pt-4 space-y-4">
                    <div>
                      <p className="text-xs text-[#7088A8] mb-2">Scopes</p>
                      <div className="flex flex-wrap gap-1.5">
                        {c.scopes.map((s) => <ScopeChip key={s} scope={s} />)}
                      </div>
                    </div>

                    {rotateConfirmId === c.id ? (
                      <div className="bg-[#E05252]/10 border border-[#E05252]/30 rounded-xl p-4">
                        <p className="text-sm text-white mb-3">This will invalidate the current secret. Are you sure?</p>
                        <div className="flex gap-2">
                          <button onClick={() => handleRotate(c.id)}
                            className="bg-[#E05252] text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-red-500 transition">
                            Yes, Rotate Secret
                          </button>
                          <button onClick={() => setRotateConfirmId(null)}
                            className="text-[#7088A8] text-sm px-4 py-2 rounded-lg hover:text-white transition">Cancel</button>
                        </div>
                      </div>
                    ) : (
                      <button onClick={() => setRotateConfirmId(c.id)}
                        className="text-sm text-amber-400 hover:underline">
                        Rotate Secret
                      </button>
                    )}

                    <WebhookDeliveriesTable clientId={c.id} />
                  </div>
                )}
              </div>
            ))}
          </div>
        )}
      </div>

      {showCreate && (
        <CreateClientModal
          onClose={() => setShowCreate(false)}
          onCreate={handleCreate}
        />
      )}
    </div>
  );
}
