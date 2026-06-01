"use client";

import KpiCard from "@/components/analytics/KpiCard";
import RevenueChart from "@/components/fleet/RevenueChart";

const MONTHLY_REVENUE = [
  { month: "Jul", value: 180000 },
  { month: "Aug", value: 220000 },
  { month: "Sep", value: 195000 },
  { month: "Oct", value: 260000 },
  { month: "Nov", value: 310000 },
  { month: "Dec", value: 290000 },
  { month: "Jan", value: 245000 },
  { month: "Feb", value: 270000 },
  { month: "Mar", value: 330000 },
  { month: "Apr", value: 305000 },
  { month: "May", value: 350000 },
  { month: "Jun", value: 380000 },
];

const TOP_ASSETS = [
  { rank: 1, name: "Komatsu PC200 Excavator", revenueKES: 420000, bookings: 12, utilisation: 91 },
  { rank: 2, name: "Isuzu NQR Bus", revenueKES: 310000, bookings: 18, utilisation: 62 },
  { rank: 3, name: "Toyota Land Cruiser V8", revenueKES: 245000, bookings: 24, utilisation: 78 },
  { rank: 4, name: "CAT 320 Excavator", revenueKES: 180000, bookings: 9, utilisation: 45 },
];

const IDLE_ASSETS = [
  { name: "Mercedes Sprinter", idleDays: 14, lastRented: "2024-05-18" },
];

const REFERRAL_SOURCES = [
  { source: "Direct", pct: 45, colorClass: "bg-amber-500" },
  { source: "Broker — Safari Rentals", pct: 28, colorClass: "bg-blue-500" },
  { source: "Broker — NairobiCars", pct: 17, colorClass: "bg-green-500" },
  { source: "Other", pct: 10, colorClass: "bg-gray-400" },
];

export default function OwnerAnalyticsPage() {
  return (
    <div className="p-6 space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-gray-900">Analytics</h1>
        <div className="flex gap-3">
          <button className="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Export PDF</button>
          <button className="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Export Excel</button>
        </div>
      </div>

      {/* KPI Cards */}
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
        <KpiCard title="Revenue MTD" value="KES 380,000" change={8.6} />
        <KpiCard title="Total Bookings" value="63" change={12.3} />
        <KpiCard title="Fleet Utilisation" value="67.2%" change={-2.1} />
        <KpiCard title="Repeat Renter Rate" value="38%" change={5.4} />
      </div>

      {/* Revenue Chart */}
      <div className="bg-white rounded-xl border border-gray-200 p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Revenue Trend (Last 12 Months)</h2>
        <RevenueChart data={MONTHLY_REVENUE} height={220} />
      </div>

      <div className="grid grid-cols-2 gap-6">
        {/* Top Performing Assets */}
        <div className="bg-white rounded-xl border border-gray-200 p-6">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Top Performing Assets</h2>
          <div className="space-y-3">
            {TOP_ASSETS.map((a) => (
              <div key={a.rank} className="flex items-center gap-3">
                <span className="w-6 h-6 rounded-full bg-amber-100 text-amber-800 text-xs font-bold flex items-center justify-center">
                  {a.rank}
                </span>
                <div className="flex-1">
                  <p className="text-sm font-medium text-gray-900">{a.name}</p>
                  <p className="text-xs text-gray-500">{a.bookings} bookings · {a.utilisation}% utilised</p>
                </div>
                <span className="text-sm font-semibold text-gray-900">KES {(a.revenueKES / 1000).toFixed(0)}k</span>
              </div>
            ))}
          </div>
        </div>

        {/* Referral Sources */}
        <div className="bg-white rounded-xl border border-gray-200 p-6">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Booking Sources</h2>
          <div className="space-y-3">
            {REFERRAL_SOURCES.map((s) => (
              <div key={s.source}>
                <div className="flex justify-between text-sm mb-1">
                  <span className="text-gray-700">{s.source}</span>
                  <span className="font-medium">{s.pct}%</span>
                </div>
                <div className="h-2 bg-gray-100 rounded-full overflow-hidden">
                  <div className={`h-full ${s.colorClass} rounded-full`} style={{ width: `${s.pct}%` }} />
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Idle Assets Warning */}
      {IDLE_ASSETS.length > 0 && (
        <div className="bg-red-50 border border-red-200 rounded-xl p-6">
          <h2 className="text-lg font-semibold text-red-800 mb-3">⚠ Idle Assets</h2>
          <div className="space-y-2">
            {IDLE_ASSETS.map((a) => (
              <div key={a.name} className="flex items-center justify-between p-3 bg-white rounded-lg border border-red-100">
                <div>
                  <p className="font-medium text-gray-900 text-sm">{a.name}</p>
                  <p className="text-xs text-gray-500">Last rented: {a.lastRented}</p>
                </div>
                <span className="text-sm font-semibold text-red-700">{a.idleDays} days idle</span>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}
