"use client";

import { useState } from "react";

interface SaleOffer {
  id: string;
  vehicle: string;
  offerAmountKES: number;
  status: "Pending" | "Accepted" | "Counter-offered" | "Declined";
  updatedAt: string;
}

interface TestDriveAppointment {
  id: string;
  vehicle: string;
  date: string;
  location: string;
  status: "Confirmed" | "Pending" | "Completed";
}

interface SignedAgreement {
  id: string;
  vehicle: string;
  agreedPriceKES: number;
  signedAt: string;
  documentUrl: string;
}

const OFFERS: SaleOffer[] = [
  { id: "o1", vehicle: "Toyota Land Cruiser V8 2020", offerAmountKES: 8100000, status: "Accepted", updatedAt: "2024-06-11" },
  { id: "o2", vehicle: "Mercedes-Benz GLE 2021", offerAmountKES: 6500000, status: "Counter-offered", updatedAt: "2024-06-10" },
];

const TEST_DRIVES: TestDriveAppointment[] = [
  { id: "t1", vehicle: "Toyota Land Cruiser V8 2020", date: "2024-06-15 10:00 AM", location: "Karen, Nairobi", status: "Confirmed" },
];

const AGREEMENTS: SignedAgreement[] = [
  { id: "ag1", vehicle: "Isuzu D-Max 2019", agreedPriceKES: 3200000, signedAt: "2024-05-22", documentUrl: "#" },
];

const TRANSFER_CHECKLIST = [
  { item: "Log Book (title deed)", done: true },
  { item: "Insurance transfer", done: true },
  { item: "NTSA ownership transfer", done: false },
  { item: "Inspection certificate", done: false },
  { item: "Final payment receipt", done: true },
];

const STATUS_COLORS: Record<SaleOffer["status"], string> = {
  Pending: "bg-yellow-100 text-yellow-800",
  Accepted: "bg-green-100 text-green-800",
  "Counter-offered": "bg-blue-100 text-blue-800",
  Declined: "bg-red-100 text-red-800",
};

export default function ClientPurchasesPage() {
  return (
    <div className="p-6 space-y-8">
      <h1 className="text-2xl font-bold text-gray-900">My Purchases</h1>

      {/* Active Offers */}
      <section>
        <h2 className="text-lg font-semibold text-gray-900 mb-3">Active Sale Offers</h2>
        <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
          <table className="w-full text-sm">
            <thead className="bg-gray-50 border-b">
              <tr>
                <th className="p-3 text-left font-semibold text-gray-700">Vehicle</th>
                <th className="p-3 text-right font-semibold text-gray-700">Offer (KES)</th>
                <th className="p-3 text-left font-semibold text-gray-700">Status</th>
                <th className="p-3 text-left font-semibold text-gray-700">Updated</th>
                <th className="p-3 text-right font-semibold text-gray-700">Action</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {OFFERS.map((o) => (
                <tr key={o.id} className="hover:bg-gray-50">
                  <td className="p-3 font-medium text-gray-900">{o.vehicle}</td>
                  <td className="p-3 text-right">{o.offerAmountKES.toLocaleString()}</td>
                  <td className="p-3">
                    <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${STATUS_COLORS[o.status]}`}>{o.status}</span>
                  </td>
                  <td className="p-3 text-gray-500">{o.updatedAt}</td>
                  <td className="p-3 text-right">
                    <a href={`/listings/${o.id}/buy`} className="text-amber-600 text-xs hover:underline">View →</a>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>

      {/* Test Drive Appointments */}
      <section>
        <h2 className="text-lg font-semibold text-gray-900 mb-3">Test Drive Appointments</h2>
        <div className="space-y-3">
          {TEST_DRIVES.map((td) => (
            <div key={td.id} className="bg-white rounded-xl border border-gray-200 p-4 flex items-center justify-between">
              <div>
                <p className="font-medium text-gray-900">{td.vehicle}</p>
                <p className="text-sm text-gray-500">{td.date} · {td.location}</p>
              </div>
              <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${
                td.status === "Confirmed" ? "bg-green-100 text-green-800" : "bg-yellow-100 text-yellow-800"
              }`}>{td.status}</span>
            </div>
          ))}
        </div>
      </section>

      {/* Signed Agreements */}
      <section>
        <h2 className="text-lg font-semibold text-gray-900 mb-3">Signed Agreements</h2>
        <div className="space-y-3">
          {AGREEMENTS.map((a) => (
            <div key={a.id} className="bg-white rounded-xl border border-gray-200 p-4 flex items-center justify-between">
              <div>
                <p className="font-medium text-gray-900">{a.vehicle}</p>
                <p className="text-sm text-gray-500">Agreed: KES {a.agreedPriceKES.toLocaleString()} · Signed {a.signedAt}</p>
              </div>
              <a href={a.documentUrl} className="px-3 py-1.5 border border-gray-300 rounded-lg text-xs hover:bg-gray-50">Download PDF</a>
            </div>
          ))}
        </div>
      </section>

      {/* Transfer Checklist */}
      <section>
        <h2 className="text-lg font-semibold text-gray-900 mb-3">Transfer Documentation Checklist</h2>
        <div className="bg-white rounded-xl border border-gray-200 p-5 space-y-3">
          {TRANSFER_CHECKLIST.map((item, i) => (
            <div key={i} className="flex items-center gap-3">
              <span className={`w-5 h-5 rounded flex items-center justify-center text-xs ${
                item.done ? "bg-green-500 text-white" : "bg-gray-200 text-gray-400"
              }`}>{item.done ? "✓" : ""}</span>
              <span className={`text-sm ${item.done ? "text-gray-900" : "text-gray-500"}`}>{item.item}</span>
            </div>
          ))}
        </div>
      </section>
    </div>
  );
}
