"use client";

import { useState } from "react";

const ENDPOINTS = [
  { method: "GET", path: "/public/v1/listings", description: "List all published listings", auth: true },
  { method: "GET", path: "/public/v1/listings/{id}", description: "Get listing details", auth: true },
  { method: "GET", path: "/public/v1/listings/{id}/availability", description: "Get availability calendar", auth: true },
  { method: "POST", path: "/public/v1/bookings", description: "Create a booking", auth: true },
  { method: "GET", path: "/public/v1/bookings/{id}", description: "Get booking status", auth: true },
];

const WEBHOOK_EVENTS = [
  { event: "listing.published", description: "A listing has been published and is live" },
  { event: "booking.confirmed", description: "A booking has been confirmed by the owner" },
  { event: "booking.completed", description: "A booking has been marked as completed" },
  { event: "payment.received", description: "A payment has been received and processed" },
  { event: "escrow.released", description: "Escrow funds have been released to the owner" },
];

const CODE_EXAMPLES: Record<string, string> = {
  cURL: `# List all published listings
curl -X GET "https://api.theonlineyard.com/public/v1/listings" \\
  -H "X-API-Client-Id: YOUR_CLIENT_ID" \\
  -H "X-API-Client-Secret: YOUR_CLIENT_SECRET" \\
  -H "Accept: application/json"

# Create a booking
curl -X POST "https://api.theonlineyard.com/public/v1/bookings" \\
  -H "X-API-Client-Id: YOUR_CLIENT_ID" \\
  -H "X-API-Client-Secret: YOUR_CLIENT_SECRET" \\
  -H "Content-Type: application/json" \\
  -d '{
    "listing_id": "lst_abc123",
    "start_date": "2025-01-10",
    "end_date": "2025-01-15",
    "renter_name": "John Doe",
    "renter_phone": "+254700000000"
  }'`,

  JavaScript: `// List all published listings
const response = await fetch(
  'https://api.theonlineyard.com/public/v1/listings',
  {
    headers: {
      'X-API-Client-Id': process.env.YARD_CLIENT_ID,
      'X-API-Client-Secret': process.env.YARD_CLIENT_SECRET,
      'Accept': 'application/json',
    },
  }
);
const { listings } = await response.json();

// Create a booking
const booking = await fetch(
  'https://api.theonlineyard.com/public/v1/bookings',
  {
    method: 'POST',
    headers: {
      'X-API-Client-Id': process.env.YARD_CLIENT_ID,
      'X-API-Client-Secret': process.env.YARD_CLIENT_SECRET,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      listing_id: 'lst_abc123',
      start_date: '2025-01-10',
      end_date: '2025-01-15',
    }),
  }
).then((r) => r.json());`,

  Python: `import requests

BASE_URL = "https://api.theonlineyard.com"
HEADERS = {
    "X-API-Client-Id": "YOUR_CLIENT_ID",
    "X-API-Client-Secret": "YOUR_CLIENT_SECRET",
}

# List all published listings
response = requests.get(
    f"{BASE_URL}/public/v1/listings",
    headers=HEADERS
)
listings = response.json()["listings"]

# Create a booking
booking = requests.post(
    f"{BASE_URL}/public/v1/bookings",
    headers=HEADERS,
    json={
        "listing_id": "lst_abc123",
        "start_date": "2025-01-10",
        "end_date": "2025-01-15",
    }
).json()`,

  PHP: `<?php
$client_id = getenv('YARD_CLIENT_ID');
$client_secret = getenv('YARD_CLIENT_SECRET');
$base_url = 'https://api.theonlineyard.com';

// List all published listings
$context = stream_context_create([
    'http' => [
        'header' => implode("\r\n", [
            "X-API-Client-Id: $client_id",
            "X-API-Client-Secret: $client_secret",
            "Accept: application/json",
        ]),
    ],
]);
$response = file_get_contents(
    "$base_url/public/v1/listings",
    false, $context
);
$listings = json_decode($response, true)['listings'];

// Create a booking
$payload = json_encode([
    'listing_id' => 'lst_abc123',
    'start_date' => '2025-01-10',
    'end_date' => '2025-01-15',
]);
$context = stream_context_create(['http' => [
    'method' => 'POST',
    'header' => implode("\r\n", [
        "X-API-Client-Id: $client_id",
        "X-API-Client-Secret: $client_secret",
        "Content-Type: application/json",
        "Content-Length: " . strlen($payload),
    ]),
    'content' => $payload,
]]);
$booking = json_decode(file_get_contents(
    "$base_url/public/v1/bookings",
    false, $context
), true);`,
};

const METHOD_COLORS: Record<string, string> = {
  GET: "bg-blue-500/20 text-blue-300",
  POST: "bg-[#2ECC8A]/20 text-[#2ECC8A]",
  PATCH: "bg-amber-500/20 text-amber-400",
  DELETE: "bg-[#E05252]/20 text-[#E05252]",
};

export default function DeveloperPortalPage() {
  const [activeTab, setActiveTab] = useState<keyof typeof CODE_EXAMPLES>("cURL");

  return (
    <div className="min-h-screen bg-[#080C12]">
      {/* Hero */}
      <div className="border-b border-[#1e2d42] bg-gradient-to-br from-[#141D2B] to-[#080C12]">
        <div className="max-w-6xl mx-auto px-6 py-16">
          <div className="flex items-center gap-3 mb-4">
            <span className="text-3xl">🛠️</span>
            <span className="text-xs font-semibold px-3 py-1 rounded-full bg-[#E8922A]/20 text-[#E8922A]">v1 · REST API</span>
          </div>
          <h1 className="text-4xl font-bold text-white mb-3">YardOS Public API</h1>
          <p className="text-[#7088A8] text-lg max-w-2xl">
            Integrate TheOnlineYard into your fleet management system, booking engine, or marketplace.
          </p>
          <div className="flex gap-4 mt-8">
            <a href="/dashboard/owner/api-clients"
              className="bg-[#E8922A] text-black font-semibold px-6 py-3 rounded-xl hover:bg-amber-400 transition">
              Get API Credentials
            </a>
            <a href="#endpoints"
              className="border border-[#1e2d42] text-white font-semibold px-6 py-3 rounded-xl hover:bg-[#141D2B] transition">
              View Endpoints
            </a>
          </div>
        </div>
      </div>

      <div className="max-w-6xl mx-auto px-6 py-12 space-y-16">
        {/* Quick Start */}
        <section>
          <h2 className="text-2xl font-bold text-white mb-6">Quick Start</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            {[
              { step: 1, title: "Get Credentials", desc: "Create an API client in your owner dashboard to receive your Client ID and Client Secret." },
              { step: 2, title: "Authenticate", desc: "Pass X-API-Client-Id and X-API-Client-Secret headers with every request." },
              { step: 3, title: "Make Your First Call", desc: "Call GET /public/v1/listings to retrieve all published listings on the platform." },
            ].map(({ step, title, desc }) => (
              <div key={step} className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-6">
                <div className="w-9 h-9 rounded-full bg-[#E8922A] text-black font-bold flex items-center justify-center text-lg mb-4">{step}</div>
                <h3 className="text-white font-semibold mb-2">{title}</h3>
                <p className="text-sm text-[#7088A8]">{desc}</p>
              </div>
            ))}
          </div>
        </section>

        {/* Authentication */}
        <section>
          <h2 className="text-2xl font-bold text-white mb-2">Authentication</h2>
          <p className="text-[#7088A8] mb-6">All API requests must include the following headers:</p>
          <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-6 font-mono text-sm space-y-2">
            <div><span className="text-[#2ECC8A]">X-API-Client-Id</span><span className="text-[#7088A8]">: </span><span className="text-amber-300">your_client_id_here</span></div>
            <div><span className="text-[#2ECC8A]">X-API-Client-Secret</span><span className="text-[#7088A8]">: </span><span className="text-amber-300">your_client_secret_here</span></div>
          </div>
          <div className="mt-4 bg-[#1e2d42]/40 rounded-xl p-4">
            <p className="text-sm text-[#7088A8]">
              <span className="text-amber-400 font-medium">Security note:</span> Never expose your Client Secret in client-side code or public repositories. Always use environment variables.
            </p>
          </div>
        </section>

        {/* Endpoints */}
        <section id="endpoints">
          <h2 className="text-2xl font-bold text-white mb-6">API Endpoints</h2>
          <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] overflow-x-auto">
            <table className="w-full text-sm">
              <thead>
                <tr className="border-b border-[#1e2d42]">
                  <th className="text-left p-4 text-[#7088A8] font-medium">Method</th>
                  <th className="text-left p-4 text-[#7088A8] font-medium">Endpoint</th>
                  <th className="text-left p-4 text-[#7088A8] font-medium">Description</th>
                  <th className="text-center p-4 text-[#7088A8] font-medium">Auth Required</th>
                </tr>
              </thead>
              <tbody>
                {ENDPOINTS.map((ep, i) => (
                  <tr key={i} className="border-b border-[#1e2d42] last:border-0 hover:bg-[#1e2d42]/40 transition">
                    <td className="p-4">
                      <span className={`text-xs font-bold px-2 py-1 rounded ${METHOD_COLORS[ep.method] ?? "bg-[#1e2d42] text-white"}`}>
                        {ep.method}
                      </span>
                    </td>
                    <td className="p-4 font-mono text-[#E8922A] text-xs">{ep.path}</td>
                    <td className="p-4 text-[#7088A8]">{ep.description}</td>
                    <td className="p-4 text-center">{ep.auth ? <span className="text-[#2ECC8A] text-xs">✔ API Key</span> : <span className="text-[#7088A8] text-xs">No</span>}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </section>

        {/* Webhooks */}
        <section>
          <h2 className="text-2xl font-bold text-white mb-6">Webhook Events</h2>
          <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] overflow-hidden">
            {WEBHOOK_EVENTS.map((w, i) => (
              <div key={i} className={`flex items-center justify-between p-4 ${i < WEBHOOK_EVENTS.length - 1 ? "border-b border-[#1e2d42]" : ""}`}>
                <code className="text-[#E8922A] text-sm font-mono">{w.event}</code>
                <span className="text-sm text-[#7088A8] text-right max-w-xs">{w.description}</span>
              </div>
            ))}
          </div>
        </section>

        {/* Code Examples */}
        <section>
          <h2 className="text-2xl font-bold text-white mb-6">Code Examples</h2>
          <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] overflow-hidden">
            <div className="flex border-b border-[#1e2d42]">
              {(Object.keys(CODE_EXAMPLES) as (keyof typeof CODE_EXAMPLES)[]).map((tab) => (
                <button
                  key={tab}
                  onClick={() => setActiveTab(tab)}
                  className={`px-5 py-3 text-sm font-medium transition ${
                    activeTab === tab ? "text-white border-b-2 border-[#E8922A]" : "text-[#7088A8] hover:text-white"
                  }`}
                >
                  {tab}
                </button>
              ))}
            </div>
            <pre className="p-6 overflow-x-auto text-xs text-[#7088A8] leading-relaxed">
              <code>{CODE_EXAMPLES[activeTab]}</code>
            </pre>
          </div>
        </section>

        {/* Rate Limits */}
        <section>
          <h2 className="text-2xl font-bold text-white mb-6">Rate Limits</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="bg-[#141D2B] rounded-xl border border-[#1e2d42] p-6">
              <div className="flex items-center gap-3 mb-3">
                <span className="text-2xl">📊</span>
                <h3 className="text-white font-semibold">Standard Plan</h3>
              </div>
              <p className="text-3xl font-bold text-white">60 <span className="text-base font-normal text-[#7088A8]">req / min</span></p>
              <p className="text-sm text-[#7088A8] mt-2">Default for all API clients. Suitable for small integrations.</p>
            </div>
            <div className="bg-[#141D2B] rounded-xl border border-[#E8922A]/30 p-6">
              <div className="flex items-center gap-3 mb-3">
                <span className="text-2xl">⚡</span>
                <h3 className="text-white font-semibold">Enterprise Plan</h3>
                <span className="text-xs px-2 py-0.5 rounded-full bg-[#E8922A]/20 text-[#E8922A]">Contact sales</span>
              </div>
              <p className="text-3xl font-bold text-white">1,000 <span className="text-base font-normal text-[#7088A8]">req / min</span></p>
              <p className="text-sm text-[#7088A8] mt-2">For large integrations and fleet management systems.</p>
            </div>
          </div>
          <div className="mt-4 bg-[#1e2d42]/40 rounded-xl p-4">
            <p className="text-sm text-[#7088A8]">
              Rate limit headers are returned with every response: <code className="text-[#E8922A]">X-RateLimit-Limit</code>, <code className="text-[#E8922A]">X-RateLimit-Remaining</code>, <code className="text-[#E8922A]">X-RateLimit-Reset</code>.
            </p>
          </div>
        </section>

        {/* CTA */}
        <section className="bg-gradient-to-r from-[#E8922A]/10 to-[#1e2d42]/30 rounded-2xl border border-[#E8922A]/20 p-10 text-center">
          <h2 className="text-2xl font-bold text-white mb-3">Ready to Integrate?</h2>
          <p className="text-[#7088A8] mb-6">Create your API credentials from the owner dashboard and start building.</p>
          <a href="/dashboard/owner/api-clients"
            className="inline-block bg-[#E8922A] text-black font-semibold px-8 py-3 rounded-xl hover:bg-amber-400 transition">
            Get API Credentials
          </a>
        </section>
      </div>
    </div>
  );
}
