"use client";

import { useState } from "react";
import SaleOfferThread from "@/components/sales/SaleOfferThread";

interface SaleEnquiry {
  id: string;
  vehicle: string;
  buyerName: string;
  buyerPhone: string;
  offerAmountKES: number;
  askingPriceKES: number;
  status: "New" | "Counter-offered" | "Accepted" | "Declined";
  submittedAt: string;
}

const ENQUIRIES: SaleEnquiry[] = [
  { id: "e1", vehicle: "Toyota Land Cruiser V8 2020", buyerName: "John Kamau", buyerPhone: "+254 712 345 678", offerAmountKES: 8100000, askingPriceKES: 8500000, status: "New", submittedAt: "2024-06-11 08:30" },
  { id: "e2", vehicle: "Toyota Land Cruiser V8 2020", buyerName: "Grace Muthoni", buyerPhone: "+254 723 456 789", offerAmountKES: 7900000, askingPriceKES: 8500000, status: "Counter-offered", submittedAt: "2024-06-10 14:15" },
];

const COMPLETED_SALES = [
  { id: "cs1", vehicle: "Isuzu D-Max 2019", buyerName: "Peter Njoroge", finalPriceKES: 3200000, completedAt: "2024-05-22" },
];

const TEST_DRIVE_REQUESTS = [
  { id: "td1", vehicle: "Toyota Land Cruiser V8 2020", buyerName: "John Kamau", requestedDate: "2024-06-15", status: "Confirmed" },
];

const STATUS_COLORS: Record<SaleEnquiry["status"], string> = {
  New: "bg-blue-100 text-blue-800",
  "Counter-offered": "bg-amber-100 text-amber-800",
  Accepted: "bg-green-100 text-green-800",
  Declined: "bg-red-100 text-red-800",
};

export default function OwnerSalesPage() {
  const [selectedEnquiry, setSelectedEnquiry] = useState<SaleEnquiry | null>(null);
  const [counterAmount, setCounterAmount] = useState("");

  return (
    <div className="p-6 space-y-8">
      <h1 className="text-2xl font-bold text-gray-900">Sales Dashboard</h1>

      {/* Active Enquiries */}
      <section>
        <h2 className="text-lg font-semibold text-gray-900 mb-3">Active Sale Enquiries</h2>
        <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
          <table className="w-full text-sm">
            <thead className="bg-gray-50 border-b">
              <tr>
                <th className="p-3 text-left font-semibold text-gray-700">Vehicle</th>
                <th className="p-3 text-left font-semibold text-gray-700">Buyer</th>
                <th className="p-3 text-right font-semibold text-gray-700">Offer (KES)</th>
                <th className="p-3 text-right font-semibold text-gray-700">Asking (KES)</th>
                <th className="p-3 text-left font-semibold text-gray-700">Status</th>
                <th className="p-3 text-right font-semibold text-gray-700">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {ENQUIRIES.map((e) => (
                <tr key={e.id} className="hover:bg-gray-50">
                  <td className="p-3 font-medium text-gray-900">{e.vehicle}</td>
                  <td className="p-3">
                    <p className="font-medium text-gray-900">{e.buyerName}</p>
                    <p className="text-xs text-gray-500">{e.buyerPhone}</p>
                  </td>
                  <td className="p-3 text-right font-semibold">{e.offerAmountKES.toLocaleString()}</td>
                  <td className="p-3 text-right text-gray-500">{e.askingPriceKES.toLocaleString()}</td>
                  <td className="p-3">
                    <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${STATUS_COLORS[e.status]}`}>{e.status}</span>
                  </td>
                  <td className="p-3">
                    <div className="flex gap-1 justify-end">
                      <button className="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700">Accept</button>
                      <button
                        onClick={() => setSelectedEnquiry(e)}
                        className="px-2 py-1 text-xs bg-amber-500 text-white rounded hover:bg-amber-600"
                      >Counter</button>
                      <button className="px-2 py-1 text-xs border border-gray-300 rounded hover:bg-gray-50">Decline</button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>

      {/* Test Drive Requests */}
      <section>
        <h2 className="text-lg font-semibold text-gray-900 mb-3">Test Drive Requests</h2>
        <div className="space-y-3">
          {TEST_DRIVE_REQUESTS.map((td) => (
            <div key={td.id} className="bg-white rounded-xl border border-gray-200 p-4 flex items-center justify-between">
              <div>
                <p className="font-medium text-gray-900">{td.vehicle}</p>
                <p className="text-sm text-gray-500">{td.buyerName} · Requested: {td.requestedDate}</p>
              </div>
              <span className="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{td.status}</span>
            </div>
          ))}
        </div>
      </section>

      {/* Completed Sales */}
      <section>
        <h2 className="text-lg font-semibold text-gray-900 mb-3">Completed Sales</h2>
        <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
          <table className="w-full text-sm">
            <thead className="bg-gray-50 border-b">
              <tr>
                <th className="p-3 text-left font-semibold text-gray-700">Vehicle</th>
                <th className="p-3 text-left font-semibold text-gray-700">Buyer</th>
                <th className="p-3 text-right font-semibold text-gray-700">Final Price (KES)</th>
                <th className="p-3 text-left font-semibold text-gray-700">Completed</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {COMPLETED_SALES.map((cs) => (
                <tr key={cs.id} className="hover:bg-gray-50">
                  <td className="p-3 font-medium text-gray-900">{cs.vehicle}</td>
                  <td className="p-3 text-gray-600">{cs.buyerName}</td>
                  <td className="p-3 text-right font-semibold text-green-700">{cs.finalPriceKES.toLocaleString()}</td>
                  <td className="p-3 text-gray-500">{cs.completedAt}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>

      {/* Counter Offer Modal */}
      {selectedEnquiry && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-xl p-6 w-full max-w-md">
            <h3 className="text-lg font-semibold mb-2">Counter Offer — {selectedEnquiry.buyerName}</h3>
            <p className="text-sm text-gray-500 mb-4">Current offer: KES {selectedEnquiry.offerAmountKES.toLocaleString()}</p>
            <div className="mb-4">
              <label className="block text-sm font-medium text-gray-700 mb-1">Your Counter Offer (KES)</label>
              <input
                type="number"
                value={counterAmount}
                onChange={(e) => setCounterAmount(e.target.value)}
                placeholder={selectedEnquiry.askingPriceKES.toString()}
                className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
              />
            </div>
            <div className="flex gap-3">
              <button onClick={() => setSelectedEnquiry(null)} className="flex-1 py-2 border border-gray-300 rounded-lg text-sm">Cancel</button>
              <button className="flex-1 py-2 bg-amber-500 text-white rounded-lg text-sm font-medium hover:bg-amber-600">Send Counter</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
