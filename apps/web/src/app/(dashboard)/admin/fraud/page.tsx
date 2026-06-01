"use client";

import { useEffect, useState } from "react";
import RiskScoreBadge from "@/components/fraud/RiskScoreBadge";
import FraudFlagCard from "@/components/fraud/FraudFlagCard";

interface FraudFlag {
  id: string;
  userId: string;
  userDisplayName: string;
  userEmail: string;
  userPhotoUrl: string | null;
  kycStatus: "verified" | "pending" | "failed" | "none";
  accountAgeDays: number;
  trustScore: number;
  flagType: string;
  riskScore: number;
  riskFactors: { factor: string; weight: number }[];
  evidence: Record<string, unknown>;
  dateRaised: string;
  status: "open" | "investigating" | "resolved" | "false_positive";
}

interface RiskProfile {
  userId: string;
  displayName: string;
  email: string;
  overallScore: number;
  flagCount: number;
  lastActivity: string;
}

interface KpiStats {
  openFlags: number;
  highRiskUsers: number;
  confirmedFraud30d: number;
  falsePositives30d: number;
}

const FLAG_TYPE_COLORS: Record<string, string> = {
  payment_anomaly: "bg-[#E05252]/20 text-[#E05252]",
  identity_mismatch: "bg-purple-500/20 text-purple-300",
  velocity_abuse: "bg-orange-500/20 text-orange-300",
  device_fingerprint: "bg-blue-500/20 text-blue-300",
  chargeback_risk: "bg-red-600/20 text-red-400",
  kyc_failure: "bg-amber-500/20 text-amber-400",
  suspicious_listing: "bg-pink-500/20 text-pink-300",
};

const KYC_LABELS: Record<string, string> = {
  verified: "KYC Verified",
  pending: "KYC Pending",
  failed: "KYC Failed",
  none: "No KYC",
};

const KYC_COLORS: Record<string, string> = {
  verified: "text-[#2ECC8A]",
  pending: "text-amber-400",
  failed: "text-[#E05252]",
  none: "text-[#7088A8]",
};

function FlagDetailModal({ flag, onClose, onConfirm, onFalsePositive, onReKyc }: {
  flag: FraudFlag;
  onClose: () => void;
  onConfirm: (id: string) => Promise<void>;
  onFalsePositive: (id: string) => Promise<void>;
  onReKyc: (id: string) => Promise<void>;
}) {
  const [acting, setActing] = useState<string | null>(null);

  const act = async (type: string, fn: () => Promise<void>) => {
    setActing(type);
    await fn();
    setActing(null);
    onClose();
  };

  return (
    <div className="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto">
      <div className="bg-[#141D2B] rounded-2xl border border-[#1e2d42] w-full max-w-lg my-4">
        <div className="flex items-center justify-between p-5 border-b border-[#1e2d42]">
          <h2 className="font-semibold text-white">Fraud Flag — {flag.flagType.replace(/_/g, " ").toUpperCase()}</h2>
          <button onClick={onClose} className="text-[#7088A8] hover:text-white text-xl">×</button>
        </div>
        <div className="p-5 space-y-5">
          {/* User summary */}
          <div className="flex items-center gap-4">
            <div className="w-12 h-12 rounded-full bg-[#1e2d42] flex items-center justify-center text-xl font-bold text-[#E8922A] shrink-0">
              {flag.userPhotoUrl
                ? <img src={flag.userPhotoUrl} alt="" className="w-full h-full rounded-full object-cover" />
                : flag.userDisplayName[0]?.toUpperCase()}
            </div>
            <div>
              <p className="text-white font-semibold">{flag.userDisplayName}</p>
              <p className="text-xs text-[#7088A8]">{flag.userEmail}</p>
              <div className="flex gap-3 mt-1">
                <span className={`text-xs ${KYC_COLORS[flag.kycStatus]}`}>{KYC_LABELS[flag.kycStatus]}</span>
                <span className="text-xs text-[#7088A8]">{flag.accountAgeDays}d old account</span>
                <span className="text-xs text-[#7088A8]">Trust: {flag.trustScore}/100</span>
              </div>
            </div>
            <div className="ml-auto">
              <RiskScoreBadge score={flag.riskScore} />
            </div>
          </div>

          {/* Risk score breakdown */}
          <div>
            <p className="text-xs text-[#7088A8] mb-2">Risk Score Breakdown</p>
            <div className="space-y-1.5">
              {flag.riskFactors.map((f, i) => (
                <div key={i} className="flex items-center gap-2">
                  <span className="text-xs text-[#7088A8] w-36 shrink-0 truncate">{f.factor}</span>
                  <div className="flex-1 h-1.5 bg-[#1e2d42] rounded-full">
                    <div className="h-full rounded-full bg-[#E05252]" style={{ width: `${Math.min(100, f.weight)}%` }} />
                  </div>
                  <span className="text-xs text-[#7088A8] w-8 text-right">{f.weight}</span>
                </div>
              ))}
            </div>
          </div>

          {/* Evidence */}
          <div>
            <p className="text-xs text-[#7088A8] mb-2">Evidence</p>
            <pre className="bg-[#080C12] rounded-xl p-4 text-xs text-[#7088A8] overflow-x-auto max-h-32">
              {JSON.stringify(flag.evidence, null, 2)}
            </pre>
          </div>

          {/* Actions */}
          <div className="flex flex-col gap-2">
            <button
              onClick={() => act("confirm", () => onConfirm(flag.id))}
              disabled={!!acting}
              className="w-full bg-[#E05252] text-white text-sm font-semibold py-3 rounded-xl hover:bg-red-500 transition disabled:opacity-50"
            >
              {acting === "confirm" ? "Processing…" : "⚠️ Confirm Fraud → Suspend Account"}
            </button>
            <button
              onClick={() => act("fp", () => onFalsePositive(flag.id))}
              disabled={!!acting}
              className="w-full bg-[#1e2d42] text-white text-sm font-semibold py-3 rounded-xl hover:bg-[#243447] transition disabled:opacity-50"
            >
              {acting === "fp" ? "Processing…" : "✓ Mark False Positive"}
            </button>
            <button
              onClick={() => act("kyc", () => onReKyc(flag.id))}
              disabled={!!acting}
              className="w-full border border-amber-500/30 text-amber-400 text-sm font-semibold py-3 rounded-xl hover:bg-amber-500/10 transition disabled:opacity-50"
            >
              {acting === "kyc" ? "Processing…" : "🔄 Request KYC Re-verification"}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}

export default function FraudDashboardPage() {
  const [kpi, setKpi] = useState<KpiStats | null>(null);
  const [flags, setFlags] = useState<FraudFlag[]>([]);
  const [riskProfiles, setRiskProfiles] = useState<RiskProfile[]>([]);
  const [loading, setLoading] = useState(true);
  const [selectedFlag, setSelectedFlag] = useState<FraudFlag | null>(null);

  useEffect(() => {
    const load = async () => {
      try {
        const [kpiRes, flagsRes, profilesRes] = await Promise.all([
          fetch("/api/v1/admin/fraud/stats"),
          fetch("/api/v1/admin/fraud/flags?status=open"),
          fetch("/api/v1/admin/fraud/risk-profiles?limit=20"),
        ]);
        const [kpiData, flagsData, profilesData] = await Promise.all([
          kpiRes.json(), flagsRes.json(), profilesRes.json(),
        ]);
        setKpi(kpiData);
        setFlags(flagsData.flags ?? []);
        setRiskProfiles(profilesData.profiles ?? []);
      } catch { /* ignore */ } finally { setLoading(false); }
    };
    load();
  }, []);

  const handleConfirm = async (id: string) => {
    await fetch(`/api/v1/admin/fraud/flags/${id}/confirm`, { method: "POST" });
    setFlags((p) => p.filter((f) => f.id !== id));
    if (kpi) setKpi({ ...kpi, openFlags: kpi.openFlags - 1, confirmedFraud30d: kpi.confirmedFraud30d + 1 });
  };
  const handleFalsePositive = async (id: string) => {
    await fetch(`/api/v1/admin/fraud/flags/${id}/false-positive`, { method: "POST" });
    setFlags((p) => p.filter((f) => f.id !== id));
    if (kpi) setKpi({ ...kpi, openFlags: kpi.openFlags - 1, falsePositives30d: kpi.falsePositives30d + 1 });
  };
  const handleReKyc = async (id: string) => {
    await fetch(`/api/v1/admin/fraud/flags/${id}/request-kyc`, { method: "POST" });
  };

  return (
    <div className="min-h-screen bg-[#080C12] p-6">
      <div className="max-w-7xl mx-auto space-y-8">
        <div>
          <h1 className="text-2xl font-bold text-white">Fraud Detection</h1>
          <p className="text-[#7088A8] mt-1">AI-powered fraud monitoring and risk management</p>
        </div>

        {/* KPI Cards */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {loading ? (
            [...Array(4)].map((_, i) => <div key={i} className="h-24 bg-[#141D2B] rounded-xl animate-pulse" />)
          ) : kpi ? (
            [
              { label: "Open Flags", value: kpi.openFlags, color: "text-[#E05252]" },
              { label: "High Risk Users", value: kpi.highRiskUsers, color: "text-amber-400" },
              { label: "Confirmed Fraud (30d)", value: kpi.confirmedFraud30d, color: "text-[#E05252]" },
              { label: "False Positives (30d)", value: kpi.falsePositives30d, color: "text-[#7088A8]" },
            ].map((k) => (
              <div key={k.label} className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-5 text-center">
                <p className={`text-3xl font-bold ${k.color}`}>{k.value}</p>
                <p className="text-xs text-[#7088A8] mt-1">{k.label}</p>
              </div>
            ))
          ) : null}
        </div>

        {/* Open Flags Table */}
        <div>
          <h2 className="text-lg font-semibold text-white mb-4">Open Fraud Flags</h2>
          {loading ? (
            <div className="space-y-3">{[...Array(4)].map((_, i) => <div key={i} className="h-20 bg-[#141D2B] rounded-xl animate-pulse" />)}</div>
          ) : flags.length === 0 ? (
            <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-10 text-center">
              <p className="text-3xl mb-3">✅</p>
              <p className="text-white font-semibold">No Open Fraud Flags</p>
              <p className="text-[#7088A8] text-sm mt-1">The platform looks clean!</p>
            </div>
          ) : (
            <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] overflow-x-auto">
              <table className="w-full text-sm">
                <thead>
                  <tr className="border-b border-[#1e2d42]">
                    <th className="text-left p-4 text-[#7088A8] font-medium">User</th>
                    <th className="text-left p-4 text-[#7088A8] font-medium">Flag Type</th>
                    <th className="text-left p-4 text-[#7088A8] font-medium">Risk Score</th>
                    <th className="text-left p-4 text-[#7088A8] font-medium">Date Raised</th>
                    <th className="text-left p-4 text-[#7088A8] font-medium">Status</th>
                    <th className="text-right p-4 text-[#7088A8] font-medium">Action</th>
                  </tr>
                </thead>
                <tbody>
                  {flags.map((f) => (
                    <tr key={f.id} className="border-b border-[#1e2d42] hover:bg-[#1e2d42]/30 transition cursor-pointer" onClick={() => setSelectedFlag(f)}>
                      <td className="p-4">
                        <p className="text-white">{f.userDisplayName}</p>
                        <p className="text-xs text-[#7088A8]">{f.userEmail}</p>
                      </td>
                      <td className="p-4">
                        <span className={`text-xs px-2 py-0.5 rounded-full capitalize ${FLAG_TYPE_COLORS[f.flagType] ?? "bg-[#1e2d42] text-[#7088A8]"`}>
                          {f.flagType.replace(/_/g, " ")}
                        </span>
                      </td>
                      <td className="p-4">
                        <div className="flex items-center gap-2">
                          <div className="w-20 h-1.5 bg-[#1e2d42] rounded-full">
                            <div className="h-full rounded-full bg-[#E05252]" style={{ width: `${f.riskScore}%` }} />
                          </div>
                          <span className="text-xs text-white">{f.riskScore}</span>
                        </div>
                      </td>
                      <td className="p-4 text-xs text-[#7088A8]">{new Date(f.dateRaised).toLocaleDateString()}</td>
                      <td className="p-4">
                        <span className={`text-xs px-2 py-0.5 rounded-full capitalize ${
                          f.status === "open" ? "bg-[#E05252]/20 text-[#E05252]" :
                          f.status === "investigating" ? "bg-amber-500/20 text-amber-400" :
                          "bg-[#1e2d42] text-[#7088A8]"
                        }`}>{f.status}</span>
                      </td>
                      <td className="p-4 text-right">
                        <button className="text-xs text-[#E8922A] hover:underline">Investigate</button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>

        {/* Risk Profiles */}
        <div>
          <h2 className="text-lg font-semibold text-white mb-4">Top 20 Highest Risk Users</h2>
          <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] overflow-x-auto">
            <table className="w-full text-sm">
              <thead>
                <tr className="border-b border-[#1e2d42]">
                  <th className="text-left p-4 text-[#7088A8] font-medium">User</th>
                  <th className="text-left p-4 text-[#7088A8] font-medium">Risk Score</th>
                  <th className="text-center p-4 text-[#7088A8] font-medium">Flags</th>
                  <th className="text-right p-4 text-[#7088A8] font-medium">Last Activity</th>
                </tr>
              </thead>
              <tbody>
                {loading ? (
                  [...Array(5)].map((_, i) => (
                    <tr key={i} className="border-b border-[#1e2d42]">
                      {[...Array(4)].map((__, j) => <td key={j} className="p-4"><div className="h-4 bg-[#1e2d42] rounded animate-pulse" /></td>)}
                    </tr>
                  ))
                ) : riskProfiles.length === 0 ? (
                  <tr><td colSpan={4} className="p-8 text-center text-[#7088A8]">No high-risk profiles.</td></tr>
                ) : (
                  riskProfiles.map((p) => (
                    <tr key={p.userId} className="border-b border-[#1e2d42] hover:bg-[#1e2d42]/30 transition">
                      <td className="p-4">
                        <p className="text-white">{p.displayName}</p>
                        <p className="text-xs text-[#7088A8]">{p.email}</p>
                      </td>
                      <td className="p-4"><RiskScoreBadge score={p.overallScore} /></td>
                      <td className="p-4 text-center text-white">{p.flagCount}</td>
                      <td className="p-4 text-right text-xs text-[#7088A8]">{new Date(p.lastActivity).toLocaleDateString()}</td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {selectedFlag && (
        <FlagDetailModal
          flag={selectedFlag}
          onClose={() => setSelectedFlag(null)}
          onConfirm={handleConfirm}
          onFalsePositive={handleFalsePositive}
          onReKyc={handleReKyc}
        />
      )}
    </div>
  );
}
