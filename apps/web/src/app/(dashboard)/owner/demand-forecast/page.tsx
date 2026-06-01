"use client";

import { useEffect, useState } from "react";

interface DemandSpike {
  id: string;
  periodName: string;
  category: string;
  county: string;
  demandIndex: number;
  startDate: string;
  endDate: string;
  drivers: string[];
  recommendedRateIncrease: number;
  recommendedAction: string;
}

interface InvestmentSignal {
  id: string;
  recommendation: string;
  assetType: string;
  county: string;
  quantity: number;
  projectedRevenue: number;
  confidence: number;
  startDate: string;
  endDate: string;
}

interface MonthDemand {
  month: string;
  year: number;
  demandIndex: number;
}

interface CategoryDemand {
  category: string;
  county: string;
  demandIndex: number;
  avgRate: number;
  bookingVolume: number;
}

const INTENSITY_COLORS: Record<string, string> = {
  high: "border-[#E05252] bg-[#E05252]/5",
  medium: "border-amber-500 bg-amber-500/5",
  low: "border-[#2ECC8A] bg-[#2ECC8A]/5",
};

const DRIVER_COLORS = ["bg-purple-500/20 text-purple-300", "bg-blue-500/20 text-blue-300", "bg-teal-500/20 text-teal-300", "bg-pink-500/20 text-pink-300"];

function getIntensity(index: number): "high" | "medium" | "low" {
  if (index > 1.5) return "high";
  if (index > 1.2) return "medium";
  return "low";
}

function heatColor(index: number) {
  if (index > 1.7) return "bg-[#E05252]";
  if (index > 1.5) return "bg-red-500";
  if (index > 1.3) return "bg-amber-500";
  if (index > 1.1) return "bg-amber-400";
  if (index > 0.9) return "bg-[#2ECC8A]/60";
  return "bg-[#1e2d42]";
}

function SpikeCard({ spike, onAction }: { spike: DemandSpike; onAction: (id: string) => void }) {
  const intensity = getIntensity(spike.demandIndex);
  return (
    <div className={`rounded-xl border p-5 ${INTENSITY_COLORS[intensity]}`}>
      <div className="flex items-start justify-between gap-3">
        <div>
          <h3 className="font-semibold text-white">{spike.periodName}</h3>
          <p className="text-xs text-[#7088A8] mt-0.5">
            {spike.county} · {spike.category}
          </p>
        </div>
        <div className="text-right shrink-0">
          <p className="text-lg font-bold text-white">{spike.demandIndex.toFixed(2)}x</p>
          <p className="text-xs text-[#7088A8]">demand index</p>
        </div>
      </div>
      <div className="flex flex-wrap gap-1 mt-3">
        {spike.drivers.map((d, i) => (
          <span key={d} className={`text-xs px-2 py-0.5 rounded-full ${DRIVER_COLORS[i % DRIVER_COLORS.length]}`}>{d}</span>
        ))}
      </div>
      <p className="text-xs text-[#7088A8] mt-3">{spike.recommendedAction}</p>
      <button
        onClick={() => onAction(spike.id)}
        className="mt-3 w-full bg-[#E8922A] text-black text-sm font-semibold py-2 rounded-lg hover:bg-amber-400 transition"
      >
        Increase rates by {spike.recommendedRateIncrease}%
      </button>
    </div>
  );
}

function InvestmentCard({ signal }: { signal: InvestmentSignal }) {
  const confColor = signal.confidence >= 75 ? "text-[#2ECC8A]" : signal.confidence >= 50 ? "text-amber-400" : "text-[#E05252]";
  return (
    <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-5">
      <div className="flex items-start justify-between gap-3">
        <p className="text-sm text-white">{signal.recommendation}</p>
        <span className={`text-xs font-bold px-2 py-1 rounded-full bg-[#1e2d42] shrink-0 ${confColor}`}>
          {signal.confidence}% confident
        </span>
      </div>
      <div className="mt-3 flex items-center justify-between">
        <span className="text-xs text-[#7088A8]">
          {signal.startDate} – {signal.endDate}
        </span>
        <span className="text-sm font-semibold text-[#2ECC8A]">
          +KES {signal.projectedRevenue.toLocaleString()}
        </span>
      </div>
    </div>
  );
}

const MONTHS = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

export default function DemandForecastPage() {
  const [spikes, setSpikes] = useState<DemandSpike[]>([]);
  const [signals, setSignals] = useState<InvestmentSignal[]>([]);
  const [monthlyData, setMonthlyData] = useState<MonthDemand[]>([]);
  const [categoryData, setCategoryData] = useState<CategoryDemand[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const load = async () => {
      try {
        const [spikesRes, signalsRes, calRes, catRes] = await Promise.all([
          fetch("/api/v1/ai/demand/spikes"),
          fetch("/api/v1/ai/demand/investment-signals"),
          fetch("/api/v1/ai/demand/monthly"),
          fetch("/api/v1/ai/demand/categories"),
        ]);
        const [s, inv, cal, cat] = await Promise.all([
          spikesRes.json(),
          signalsRes.json(),
          calRes.json(),
          catRes.json(),
        ]);
        setSpikes(s.spikes ?? []);
        setSignals(inv.signals ?? []);
        setMonthlyData(cal.months ?? []);
        setCategoryData(cat.categories ?? []);
      } catch (err: any) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };
    load();
  }, []);

  const handleSpikeAction = async (id: string) => {
    try {
      await fetch(`/api/v1/ai/demand/spikes/${id}/apply`, { method: "POST" });
    } catch { /* ignore */ }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-[#080C12] p-6">
        <div className="max-w-7xl mx-auto animate-pulse space-y-8">
          <div className="h-8 bg-[#141D2B] rounded w-64" />
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {[...Array(3)].map((_, i) => <div key={i} className="h-40 bg-[#141D2B] rounded-xl" />)}
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-[#080C12] p-6">
      <div className="max-w-7xl mx-auto space-y-10">
        <div>
          <h1 className="text-2xl font-bold text-white">Demand Forecasting</h1>
          <p className="text-[#7088A8] mt-1">AI-powered demand predictions to maximise fleet revenue</p>
        </div>

        {error && (
          <div className="bg-[#E05252]/10 border border-[#E05252]/30 rounded-xl p-4 text-[#E05252] text-sm">{error}</div>
        )}

        {/* Demand Spikes */}
        <section>
          <h2 className="text-lg font-semibold text-white mb-4">🔥 Upcoming Demand Spikes</h2>
          {spikes.length === 0 ? (
            <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-8 text-center text-[#7088A8]">
              No upcoming demand spikes detected.
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              {spikes.map((s) => <SpikeCard key={s.id} spike={s} onAction={handleSpikeAction} />)}
            </div>
          )}
        </section>

        {/* Investment Signals */}
        <section>
          <h2 className="text-lg font-semibold text-white mb-4">💡 Fleet Investment Signals</h2>
          {signals.length === 0 ? (
            <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-8 text-center text-[#7088A8]">
              No investment signals at this time.
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {signals.map((s) => <InvestmentCard key={s.id} signal={s} />)}
            </div>
          )}
        </section>

        {/* Heatmap */}
        <section>
          <h2 className="text-lg font-semibold text-white mb-4">📅 Monthly Demand Heatmap</h2>
          <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-6">
            {monthlyData.length === 0 ? (
              <p className="text-center text-[#7088A8]">No monthly data available.</p>
            ) : (
              <div className="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-12 gap-2">
                {MONTHS.map((m, i) => {
                  const md = monthlyData.find((d) => d.month === m);
                  const idx = md?.demandIndex ?? 1;
                  return (
                    <div key={m} className={`rounded-lg p-3 text-center ${heatColor(idx)}`}>
                      <p className="text-xs font-semibold text-white">{m}</p>
                      <p className="text-xs text-white/80 mt-0.5">{idx.toFixed(2)}x</p>
                    </div>
                  );
                })}
              </div>
            )}
            <div className="flex gap-4 flex-wrap mt-4 text-xs text-[#7088A8]">
              <span className="flex items-center gap-1"><span className="w-3 h-3 rounded bg-[#E05252] inline-block" /> Very High (&gt;1.7x)</span>
              <span className="flex items-center gap-1"><span className="w-3 h-3 rounded bg-amber-500 inline-block" /> High (1.3–1.7x)</span>
              <span className="flex items-center gap-1"><span className="w-3 h-3 rounded bg-[#2ECC8A]/60 inline-block" /> Normal (0.9–1.1x)</span>
              <span className="flex items-center gap-1"><span className="w-3 h-3 rounded bg-[#1e2d42] inline-block" /> Low (&lt;0.9x)</span>
            </div>
          </div>
        </section>

        {/* Category Table */}
        <section>
          <h2 className="text-lg font-semibold text-white mb-4">📊 Category Demand Comparison</h2>
          <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] overflow-x-auto">
            <table className="w-full text-sm">
              <thead>
                <tr className="border-b border-[#1e2d42]">
                  <th className="text-left p-4 text-[#7088A8] font-medium">Category</th>
                  <th className="text-left p-4 text-[#7088A8] font-medium">County</th>
                  <th className="text-right p-4 text-[#7088A8] font-medium">Demand Index</th>
                  <th className="text-right p-4 text-[#7088A8] font-medium">Avg Rate (KES)</th>
                  <th className="text-right p-4 text-[#7088A8] font-medium">Volume</th>
                </tr>
              </thead>
              <tbody>
                {categoryData.length === 0 ? (
                  <tr><td colSpan={5} className="p-8 text-center text-[#7088A8]">No category data available.</td></tr>
                ) : (
                  categoryData.map((c, i) => (
                    <tr key={i} className="border-b border-[#1e2d42] hover:bg-[#1e2d42]/50 transition">
                      <td className="p-4 text-white">{c.category}</td>
                      <td className="p-4 text-[#7088A8]">{c.county}</td>
                      <td className="p-4 text-right">
                        <span className={`font-semibold ${c.demandIndex > 1.3 ? "text-[#E8922A]" : "text-white"}`}>
                          {c.demandIndex.toFixed(2)}x
                        </span>
                      </td>
                      <td className="p-4 text-right text-white">{c.avgRate.toLocaleString()}</td>
                      <td className="p-4 text-right text-[#7088A8]">{c.bookingVolume}</td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </div>
  );
}
