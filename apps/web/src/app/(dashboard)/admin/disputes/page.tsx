"use client";

import { useState } from "react";

type DisputeStatus = "open" | "under_review" | "ruled" | "appealed";

interface Dispute {
  id: string;
  bookingRef: string;
  ownerName: string;
  clientName: string;
  disputeType: string;
  raisedAt: string;
  daysOpen: number;
  escrowKES: number;
  status: DisputeStatus;
}

const DISPUTES: Dispute[] = [
  { id: "d1", bookingRef: "BK-20240601-001", ownerName: "John Mwangi", clientName: "Grace Otieno", disputeType: "Vehicle damage", raisedAt: "2024-06-01 09:00", daysOpen: 3, escrowKES: 45000, status: "open" },
  { id: "d2", bookingRef: "BK-20240528-007", ownerName: "Alice Kamau", clientName: "Peter Njoroge", disputeType: "Early return", raisedAt: "2024-05-28 14:30", daysOpen: 7, escrowKES: 18000, status: "under_review" },
  { id: "d3", bookingRef: "BK-20240520-003", ownerName: "Samuel Odhiambo", clientName: "Mercy Wanjiku", disputeType: "No-show", raisedAt: "2024-05-20 08:15", daysOpen: 15, escrowKES: 30000, status: "ruled" },
  { id: "d4", bookingRef: "BK-20240515-009", ownerName: "Mary Wanjiru", clientName: "Tom Gitau", disputeType: "Late return", raisedAt: "2024-05-15 11:00", daysOpen: 20, escrowKES: 22000, status: "appealed" },
];

const STATUS_COLORS: Record<DisputeStatus, string> = {
  open: "bg-red-100 text-red-800",
  under_review: "bg-amber-100 text-amber-800",
  ruled: "bg-green-100 text-green-800",
  appealed: "bg-purple-100 text-purple-800",
};

const STATUS_LABELS: Record<DisputeStatus, string> = {
  open: "Open",
  under_review: "Under Review",
  ruled: "Ruled",
  appealed: "Appealed",
};

const FILTER_OPTIONS: ("all" | DisputeStatus)[] = ["all", "open", "under_review", "ruled", "appealed"];

export default function AdminDisputesPage() {
  const [filterStatus, setFilterStatus] = useState<"all" | DisputeStatus>("all");
  const [selectedDispute, setSelectedDispute] = useState<Dispute | null>(null);
  const [ruling, setRuling] = useState<"full_owner" | "full_client" | "split">("full_owner");
  const [splitPct, setSplitPct] = useState(50);

  const filtered = DISPUTES.filter((d) => filterStatus === "all" || d.status === filterStatus);

  return (
    <div className="p-6 space-y-6">
      <h1 className="text-2xl font-bold text-gray-900">Dispute Management</h1>

      {/* Filters */}
      <div className="flex gap-1 bg-gray-100 p-1 rounded-lg w-fit">
        {FILTER_OPTIONS.map((s) => (
          <button
            key={s}
            onClick={() => setFilterStatus(s)}
            className={`px-3 py-1.5 text-sm rounded-md transition-colors capitalize ${
              filterStatus === s ? "bg-white shadow text-gray-900 font-medium" : "text-gray-500 hover:text-gray-700"
            }`}
          >
            {s === "all" ? "All" : STATUS_LABELS[s]}
          </button>
        ))}
      </div>

      {/* Table */}
      <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table className="w-full text-sm">
          <thead className="bg-gray-50 border-b">
            <tr>
              <th className="p-3 text-left font-semibold text-gray-700">Booking Ref</th>
              <th className="p-3 text-left font-semibold text-gray-700">Parties</th>
              <th className="p-3 text-left font-semibold text-gray-700">Type</th>
              <th className="p-3 text-left font-semibold text-gray-700">Raised At</th>
              <th className="p-3 text-right font-semibold text-gray-700">Days Open</th>
              <th className="p-3 text-left font-semibold text-gray-700">Status</th>
              <th className="p-3 text-right font-semibold text-gray-700">Action</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {filtered.map((d) => (
              <tr key={d.id} className="hover:bg-gray-50">
                <td className="p-3 font-mono text-xs text-gray-600">{d.bookingRef}</td>
                <td className="p-3">
                  <p className="text-xs text-gray-500">Owner: {d.ownerName}</p>
                  <p className="text-xs text-gray-500">Client: {d.clientName}</p>
                </td>
                <td className="p-3 text-gray-700">{d.disputeType}</td>
                <td className="p-3 text-gray-500 text-xs">{d.raisedAt}</td>
                <td className="p-3 text-right">
                  <span className={d.daysOpen > 7 ? "text-red-600 font-semibold" : "text-gray-700"}>{d.daysOpen}</span>
                </td>
                <td className="p-3">
                  <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${STATUS_COLORS[d.status]}`}>
                    {STATUS_LABELS[d.status]}
                  </span>
                </td>
                <td className="p-3 text-right">
                  <button
                    onClick={() => setSelectedDispute(d)}
                    className="text-amber-600 text-xs hover:underline"
                  >
                    Review →
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Dispute Detail Modal */}
      {selectedDispute && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4 overflow-y-auto">
          <div className="bg-white rounded-xl w-full max-w-2xl my-4">
            <div className="flex items-center justify-between p-6 border-b">
              <h3 className="text-lg font-semibold">Dispute: {selectedDispute.bookingRef}</h3>
              <button onClick={() => setSelectedDispute(null)} className="text-gray-400 hover:text-gray-600 text-xl">×</button>
            </div>
            <div className="p-6 space-y-6">
              {/* Evidence Photos */}
              <div>
                <h4 className="font-semibold text-gray-900 mb-3">Evidence Photos</h4>
                <div className="grid grid-cols-4 gap-2">
                  {["Owner", "Owner", "Client", "Client"].map((party, i) => (
                    <div key={i} className="aspect-square bg-gray-100 rounded-lg flex items-center justify-center text-xs text-gray-400 flex-col gap-1">
                      <span className="text-2xl">📷</span>
                      <span>{party}</span>
                    </div>
                  ))}
                </div>
              </div>

              {/* Timeline */}
              <div>
                <h4 className="font-semibold text-gray-900 mb-3">Booking Event Timeline</h4>
                <div className="space-y-2">
                  {[
                    { event: "Booking confirmed", time: "2024-05-29 10:00" },
                    { event: "Asset picked up", time: "2024-06-01 08:30" },
                    { event: "Dispute raised", time: selectedDispute.raisedAt },
                    { event: "Evidence uploaded", time: "2024-06-01 11:00" },
                  ].map((e, i) => (
                    <div key={i} className="flex items-center gap-3 text-sm">
                      <div className="w-2 h-2 rounded-full bg-amber-500 flex-shrink-0" />
                      <span className="text-gray-700">{e.event}</span>
                      <span className="text-gray-400 ml-auto">{e.time}</span>
                    </div>
                  ))}
                </div>
              </div>

              {/* Escrow */}
              <div className="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                <p className="text-sm font-semibold text-amber-800">Escrow Amount: KES {selectedDispute.escrowKES.toLocaleString()}</p>
              </div>

              {/* Ruling Form */}
              <div>
                <h4 className="font-semibold text-gray-900 mb-3">Ruling</h4>
                <div className="space-y-2">
                  {([
                    { value: "full_owner" as const, label: "Full amount to Owner" },
                    { value: "full_client" as const, label: "Full amount to Client (refund)" },
                    { value: "split" as const, label: "Split between parties" },
                  ]).map((opt) => (
                    <label key={opt.value} className="flex items-center gap-3 cursor-pointer">
                      <input
                        type="radio"
                        name="ruling"
                        value={opt.value}
                        checked={ruling === opt.value}
                        onChange={() => setRuling(opt.value)}
                        className="accent-amber-500"
                      />
                      <span className="text-sm">{opt.label}</span>
                    </label>
                  ))}
                </div>
                {ruling === "split" && (
                  <div className="mt-3">
                    <label className="text-sm font-medium text-gray-700">Owner Share: <span className="text-amber-600 font-bold">{splitPct}%</span></label>
                    <input
                      type="range" min={0} max={100} value={splitPct}
                      onChange={(e) => setSplitPct(Number(e.target.value))}
                      className="w-full mt-1 accent-amber-500"
                    />
                  </div>
                )}
              </div>

              <button className="w-full py-3 bg-amber-500 text-white font-semibold rounded-xl hover:bg-amber-600">
                Submit Ruling
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
