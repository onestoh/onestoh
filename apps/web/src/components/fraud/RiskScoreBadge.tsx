"use client";

import { useState } from "react";

export interface RiskScoreBadgeProps {
  score: number;
  factors?: string[];
  size?: "sm" | "md";
}

export default function RiskScoreBadge({ score, factors = [], size = "md" }: RiskScoreBadgeProps) {
  const [showTooltip, setShowTooltip] = useState(false);

  const color =
    score < 40 ? { ring: "ring-[#2ECC8A]", bg: "bg-[#2ECC8A]/10", text: "text-[#2ECC8A]" }
    : score < 70 ? { ring: "ring-amber-500", bg: "bg-amber-500/10", text: "text-amber-400" }
    : { ring: "ring-[#E05252]", bg: "bg-[#E05252]/10", text: "text-[#E05252]" };

  const dim = size === "sm" ? "w-8 h-8 text-xs" : "w-10 h-10 text-sm";

  return (
    <div className="relative inline-block">
      <div
        className={`${dim} rounded-full ring-2 ${color.ring} ${color.bg} flex items-center justify-center font-bold ${color.text} cursor-pointer select-none`}
        onMouseEnter={() => setShowTooltip(true)}
        onMouseLeave={() => setShowTooltip(false)}
      >
        {score}
      </div>

      {showTooltip && (
        <div className="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 z-50 w-48 bg-[#141D2B] border border-[#1e2d42] rounded-xl p-3 shadow-xl">
          <p className="text-xs font-semibold text-white mb-1.5">Risk Score: {score}/100</p>
          {factors.length > 0 ? (
            <ul className="space-y-0.5">
              {factors.map((f, i) => (
                <li key={i} className="text-xs text-[#7088A8] flex items-start gap-1">
                  <span className="text-[#E05252] mt-0.5">•</span> {f}
                </li>
              ))}
            </ul>
          ) : (
            <p className="text-xs text-[#7088A8]">
              {score < 40 ? "Low risk — no significant concerns." :
               score < 70 ? "Moderate risk — monitor activity." :
               "High risk — immediate review recommended."}
            </p>
          )}
          <div className="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#1e2d42]" />
        </div>
      )}
    </div>
  );
}
