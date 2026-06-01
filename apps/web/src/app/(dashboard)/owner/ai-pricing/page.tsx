"use client";

import { useState } from "react";

interface PricingSuggestion {
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

const RECOMMENDATION_STYLES: Record<string, { bg: string; text: string; label: string }> = {
  INCREASE: { bg: "bg-[#2ECC8A]/10", text: "text-[#2ECC8A]", label: "INCREASE" },
  DECREASE: { bg: "bg-[#E05252]/10", text: "text-[#E05252]", label: "DECREASE" },
  MAINTAIN: { bg: "bg-amber-500/10", text: "text-amber-400", label: "MAINTAIN" },
};

function ConfidenceCircle({ score }: { score: number }) {
  const r = 20;
  const circ = 2 * Math.PI * r;
  const dash = (score / 100) * circ;
  const color = score >= 75 ? "#2ECC8A" : score >= 50 ? "#E8922A" : "#E05252";
  return (
    <div className="flex flex-col items-center gap-1">
      <svg width="52" height="52" className="-rotate-90">
        <circle cx="26" cy="26" r={r} fill="none" stroke="#1e2d42" strokeWidth="5" />
        <circle
          cx="26"
          cy="26"
          r={r}
          fill="none"
          stroke={color}
          strokeWidth="5"
          strokeDasharray={`${dash} ${circ}`}
          strokeLinecap="round"
        />
      </svg>
      <span className="text-xs text-[#7088A8] -mt-8 relative z-10" style={{ lineHeight: "52px", position: "absolute", marginTop: 0 }}></span>
      <div className="relative" style={{ marginTop: "-44px" }}>
        <span className="text-sm font-bold text-white">{score}%</span>
      </div>
    </div>
  );
}

function MarketRangeBar({
  min, avg, max, current, suggested,
}: {
  min: number; avg: number; max: number; current: number; suggested: number;
}) {
  const range = max - min || 1;
  const pos = (v: number) => Math.max(0, Math.min(100, ((v - min) / range) * 100));
  return (
    <div className="mt-3">
      <div className="flex justify-between text-xs text-[#7088A8] mb-1">
        <span>Min KES {min.toLocaleString()}</span>
        <span>Avg KES {avg.toLocaleString()}</span>
        <span>Max KES {max.toLocaleString()}</span>
      </div>
      <div className="relative h-2 bg-[#1e2d42] rounded-full">
        <div
          className="absolute h-2 bg-gradient-to-r from-[#2ECC8A]/40 to-[#E8922A]/40 rounded-full"
          style={{ left: `${pos(min)}%`, width: `${pos(max) - pos(min)}%` }}
        />
        <div
          className="absolute w-2 h-4 -top-1 rounded-sm bg-[#7088A8] border border-[#141D2B]"
          style={{ left: `${pos(current)}%`, transform: "translateX(-50%)" }}
          title={`Current: KES ${current.toLocaleString()}`}
        />
        <div
          className="absolute w-2 h-4 -top-1 rounded-sm bg-[#E8922A] border border-[#141D2B]"
          style={{ left: `${pos(suggested)}%`, transform: "translateX(-50%)" }}
          title={`Suggested: KES ${suggested.toLocaleString()}`}
        />
      </div>
      <div className="flex gap-4 mt-1">
        <span className="flex items-center gap-1 text-xs text-[#7088A8]">
          <span className="w-2 h-2 rounded-sm bg-[#7088A8] inline-block" /> Current
        </span>
        <span className="flex items-center gap-1 text-xs text-[#E8922A]">
          <span className="w-2 h-2 rounded-sm bg-[#E8922A] inline-block" /> Suggested
        </span>
      </div>
    </div>
  );
}

function PricingSuggestionCard({
  suggestion,
  onApply,
  onCustomRate,
  onDismiss,
}: {
  suggestion: PricingSuggestion;
  onApply: (id: string) => void;
  onCustomRate: (id: string, rate: number) => void;
  onDismiss: (id: string) => void;
}) {
  const [showCustom, setShowCustom] = useState(false);
  const [customVal, setCustomVal] = useState("");
  const [expanded, setExpanded] = useState(false);
  const rec = RECOMMENDATION_STYLES[suggestion.recommendation];
  const diff = suggestion.suggestedRate - suggestion.currentRate;
  const pct = ((diff / suggestion.currentRate) * 100).toFixed(1);

  if (suggestion.dismissed) return null;

  return (
    <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-5 flex flex-col gap-4">
      <div className="flex items-start justify-between">
        <div>
          <h3 className="font-semibold text-white">{suggestion.assetName}</h3>
          <span className="text-xs text-[#7088A8]">{DURATION_LABELS[suggestion.durationType]}</span>
        </div>
        <div className="flex items-center gap-2">
          <span className={`text-xs font-bold px-2 py-1 rounded-full ${rec.bg} ${rec.text}`}>
            {rec.label}
          </span>
          <ConfidenceCircle score={suggestion.confidenceScore} />
        </div>
      </div>

      <div className="flex items-end gap-4">
        <div>
          <p className="text-xs text-[#7088A8]">Current Rate</p>
          <p className="text-2xl font-bold text-white">KES {suggestion.currentRate.toLocaleString()}</p>
        </div>
        <div className="text-[#7088A8] text-xl mb-1">→</div>
        <div>
          <p className="text-xs text-[#7088A8]">Suggested Rate</p>
          <p className="text-2xl font-bold text-[#E8922A]">KES {suggestion.suggestedRate.toLocaleString()}</p>
        </div>
        <div className="ml-auto">
          <span className={`text-sm font-semibold ${diff >= 0 ? "text-[#2ECC8A]" : "text-[#E05252]"}`}>
            {diff >= 0 ? "+" : ""}{pct}%
          </span>
        </div>
      </div>

      <MarketRangeBar
        min={suggestion.marketMin}
        avg={suggestion.marketAvg}
        max={suggestion.marketMax}
        current={suggestion.currentRate}
        suggested={suggestion.suggestedRate}
      />

      <button
        onClick={() => setExpanded(!expanded)}
        className="text-xs text-[#7088A8] hover:text-white flex items-center gap-1 transition-colors"
      >
        {expanded ? "▲" : "▼"} {suggestion.reasons.length} Factors
      </button>
      {expanded && (
        <ul className="list-disc list-inside space-y-1">
          {suggestion.reasons.map((r, i) => (
            <li key={i} className="text-xs text-[#7088A8]">{r}</li>
          ))}
        </ul>
      )}

      {showCustom && (
        <div className="flex gap-2">
          <input
            type="number"
            value={customVal}
            onChange={(e) => setCustomVal(e.target.value)}
            placeholder="Enter custom rate (KES)"
            className="flex-1 bg-[#080C12] border border-[#1e2d42] rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-[#E8922A]"
          />
          <button
            onClick={() => {
              const r = parseFloat(customVal);
              if (!isNaN(r) && r > 0) {
                onCustomRate(suggestion.id, r);
                setShowCustom(false);
              }
            }}
            className="bg-[#E8922A] text-black text-sm font-semibold px-4 py-2 rounded-lg hover:bg-amber-400 transition"
          >
            Set
          </button>
          <button onClick={() => setShowCustom(false)} className="text-[#7088A8] text-sm px-2">Cancel</button>
        </div>
      )}

      {suggestion.applied ? (
        <div className="text-center text-sm text-[#2ECC8A] font-medium py-1">✓ Rate Applied</div>
      ) : (
        <div className="flex gap-2 flex-wrap">
          <button
            onClick={() => onApply(suggestion.id)}
            className="flex-1 bg-[#E8922A] text-black text-sm font-semibold px-3 py-2 rounded-lg hover:bg-amber-400 transition"
          >
            Apply Suggested Rate
          </button>
          <button
            onClick={() => setShowCustom(!showCustom)}
            className="flex-1 bg-[#1e2d42] text-white text-sm font-semibold px-3 py-2 rounded-lg hover:bg-[#243447] transition"
          >
            Set Custom Rate
          </button>
          <button
            onClick={() => onDismiss(suggestion.id)}
            className="text-[#7088A8] text-sm px-3 py-2 rounded-lg hover:text-white hover:bg-[#1e2d42] transition"
          >
            Dismiss
          </button>
        </div>
      )}
    </div>
  );
}

function LoadingSkeleton() {
  return (
    <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
      {[...Array(6)].map((_, i) => (
        <div key={i} className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-5 animate-pulse">
          <div className="h-4 bg-[#1e2d42] rounded w-2/3 mb-2" />
          <div className="h-3 bg-[#1e2d42] rounded w-1/3 mb-4" />
          <div className="h-8 bg-[#1e2d42] rounded w-1/2 mb-2" />
          <div className="h-2 bg-[#1e2d42] rounded w-full mb-4" />
          <div className="flex gap-2">
            <div className="h-9 bg-[#1e2d42] rounded flex-1" />
            <div className="h-9 bg-[#1e2d42] rounded flex-1" />
          </div>
        </div>
      ))}
    </div>
  );
}

export default function AIPricingPage() {
  const [suggestions, setSuggestions] = useState<PricingSuggestion[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [stats, setStats] = useState({ avgConfidence: 0, acceptedThisMonth: 0, revenueImpact: 0 });

  const generateSuggestions = async () => {
    setLoading(true);
    setError(null);
    try {
      const res = await fetch("/api/v1/ai/pricing/suggestions", { method: "POST" });
      if (!res.ok) throw new Error(`Failed to generate suggestions (${res.status})`);
      const data = await res.json();
      setSuggestions(data.suggestions ?? []);
      setStats(data.stats ?? { avgConfidence: 0, acceptedThisMonth: 0, revenueImpact: 0 });
    } catch (err: any) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  const handleApply = async (id: string) => {
    try {
      await fetch(`/api/v1/ai/pricing/suggestions/${id}/apply`, { method: "POST" });
      setSuggestions((prev) =>
        prev.map((s) => (s.id === id ? { ...s, applied: true } : s))
      );
      setStats((prev) => ({ ...prev, acceptedThisMonth: prev.acceptedThisMonth + 1 }));
    } catch {
      /* ignore */
    }
  };

  const handleCustomRate = async (id: string, rate: number) => {
    try {
      await fetch(`/api/v1/ai/pricing/suggestions/${id}/custom`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ rate }),
      });
      setSuggestions((prev) =>
        prev.map((s) => (s.id === id ? { ...s, applied: true, suggestedRate: rate } : s))
      );
    } catch {
      /* ignore */
    }
  };

  const handleDismiss = async (id: string) => {
    try {
      await fetch(`/api/v1/ai/pricing/suggestions/${id}/dismiss`, { method: "POST" });
      setSuggestions((prev) =>
        prev.map((s) => (s.id === id ? { ...s, dismissed: true } : s))
      );
    } catch {
      /* ignore */
    }
  };

  const visible = suggestions.filter((s) => !s.dismissed);

  return (
    <div className="min-h-screen bg-[#080C12] p-6">
      <div className="max-w-7xl mx-auto">
        {/* Header */}
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
          <div>
            <h1 className="text-2xl font-bold text-white">AI Pricing Intelligence</h1>
            <p className="text-[#7088A8] mt-1">Market-optimised rates for your fleet</p>
          </div>
          <button
            onClick={generateSuggestions}
            disabled={loading}
            className="bg-[#E8922A] text-black font-semibold px-6 py-3 rounded-xl hover:bg-amber-400 transition disabled:opacity-50 flex items-center gap-2"
          >
            {loading ? (
              <><span className="w-4 h-4 border-2 border-black/30 border-t-black rounded-full animate-spin inline-block" /> Generating…</>
            ) : (
              "✦ Generate Suggestions"
            )}
          </button>
        </div>

        {/* Stats bar */}
        {suggestions.length > 0 && (
          <div className="grid grid-cols-3 gap-4 mb-8">
            <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-4 text-center">
              <p className="text-2xl font-bold text-white">{stats.avgConfidence}%</p>
              <p className="text-xs text-[#7088A8] mt-1">Avg Confidence</p>
            </div>
            <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-4 text-center">
              <p className="text-2xl font-bold text-[#2ECC8A]">{stats.acceptedThisMonth}</p>
              <p className="text-xs text-[#7088A8] mt-1">Accepted This Month</p>
            </div>
            <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-4 text-center">
              <p className="text-2xl font-bold text-[#E8922A]">
                KES {stats.revenueImpact.toLocaleString()}
              </p>
              <p className="text-xs text-[#7088A8] mt-1">Est. Revenue Impact</p>
            </div>
          </div>
        )}

        {/* Error */}
        {error && (
          <div className="bg-[#E05252]/10 border border-[#E05252]/30 rounded-xl p-4 mb-6 text-[#E05252] text-sm">
            {error}
          </div>
        )}

        {/* Content */}
        {loading ? (
          <LoadingSkeleton />
        ) : visible.length === 0 ? (
          <div className="flex flex-col items-center justify-center py-24 text-center">
            <div className="w-16 h-16 rounded-full bg-[#141D2B] flex items-center justify-center text-3xl mb-4">✦</div>
            <h2 className="text-white font-semibold text-lg mb-2">No Suggestions Yet</h2>
            <p className="text-[#7088A8] max-w-sm">
              Click "Generate Suggestions" to let YardAI analyse market data and recommend optimal rates for your fleet.
            </p>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            {visible.map((s) => (
              <PricingSuggestionCard
                key={s.id}
                suggestion={s}
                onApply={handleApply}
                onCustomRate={handleCustomRate}
                onDismiss={handleDismiss}
              />
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
