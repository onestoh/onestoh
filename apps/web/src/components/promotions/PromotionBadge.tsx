type BadgeType = "Featured" | "Sponsored";

interface PromotionBadgeProps {
  type: BadgeType;
  className?: string;
}

export default function PromotionBadge({ type, className = "" }: PromotionBadgeProps) {
  const styles: Record<BadgeType, { bg: string; text: string; icon: string }> = {
    Featured: {
      bg: "bg-amber-500",
      text: "text-white",
      icon: "⭐",
    },
    Sponsored: {
      bg: "bg-blue-600",
      text: "text-white",
      icon: "📢",
    },
  };

  const s = styles[type];

  return (
    <span
      className={`inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold ${s.bg} ${s.text} ${className}`}
    >
      <span style={{ fontSize: 10 }}>{s.icon}</span>
      {type}
    </span>
  );
}
