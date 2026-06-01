"use client";

import { useState } from "react";
import { useParams } from "next/navigation";
import SaleOfferThread from "@/components/sales/SaleOfferThread";

const LISTING = {
  id: "l-001",
  name: "Toyota Land Cruiser V8 GX 2020",
  askingPriceKES: 8500000,
  mileageKm: 65000,
  year: 2020,
  color: "White",
  location: "Nairobi, Karen",
};

const OFFER_THREAD = [
  { id: "1", author: "Buyer", amountKES: 7800000, message: "Initial offer", timestamp: "2024-06-10 09:12", type: "offer" as const },
  { id: "2", author: "Owner", amountKES: 8300000, message: "Counter-offer — price is firm, minor negotiation possible", timestamp: "2024-06-10 11:45", type: "counter" as const },
  { id: "3", author: "Buyer", amountKES: 8100000, message: "Can we meet at 8.1M? Includes immediate 10% reservation.", timestamp: "2024-06-11 08:30", type: "offer" as const },
];

const STEPS = [
  "Submit offer",
  "Owner accepts / counter-offers",
  "Agree price & pay 10% reservation via M-Pesa or card",
  "Schedule test drive",
  "Final payment & documentation transfer",
];

export default function BuyListingPage() {
  const params = useParams();
  const [offerAmount, setOfferAmount] = useState("");
  const [offerMessage, setOfferMessage] = useState("");
  const [agreed, setAgreed] = useState(false);
  const [reservationPaid, setReservationPaid] = useState(false);
  const [testDriveDate, setTestDriveDate] = useState("");
  const [activeStep, setActiveStep] = useState(1);

  const agreedPrice = 8100000;
  const reservationFee = Math.round(agreedPrice * 0.1);

  return (
    <div className="max-w-5xl mx-auto px-4 py-8 space-y-8">
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Main Content */}
        <div className="lg:col-span-2 space-y-6">
          {/* Vehicle Summary */}
          <div className="bg-white rounded-xl border border-gray-200 p-6">
            <div className="flex items-start justify-between">
              <div>
                <h1 className="text-2xl font-bold text-gray-900">{LISTING.name}</h1>
                <p className="text-gray-500 text-sm mt-1">{LISTING.mileageKm.toLocaleString()} km · {LISTING.color} · {LISTING.location}</p>
              </div>
              <div className="text-right">
                <p className="text-2xl font-bold text-gray-900">KES {LISTING.askingPriceKES.toLocaleString()}</p>
                <p className="text-sm text-gray-500">Asking price</p>
              </div>
            </div>
          </div>

          {/* Offer Thread */}
          <div className="bg-white rounded-xl border border-gray-200 p-6">
            <h2 className="text-lg font-semibold text-gray-900 mb-4">Offer History</h2>
            <SaleOfferThread offers={OFFER_THREAD} />
          </div>

          {/* Make Offer Form */}
          <div className="bg-white rounded-xl border border-gray-200 p-6">
            <h2 className="text-lg font-semibold text-gray-900 mb-4">Make an Offer</h2>
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Your Offer (KES)</label>
                <input
                  type="number"
                  value={offerAmount}
                  onChange={(e) => setOfferAmount(e.target.value)}
                  placeholder={LISTING.askingPriceKES.toString()}
                  className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Message (optional)</label>
                <textarea
                  value={offerMessage}
                  onChange={(e) => setOfferMessage(e.target.value)}
                  rows={2}
                  placeholder="Add a note to your offer..."
                  className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                />
              </div>
              <button className="w-full py-2.5 bg-amber-500 text-white font-semibold rounded-lg hover:bg-amber-600">
                Submit Offer
              </button>
            </div>
          </div>

          {/* Price Agreement */}
          {!agreed && (
            <div className="bg-green-50 border border-green-200 rounded-xl p-6">
              <h2 className="text-lg font-semibold text-green-800 mb-2">Price Agreed: KES {agreedPrice.toLocaleString()}</h2>
              <p className="text-sm text-green-700 mb-4">Both parties have agreed on this price. Confirm to proceed to reservation payment.</p>
              <button
                onClick={() => setAgreed(true)}
                className="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium"
              >
                Confirm Agreement
              </button>
            </div>
          )}

          {/* Reservation Payment */}
          {agreed && !reservationPaid && (
            <div className="bg-white border border-gray-200 rounded-xl p-6">
              <h2 className="text-lg font-semibold text-gray-900 mb-4">Pay Reservation Fee (10%)</h2>
              <p className="text-sm text-gray-600 mb-4">Secure this vehicle with a KES {reservationFee.toLocaleString()} reservation fee. Refundable if deal falls through on seller&apos;s side.</p>
              <div className="flex gap-3">
                <button
                  onClick={() => setReservationPaid(true)}
                  className="flex-1 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium"
                >
                  Pay via M-Pesa
                </button>
                <button
                  onClick={() => setReservationPaid(true)}
                  className="flex-1 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium"
                >
                  Pay by Card
                </button>
              </div>
            </div>
          )}

          {/* Test Drive */}
          {reservationPaid && (
            <div className="bg-white border border-gray-200 rounded-xl p-6">
              <h2 className="text-lg font-semibold text-gray-900 mb-4">Schedule Test Drive</h2>
              <div className="flex gap-3">
                <input
                  type="date"
                  value={testDriveDate}
                  onChange={(e) => setTestDriveDate(e.target.value)}
                  className="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm"
                />
                <button className="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 font-medium text-sm">
                  Confirm Date
                </button>
              </div>
            </div>
          )}
        </div>

        {/* Sidebar */}
        <div className="space-y-4">
          <div className="bg-white rounded-xl border border-gray-200 p-5 sticky top-4">
            <h3 className="font-semibold text-gray-900 mb-4">What Happens Next</h3>
            <ol className="space-y-3">
              {STEPS.map((step, i) => (
                <li key={i} className="flex items-start gap-3">
                  <span className={`w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold ${
                    i < activeStep ? "bg-green-500 text-white" : i === activeStep ? "bg-amber-500 text-white" : "bg-gray-100 text-gray-500"
                  }`}>
                    {i < activeStep ? "✓" : i + 1}
                  </span>
                  <span className={`text-sm ${i === activeStep ? "font-medium text-gray-900" : "text-gray-500"}`}>{step}</span>
                </li>
              ))}
            </ol>
          </div>
        </div>
      </div>
    </div>
  );
}
