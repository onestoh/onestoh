"use client";

import { useState } from "react";
import { useParams } from "next/navigation";
import RevenueChart from "@/components/fleet/RevenueChart";

type Tab = "Overview" | "Bookings" | "Maintenance" | "Analytics";

const MAINTENANCE_LOG = [
  { id: "m1", date: "2024-05-10", type: "Oil Change", cost: 8500, technician: "James Mwangi", notes: "Engine oil + filter replaced" },
  { id: "m2", date: "2024-03-22", type: "Tyre Replacement", cost: 32000, technician: "Auto Centre Nairobi", notes: "2 front tyres replaced" },
  { id: "m3", date: "2024-01-15", type: "Service", cost: 15000, technician: "Toyota Kenya", notes: "60,000km major service" },
];

const SCHEDULES = [
  { id: "s1", type: "Oil Change", dueDate: "2024-08-10", dueSoon: true },
  { id: "s2", type: "Major Service", dueDate: "2025-01-15", dueSoon: false },
  { id: "s3", type: "Insurance Renewal", dueDate: "2024-07-30", dueSoon: true },
];

const UPCOMING_BOOKING = {
  client: "Grace Muthoni",
  from: "2024-07-15",
  to: "2024-07-18",
  valueKES: 54000,
};

const MONTHLY_REVENUE = [
  { month: "Jan", value: 42000 },
  { month: "Feb", value: 38000 },
  { month: "Mar", value: 61000 },
  { month: "Apr", value: 55000 },
  { month: "May", value: 70000 },
  { month: "Jun", value: 65000 },
];

function DonutChart({ value }: { value: number }) {
  const r = 40;
  const circ = 2 * Math.PI * r;
  const offset = circ - (value / 100) * circ;
  const color = value >= 70 ? "#16a34a" : value >= 40 ? "#d97706" : "#dc2626";
  return (
    <svg width="100" height="100" viewBox="0 0 100 100">
      <circle cx="50" cy="50" r={r} fill="none" stroke="#e5e7eb" strokeWidth="12" />
      <circle
        cx="50" cy="50" r={r} fill="none" stroke={color} strokeWidth="12"
        strokeDasharray={circ} strokeDashoffset={offset}
        strokeLinecap="round" transform="rotate(-90 50 50)"
      />
      <text x="50" y="54" textAnchor="middle" fontSize="16" fontWeight="bold" fill="#111827">
        {value}%
      </text>
    </svg>
  );
}

export default function AssetDetailPage() {
  const params = useParams();
  const assetId = params.assetId as string;
  const [activeTab, setActiveTab] = useState<Tab>("Overview");
  const [showLogModal, setShowLogModal] = useState(false);

  const TABS: Tab[] = ["Overview", "Bookings", "Maintenance", "Analytics"];

  return (
    <div className="p-6 space-y-6">
      {/* Asset Header */}
      <div className="bg-white rounded-xl border border-gray-200 p-6">
        <div className="flex gap-6">
          <div className="w-32 h-24 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-4xl">
            🚗
          </div>
          <div className="flex-1">
            <div className="flex items-start justify-between">
              <div>
                <h1 className="text-xl font-bold text-gray-900">Toyota Land Cruiser V8</h1>
                <p className="text-sm text-gray-500">2021 · KDJ 123A · Asset #{assetId}</p>
              </div>
              <span className="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">Active</span>
            </div>
            <div className="mt-4 flex items-center gap-6">
              <div>
                <p className="text-xs text-gray-500 mb-1">Utilisation Rate</p>
                <DonutChart value={78} />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <p className="text-xs text-gray-500">Revenue MTD</p>
                  <p className="text-lg font-bold text-gray-900">KES 70,000</p>
                </div>
                <div>
                  <p className="text-xs text-gray-500">Total Bookings</p>
                  <p className="text-lg font-bold text-gray-900">24</p>
                </div>
                <div>
                  <p className="text-xs text-gray-500">Avg Rating</p>
                  <p className="text-lg font-bold text-gray-900">4.8 ⭐</p>
                </div>
                <div>
                  <p className="text-xs text-gray-500">Days on Platform</p>
                  <p className="text-lg font-bold text-gray-900">180</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Tab Navigation */}
      <div className="flex gap-1 bg-gray-100 p-1 rounded-lg w-fit">
        {TABS.map((tab) => (
          <button
            key={tab}
            onClick={() => setActiveTab(tab)}
            className={`px-4 py-2 text-sm rounded-md transition-colors ${
              activeTab === tab ? "bg-white shadow text-gray-900 font-medium" : "text-gray-500 hover:text-gray-700"
            }`}
          >
            {tab}
          </button>
        ))}
      </div>

      {/* Tab Content */}
      {activeTab === "Overview" && (
        <div className="grid grid-cols-2 gap-6">
          <div className="bg-white rounded-xl border border-gray-200 p-6">
            <h3 className="font-semibold text-gray-900 mb-4">Next Booking</h3>
            <div className="space-y-2">
              <p className="text-sm"><span className="text-gray-500">Client:</span> <span className="font-medium">{UPCOMING_BOOKING.client}</span></p>
              <p className="text-sm"><span className="text-gray-500">From:</span> <span className="font-medium">{UPCOMING_BOOKING.from}</span></p>
              <p className="text-sm"><span className="text-gray-500">To:</span> <span className="font-medium">{UPCOMING_BOOKING.to}</span></p>
              <p className="text-sm"><span className="text-gray-500">Value:</span> <span className="font-medium text-green-700">KES {UPCOMING_BOOKING.valueKES.toLocaleString()}</span></p>
            </div>
          </div>
          <div className="bg-white rounded-xl border border-gray-200 p-6">
            <h3 className="font-semibold text-gray-900 mb-4">Due Soon</h3>
            <div className="space-y-3">
              {SCHEDULES.filter((s) => s.dueSoon).map((s) => (
                <div key={s.id} className="flex items-center justify-between p-2 bg-amber-50 border border-amber-100 rounded-lg">
                  <span className="text-sm font-medium text-amber-800">{s.type}</span>
                  <span className="text-xs text-amber-600">{s.dueDate}</span>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}

      {activeTab === "Maintenance" && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-lg font-semibold text-gray-900">Maintenance Log</h2>
            <button
              onClick={() => setShowLogModal(true)}
              className="px-4 py-2 bg-amber-500 text-white text-sm rounded-lg hover:bg-amber-600 font-medium"
            >
              + Log Service
            </button>
          </div>
          <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table className="w-full text-sm">
              <thead className="bg-gray-50 border-b">
                <tr>
                  <th className="p-3 text-left font-semibold text-gray-700">Date</th>
                  <th className="p-3 text-left font-semibold text-gray-700">Type</th>
                  <th className="p-3 text-left font-semibold text-gray-700">Technician</th>
                  <th className="p-3 text-left font-semibold text-gray-700">Notes</th>
                  <th className="p-3 text-right font-semibold text-gray-700">Cost (KES)</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100">
                {MAINTENANCE_LOG.map((log) => (
                  <tr key={log.id} className="hover:bg-gray-50">
                    <td className="p-3 text-gray-600">{log.date}</td>
                    <td className="p-3 font-medium text-gray-900">{log.type}</td>
                    <td className="p-3 text-gray-600">{log.technician}</td>
                    <td className="p-3 text-gray-500 text-xs">{log.notes}</td>
                    <td className="p-3 text-right font-medium">{log.cost.toLocaleString()}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
          <div>
            <h3 className="text-lg font-semibold text-gray-900 mb-3">Maintenance Schedules</h3>
            <div className="space-y-2">
              {SCHEDULES.map((s) => (
                <div
                  key={s.id}
                  className={`flex items-center justify-between p-3 rounded-lg border ${
                    s.dueSoon ? "bg-amber-50 border-amber-200" : "bg-white border-gray-200"
                  }`}
                >
                  <span className={`font-medium text-sm ${s.dueSoon ? "text-amber-800" : "text-gray-900"}`}>{s.type}</span>
                  <div className="flex items-center gap-3">
                    {s.dueSoon && <span className="text-xs px-2 py-0.5 bg-amber-200 text-amber-800 rounded-full">Due Soon</span>}
                    <span className="text-sm text-gray-500">{s.dueDate}</span>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}

      {activeTab === "Analytics" && (
        <div className="space-y-6">
          <div className="bg-white rounded-xl border border-gray-200 p-6">
            <h3 className="font-semibold text-gray-900 mb-4">Monthly Revenue (Last 6 Months)</h3>
            <RevenueChart data={MONTHLY_REVENUE} />
          </div>
        </div>
      )}

      {activeTab === "Bookings" && (
        <div className="bg-white rounded-xl border border-gray-200 p-6">
          <p className="text-gray-500 text-sm">Booking history for this asset will appear here.</p>
        </div>
      )}

      {/* Log Service Modal */}
      {showLogModal && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
          <div className="bg-white rounded-xl p-6 w-full max-w-md mx-4">
            <h3 className="text-lg font-semibold mb-4">Log Service Record</h3>
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Service Type</label>
                <input type="text" className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="e.g. Oil Change" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Cost (KES)</label>
                <input type="number" className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="0" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" rows={3} placeholder="Service notes..." />
              </div>
            </div>
            <div className="flex gap-3 mt-6">
              <button onClick={() => setShowLogModal(false)} className="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm">Cancel</button>
              <button className="flex-1 px-4 py-2 bg-amber-500 text-white rounded-lg text-sm font-medium hover:bg-amber-600">Save Record</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
