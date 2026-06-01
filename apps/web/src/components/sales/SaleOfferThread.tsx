interface Offer {
  id: string;
  author: string;
  amountKES: number;
  message: string;
  timestamp: string;
  type: "offer" | "counter";
}

interface SaleOfferThreadProps {
  offers: Offer[];
}

export default function SaleOfferThread({ offers }: SaleOfferThreadProps) {
  return (
    <div className="space-y-4">
      {offers.map((offer) => (
        <div
          key={offer.id}
          className={`flex ${
            offer.author === "Buyer" ? "justify-start" : "justify-end"
          }`}
        >
          <div
            className={`max-w-xs rounded-xl px-4 py-3 ${
              offer.author === "Buyer"
                ? "bg-gray-100 text-gray-900"
                : "bg-amber-500 text-white"
            }`}
          >
            <div className="flex items-center justify-between gap-4 mb-1">
              <span className="text-xs font-semibold opacity-70">{offer.author}</span>
              <span
                className={`text-xs px-2 py-0.5 rounded-full font-medium ${
                  offer.type === "offer"
                    ? offer.author === "Buyer"
                      ? "bg-blue-100 text-blue-800"
                      : "bg-blue-700 text-white"
                    : offer.author === "Buyer"
                    ? "bg-amber-100 text-amber-800"
                    : "bg-amber-700 text-white"
                }`}
              >
                {offer.type === "offer" ? "Offer" : "Counter"}
              </span>
            </div>
            <p className="text-lg font-bold">KES {offer.amountKES.toLocaleString()}</p>
            {offer.message && (
              <p className={`text-sm mt-1 ${
                offer.author === "Buyer" ? "text-gray-600" : "text-amber-100"
              }`}>{offer.message}</p>
            )}
            <p className={`text-xs mt-2 ${
              offer.author === "Buyer" ? "text-gray-400" : "text-amber-200"
            }`}>{offer.timestamp}</p>
          </div>
        </div>
      ))}
    </div>
  );
}
