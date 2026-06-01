"use client";

import { useState } from "react";

export interface PricingSuggestion {
  id: string;
  assetId: string;
  assetName: string;
  durationType: "hourly" | "daily" | "weekly" | "monthly";
  currentRate: number;
  suggestedRate: number;
  marketMin: number;
  marketAvg: number;
  marketMax: number;
  recommendation: "INCREASE" | "DECREASE" | "MAINTAIN";
  confidenceScore: number;
  reasons: string[];
  dismissed?: boolean;
  applied?: boolean;
}

const DURATION_LABELS: Record<string, string> = {
  hourly: "Per Hour",
  daily: "Per Day",
  weekly: "Per Week",
  monthly: "Per Month",
};

const REC_STYLES = {
  INCREASE: { bg: "bg-[#2ECC8A]/10", text: "text-[#2ECC8A]" },
  DECREASE: { bg: "bg-[#E05252]/10", text: "text-[#E05252]" },
  MAINTAIN: { bg: "bg-amber-500/10", text: "text-amber-400" },
};

function ConfidenceCircle({ score }: { score: number }) {
  const r = 18;
  const circ = 2 * Math.PI * r;
  const dash = (score / 100) * circ;
  const color = score >= 75 ? "#2ECC8A" : score >= 50 ? "#E8922A" : "#E05252";
  return (
    <div className="relative w-12 h-12 flex items-center justify-center">
      <svg width="48" height="48" className="absolute -rotate-90">
        <circle cx="24" cy="24" r={r} fill="none" stroke="#1e2d42" strokeWidth="4" />
        <circle cx="24" cy="24" r={r} fill="none" stroke={color} strokeWidth="4"
          strokeDasharray={`${dash} ${circ}`} strokeLinecap="round" />
      </svg>
      <span className="text-xs font-bold text-white z-10">{score}%</span>
    </div>
  );
}

function MarketBar({ min, avg, max, current, suggested }: {
  min: number; avg: number; max: number; current: number; suggested: number;
}) {
  const range = max - min || 1;
  const p = (v: number) => Math.max(0, Math.min(100, ((v - min) / range) * 100));
  return (
    <div className="mt-3">
      <div className="flex justify-between text-xs text-[#7088A8] mb-1">
        <span>KES {min.toLocaleString()}</span>
        <span>KES {avg.toLocaleString()} avg</span>
        <span>KES {max.toLocaleString()}</span>
      </div>
      <div className="relative h-2 bg-[#1e2d42] rounded-full">
        <div className="absolute h-2 rounded-full bg-gradient-to-r from-[#2ECC8A]/30 to-[#E8922A]/30"
          style={{ left: `${p(min)}%`, width: `${p(max) - p(min)}%` }} />
        <div className="absolute w-2 h-4 -top-1 rounded-sm bg-[#7088A8] border border-[#141D2B]"
          style={{ left: `${p(current)}%`, transform: "translateX(-50%)" }} />
        <div className="absolute w-2 h-4 -top-1 rounded-sm bg-[#E8922A] border border-[#141D2B]"
          style={{ left: `${p(suggested)}%`, transform: "translateX(-50%)" }}
          className="absolute w-2 h-4 -top-1 rounded-sm bg-[#E8922A] border border-[#141D2B] transition-all duration-500" />
      </div>
      <div className="flex gap-3 mt-2 text-xs">
        <span className="flex items-center gap-1 text-[#7088A8]"><span className="w-2 h-2 rounded-sm bg-[#7088A8] inline-block" />Current</span>
        <span className="flex items-center gap-1 text-[#E8922A]"><span className="w-2 h-2 rounded-sm bg-[#E8922A] inline-block" />Suggested</span>
      </div>
    </div>
  );
}

export interface PricingSuggestionCardProps {
  suggestion: PricingSuggestion;
  onApply: (id: string) => void;
  onCustomRate: (id: string, rate: number) => void;
  onDismiss: (id: string) => void;
}

export default function PricingSuggestionCard({ suggestion, onApply, onCustomRate, onDismiss }: PricingSuggestionCardProps) {
  const [showCustom, setShowCustom] = useState(false);
  const [customVal, setCustomVal] = useState("");
  const [expanded, setExpanded] = useState(false);
  const rec = REC_STYLES[suggestion.recommendation];
  const diff = suggestion.suggestedRate - suggestion.currentRate;
  const pct = ((diff / suggestion.currentRate) * 100).toFixed(1);

  return (
    <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-5 flex flex-col gap-4">
      <div className="flex items-start justify-between">
        <div>
          <h3 className="font-semibold text-white">{suggestion.assetName}</h3>
          <span className="text-xs text-[#7088A8]">{DURATION_LABELS[suggestion.durationType]}</span>
        </div>
        <div className="flex items-center gap-2">
          <span className={`text-xs font-bold px-2 py-1 rounded-full ${rec.bg} ${rec.text}`}>
            {suggestion.recommendation}
          </span>
          <ConfidenceCircle score={suggestion.confidenceScore} />
        </div>
      </div>

      <div className="flex items-end gap-4">
        <div>
          <p className="text-xs text-[#7088A8]">Current</p>
          <p className="text-2xl font-bold text-white">KES {suggestion.currentRate.toLocaleString()}</p>
        </div>
        <div className="text-[#7088A8] mb-1">→</div>
        <div>
          <p className="text-xs text-[#7088A8]">Suggested</p>
          <p className="text-2xl font-bold text-[#E8922A]">KES {suggestion.suggestedRate.toLocaleString()}</p>
        </div>
        <span className={`ml-auto text-sm font-semibold ${diff >= 0 ? "text-[#2ECC8A]" : "text-[#E05252]"}`}>
          {diff >= 0 ? "+" : ""}{pct}%
        </span>
      </div>

      <MarketBar
        min={suggestion.marketMin} avg={suggestion.marketAvg} max={suggestion.marketMax}
        current={suggestion.currentRate} suggested={suggestion.suggestedRate}
      />

      <button onClick={() => setExpanded(!expanded)}
        className="text-xs text-[#7088A8] hover:text-white flex items-center gap-1 transition-colors">
        {expanded ? "▲" : "▼"} {suggestion.reasons.length} Contributing Factors
      </button>

      {expanded && (
        <ul className="space-y-1 border-l-2 border-[#E8922A]/30 pl-3">
          {suggestion.reasons.map((r, i) => (
            <li key={i} className="text-xs text-[#7088A8]">{r}</li>
          ))}
        </ul>
      )}

      {showCustom && (
        <div className="flex gap-2">
          <input type="number" value={customVal} onChange={(e) => setCustomVal(e.target.value)}
            placeholder="Custom rate (KES)"
            className="flex-1 bg-[#080C12] border border-[#1e2d42] rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-[#E8922A]" />
          <button onClick={() => {
            const r = parseFloat(customVal);
            if (!isNaN(r) && r > 0) { onCustomRate(suggestion.id, r); setShowCustom(false); }
          }} className="bg-[#E8922A] text-black text-sm font-semibold px-4 rounded-lg hover:bg-amber-400 transition">Set</button>
          <button onClick={() => setShowCustom(false)} className="text-[#7088A8] text-sm px-2">✕</button>
        </div>
      )}

      {suggestion.applied ? (
        <div className="text-center text-sm text-[#2ECC8A] font-medium">✓ Rate Applied</div>
      ) : (
        <div className="flex gap-2 flex-wrap">
          <button onClick={() => onApply(suggestion.id)}
            className="flex-1 bg-[#E8922A] text-black text-sm font-semibold px-3 py-2 rounded-lg hover:bg-amber-400 transition">
            Apply Suggested Rate
          </button>
          <button onClick={() => setShowCustom(!showCustom)}
            className="flex-1 bg-[#1e2d42] text-white text-sm font-semibold px-3 py-2 rounded-lg hover:bg-[#243447] transition">
            Set Custom Rate
          </button>
          <button onClick={() => onDismiss(suggestion.id)}
            className="text-[#7088A8] text-sm px-3 py-2 rounded-lg hover:text-white hover:bg-[#1e2d42] transition">
            Dismiss
          </button>
        </div>
      )}
    </div>
  );
}
