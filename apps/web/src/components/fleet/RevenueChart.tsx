interface DataPoint {
  month: string;
  value: number;
}

interface RevenueChartProps {
  data: DataPoint[];
  height?: number;
}

export default function RevenueChart({ data, height = 180 }: RevenueChartProps) {
  const max = Math.max(...data.map((d) => d.value), 1);
  const padding = { top: 16, right: 8, bottom: 32, left: 48 };
  const chartWidth = 600;
  const chartHeight = height;
  const innerWidth = chartWidth - padding.left - padding.right;
  const innerHeight = chartHeight - padding.top - padding.bottom;
  const barWidth = (innerWidth / data.length) * 0.6;
  const gap = innerWidth / data.length;

  const yTicks = [0, 0.25, 0.5, 0.75, 1].map((t) => ({
    value: Math.round(max * t),
    y: innerHeight - t * innerHeight,
  }));

  return (
    <div className="w-full overflow-x-auto">
      <svg
        viewBox={`0 0 ${chartWidth} ${chartHeight}`}
        className="w-full"
        style={{ minWidth: 320, height }}
      >
        <g transform={`translate(${padding.left},${padding.top})`}>
          {/* Y-axis grid lines and labels */}
          {yTicks.map(({ value, y }) => (
            <g key={value}>
              <line x1={0} y1={y} x2={innerWidth} y2={y} stroke="#e5e7eb" strokeWidth={1} />
              <text x={-6} y={y + 4} textAnchor="end" fontSize={10} fill="#9ca3af">
                {value >= 1000 ? `${(value / 1000).toFixed(0)}k` : value}
              </text>
            </g>
          ))}

          {/* Bars */}
          {data.map((d, i) => {
            const barHeight = (d.value / max) * innerHeight;
            const x = i * gap + (gap - barWidth) / 2;
            const y = innerHeight - barHeight;
            return (
              <g key={d.month}>
                <rect
                  x={x}
                  y={y}
                  width={barWidth}
                  height={barHeight}
                  rx={3}
                  fill="#E8922A"
                  opacity={0.85}
                />
                <text
                  x={x + barWidth / 2}
                  y={innerHeight + 16}
                  textAnchor="middle"
                  fontSize={10}
                  fill="#6b7280"
                >
                  {d.month}
                </text>
              </g>
            );
          })}
        </g>
      </svg>
    </div>
  );
}
