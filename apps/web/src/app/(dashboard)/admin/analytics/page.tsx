"use client";

import KpiCard from "@/components/analytics/KpiCard";
import FunnelChart from "@/components/analytics/FunnelChart";

const COUNTY_DATA = [
  { county: "Nairobi", bookings: 412, gmvKES: 8240000 },
  { county: "Mombasa", bookings: 178, gmvKES: 3560000 },
  { county: "Kisumu", bookings: 89, gmvKES: 1780000 },
  { county: "Nakuru", bookings: 67, gmvKES: 1340000 },
  { county: "Eldoret", bookings: 54, gmvKES: 1080000 },
];

const TOP_YARDS = [
  { rank: 1, name: "Premium Auto Rentals Nairobi", revenueKES: 1240000, bookings: 48 },
  { rank: 2, name: "Safari Drive Kenya", revenueKES: 980000, bookings: 37 },
  { rank: 3, name: "Mombasa Fleet Solutions", revenueKES: 760000, bookings: 29 },
  { rank: 4, name: "Kisumu Car Hire", revenueKES: 620000, bookings: 24 },
  { rank: 5, name: "Nakuru Express Rentals", revenueKES: 540000, bookings: 20 },
];

const WEEKLY_SIGNUPS = [
  { week: "W1", value: 28 },
  { week: "W2", value: 35 },
  { week: "W3", value: 41 },
  { week: "W4", value: 38 },
  { week: "W5", value: 52 },
  { week: "W6", value: 48 },
  { week: "W7", value: 61 },
  { week: "W8", value: 73 },
  { week: "W9", value: 65 },
  { week: "W10", value: 82 },
  { week: "W11", value: 78 },
  { week: "W12", value: 91 },
];

const FUNNEL_STEPS = [
  { label: "Listings Viewed", value: 14820, color: "bg-blue-500" },
  { label: "Calendar Opened", value: 6340, color: "bg-indigo-500" },
  { label: "Booking Started", value: 2180, color: "bg-amber-500" },
  { label: "Payment Completed", value: 1560, color: "bg-green-500" },
];

function MiniBarChart({ data }: { data: { week: string; value: number }[] }) {
  const max = Math.max(...data.map((d) => d.value));
  return (
    <div className="flex items-end gap-1 h-24">
      {data.map((d) => (
        <div key={d.week} className="flex-1 flex flex-col items-center gap-1">
          <div
            className="w-full bg-amber-500 rounded-sm opacity-80"
            style={{ height: `${(d.value / max) * 80}px` }}
          />
          <span className="text-xs text-gray-400 rotate-45 origin-left" style={{ fontSize: 8 }}>{d.week}</span>
        </div>
      ))}
    </div>
  );
}

export default function AdminAnalyticsPage() {
  return (
    <div className="p-6 space-y-6">
      <h1 className="text-2xl font-bold text-gray-900">Platform Analytics</h1>

      {/* Live Counter KPIs */}
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
        <KpiCard title="Bookings Today" value="47" change={12.8} />
        <KpiCard title="GMV Today" value="KES 2.3M" change={8.4} />
        <KpiCard title="New Signups" value="23" change={-3.2} />
        <KpiCard title="Active Rentals" value="189" change={5.1} />
      </div>

      {/* Conversion Funnel */}
      <div className="bg-white rounded-xl border border-gray-200 p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Conversion Funnel</h2>
        <FunnelChart steps={FUNNEL_STEPS} />
      </div>

      <div className="grid grid-cols-2 gap-6">
        {/* Geographic Breakdown */}
        <div className="bg-white rounded-xl border border-gray-200 p-6">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">County Breakdown</h2>
          <table className="w-full text-sm">
            <thead className="border-b">
              <tr>
                <th className="pb-2 text-left font-semibold text-gray-700">County</th>
                <th className="pb-2 text-right font-semibold text-gray-700">Bookings</th>
                <th className="pb-2 text-right font-semibold text-gray-700">GMV (KES)</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-50">
              {COUNTY_DATA.map((c) => (
                <tr key={c.county}>
                  <td className="py-2 font-medium text-gray-900">{c.county}</td>
                  <td className="py-2 text-right text-gray-600">{c.bookings}</td>
                  <td className="py-2 text-right font-medium">{(c.gmvKES / 1000).toFixed(0)}k</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {/* Dispute Metrics */}
        <div className="space-y-4">
          <div className="bg-white rounded-xl border border-gray-200 p-6">
            <h2 className="text-lg font-semibold text-gray-900 mb-2">Dispute Metrics</h2>
            <div className="grid grid-cols-2 gap-4">
              <div>
                <p className="text-xs text-gray-500">Dispute Rate</p>
                <p className="text-2xl font-bold text-gray-900">1.8%</p>
              </div>
              <div>
                <p className="text-xs text-gray-500">Avg Resolution</p>
                <p className="text-2xl font-bold text-gray-900">3.2 days</p>
              </div>
            </div>
          </div>
          <div className="bg-white rounded-xl border border-gray-200 p-6">
            <h2 className="text-lg font-semibold text-gray-900 mb-3">Weekly Signups (Last 12 Weeks)</h2>
            <MiniBarChart data={WEEKLY_SIGNUPS} />
          </div>
        </div>
      </div>

      {/* Top 10 Yards */}
      <div className="bg-white rounded-xl border border-gray-200 p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Top Yards by Revenue</h2>
        <div className="space-y-3">
          {TOP_YARDS.map((y) => (
            <div key={y.rank} className="flex items-center gap-3">
              <span className="w-6 h-6 rounded-full bg-amber-100 text-amber-800 text-xs font-bold flex items-center justify-center">{y.rank}</span>
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-900">{y.name}</p>
                <p className="text-xs text-gray-500">{y.bookings} bookings</p>
              </div>
              <span className="font-semibold text-sm">KES {(y.revenueKES / 1000).toFixed(0)}k</span>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
