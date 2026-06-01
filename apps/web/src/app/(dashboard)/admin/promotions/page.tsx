"use client";

interface Promotion {
  id: string;
  asset: string;
  type: "Featured" | "Sponsored";
  startsAt: string;
  endsAt: string;
  impressions: number;
  clicks: number;
  conversions: number;
  status: "Active" | "Pending Payment" | "Expired";
}

const PROMOTIONS: Promotion[] = [
  { id: "p1", asset: "Toyota Land Cruiser V8 2020", type: "Featured", startsAt: "2024-06-01", endsAt: "2024-06-30", impressions: 4200, clicks: 342, conversions: 18, status: "Active" },
  { id: "p2", asset: "CAT 320 Excavator", type: "Sponsored", startsAt: "2024-06-15", endsAt: "2024-07-15", impressions: 1800, clicks: 120, conversions: 7, status: "Active" },
  { id: "p3", asset: "Mercedes-Benz GLE 2021", type: "Featured", startsAt: "2024-07-01", endsAt: "2024-07-31", impressions: 0, clicks: 0, conversions: 0, status: "Pending Payment" },
  { id: "p4", asset: "Isuzu NQR Bus", type: "Sponsored", startsAt: "2024-07-01", endsAt: "2024-07-31", impressions: 0, clicks: 0, conversions: 0, status: "Pending Payment" },
];

const PRICING = [
  { type: "Featured Listing", duration: "7 days", priceKES: 2000, description: "Top slot in search results" },
  { type: "Featured Listing", duration: "30 days", priceKES: 6500, description: "Top slot in search results" },
  { type: "Sponsored", duration: "7 days", priceKES: 1000, description: "Badge + boosted visibility" },
  { type: "Sponsored", duration: "30 days", priceKES: 3500, description: "Badge + boosted visibility" },
];

const STATUS_COLORS: Record<Promotion["status"], string> = {
  Active: "bg-green-100 text-green-800",
  "Pending Payment": "bg-yellow-100 text-yellow-800",
  Expired: "bg-gray-100 text-gray-500",
};

export default function AdminPromotionsPage() {
  const active = PROMOTIONS.filter((p) => p.status === "Active");
  const pending = PROMOTIONS.filter((p) => p.status === "Pending Payment");

  return (
    <div className="p-6 space-y-8">
      <h1 className="text-2xl font-bold text-gray-900">Promotions Management</h1>

      {/* Active Promotions */}
      <section>
        <h2 className="text-lg font-semibold text-gray-900 mb-3">Active Promotions</h2>
        <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
          <table className="w-full text-sm">
            <thead className="bg-gray-50 border-b">
              <tr>
                <th className="p-3 text-left font-semibold text-gray-700">Asset</th>
                <th className="p-3 text-left font-semibold text-gray-700">Type</th>
                <th className="p-3 text-left font-semibold text-gray-700">Period</th>
                <th className="p-3 text-right font-semibold text-gray-700">Impressions</th>
                <th className="p-3 text-right font-semibold text-gray-700">Clicks</th>
                <th className="p-3 text-right font-semibold text-gray-700">Conversions</th>
                <th className="p-3 text-left font-semibold text-gray-700">Status</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {active.map((p) => (
                <tr key={p.id} className="hover:bg-gray-50">
                  <td className="p-3 font-medium text-gray-900">{p.asset}</td>
                  <td className="p-3">
                    <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${
                      p.type === "Featured" ? "bg-amber-100 text-amber-800" : "bg-blue-100 text-blue-800"
                    }`}>{p.type}</span>
                  </td>
                  <td className="p-3 text-xs text-gray-500">{p.startsAt} → {p.endsAt}</td>
                  <td className="p-3 text-right">{p.impressions.toLocaleString()}</td>
                  <td className="p-3 text-right">{p.clicks.toLocaleString()}</td>
                  <td className="p-3 text-right font-medium text-green-700">{p.conversions}</td>
                  <td className="p-3">
                    <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${STATUS_COLORS[p.status]}`}>{p.status}</span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>

      {/* Pending Payment */}
      <section>
        <h2 className="text-lg font-semibold text-gray-900 mb-3">Pending Payment ({pending.length})</h2>
        <div className="space-y-3">
          {pending.map((p) => (
            <div key={p.id} className="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-center justify-between">
              <div>
                <p className="font-medium text-gray-900">{p.asset}</p>
                <p className="text-sm text-gray-500">{p.type} · {p.startsAt} → {p.endsAt}</p>
              </div>
              <div className="flex gap-2">
                <button className="px-3 py-1.5 text-xs bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">Approve</button>
                <button className="px-3 py-1.5 text-xs border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">Cancel</button>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* Pricing Table */}
      <section>
        <h2 className="text-lg font-semibold text-gray-900 mb-3">Promotion Pricing</h2>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {PRICING.map((plan, i) => (
            <div key={i} className="bg-white rounded-xl border border-gray-200 p-4">
              <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${
                plan.type === "Featured Listing" ? "bg-amber-100 text-amber-800" : "bg-blue-100 text-blue-800"
              }`}>{plan.type}</span>
              <p className="text-xl font-bold text-gray-900 mt-3">KES {plan.priceKES.toLocaleString()}</p>
              <p className="text-sm text-gray-500">{plan.duration}</p>
              <p className="text-xs text-gray-400 mt-1">{plan.description}</p>
            </div>
          ))}
        </div>
      </section>
    </div>
  );
}
