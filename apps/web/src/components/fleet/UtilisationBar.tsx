interface UtilisationBarProps {
  value: number; // 0–100
  showLabel?: boolean;
}

export default function UtilisationBar({ value, showLabel = true }: UtilisationBarProps) {
  const clampedValue = Math.min(100, Math.max(0, value));

  const color =
    clampedValue >= 70
      ? "bg-green-500"
      : clampedValue >= 40
      ? "bg-amber-500"
      : "bg-red-500";

  return (
    <div className="w-full">
      <div className="h-2 bg-gray-100 rounded-full overflow-hidden">
        <div
          className={`h-full ${color} rounded-full transition-all duration-300`}
          style={{ width: `${clampedValue}%` }}
        />
      </div>
      {showLabel && (
        <span className="text-xs text-gray-500 mt-0.5 block">{clampedValue}%</span>
      )}
    </div>
  );
}
