"use client";

import { useEffect, useRef, useState } from "react";

interface VerificationResult {
  id: string;
  listingId: string;
  listingTitle: string;
  photoCount: number;
  qualityScore: number;
  issues: string[];
  status: "passed" | "failed" | "pending";
  createdAt: string;
}

interface DamageAssessment {
  hasDamage: boolean;
  confidence: number;
  regions: { label: string; severity: "minor" | "moderate" | "severe" }[];
  summary: string;
}

const ISSUE_COLORS: Record<string, string> = {
  blurry: "bg-amber-500/20 text-amber-400",
  low_resolution: "bg-orange-500/20 text-orange-300",
  poor_lighting: "bg-yellow-500/20 text-yellow-300",
  watermark: "bg-purple-500/20 text-purple-300",
  duplicate: "bg-blue-500/20 text-blue-300",
  inappropriate: "bg-[#E05252]/20 text-[#E05252]",
};

const SEVERITY_COLORS = { minor: "text-amber-400", moderate: "text-orange-400", severe: "text-[#E05252]" };

function QualityBar({ score }: { score: number }) {
  const color = score >= 75 ? "bg-[#2ECC8A]" : score >= 50 ? "bg-amber-500" : "bg-[#E05252]";
  return (
    <div className="flex items-center gap-2">
      <div className="flex-1 h-1.5 bg-[#1e2d42] rounded-full">
        <div className={`h-full rounded-full ${color}`} style={{ width: `${score}%` }} />
      </div>
      <span className="text-xs text-white w-8 text-right">{score}%</span>
    </div>
  );
}

export default function PhotoVerificationPage() {
  const [results, setResults] = useState<VerificationResult[]>([]);
  const [loading, setLoading] = useState(true);
  const [beforeFile, setBeforeFile] = useState<File | null>(null);
  const [afterFile, setAfterFile] = useState<File | null>(null);
  const [beforeUrl, setBeforeUrl] = useState<string | null>(null);
  const [afterUrl, setAfterUrl] = useState<string | null>(null);
  const [assessment, setAssessment] = useState<DamageAssessment | null>(null);
  const [assessing, setAssessing] = useState(false);
  const [assessError, setAssessError] = useState<string | null>(null);
  const beforeRef = useRef<HTMLInputElement>(null);
  const afterRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    const load = async () => {
      try {
        const res = await fetch("/api/v1/admin/photo-verification/results");
        const data = await res.json();
        setResults(data.results ?? []);
      } catch { /* ignore */ } finally { setLoading(false); }
    };
    load();
  }, []);

  const handleOverride = async (id: string) => {
    await fetch(`/api/v1/admin/photo-verification/${id}/override`, { method: "POST" });
    setResults((p) => p.map((r) => r.id === id ? { ...r, status: "passed" } : r));
  };

  const handleRequestNew = async (id: string) => {
    await fetch(`/api/v1/admin/photo-verification/${id}/request-new`, { method: "POST" });
    setResults((p) => p.filter((r) => r.id !== id));
  };

  const handleFileChange = (type: "before" | "after") => (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;
    const url = URL.createObjectURL(file);
    if (type === "before") { setBeforeFile(file); setBeforeUrl(url); }
    else { setAfterFile(file); setAfterUrl(url); }
    setAssessment(null);
  };

  const runAssessment = async () => {
    if (!beforeFile || !afterFile) return;
    setAssessing(true); setAssessError(null); setAssessment(null);
    try {
      const form = new FormData();
      form.append("before", beforeFile);
      form.append("after", afterFile);
      const res = await fetch("/api/v1/admin/photo-verification/damage-assessment", { method: "POST", body: form });
      if (!res.ok) throw new Error("Assessment failed");
      const data = await res.json();
      setAssessment(data.assessment);
    } catch (err: any) {
      setAssessError(err.message);
    } finally {
      setAssessing(false);
    }
  };

  const failedResults = results.filter((r) => r.status === "failed");
  const pendingResults = results.filter((r) => r.status === "pending");

  return (
    <div className="min-h-screen bg-[#080C12] p-6">
      <div className="max-w-6xl mx-auto space-y-10">
        <div>
          <h1 className="text-2xl font-bold text-white">Photo Verification</h1>
          <p className="text-[#7088A8] mt-1">AI-powered photo quality control and damage assessment</p>
        </div>

        {/* Pending verifications */}
        {pendingResults.length > 0 && (
          <section>
            <h2 className="text-lg font-semibold text-white mb-4">Pending Verification ({pendingResults.length})</h2>
            <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] overflow-x-auto">
              <table className="w-full text-sm">
                <thead>
                  <tr className="border-b border-[#1e2d42]">
                    {["Listing", "Photos", "Quality Score", "Issues", ""].map((h) => (
                      <th key={h} className="text-left p-4 text-[#7088A8] font-medium">{h}</th>
                    ))}
                  </tr>
                </thead>
                <tbody>
                  {pendingResults.map((r) => (
                    <tr key={r.id} className="border-b border-[#1e2d42]">
                      <td className="p-4">
                        <p className="text-white">{r.listingTitle}</p>
                        <p className="text-xs text-[#7088A8]">{r.listingId}</p>
                      </td>
                      <td className="p-4 text-white">{r.photoCount}</td>
                      <td className="p-4"><div className="w-32"><QualityBar score={r.qualityScore} /></div></td>
                      <td className="p-4">
                        <div className="flex flex-wrap gap-1">
                          {r.issues.map((issue) => (
                            <span key={issue} className={`text-xs px-2 py-0.5 rounded-full ${ISSUE_COLORS[issue] ?? "bg-[#1e2d42] text-[#7088A8]"}`}>
                              {issue.replace(/_/g, " ")}
                            </span>
                          ))}
                        </div>
                      </td>
                      <td className="p-4 text-xs text-amber-400">Pending</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </section>
        )}

        {/* All results */}
        <section>
          <h2 className="text-lg font-semibold text-white mb-4">Verification Results</h2>
          <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] overflow-x-auto">
            <table className="w-full text-sm">
              <thead>
                <tr className="border-b border-[#1e2d42]">
                  {["Listing", "Photos", "Quality Score", "Issues", "Status", "Actions"].map((h) => (
                    <th key={h} className="text-left p-4 text-[#7088A8] font-medium">{h}</th>
                  ))}
                </tr>
              </thead>
              <tbody>
                {loading ? (
                  [...Array(5)].map((_, i) => (
                    <tr key={i} className="border-b border-[#1e2d42]">
                      {[...Array(6)].map((__, j) => <td key={j} className="p-4"><div className="h-4 bg-[#1e2d42] rounded animate-pulse" /></td>)}
                    </tr>
                  ))
                ) : results.length === 0 ? (
                  <tr><td colSpan={6} className="p-8 text-center text-[#7088A8]">No verification results.</td></tr>
                ) : (
                  results.map((r) => (
                    <tr key={r.id} className="border-b border-[#1e2d42]">
                      <td className="p-4">
                        <p className="text-white">{r.listingTitle}</p>
                        <p className="text-xs text-[#7088A8]">{r.listingId}</p>
                      </td>
                      <td className="p-4 text-white">{r.photoCount}</td>
                      <td className="p-4"><div className="w-28"><QualityBar score={r.qualityScore} /></div></td>
                      <td className="p-4">
                        <div className="flex flex-wrap gap-1">
                          {r.issues.length === 0 ? (
                            <span className="text-xs text-[#2ECC8A]">None</span>
                          ) : r.issues.map((issue) => (
                            <span key={issue} className={`text-xs px-1.5 py-0.5 rounded-full ${ISSUE_COLORS[issue] ?? "bg-[#1e2d42] text-[#7088A8]"}`}>
                              {issue.replace(/_/g, " ")}
                            </span>
                          ))}
                        </div>
                      </td>
                      <td className="p-4">
                        <span className={`text-xs px-2 py-0.5 rounded-full ${
                          r.status === "passed" ? "bg-[#2ECC8A]/20 text-[#2ECC8A]" :
                          r.status === "failed" ? "bg-[#E05252]/20 text-[#E05252]" :
                          "bg-amber-500/20 text-amber-400"
                        }`}>{r.status}</span>
                      </td>
                      <td className="p-4">
                        {r.status === "failed" && (
                          <div className="flex gap-2">
                            <button onClick={() => handleOverride(r.id)}
                              className="text-xs text-[#2ECC8A] hover:underline">Override</button>
                            <button onClick={() => handleRequestNew(r.id)}
                              className="text-xs text-amber-400 hover:underline">Request New</button>
                          </div>
                        )}
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </section>

        {/* Damage comparison tool */}
        <section>
          <h2 className="text-lg font-semibold text-white mb-2">Damage Comparison Tool</h2>
          <p className="text-[#7088A8] text-sm mb-5">Upload before and after rental photos to get an AI damage assessment for dispute resolution.</p>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {(["before", "after"] as const).map((type) => {
              const url = type === "before" ? beforeUrl : afterUrl;
              const ref = type === "before" ? beforeRef : afterRef;
              return (
                <div key={type}>
                  <p className="text-sm text-[#7088A8] mb-2 capitalize">{type} Photo</p>
                  <div
                    onClick={() => ref.current?.click()}
                    className="border-2 border-dashed border-[#1e2d42] rounded-xl h-48 flex items-center justify-center cursor-pointer hover:border-[#E8922A]/50 transition overflow-hidden"
                  >
                    {url ? (
                      <img src={url} alt={type} className="w-full h-full object-cover" />
                    ) : (
                      <div className="text-center">
                        <p className="text-3xl mb-2">📷</p>
                        <p className="text-sm text-[#7088A8]">Click to upload {type} photo</p>
                      </div>
                    )}
                  </div>
                  <input ref={ref} type="file" accept="image/*" className="hidden" onChange={handleFileChange(type)} />
                </div>
              );
            })}
          </div>

          <button
            onClick={runAssessment}
            disabled={!beforeFile || !afterFile || assessing}
            className="mt-5 bg-[#E8922A] text-black font-semibold px-6 py-3 rounded-xl hover:bg-amber-400 transition disabled:opacity-50 flex items-center gap-2"
          >
            {assessing ? (
              <><span className="w-4 h-4 border-2 border-black/30 border-t-black rounded-full animate-spin" /> Analysing…</>
            ) : "🔍 Run Damage Assessment"}
          </button>

          {assessError && (
            <div className="mt-4 bg-[#E05252]/10 border border-[#E05252]/30 rounded-xl p-4 text-[#E05252] text-sm">{assessError}</div>
          )}

          {assessment && (
            <div className="mt-5 bg-[#141D2B] rounded-xl border border-[#1e2d42] p-6 space-y-4">
              <div className="flex items-center justify-between">
                <h3 className="text-white font-semibold">Assessment Result</h3>
                <span className={`text-sm font-semibold px-3 py-1 rounded-full ${
                  assessment.hasDamage ? "bg-[#E05252]/20 text-[#E05252]" : "bg-[#2ECC8A]/20 text-[#2ECC8A]"
                }`}>
                  {assessment.hasDamage ? "Damage Detected" : "No Damage Detected"}
                </span>
              </div>
              <div className="flex items-center gap-3">
                <div className="flex-1 h-2 bg-[#1e2d42] rounded-full">
                  <div
                    className={`h-full rounded-full ${assessment.confidence >= 75 ? "bg-[#2ECC8A]" : assessment.confidence >= 50 ? "bg-amber-500" : "bg-[#E05252]"}`}
                    style={{ width: `${assessment.confidence}%` }}
                  />
                </div>
                <span className="text-sm text-white font-semibold">{assessment.confidence}% confident</span>
              </div>
              <p className="text-sm text-[#7088A8]">{assessment.summary}</p>
              {assessment.regions.length > 0 && (
                <div>
                  <p className="text-xs text-[#7088A8] mb-2">Damage Regions</p>
                  <div className="flex flex-wrap gap-2">
                    {assessment.regions.map((r, i) => (
                      <span key={i} className="text-xs px-3 py-1 rounded-full bg-[#1e2d42]">
                        <span className="text-white">{r.label}</span>
                        <span className={` ml-1.5 ${SEVERITY_COLORS[r.severity]}`}>({r.severity})</span>
                      </span>
                    ))}
                  </div>
                </div>
              )}
            </div>
          )}
        </section>
      </div>
    </div>
  );
}
