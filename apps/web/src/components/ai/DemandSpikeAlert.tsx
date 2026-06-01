"use client";

export interface DemandSpike {
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

const DRIVER_CHIP_COLORS = [
  "bg-purple-500/20 text-purple-300",
  "bg-blue-500/20 text-blue-300",
  "bg-teal-500/20 text-teal-300",
  "bg-pink-500/20 text-pink-300",
  "bg-orange-500/20 text-orange-300",
];

function intensity(index: number) {
  if (index > 1.5) return { border: "border-[#E05252]", bg: "bg-[#E05252]/5", badge: "bg-[#E05252]/20 text-[#E05252]", label: "High Demand" };
  if (index > 1.2) return { border: "border-amber-500", bg: "bg-amber-500/5", badge: "bg-amber-500/20 text-amber-300", label: "Moderate Demand" };
  return { border: "border-[#2ECC8A]", bg: "bg-[#2ECC8A]/5", badge: "bg-[#2ECC8A]/20 text-[#2ECC8A]", label: "Normal Demand" };
}

export interface DemandSpikeAlertProps {
  spike: DemandSpike;
  onAction?: (id: string) => void;
}

export default function DemandSpikeAlert({ spike, onAction }: DemandSpikeAlertProps) {
  const { border, bg, badge, label } = intensity(spike.demandIndex);
  return (
    <div className={`rounded-xl border ${border} ${bg} p-5`}>
      <div className="flex items-start justify-between gap-3 mb-3">
        <div>
          <h3 className="font-semibold text-white">{spike.periodName}</h3>
          <p className="text-xs text-[#7088A8] mt-0.5">{spike.county} · {spike.category}</p>
        </div>
        <div className="text-right shrink-0">
          <p className="text-lg font-bold text-white">{spike.demandIndex.toFixed(2)}x</p>
          <span className={`text-xs font-semibold px-2 py-0.5 rounded-full ${badge}`}>{label}</span>
        </div>
      </div>

      <div className="flex gap-1 flex-wrap mb-3">
        {spike.drivers.map((d, i) => (
          <span key={d} className={`text-xs px-2 py-0.5 rounded-full ${DRIVER_CHIP_COLORS[i % DRIVER_CHIP_COLORS.length]}`}>{d}</span>
        ))}
      </div>

      <p className="text-xs text-[#7088A8] mb-1">{spike.startDate} – {spike.endDate}</p>
      <p className="text-xs text-[#7088A8] mb-3">{spike.recommendedAction}</p>

      {onAction && (
        <button onClick={() => onAction(spike.id)}
          className="w-full bg-[#E8922A] text-black text-sm font-semibold py-2 rounded-lg hover:bg-amber-400 transition">
          Increase rates by {spike.recommendedRateIncrease}%
        </button>
      )}
    </div>
  );
}
