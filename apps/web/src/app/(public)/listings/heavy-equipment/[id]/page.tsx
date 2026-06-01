"use client";

import { useState } from "react";
import ProjectBookingForm from "@/components/booking/ProjectBookingForm";

const MACHINE = {
  id: "he-001",
  name: "CAT 320 Hydraulic Excavator",
  make: "Caterpillar",
  model: "320",
  year: 2019,
  dailyRateKES: 45000,
  minimumHireDays: 5,
  specs: {
    operatingWeightKg: 20000,
    bucketCapacityM3: 0.9,
    enginePower: "121 kW",
    fuelConsumptionLph: 22,
    maxDigDepthM: 6.7,
    transportMethod: "Low-loader flatbed",
    operatorCertRequired: "NITA Plant Operator Class 2",
  },
  mobilisationBaseKES: 25000,
  demobilisationBaseKES: 25000,
};

const BADGES = [
  { label: "NITA Certified", icon: "✅" },
  { label: "Insured", icon: "🛡" },
  { label: "Roadworthy", icon: "🔧" },
];

export default function HeavyEquipmentDetailPage() {
  const [siteLocation, setSiteLocation] = useState("");
  const [mobilisationCost, setMobilisationCost] = useState<number | null>(null);
  const [showBookingForm, setShowBookingForm] = useState(false);

  const estimateMobilisation = () => {
    // Stub: in production this would call a distance API
    const cost = siteLocation.trim().length > 0 ? MACHINE.mobilisationBaseKES + 5000 : null;
    setMobilisationCost(cost);
  };

  return (
    <div className="max-w-5xl mx-auto px-4 py-8 space-y-8">
      {/* Owner Review Banner */}
      <div className="bg-amber-50 border border-amber-300 rounded-xl p-4 flex items-start gap-3">
        <span className="text-amber-500 text-xl">ℹ</span>
        <div>
          <p className="font-semibold text-amber-800">Owner Review Required</p>
          <p className="text-sm text-amber-700 mt-0.5">
            All heavy equipment hire enquiries are reviewed by the asset owner before confirmation. You will receive a
            response within 24 hours of submitting your project details.
          </p>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div className="lg:col-span-2 space-y-6">
          {/* Machine Overview */}
          <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div className="h-64 bg-gray-100 flex items-center justify-center text-6xl">🚜</div>
            <div className="p-6">
              <div className="flex items-start justify-between">
                <div>
                  <h1 className="text-2xl font-bold text-gray-900">{MACHINE.name}</h1>
                  <p className="text-gray-500 text-sm mt-1">{MACHINE.year} · {MACHINE.make} {MACHINE.model}</p>
                </div>
                <div className="text-right">
                  <p className="text-2xl font-bold text-gray-900">KES {MACHINE.dailyRateKES.toLocaleString()}</p>
                  <p className="text-sm text-gray-500">per day</p>
                </div>
              </div>

              <div className="mt-4 flex flex-wrap gap-2">
                {BADGES.map((b) => (
                  <span key={b.label} className="flex items-center gap-1.5 px-3 py-1.5 bg-green-50 border border-green-200 rounded-full text-sm text-green-800 font-medium">
                    {b.icon} {b.label}
                  </span>
                ))}
              </div>

              <div className="mt-4 p-3 bg-blue-50 border border-blue-100 rounded-lg">
                <p className="text-sm text-blue-800">
                  <span className="font-semibold">Minimum hire period:</span> {MACHINE.minimumHireDays} days
                </p>
              </div>
            </div>
          </div>

          {/* Machine Specs */}
          <div className="bg-white rounded-xl border border-gray-200 p-6">
            <h2 className="text-lg font-semibold text-gray-900 mb-4">Machine Specifications</h2>
            <div className="grid grid-cols-2 gap-4">
              {Object.entries({
                "Operating Weight": `${MACHINE.specs.operatingWeightKg.toLocaleString()} kg`,
                "Bucket Capacity": `${MACHINE.specs.bucketCapacityM3} m³`,
                "Engine Power": MACHINE.specs.enginePower,
                "Fuel Consumption": `${MACHINE.specs.fuelConsumptionLph} L/hr`,
                "Max Dig Depth": `${MACHINE.specs.maxDigDepthM} m`,
                "Transport Method": MACHINE.specs.transportMethod,
                "Operator Cert Required": MACHINE.specs.operatorCertRequired,
              }).map(([key, val]) => (
                <div key={key} className="p-3 bg-gray-50 rounded-lg">
                  <p className="text-xs text-gray-500 font-medium">{key}</p>
                  <p className="text-sm font-semibold text-gray-900 mt-0.5">{val}</p>
                </div>
              ))}
            </div>
          </div>

          {/* Mobilisation Cost Calculator */}
          <div className="bg-white rounded-xl border border-gray-200 p-6">
            <h2 className="text-lg font-semibold text-gray-900 mb-4">Mobilisation Cost Calculator</h2>
            <p className="text-sm text-gray-500 mb-4">Enter your site location to estimate mobilisation and demobilisation costs.</p>
            <div className="flex gap-3">
              <input
                type="text"
                placeholder="Enter site location or address..."
                value={siteLocation}
                onChange={(e) => setSiteLocation(e.target.value)}
                className="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm"
              />
              <button
                onClick={estimateMobilisation}
                className="px-4 py-2 bg-amber-500 text-white text-sm rounded-lg hover:bg-amber-600 font-medium whitespace-nowrap"
              >
                Estimate
              </button>
            </div>
            {mobilisationCost !== null && (
              <div className="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div className="flex justify-between text-sm">
                  <span className="text-gray-700">Mobilisation</span>
                  <span className="font-semibold">KES {mobilisationCost.toLocaleString()}</span>
                </div>
                <div className="flex justify-between text-sm mt-1">
                  <span className="text-gray-700">Demobilisation</span>
                  <span className="font-semibold">KES {MACHINE.demobilisationBaseKES.toLocaleString()}</span>
                </div>
                <div className="border-t border-green-200 mt-2 pt-2 flex justify-between">
                  <span className="font-semibold text-gray-900">Total Transport</span>
                  <span className="font-bold text-green-800">KES {(mobilisationCost + MACHINE.demobilisationBaseKES).toLocaleString()}</span>
                </div>
              </div>
            )}
          </div>
        </div>

        {/* Booking Sidebar */}
        <div className="space-y-4">
          <div className="bg-white rounded-xl border border-gray-200 p-5 sticky top-4">
            <h3 className="font-semibold text-gray-900 mb-4">Submit Project Enquiry</h3>
            <button
              onClick={() => setShowBookingForm(true)}
              className="w-full py-3 bg-amber-500 text-white font-semibold rounded-xl hover:bg-amber-600 transition-colors"
            >
              Submit Project Details
            </button>
            <p className="text-xs text-gray-400 mt-3 text-center">No charge until owner confirms</p>
          </div>
        </div>
      </div>

      {/* Project Booking Form Modal */}
      {showBookingForm && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div className="flex items-center justify-between p-6 border-b">
              <h3 className="text-lg font-semibold">Project Booking — {MACHINE.name}</h3>
              <button onClick={() => setShowBookingForm(false)} className="text-gray-400 hover:text-gray-600 text-xl">×</button>
            </div>
            <div className="p-6">
              <ProjectBookingForm
                dailyRateKES={MACHINE.dailyRateKES}
                mobilisationBaseKES={MACHINE.mobilisationBaseKES}
                demobilisationBaseKES={MACHINE.demobilisationBaseKES}
                onClose={() => setShowBookingForm(false)}
              />
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
