"use client";

import { useState } from "react";

interface InsuranceProduct {
  id: string;
  name: string;
  fullName: string;
  description: string;
  dailyPremiumKES: number;
  coverageKES: number;
}

const PRODUCTS: InsuranceProduct[] = [
  {
    id: "cdw",
    name: "CDW",
    fullName: "Collision Damage Waiver",
    description: "Covers damage to the rented vehicle in case of accident or collision. Excess applies.",
    dailyPremiumKES: 500,
    coverageKES: 1500000,
  },
  {
    id: "tpl",
    name: "TPL",
    fullName: "Third Party Liability",
    description: "Covers damage or injury caused to third parties. Mandatory for public roads.",
    dailyPremiumKES: 300,
    coverageKES: 5000000,
  },
];

interface InsuranceAddonProps {
  hireDays: number;
  onTotalChange?: (addedKES: number) => void;
}

export default function InsuranceAddon({ hireDays, onTotalChange }: InsuranceAddonProps) {
  const [selected, setSelected] = useState<Set<string>>(new Set(["tpl"]));

  const toggle = (id: string) => {
    setSelected((prev) => {
      const next = new Set(prev);
      next.has(id) ? next.delete(id) : next.add(id);
      const total = PRODUCTS.filter((p) => next.has(p.id)).reduce((s, p) => s + p.dailyPremiumKES * hireDays, 0);
      onTotalChange?.(total);
      return next;
    });
  };

  const selectedTotal = PRODUCTS.filter((p) => selected.has(p.id)).reduce((s, p) => s + p.dailyPremiumKES * hireDays, 0);

  return (
    <div className="space-y-3">
      <h3 className="font-semibold text-gray-900">Insurance Add-ons</h3>
      {PRODUCTS.map((product) => {
        const isSelected = selected.has(product.id);
        return (
          <div
            key={product.id}
            className={`border rounded-xl p-4 cursor-pointer transition-all ${
              isSelected ? "border-amber-400 bg-amber-50" : "border-gray-200 bg-white hover:border-gray-300"
            }`}
            onClick={() => toggle(product.id)}
          >
            <div className="flex items-start justify-between">
              <div className="flex items-start gap-3">
                <div
                  className={`w-5 h-5 rounded flex-shrink-0 border-2 flex items-center justify-center mt-0.5 ${
                    isSelected ? "bg-amber-500 border-amber-500" : "border-gray-300"
                  }`}
                >
                  {isSelected && <span className="text-white text-xs">✓</span>}
                </div>
                <div>
                  <div className="flex items-center gap-2">
                    <span className="font-semibold text-gray-900">{product.fullName}</span>
                    <span className="text-xs px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded font-mono">{product.name}</span>
                  </div>
                  <p className="text-sm text-gray-500 mt-0.5">{product.description}</p>
                  <p className="text-xs text-gray-400 mt-1">Coverage up to KES {product.coverageKES.toLocaleString()}</p>
                </div>
              </div>
              <div className="text-right flex-shrink-0 ml-4">
                <p className="font-bold text-gray-900">KES {product.dailyPremiumKES.toLocaleString()}</p>
                <p className="text-xs text-gray-400">/day</p>
                {hireDays > 1 && (
                  <p className="text-xs text-amber-600 font-medium mt-1">
                    KES {(product.dailyPremiumKES * hireDays).toLocaleString()} total
                  </p>
                )}
              </div>
            </div>
          </div>
        );
      })}
      {selectedTotal > 0 && (
        <div className="flex justify-between text-sm font-medium bg-amber-50 border border-amber-200 rounded-lg px-4 py-2">
          <span className="text-amber-800">Insurance Total ({hireDays} days)</span>
          <span className="text-amber-800">KES {selectedTotal.toLocaleString()}</span>
        </div>
      )}
    </div>
  );
}
