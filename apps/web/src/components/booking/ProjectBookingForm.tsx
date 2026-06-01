"use client";

import { useState } from "react";

interface ProjectBookingFormProps {
  dailyRateKES: number;
  mobilisationBaseKES: number;
  demobilisationBaseKES: number;
  onClose?: () => void;
}

export default function ProjectBookingForm({
  dailyRateKES,
  mobilisationBaseKES,
  demobilisationBaseKES,
  onClose,
}: ProjectBookingFormProps) {
  const [siteLocation, setSiteLocation] = useState("");
  const [jobDescription, setJobDescription] = useState("");
  const [mobilisationDate, setMobilisationDate] = useState("");
  const [operationalStartDate, setOperationalStartDate] = useState("");
  const [hireDays, setHireDays] = useState(5);
  const [dailyHours, setDailyHours] = useState(8);
  const [fuelArrangement, setFuelArrangement] = useState<"client-provided" | "owner-provided">("client-provided");
  const [submitted, setSubmitted] = useState(false);

  const PLATFORM_FEE_PCT = 0.07;
  const FUEL_SURCHARGE = fuelArrangement === "owner-provided" ? dailyHours * 22 * 150 : 0; // 22L/hr × KES 150/L

  const baseTotal = dailyRateKES * hireDays;
  const platformFee = Math.round(baseTotal * PLATFORM_FEE_PCT);
  const grandTotal = baseTotal + mobilisationBaseKES + demobilisationBaseKES + platformFee + FUEL_SURCHARGE * hireDays;

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitted(true);
  };

  if (submitted) {
    return (
      <div className="text-center py-8">
        <div className="text-5xl mb-4">✅</div>
        <h3 className="text-xl font-bold text-gray-900">Enquiry Submitted</h3>
        <p className="text-gray-500 mt-2">The asset owner will review your project and respond within 24 hours.</p>
        {onClose && (
          <button onClick={onClose} className="mt-6 px-6 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600">
            Close
          </button>
        )}
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-5">
      {/* Site Location */}
      <div>
        <label className="block text-sm font-medium text-gray-700 mb-1">Site Location / Address</label>
        <input
          type="text"
          required
          value={siteLocation}
          onChange={(e) => setSiteLocation(e.target.value)}
          placeholder="e.g. Mombasa Road, Nairobi (Google Maps autocomplete)"
          className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
        />
        <p className="text-xs text-gray-400 mt-1">Start typing for address suggestions (autocomplete integration pending)</p>
      </div>

      {/* Job Description */}
      <div>
        <label className="block text-sm font-medium text-gray-700 mb-1">Job Description</label>
        <textarea
          required
          value={jobDescription}
          onChange={(e) => setJobDescription(e.target.value)}
          rows={3}
          placeholder="Describe the scope of work, ground conditions, any special requirements..."
          className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
        />
      </div>

      {/* Dates */}
      <div className="grid grid-cols-2 gap-4">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Mobilisation Date</label>
          <input
            type="date"
            required
            value={mobilisationDate}
            onChange={(e) => setMobilisationDate(e.target.value)}
            className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
          />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Operational Start Date</label>
          <input
            type="date"
            required
            value={operationalStartDate}
            onChange={(e) => setOperationalStartDate(e.target.value)}
            className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
          />
        </div>
      </div>

      {/* Hire Days */}
      <div>
        <label className="block text-sm font-medium text-gray-700 mb-1">Estimated Hire Duration (days): <span className="text-amber-600 font-bold">{hireDays}</span></label>
        <input
          type="range" min={5} max={90} value={hireDays}
          onChange={(e) => setHireDays(Number(e.target.value))}
          className="w-full accent-amber-500"
        />
        <div className="flex justify-between text-xs text-gray-400">
          <span>5 days (min)</span>
          <span>90 days</span>
        </div>
      </div>

      {/* Daily Hours */}
      <div>
        <label className="block text-sm font-medium text-gray-700 mb-1">Agreed Daily Operating Hours: <span className="text-amber-600 font-bold">{dailyHours} hrs</span></label>
        <input
          type="range" min={1} max={12} value={dailyHours}
          onChange={(e) => setDailyHours(Number(e.target.value))}
          className="w-full accent-amber-500"
        />
        <div className="flex justify-between text-xs text-gray-400">
          <span>1 hr</span>
          <span>12 hrs</span>
        </div>
      </div>

      {/* Fuel Arrangement */}
      <div>
        <label className="block text-sm font-medium text-gray-700 mb-2">Fuel Arrangement</label>
        <div className="flex gap-4">
          {(["client-provided", "owner-provided"] as const).map((opt) => (
            <label key={opt} className="flex items-center gap-2 cursor-pointer">
              <input
                type="radio"
                name="fuel"
                value={opt}
                checked={fuelArrangement === opt}
                onChange={() => setFuelArrangement(opt)}
                className="accent-amber-500"
              />
              <span className="text-sm capitalize">{opt.replace("-", " ")}</span>
            </label>
          ))}
        </div>
      </div>

      {/* Price Breakdown */}
      <div className="bg-gray-50 rounded-xl p-4 space-y-2 border border-gray-200">
        <h4 className="font-semibold text-gray-900 text-sm mb-3">Price Breakdown (Estimate)</h4>
        <div className="flex justify-between text-sm">
          <span className="text-gray-600">Base rate (KES {dailyRateKES.toLocaleString()} × {hireDays} days)</span>
          <span className="font-medium">KES {baseTotal.toLocaleString()}</span>
        </div>
        <div className="flex justify-between text-sm">
          <span className="text-gray-600">Mobilisation</span>
          <span className="font-medium">KES {mobilisationBaseKES.toLocaleString()}</span>
        </div>
        <div className="flex justify-between text-sm">
          <span className="text-gray-600">Demobilisation</span>
          <span className="font-medium">KES {demobilisationBaseKES.toLocaleString()}</span>
        </div>
        {fuelArrangement === "owner-provided" && (
          <div className="flex justify-between text-sm">
            <span className="text-gray-600">Fuel surcharge ({dailyHours}hrs/day × {hireDays} days)</span>
            <span className="font-medium">KES {(FUEL_SURCHARGE * hireDays).toLocaleString()}</span>
          </div>
        )}
        <div className="flex justify-between text-sm">
          <span className="text-gray-600">Platform fee (7%)</span>
          <span className="font-medium">KES {platformFee.toLocaleString()}</span>
        </div>
        <div className="border-t border-gray-200 pt-2 flex justify-between">
          <span className="font-bold text-gray-900">Estimated Total</span>
          <span className="font-bold text-amber-600 text-lg">KES {grandTotal.toLocaleString()}</span>
        </div>
      </div>

      <button
        type="submit"
        className="w-full py-3 bg-amber-500 text-white font-semibold rounded-xl hover:bg-amber-600 transition-colors"
      >
        Submit Project Enquiry
      </button>
    </form>
  );
}
