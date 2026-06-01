"use client";

import { useEffect, useRef, useState } from "react";

interface Message {
  id: string;
  role: "user" | "assistant";
  content: string;
  timestamp: Date;
}

const SUGGESTED_PROMPTS = [
  "How do I list a vehicle?",
  "What is M-Pesa escrow?",
  "How do commissions work?",
  "Check my booking status",
];

function TypingIndicator() {
  return (
    <div className="flex items-end gap-1 px-3 py-2">
      {[0, 1, 2].map((i) => (
        <span
          key={i}
          className="w-2 h-2 rounded-full bg-[#7088A8] animate-bounce"
          style={{ animationDelay: `${i * 0.15}s` }}
        />
      ))}
    </div>
  );
}

export default function ChatWidget() {
  const [open, setOpen] = useState(false);
  const [messages, setMessages] = useState<Message[]>([]);
  const [input, setInput] = useState("");
  const [typing, setTyping] = useState(false);
  const [sessionToken, setSessionToken] = useState<string | null>(null);
  const [unread, setUnread] = useState(false);
  const [initialising, setInitialising] = useState(false);
  const messagesEndRef = useRef<HTMLDivElement>(null);
  const textareaRef = useRef<HTMLTextAreaElement>(null);

  // Initialise session on first open
  useEffect(() => {
    if (!open) return;
    setUnread(false);
    if (sessionToken) return;
    const stored = sessionStorage.getItem("yardai_session_token");
    if (stored) { setSessionToken(stored); return; }
    const init = async () => {
      setInitialising(true);
      try {
        const res = await fetch("/api/v1/ai/chat/session", { method: "POST" });
        if (res.ok) {
          const data = await res.json();
          const token = data.session_token ?? data.sessionToken;
          if (token) {
            sessionStorage.setItem("yardai_session_token", token);
            setSessionToken(token);
          }
        } else {
          // guest session fallback
          const guestToken = `guest_${Date.now()}`;
          sessionStorage.setItem("yardai_session_token", guestToken);
          setSessionToken(guestToken);
        }
      } catch {
        const guestToken = `guest_${Date.now()}`;
        sessionStorage.setItem("yardai_session_token", guestToken);
        setSessionToken(guestToken);
      } finally {
        setInitialising(false);
      }
    };
    init();
  }, [open]);

  useEffect(() => {
    messagesEndRef.current?.scrollIntoView({ behavior: "smooth" });
  }, [messages, typing]);

  const sendMessage = async (text: string) => {
    const trimmed = text.trim();
    if (!trimmed || typing) return;
    setInput("");
    const userMsg: Message = {
      id: `u_${Date.now()}`,
      role: "user",
      content: trimmed,
      timestamp: new Date(),
    };
    setMessages((prev) => [...prev, userMsg]);
    setTyping(true);
    try {
      const res = await fetch("/api/v1/ai/chat/message", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ session_token: sessionToken, message: trimmed }),
      });
      const data = await res.json();
      const reply: Message = {
        id: `a_${Date.now()}`,
        role: "assistant",
        content: data.reply ?? data.message ?? "I'm not able to respond right now. Please try again.",
        timestamp: new Date(),
      };
      setMessages((prev) => [...prev, reply]);
      if (!open) setUnread(true);
    } catch {
      setMessages((prev) => [
        ...prev,
        { id: `err_${Date.now()}`, role: "assistant", content: "Something went wrong. Please try again.", timestamp: new Date() },
      ]);
    } finally {
      setTyping(false);
    }
  };

  const handleKeyDown = (e: React.KeyboardEvent<HTMLTextAreaElement>) => {
    if (e.key === "Enter" && !e.shiftKey) {
      e.preventDefault();
      sendMessage(input);
    }
  };

  return (
    <div className="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-3">
      {/* Chat Panel */}
      {open && (
        <div className="w-[380px] max-h-[520px] flex flex-col bg-[#141D2B] rounded-2xl border border-[#1e2d42] shadow-2xl overflow-hidden">
          {/* Header */}
          <div className="flex items-center justify-between px-4 py-3 border-b border-[#1e2d42] bg-[#080C12]">
            <div className="flex items-center gap-2">
              <div className="w-7 h-7 rounded-full bg-[#E8922A] flex items-center justify-center text-black text-xs font-bold">✦</div>
              <span className="font-semibold text-white text-sm">YardAI Assistant</span>
              <span className="w-2 h-2 rounded-full bg-[#2ECC8A] animate-pulse" />
            </div>
            <div className="flex items-center gap-3">
              <a href="/support" className="text-xs text-[#7088A8] hover:text-white transition">
                Escalate to Human
              </a>
              <button onClick={() => setOpen(false)} className="text-[#7088A8] hover:text-white transition text-lg leading-none">×</button>
            </div>
          </div>

          {/* Messages */}
          <div className="flex-1 overflow-y-auto p-4 space-y-3 min-h-0">
            {initialising && (
              <div className="text-center text-xs text-[#7088A8] py-2">Connecting to YardAI…</div>
            )}
            {messages.length === 0 && !initialising && (
              <div className="space-y-3">
                <div className="flex items-start gap-2">
                  <div className="w-6 h-6 rounded-full bg-[#E8922A] flex items-center justify-center text-black text-xs font-bold shrink-0 mt-0.5">✦</div>
                  <div className="bg-[#1e2d42] rounded-2xl rounded-tl-sm px-4 py-3 text-sm text-white max-w-[280px]">
                    <p className="text-xs text-[#7088A8] mb-1">YardAI</p>
                    Hi! I'm YardAI, your TheOnlineYard assistant. How can I help you today?
                  </div>
                </div>
                <div className="mt-3">
                  <p className="text-xs text-[#7088A8] mb-2">Suggested questions:</p>
                  <div className="flex flex-col gap-1.5">
                    {SUGGESTED_PROMPTS.map((p) => (
                      <button
                        key={p}
                        onClick={() => sendMessage(p)}
                        className="text-left text-xs px-3 py-2 rounded-xl border border-[#1e2d42] text-[#7088A8] hover:border-[#E8922A] hover:text-white transition"
                      >
                        {p}
                      </button>
                    ))}
                  </div>
                </div>
              </div>
            )}

            {messages.map((msg) => (
              <div key={msg.id} className={`flex ${msg.role === "user" ? "justify-end" : "items-start gap-2"}`}>
                {msg.role === "assistant" && (
                  <div className="w-6 h-6 rounded-full bg-[#E8922A] flex items-center justify-center text-black text-xs font-bold shrink-0 mt-0.5">✦</div>
                )}
                <div
                  className={`rounded-2xl px-4 py-3 text-sm max-w-[280px] whitespace-pre-wrap ${
                    msg.role === "user"
                      ? "bg-[#E8922A] text-black rounded-tr-sm"
                      : "bg-[#1e2d42] text-white rounded-tl-sm"
                  }`}
                >
                  {msg.role === "assistant" && <p className="text-xs text-[#7088A8] mb-1">YardAI</p>}
                  {msg.content}
                </div>
              </div>
            ))}

            {typing && (
              <div className="flex items-start gap-2">
                <div className="w-6 h-6 rounded-full bg-[#E8922A] flex items-center justify-center text-black text-xs font-bold shrink-0">✦</div>
                <div className="bg-[#1e2d42] rounded-2xl rounded-tl-sm">
                  <TypingIndicator />
                </div>
              </div>
            )}

            <div ref={messagesEndRef} />
          </div>

          {/* Input */}
          <div className="border-t border-[#1e2d42] p-3 flex gap-2 items-end bg-[#080C12]">
            <textarea
              ref={textareaRef}
              value={input}
              onChange={(e) => setInput(e.target.value)}
              onKeyDown={handleKeyDown}
              placeholder="Ask YardAI anything… (Enter to send)"
              rows={1}
              className="flex-1 bg-[#141D2B] border border-[#1e2d42] rounded-xl px-3 py-2 text-sm text-white resize-none focus:outline-none focus:border-[#E8922A] placeholder:text-[#7088A8] max-h-24 overflow-y-auto"
              style={{ lineHeight: "1.5" }}
            />
            <button
              onClick={() => sendMessage(input)}
              disabled={!input.trim() || typing}
              className="w-9 h-9 bg-[#E8922A] rounded-xl flex items-center justify-center text-black hover:bg-amber-400 transition disabled:opacity-40 shrink-0"
            >
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M2 21l21-9L2 3v7l15 2-15 2v7z" />
              </svg>
            </button>
          </div>
        </div>
      )}

      {/* Toggle Button */}
      <button
        onClick={() => setOpen(!open)}
        className="relative flex items-center gap-2 bg-[#E8922A] hover:bg-amber-400 text-black font-semibold px-4 py-3 rounded-2xl shadow-lg transition"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
          <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z" />
        </svg>
        <span className="text-sm">Ask YardAI</span>
        {unread && (
          <span className="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-[#E05252] border-2 border-[#080C12]" />
        )}
      </button>
    </div>
  );
}
