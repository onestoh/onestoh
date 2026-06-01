interface FunnelStep {
  label: string;
  value: number;
  color: string;
}

interface FunnelChartProps {
  steps: FunnelStep[];
}

export default function FunnelChart({ steps }: FunnelChartProps) {
  const max = steps[0]?.value ?? 1;

  return (
    <div className="space-y-3">
      {steps.map((step, i) => {
        const widthPct = (step.value / max) * 100;
        const conversionPct = i > 0 ? Math.round((step.value / steps[i - 1].value) * 100) : 100;
        return (
          <div key={step.label}>
            <div className="flex items-center justify-between text-sm mb-1">
              <span className="font-medium text-gray-700">{step.label}</span>
              <div className="flex items-center gap-3">
                {i > 0 && (
                  <span className="text-xs text-gray-400">{conversionPct}% from prev</span>
                )}
                <span className="font-bold text-gray-900">{step.value.toLocaleString()}</span>
              </div>
            </div>
            <div className="h-8 bg-gray-100 rounded-lg overflow-hidden flex items-stretch">
              <div
                className={`${step.color} rounded-lg transition-all duration-500 flex items-center px-3`}
                style={{ width: `${widthPct}%` }}
              >
                <span className="text-white text-xs font-semibold truncate">{step.label}</span>
              </div>
            </div>
          </div>
        );
      })}
    </div>
  );
}
