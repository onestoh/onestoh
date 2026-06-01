"use client";

interface Invoice {
  id: string;
  month: string;
  totalKES: number;
  poReference: string;
  status: "Paid" | "Pending" | "Overdue";
}

interface MemberSpend {
  member: string;
  role: string;
  spentKES: number;
}

const INVOICES: Invoice[] = [
  { id: "inv-2406", month: "June 2024", totalKES: 312000, poReference: "PO-2024-06-001", status: "Pending" },
  { id: "inv-2405", month: "May 2024", totalKES: 278000, poReference: "PO-2024-05-001", status: "Paid" },
  { id: "inv-2404", month: "April 2024", totalKES: 195000, poReference: "PO-2024-04-001", status: "Paid" },
  { id: "inv-2403", month: "March 2024", totalKES: 310000, poReference: "PO-2024-03-001", status: "Paid" },
];

const MEMBER_SPEND: MemberSpend[] = [
  { member: "Samuel Otieno", role: "Admin", spentKES: 180000 },
  { member: "Mercy Wanjiku", role: "Manager", spentKES: 85000 },
  { member: "Brian Kamau", role: "Staff", spentKES: 47000 },
];

const STATUS_COLORS: Record<Invoice["status"], string> = {
  Paid: "bg-green-100 text-green-800",
  Pending: "bg-yellow-100 text-yellow-800",
  Overdue: "bg-red-100 text-red-800",
};

export default function CorporateInvoicesPage() {
  const ytdSpend = INVOICES.filter((i) => i.status === "Paid").reduce((sum, i) => sum + i.totalKES, 0);

  return (
    <div className="p-6 space-y-6">
      <h1 className="text-2xl font-bold text-gray-900">Corporate Invoices</h1>

      {/* YTD Summary */}
      <div className="bg-white rounded-xl border border-gray-200 p-6">
        <p className="text-sm text-gray-500">Year-to-Date Spend (Paid Invoices)</p>
        <p className="text-3xl font-bold text-gray-900 mt-1">KES {ytdSpend.toLocaleString()}</p>
        <p className="text-sm text-gray-400 mt-1">{INVOICES.filter((i) => i.status === "Paid").length} invoices settled</p>
      </div>

      {/* Invoice List */}
      <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div className="p-4 border-b">
          <h2 className="text-lg font-semibold text-gray-900">Monthly Invoices</h2>
        </div>
        <table className="w-full text-sm">
          <thead className="bg-gray-50 border-b">
            <tr>
              <th className="p-3 text-left font-semibold text-gray-700">Invoice</th>
              <th className="p-3 text-left font-semibold text-gray-700">Month</th>
              <th className="p-3 text-left font-semibold text-gray-700">PO Reference</th>
              <th className="p-3 text-left font-semibold text-gray-700">Status</th>
              <th className="p-3 text-right font-semibold text-gray-700">Total (KES)</th>
              <th className="p-3 text-right font-semibold text-gray-700">Download</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {INVOICES.map((inv) => (
              <tr key={inv.id} className="hover:bg-gray-50">
                <td className="p-3 font-mono text-xs text-gray-600">{inv.id}</td>
                <td className="p-3 font-medium text-gray-900">{inv.month}</td>
                <td className="p-3 font-mono text-xs text-gray-600">{inv.poReference}</td>
                <td className="p-3">
                  <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${STATUS_COLORS[inv.status]}`}>{inv.status}</span>
                </td>
                <td className="p-3 text-right font-semibold">{inv.totalKES.toLocaleString()}</td>
                <td className="p-3 text-right">
                  <button className="text-amber-600 text-xs hover:underline">Download PDF</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Member Spend Breakdown */}
      <div className="bg-white rounded-xl border border-gray-200 p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Spend by Team Member (MTD)</h2>
        <div className="space-y-3">
          {MEMBER_SPEND.map((m) => {
            const total = MEMBER_SPEND.reduce((s, x) => s + x.spentKES, 0);
            const pct = Math.round((m.spentKES / total) * 100);
            return (
              <div key={m.member}>
                <div className="flex justify-between text-sm mb-1">
                  <div>
                    <span className="font-medium text-gray-900">{m.member}</span>
                    <span className="text-gray-400 text-xs ml-2">{m.role}</span>
                  </div>
                  <span className="font-medium">KES {m.spentKES.toLocaleString()} ({pct}%)</span>
                </div>
                <div className="h-2 bg-gray-100 rounded-full overflow-hidden">
                  <div className="h-full bg-amber-500 rounded-full" style={{ width: `${pct}%` }} />
                </div>
              </div>
            );
          })}
        </div>
      </div>
    </div>
  );
}
