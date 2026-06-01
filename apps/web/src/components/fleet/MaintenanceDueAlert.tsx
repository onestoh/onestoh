interface MaintenanceDueAlertProps {
  asset: string;
  type: string;
  dueDate: string;
  isOverdue: boolean;
}

export default function MaintenanceDueAlert({
  asset,
  type,
  dueDate,
  isOverdue,
}: MaintenanceDueAlertProps) {
  return (
    <div
      className={`rounded-xl border p-4 ${
        isOverdue
          ? "bg-red-50 border-red-200"
          : "bg-amber-50 border-amber-200"
      }`}
    >
      <div className="flex items-start gap-3">
        <span className="text-2xl">{isOverdue ? "🔴" : "🟡"}</span>
        <div className="flex-1 min-w-0">
          <p
            className={`text-xs font-semibold uppercase tracking-wide ${
              isOverdue ? "text-red-600" : "text-amber-600"
            }`}
          >
            {isOverdue ? "OVERDUE" : "DUE SOON"}
          </p>
          <p className="text-sm font-semibold text-gray-900 mt-0.5 truncate">{type}</p>
          <p className="text-xs text-gray-600 truncate">{asset}</p>
          <p
            className={`text-xs font-medium mt-1 ${
              isOverdue ? "text-red-700" : "text-amber-700"
            }`}
          >
            {isOverdue ? "Was due" : "Due"} {dueDate}
          </p>
        </div>
      </div>
      <button
        className={`mt-3 w-full py-1.5 text-xs font-medium rounded-lg ${
          isOverdue
            ? "bg-red-600 text-white hover:bg-red-700"
            : "bg-amber-500 text-white hover:bg-amber-600"
        }`}
      >
        Schedule Now
      </button>
    </div>
  );
}
