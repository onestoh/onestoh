"use client";

import { useEffect, useState } from "react";

interface ChatSession {
  id: string;
  userId: string | null;
  userEmail: string | null;
  userDisplayName: string | null;
  messageCount: number;
  tokensUsed: number;
  escalated: boolean;
  lastActive: string;
  topQuestions: string[];
}

interface SessionMessage {
  id: string;
  role: "user" | "assistant";
  content: string;
  createdAt: string;
  tokensUsed?: number;
}

interface AggregateStats {
  totalSessions: number;
  avgMessagesPerSession: number;
  escalationRate: number;
  topTopics: { topic: string; count: number }[];
}

function SessionRow({ session, onClick }: { session: ChatSession; onClick: () => void }) {
  return (
    <tr
      onClick={onClick}
      className={`border-b border-[#1e2d42] hover:bg-[#1e2d42]/50 cursor-pointer transition ${
        session.escalated ? "bg-amber-500/5" : ""
      }`}
    >
      <td className="p-4">
        <div className="flex items-center gap-3">
          <div className="w-8 h-8 rounded-full bg-[#1e2d42] flex items-center justify-center text-sm font-bold text-[#E8922A]">
            {(session.userDisplayName ?? session.userEmail ?? "G")[0].toUpperCase()}
          </div>
          <div>
            <p className="text-sm text-white">{session.userDisplayName ?? "Guest"}</p>
            <p className="text-xs text-[#7088A8]">{session.userEmail ?? "anonymous"}</p>
          </div>
        </div>
      </td>
      <td className="p-4 text-center text-sm text-white">{session.messageCount}</td>
      <td className="p-4 text-center text-sm text-[#7088A8]">{session.tokensUsed.toLocaleString()}</td>
      <td className="p-4 text-center">
        {session.escalated ? (
          <span className="text-xs font-semibold px-2 py-1 rounded-full bg-amber-500/20 text-amber-400">Escalated</span>
        ) : (
          <span className="text-xs text-[#7088A8]">-</span>
        )}
      </td>
      <td className="p-4 text-right text-xs text-[#7088A8]">{new Date(session.lastActive).toLocaleString()}</td>
    </tr>
  );
}

function ThreadModal({ session, onClose }: { session: ChatSession; onClose: () => void }) {
  const [messages, setMessages] = useState<SessionMessage[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const load = async () => {
      try {
        const res = await fetch(`/api/v1/ai/chat/sessions/${session.id}/messages`);
        const data = await res.json();
        setMessages(data.messages ?? []);
      } catch { /* ignore */ }
      finally { setLoading(false); }
    };
    load();
  }, [session.id]);

  return (
    <div className="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
      <div className="bg-[#141D2B] rounded-2xl border border-[#1e2d42] w-full max-w-xl max-h-[80vh] flex flex-col">
        <div className="flex items-center justify-between p-5 border-b border-[#1e2d42]">
          <div>
            <h2 className="font-semibold text-white">{session.userDisplayName ?? "Guest"} — Chat Thread</h2>
            <p className="text-xs text-[#7088A8]">{session.messageCount} messages · {session.tokensUsed.toLocaleString()} tokens</p>
          </div>
          {session.escalated && (
            <span className="text-xs font-semibold px-3 py-1 rounded-full bg-amber-500/20 text-amber-400 mr-3">Escalated</span>
          )}
          <button onClick={onClose} className="text-[#7088A8] hover:text-white text-xl">×</button>
        </div>
        <div className="flex-1 overflow-y-auto p-5 space-y-3">
          {loading ? (
            <div className="space-y-2">
              {[...Array(4)].map((_, i) => <div key={i} className={`h-10 bg-[#1e2d42] rounded-xl animate-pulse ${i % 2 === 0 ? "ml-8" : "mr-8"}`} />)}
            </div>
          ) : messages.map((m) => (
            <div key={m.id} className={`flex ${m.role === "user" ? "justify-end" : "justify-start"}`}>
              <div className={`rounded-2xl px-4 py-3 text-sm max-w-[80%] ${
                m.role === "user" ? "bg-[#E8922A] text-black" : "bg-[#1e2d42] text-white"
              }`}>
                <p className="text-xs opacity-60 mb-1">{m.role === "user" ? "User" : "YardAI"} · {new Date(m.createdAt).toLocaleTimeString()}</p>
                {m.content}
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

export default function AdminAIChatPage() {
  const [sessions, setSessions] = useState<ChatSession[]>([]);
  const [stats, setStats] = useState<AggregateStats | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [selectedSession, setSelectedSession] = useState<ChatSession | null>(null);

  useEffect(() => {
    const load = async () => {
      try {
        const [sessRes, statsRes] = await Promise.all([
          fetch("/api/v1/ai/chat/sessions"),
          fetch("/api/v1/ai/chat/stats"),
        ]);
        const [sessData, statsData] = await Promise.all([sessRes.json(), statsRes.json()]);
        setSessions(sessData.sessions ?? []);
        setStats(statsData);
      } catch (err: any) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };
    load();
  }, []);

  return (
    <div className="min-h-screen bg-[#080C12] p-6">
      <div className="max-w-6xl mx-auto space-y-8">
        <div>
          <h1 className="text-2xl font-bold text-white">AI Chat Oversight</h1>
          <p className="text-[#7088A8] mt-1">Monitor YardAI chat sessions and escalations</p>
        </div>

        {error && (
          <div className="bg-[#E05252]/10 border border-[#E05252]/30 rounded-xl p-4 text-[#E05252] text-sm">{error}</div>
        )}

        {/* Aggregate stats */}
        {stats && (
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-4 text-center">
              <p className="text-2xl font-bold text-white">{stats.totalSessions}</p>
              <p className="text-xs text-[#7088A8] mt-1">Total Sessions</p>
            </div>
            <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-4 text-center">
              <p className="text-2xl font-bold text-white">{stats.avgMessagesPerSession.toFixed(1)}</p>
              <p className="text-xs text-[#7088A8] mt-1">Avg Messages / Session</p>
            </div>
            <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-4 text-center">
              <p className="text-2xl font-bold text-amber-400">{(stats.escalationRate * 100).toFixed(1)}%</p>
              <p className="text-xs text-[#7088A8] mt-1">Escalation Rate</p>
            </div>
            <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-4">
              <p className="text-xs text-[#7088A8] mb-2">Top Topics</p>
              {stats.topTopics.slice(0, 3).map((t) => (
                <div key={t.topic} className="flex items-center justify-between mb-1">
                  <span className="text-xs text-white truncate max-w-[120px]">{t.topic}</span>
                  <span className="text-xs text-[#7088A8]">{t.count}</span>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* Sessions table */}
        <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b border-[#1e2d42]">
                <th className="text-left p-4 text-[#7088A8] font-medium">User</th>
                <th className="text-center p-4 text-[#7088A8] font-medium">Messages</th>
                <th className="text-center p-4 text-[#7088A8] font-medium">Tokens</th>
                <th className="text-center p-4 text-[#7088A8] font-medium">Escalated</th>
                <th className="text-right p-4 text-[#7088A8] font-medium">Last Active</th>
              </tr>
            </thead>
            <tbody>
              {loading ? (
                [...Array(5)].map((_, i) => (
                  <tr key={i} className="border-b border-[#1e2d42]">
                    {[...Array(5)].map((__, j) => (
                      <td key={j} className="p-4"><div className="h-4 bg-[#1e2d42] rounded animate-pulse" /></td>
                    ))}
                  </tr>
                ))
              ) : sessions.length === 0 ? (
                <tr><td colSpan={5} className="p-8 text-center text-[#7088A8]">No chat sessions found.</td></tr>
              ) : (
                sessions.map((s) => (
                  <SessionRow key={s.id} session={s} onClick={() => setSelectedSession(s)} />
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>

      {selectedSession && (
        <ThreadModal session={selectedSession} onClose={() => setSelectedSession(null)} />
      )}
    </div>
  );
}
