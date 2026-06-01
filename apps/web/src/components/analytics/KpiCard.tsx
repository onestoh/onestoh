interface KpiCardProps {
  title: string;
  value: string;
  change: number; // % change vs last period, positive or negative
}

export default function KpiCard({ title, value, change }: KpiCardProps) {
  const isPositive = change >= 0;
  return (
    <div className="bg-white rounded-xl border border-gray-200 p-5">
      <p className="text-sm text-gray-500 font-medium">{title}</p>
      <p className="text-2xl font-bold text-gray-900 mt-1">{value}</p>
      <div className="flex items-center gap-1 mt-2">
        <span
          className={`text-xs font-semibold flex items-center gap-0.5 ${
            isPositive ? "text-green-600" : "text-red-500"
          }`}
        >
          <span>{isPositive ? "↑" : "↓"}</span>
          <span>{Math.abs(change).toFixed(1)}%</span>
        </span>
        <span className="text-xs text-gray-400">vs last period</span>
      </div>
    </div>
  );
}
