"use client";

import RiskScoreBadge from "./RiskScoreBadge";

export interface FraudFlagCardProps {
  id: string;
  flagType: string;
  userDisplayName: string;
  riskScore: number;
  riskFactors?: string[];
  dateRaised: string;
  onInvestigate: (id: string) => void;
  onFalsePositive: (id: string) => void;
}

const FLAG_ICONS: Record<string, string> = {
  payment_anomaly: "💳",
  identity_mismatch: "🔍",
  velocity_abuse: "⚡",
  device_fingerprint: "📱",
  chargeback_risk: "⚠️",
  kyc_failure: "🧑",
  suspicious_listing: "📍",
};

const FLAG_TYPE_COLORS: Record<string, string> = {
  payment_anomaly: "bg-[#E05252]/20 text-[#E05252]",
  identity_mismatch: "bg-purple-500/20 text-purple-300",
  velocity_abuse: "bg-orange-500/20 text-orange-300",
  device_fingerprint: "bg-blue-500/20 text-blue-300",
  chargeback_risk: "bg-red-600/20 text-red-400",
  kyc_failure: "bg-amber-500/20 text-amber-400",
  suspicious_listing: "bg-pink-500/20 text-pink-300",
};

function timeAgo(dateStr: string): string {
  const ms = Date.now() - new Date(dateStr).getTime();
  const mins = Math.floor(ms / 60000);
  if (mins < 60) return `${mins}m ago`;
  const hrs = Math.floor(mins / 60);
  if (hrs < 24) return `${hrs}h ago`;
  return `${Math.floor(hrs / 24)}d ago`;
}

export default function FraudFlagCard({
  id, flagType, userDisplayName, riskScore, riskFactors = [], dateRaised, onInvestigate, onFalsePositive,
}: FraudFlagCardProps) {
  return (
    <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-4 flex items-center gap-4">
      <div className="w-10 h-10 rounded-xl bg-[#1e2d42] flex items-center justify-center text-xl shrink-0">
        {FLAG_ICONS[flagType] ?? "🚨"}
      </div>

      <div className="flex-1 min-w-0">
        <div className="flex items-center gap-2 flex-wrap">
          <span className={`text-xs px-2 py-0.5 rounded-full capitalize shrink-0 ${FLAG_TYPE_COLORS[flagType] ?? "bg-[#1e2d42] text-[#7088A8]"}`}>
            {flagType.replace(/_/g, " ")}
          </span>
          <span className="text-xs text-[#7088A8]">{timeAgo(dateRaised)}</span>
        </div>
        <p className="text-sm text-white font-medium mt-0.5 truncate">{userDisplayName}</p>
      </div>

      <RiskScoreBadge score={riskScore} factors={riskFactors} size="sm" />

      <div className="flex flex-col gap-1.5 shrink-0">
        <button
          onClick={() => onInvestigate(id)}
          className="text-xs bg-[#E8922A] text-black font-semibold px-3 py-1.5 rounded-lg hover:bg-amber-400 transition"
        >
          Investigate
        </button>
        <button
          onClick={() => onFalsePositive(id)}
          className="text-xs border border-[#1e2d42] text-[#7088A8] px-3 py-1.5 rounded-lg hover:text-white hover:border-[#7088A8] transition"
        >
          False Positive
        </button>
      </div>
    </div>
  );
}
