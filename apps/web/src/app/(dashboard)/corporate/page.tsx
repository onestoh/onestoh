"use client";

import { useState } from "react";

interface TeamMember {
  id: string;
  name: string;
  email: string;
  role: "Admin" | "Manager" | "Staff";
  spendLimitKES: number;
  spentKES: number;
}

interface PendingApproval {
  id: string;
  employee: string;
  asset: string;
  fromDate: string;
  toDate: string;
  totalKES: number;
  submittedAt: string;
}

const COMPANY = {
  name: "Acme Logistics Ltd",
  kraPin: "A123456789B",
  billingEmail: "finance@acmelogistics.co.ke",
  monthlySpendLimitKES: 500000,
  spentMTDKES: 312000,
};

const TEAM: TeamMember[] = [
  { id: "t1", name: "Samuel Otieno", email: "s.otieno@acme.co.ke", role: "Admin", spendLimitKES: 500000, spentKES: 180000 },
  { id: "t2", name: "Mercy Wanjiku", email: "m.wanjiku@acme.co.ke", role: "Manager", spendLimitKES: 150000, spentKES: 85000 },
  { id: "t3", name: "Brian Kamau", email: "b.kamau@acme.co.ke", role: "Staff", spendLimitKES: 50000, spentKES: 47000 },
];

const PENDING_APPROVALS: PendingApproval[] = [
  { id: "pa1", employee: "Brian Kamau", asset: "Toyota Hilux 2021", fromDate: "2024-07-01", toDate: "2024-07-03", totalKES: 18000, submittedAt: "2024-06-28 09:15" },
  { id: "pa2", employee: "Mercy Wanjiku", asset: "Ford Ranger", fromDate: "2024-07-05", toDate: "2024-07-07", totalKES: 21000, submittedAt: "2024-06-29 14:30" },
];

const ROLE_COLORS: Record<TeamMember["role"], string> = {
  Admin: "bg-purple-100 text-purple-800",
  Manager: "bg-blue-100 text-blue-800",
  Staff: "bg-gray-100 text-gray-800",
};

export default function CorporateHubPage() {
  const [showInviteModal, setShowInviteModal] = useState(false);
  const [inviteEmail, setInviteEmail] = useState("");
  const [inviteRole, setInviteRole] = useState<"Manager" | "Staff">("Staff");

  const spendPct = Math.round((COMPANY.spentMTDKES / COMPANY.monthlySpendLimitKES) * 100);

  return (
    <div className="p-6 space-y-6">
      <h1 className="text-2xl font-bold text-gray-900">Corporate Account</h1>

      {/* Company Profile */}
      <div className="bg-white rounded-xl border border-gray-200 p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Company Profile</h2>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div>
            <p className="text-xs text-gray-500">Company Name</p>
            <p className="font-semibold text-gray-900 mt-0.5">{COMPANY.name}</p>
          </div>
          <div>
            <p className="text-xs text-gray-500">KRA PIN</p>
            <p className="font-semibold text-gray-900 font-mono mt-0.5">{COMPANY.kraPin}</p>
          </div>
          <div>
            <p className="text-xs text-gray-500">Billing Email</p>
            <p className="font-semibold text-gray-900 mt-0.5 text-sm">{COMPANY.billingEmail}</p>
          </div>
          <div>
            <p className="text-xs text-gray-500">Monthly Spend Limit</p>
            <p className="font-semibold text-gray-900 mt-0.5">KES {COMPANY.monthlySpendLimitKES.toLocaleString()}</p>
          </div>
        </div>
      </div>

      {/* Monthly Spend Gauge */}
      <div className="bg-white rounded-xl border border-gray-200 p-6">
        <div className="flex justify-between items-center mb-3">
          <h2 className="text-lg font-semibold text-gray-900">Monthly Spend</h2>
          <span className="text-sm text-gray-500">{spendPct}% used</span>
        </div>
        <div className="h-4 bg-gray-100 rounded-full overflow-hidden">
          <div
            className={`h-full rounded-full transition-all ${
              spendPct > 90 ? "bg-red-500" : spendPct > 70 ? "bg-amber-500" : "bg-green-500"
            }`}
            style={{ width: `${spendPct}%` }}
          />
        </div>
        <div className="flex justify-between text-sm mt-2">
          <span className="font-medium">KES {COMPANY.spentMTDKES.toLocaleString()} spent</span>
          <span className="text-gray-500">Limit: KES {COMPANY.monthlySpendLimitKES.toLocaleString()}</span>
        </div>
      </div>

      {/* Team Members */}
      <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div className="flex items-center justify-between p-4 border-b">
          <h2 className="text-lg font-semibold text-gray-900">Team Members</h2>
          <button
            onClick={() => setShowInviteModal(true)}
            className="px-4 py-2 bg-amber-500 text-white text-sm rounded-lg hover:bg-amber-600 font-medium"
          >
            + Invite Member
          </button>
        </div>
        <table className="w-full text-sm">
          <thead className="bg-gray-50 border-b">
            <tr>
              <th className="p-3 text-left font-semibold text-gray-700">Name</th>
              <th className="p-3 text-left font-semibold text-gray-700">Role</th>
              <th className="p-3 text-right font-semibold text-gray-700">Spend Limit (KES)</th>
              <th className="p-3 text-right font-semibold text-gray-700">Spent MTD (KES)</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {TEAM.map((m) => (
              <tr key={m.id} className="hover:bg-gray-50">
                <td className="p-3">
                  <p className="font-medium text-gray-900">{m.name}</p>
                  <p className="text-xs text-gray-500">{m.email}</p>
                </td>
                <td className="p-3">
                  <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${ROLE_COLORS[m.role]}`}>{m.role}</span>
                </td>
                <td className="p-3 text-right">{m.spendLimitKES.toLocaleString()}</td>
                <td className="p-3 text-right">
                  <span className={m.spentKES / m.spendLimitKES > 0.9 ? "text-red-600 font-semibold" : "text-gray-900"}>
                    {m.spentKES.toLocaleString()}
                  </span>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Pending Approvals */}
      <div className="bg-white rounded-xl border border-gray-200 p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Pending Approvals ({PENDING_APPROVALS.length})</h2>
        <div className="space-y-3">
          {PENDING_APPROVALS.map((pa) => (
            <div key={pa.id} className="flex items-center justify-between p-3 bg-amber-50 border border-amber-100 rounded-lg">
              <div>
                <p className="font-medium text-gray-900 text-sm">{pa.employee} — {pa.asset}</p>
                <p className="text-xs text-gray-500">{pa.fromDate} → {pa.toDate} · Submitted {pa.submittedAt}</p>
              </div>
              <div className="flex items-center gap-3">
                <span className="font-semibold text-sm">KES {pa.totalKES.toLocaleString()}</span>
                <button className="px-3 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700">Approve</button>
                <button className="px-3 py-1 text-xs border border-gray-300 rounded hover:bg-gray-50">Reject</button>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Invite Modal */}
      {showInviteModal && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-xl p-6 w-full max-w-md">
            <h3 className="text-lg font-semibold mb-4">Invite Team Member</h3>
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input
                  type="email"
                  value={inviteEmail}
                  onChange={(e) => setInviteEmail(e.target.value)}
                  placeholder="colleague@company.co.ke"
                  className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select
                  value={inviteRole}
                  onChange={(e) => setInviteRole(e.target.value as "Manager" | "Staff")}
                  className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                >
                  <option value="Manager">Manager</option>
                  <option value="Staff">Staff</option>
                </select>
              </div>
            </div>
            <div className="flex gap-3 mt-6">
              <button onClick={() => setShowInviteModal(false)} className="flex-1 py-2 border border-gray-300 rounded-lg text-sm">Cancel</button>
              <button className="flex-1 py-2 bg-amber-500 text-white rounded-lg text-sm font-medium hover:bg-amber-600">Send Invite</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
