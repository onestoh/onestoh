"use client";

import { useState } from "react";
import UtilisationBar from "@/components/fleet/UtilisationBar";

type AssetStatus = "Active" | "Maintenance" | "Off-road";
type AssetCategory = "Car" | "Truck" | "Machinery" | "Bus";

interface FleetAsset {
  id: string;
  name: string;
  make: string;
  model: string;
  year: number;
  status: AssetStatus;
  category: AssetCategory;
  utilisation: number;
  revenueKES: number;
  plateNumber: string;
}

const MOCK_ASSETS: FleetAsset[] = [
  { id: "a1", name: "Toyota Land Cruiser V8", make: "Toyota", model: "Land Cruiser", year: 2021, status: "Active", category: "Car", utilisation: 78, revenueKES: 245000, plateNumber: "KDJ 123A" },
  { id: "a2", name: "CAT 320 Excavator", make: "Caterpillar", model: "320", year: 2019, status: "Maintenance", category: "Machinery", utilisation: 45, revenueKES: 180000, plateNumber: "KCF 456B" },
  { id: "a3", name: "Isuzu NQR Bus", make: "Isuzu", model: "NQR", year: 2020, status: "Active", category: "Bus", utilisation: 62, revenueKES: 310000, plateNumber: "KDD 789C" },
  { id: "a4", name: "Mercedes Sprinter", make: "Mercedes", model: "Sprinter", year: 2022, status: "Off-road", category: "Truck", utilisation: 0, revenueKES: 0, plateNumber: "KDE 012D" },
  { id: "a5", name: "Komatsu PC200 Excavator", make: "Komatsu", model: "PC200", year: 2018, status: "Active", category: "Machinery", utilisation: 91, revenueKES: 420000, plateNumber: "KCB 345E" },
];

const STATUS_COLORS: Record<AssetStatus, string> = {
  Active: "bg-green-100 text-green-800",
  Maintenance: "bg-amber-100 text-amber-800",
  "Off-road": "bg-red-100 text-red-800",
};

const CATEGORIES: ("All" | AssetCategory)[] = ["All", "Car", "Truck", "Machinery", "Bus"];
const STATUSES: ("All" | AssetStatus)[] = ["All", "Active", "Maintenance", "Off-road"];

export default function FleetPage() {
  const [selectedIds, setSelectedIds] = useState<Set<string>>(new Set());
  const [filterStatus, setFilterStatus] = useState<"All" | AssetStatus>("All");
  const [filterCategory, setFilterCategory] = useState<"All" | AssetCategory>("All");

  const filtered = MOCK_ASSETS.filter(
    (a) =>
      (filterStatus === "All" || a.status === filterStatus) &&
      (filterCategory === "All" || a.category === filterCategory)
  );

  const toggleSelect = (id: string) => {
    setSelectedIds((prev) => {
      const next = new Set(prev);
      next.has(id) ? next.delete(id) : next.add(id);
      return next;
    });
  };

  const selectAll = () => {
    setSelectedIds(new Set(filtered.map((a) => a.id)));
  };

  const exportCSV = () => {
    const headers = ["ID", "Name", "Make", "Model", "Year", "Status", "Category", "Utilisation%", "Revenue KES", "Plate"];
    const rows = MOCK_ASSETS.map((a) => [
      a.id, a.name, a.make, a.model, a.year, a.status, a.category, a.utilisation, a.revenueKES, a.plateNumber,
    ]);
    const csv = [headers, ...rows].map((r) => r.join(",")).join("\n");
    const blob = new Blob([csv], { type: "text/csv" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "fleet.csv";
    a.click();
    URL.revokeObjectURL(url);
  };

  return (
    <div className="p-6 space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Fleet Registry</h1>
          <p className="text-sm text-gray-500">{MOCK_ASSETS.length} assets total</p>
        </div>
        <div className="flex gap-3">
          <button
            onClick={exportCSV}
            className="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-2"
          >
            <span>↓</span> Export CSV
          </button>
          <button className="px-4 py-2 text-sm bg-amber-500 text-white rounded-lg hover:bg-amber-600 font-medium">
            + Add Asset
          </button>
        </div>
      </div>

      {/* Filters */}
      <div className="flex flex-wrap gap-4">
        <div className="flex gap-1 bg-gray-100 p-1 rounded-lg">
          {STATUSES.map((s) => (
            <button
              key={s}
              onClick={() => setFilterStatus(s)}
              className={`px-3 py-1.5 text-sm rounded-md transition-colors ${
                filterStatus === s ? "bg-white shadow text-gray-900" : "text-gray-500 hover:text-gray-700"
              }`}
            >
              {s}
            </button>
          ))}
        </div>
        <div className="flex gap-1 bg-gray-100 p-1 rounded-lg">
          {CATEGORIES.map((c) => (
            <button
              key={c}
              onClick={() => setFilterCategory(c)}
              className={`px-3 py-1.5 text-sm rounded-md transition-colors ${
                filterCategory === c ? "bg-white shadow text-gray-900" : "text-gray-500 hover:text-gray-700"
              }`}
            >
              {c}
            </button>
          ))}
        </div>
      </div>

      {/* Bulk actions */}
      {selectedIds.size > 0 && (
        <div className="flex items-center gap-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
          <span className="text-sm font-medium text-amber-800">{selectedIds.size} selected</span>
          <button className="px-3 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700">Set Active</button>
          <button className="px-3 py-1 text-xs bg-amber-600 text-white rounded hover:bg-amber-700">Set Maintenance</button>
          <button className="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">Set Off-road</button>
          <button onClick={() => setSelectedIds(new Set())} className="text-xs text-gray-500 ml-auto">Clear</button>
        </div>
      )}

      {/* Table */}
      <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table className="w-full text-sm">
          <thead className="bg-gray-50 border-b border-gray-200">
            <tr>
              <th className="p-3 w-10">
                <input type="checkbox" onChange={selectAll} className="rounded" />
              </th>
              <th className="p-3 text-left font-semibold text-gray-700">Asset</th>
              <th className="p-3 text-left font-semibold text-gray-700">Plate</th>
              <th className="p-3 text-left font-semibold text-gray-700">Status</th>
              <th className="p-3 text-left font-semibold text-gray-700">Category</th>
              <th className="p-3 text-left font-semibold text-gray-700">Utilisation</th>
              <th className="p-3 text-right font-semibold text-gray-700">Revenue (KES)</th>
              <th className="p-3 text-right font-semibold text-gray-700">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {filtered.map((asset) => (
              <tr key={asset.id} className="hover:bg-gray-50 transition-colors">
                <td className="p-3">
                  <input
                    type="checkbox"
                    checked={selectedIds.has(asset.id)}
                    onChange={() => toggleSelect(asset.id)}
                    className="rounded"
                  />
                </td>
                <td className="p-3">
                  <div className="font-medium text-gray-900">{asset.name}</div>
                  <div className="text-xs text-gray-500">{asset.year}</div>
                </td>
                <td className="p-3 text-gray-600 font-mono">{asset.plateNumber}</td>
                <td className="p-3">
                  <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${STATUS_COLORS[asset.status]}`}>
                    {asset.status}
                  </span>
                </td>
                <td className="p-3 text-gray-600">{asset.category}</td>
                <td className="p-3 w-40">
                  <UtilisationBar value={asset.utilisation} />
                </td>
                <td className="p-3 text-right font-medium text-gray-900">
                  {asset.revenueKES.toLocaleString()}
                </td>
                <td className="p-3 text-right">
                  <a
                    href={`/owner/fleet/${asset.id}`}
                    className="text-amber-600 hover:text-amber-700 font-medium text-xs"
                  >
                    Manage →
                  </a>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
        {filtered.length === 0 && (
          <div className="text-center py-12 text-gray-500">No assets match the selected filters.</div>
        )}
      </div>
    </div>
  );
}
