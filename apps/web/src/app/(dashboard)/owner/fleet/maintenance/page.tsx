"use client";

import { useState } from "react";
import MaintenanceDueAlert from "@/components/fleet/MaintenanceDueAlert";

interface MaintenanceRecord {
  id: string;
  asset: string;
  type: string;
  date: string;
  costKES: number;
  status: "Completed" | "Scheduled" | "Overdue";
  technician: string;
}

const RECORDS: MaintenanceRecord[] = [
  { id: "r1", asset: "Toyota Land Cruiser V8", type: "Oil Change", date: "2024-05-10", costKES: 8500, status: "Completed", technician: "James Mwangi" },
  { id: "r2", asset: "CAT 320 Excavator", type: "Hydraulic Service", date: "2024-06-01", costKES: 45000, status: "Scheduled", technician: "CAT Kenya" },
  { id: "r3", asset: "Mercedes Sprinter", type: "Brake Pads", date: "2024-06-15", costKES: 12000, status: "Overdue", technician: "TBA" },
  { id: "r4", asset: "Isuzu NQR Bus", type: "Major Service", date: "2024-04-20", costKES: 28000, status: "Completed", technician: "Isuzu Motors Kenya" },
];

const DUE_ALERTS = [
  { id: "d1", asset: "Mercedes Sprinter", type: "Brake Pads", dueDate: "2024-06-15", isOverdue: true },
  { id: "d2", asset: "Toyota Land Cruiser V8", type: "Insurance Renewal", dueDate: "2024-07-30", isOverdue: false },
  { id: "d3", asset: "CAT 320 Excavator", type: "Safety Inspection", dueDate: "2024-07-05", isOverdue: false },
];

const STATUS_COLORS: Record<string, string> = {
  Completed: "bg-green-100 text-green-800",
  Scheduled: "bg-blue-100 text-blue-800",
  Overdue: "bg-red-100 text-red-800",
};

export default function MaintenanceHubPage() {
  const [filterAsset, setFilterAsset] = useState("");
  const [filterType, setFilterType] = useState("");

  const filtered = RECORDS.filter(
    (r) =>
      (filterAsset === "" || r.asset.toLowerCase().includes(filterAsset.toLowerCase())) &&
      (filterType === "" || r.type.toLowerCase().includes(filterType.toLowerCase()))
  );

  const mtdCost = RECORDS.filter((r) => r.status === "Completed" && r.date.startsWith("2024-05")).reduce((sum, r) => sum + r.costKES, 0);
  const ytdCost = RECORDS.filter((r) => r.status === "Completed").reduce((sum, r) => sum + r.costKES, 0);

  return (
    <div className="p-6 space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-gray-900">Maintenance Hub</h1>
        <button className="px-4 py-2 bg-amber-500 text-white text-sm rounded-lg hover:bg-amber-600 font-medium">
          + Schedule Service
        </button>
      </div>

      {/* Due Soon Alerts */}
      <div>
        <h2 className="text-lg font-semibold text-gray-900 mb-3">Alerts</h2>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          {DUE_ALERTS.map((alert) => (
            <MaintenanceDueAlert key={alert.id} {...alert} />
          ))}
        </div>
      </div>

      {/* Cost Summary */}
      <div className="grid grid-cols-2 gap-4">
        <div className="bg-white rounded-xl border border-gray-200 p-5">
          <p className="text-sm text-gray-500">Service Cost MTD</p>
          <p className="text-2xl font-bold text-gray-900 mt-1">KES {mtdCost.toLocaleString()}</p>
        </div>
        <div className="bg-white rounded-xl border border-gray-200 p-5">
          <p className="text-sm text-gray-500">Service Cost YTD</p>
          <p className="text-2xl font-bold text-gray-900 mt-1">KES {ytdCost.toLocaleString()}</p>
        </div>
      </div>

      {/* Filters */}
      <div className="flex gap-3">
        <input
          type="text"
          placeholder="Filter by asset..."
          value={filterAsset}
          onChange={(e) => setFilterAsset(e.target.value)}
          className="border border-gray-300 rounded-lg px-3 py-2 text-sm w-48"
        />
        <input
          type="text"
          placeholder="Filter by type..."
          value={filterType}
          onChange={(e) => setFilterType(e.target.value)}
          className="border border-gray-300 rounded-lg px-3 py-2 text-sm w-48"
        />
      </div>

      {/* Records Table */}
      <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table className="w-full text-sm">
          <thead className="bg-gray-50 border-b">
            <tr>
              <th className="p-3 text-left font-semibold text-gray-700">Asset</th>
              <th className="p-3 text-left font-semibold text-gray-700">Type</th>
              <th className="p-3 text-left font-semibold text-gray-700">Date</th>
              <th className="p-3 text-left font-semibold text-gray-700">Technician</th>
              <th className="p-3 text-left font-semibold text-gray-700">Status</th>
              <th className="p-3 text-right font-semibold text-gray-700">Cost (KES)</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {filtered.map((r) => (
              <tr key={r.id} className="hover:bg-gray-50">
                <td className="p-3 font-medium text-gray-900">{r.asset}</td>
                <td className="p-3 text-gray-600">{r.type}</td>
                <td className="p-3 text-gray-600">{r.date}</td>
                <td className="p-3 text-gray-600">{r.technician}</td>
                <td className="p-3">
                  <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${STATUS_COLORS[r.status]}`}>{r.status}</span>
                </td>
                <td className="p-3 text-right font-medium">{r.costKES.toLocaleString()}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Schedule Management */}
      <div className="bg-white rounded-xl border border-gray-200 p-6">
        <h3 className="font-semibold text-gray-900 mb-4">Schedule Management</h3>
        <div className="space-y-3">
          {DUE_ALERTS.map((s) => (
            <div key={s.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <div>
                <p className="font-medium text-sm text-gray-900">{s.asset} — {s.type}</p>
                <p className="text-xs text-gray-500">Due: {s.dueDate}</p>
              </div>
              <div className="flex gap-2">
                <button className="px-3 py-1 text-xs border border-gray-300 rounded hover:bg-gray-100">Edit</button>
                <button className="px-3 py-1 text-xs bg-amber-500 text-white rounded hover:bg-amber-600">Mark Done</button>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
