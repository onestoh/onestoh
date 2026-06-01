"use client";

export interface TenantBrandingPreviewProps {
  primaryColor: string;
  logoUrl?: string;
  tenantName: string;
}

export default function TenantBrandingPreview({ primaryColor, logoUrl, tenantName }: TenantBrandingPreviewProps) {
  const isDark = isColorDark(primaryColor);
  const textOnPrimary = isDark ? "#ffffff" : "#000000";

  return (
    <div className="rounded-xl overflow-hidden border border-[#1e2d42] bg-[#080C12] text-xs" style={{ minWidth: 200 }}>
      {/* Mini Navbar */}
      <div className="flex items-center justify-between px-3 py-2" style={{ backgroundColor: primaryColor }}>
        <div className="flex items-center gap-1.5">
          {logoUrl ? (
            <img src={logoUrl} alt={tenantName} className="h-5 w-auto object-contain rounded" />
          ) : (
            <div className="w-5 h-5 rounded bg-white/20 flex items-center justify-center font-bold text-xs" style={{ color: textOnPrimary }}>
              {tenantName[0]?.toUpperCase()}
            </div>
          )}
          <span className="font-semibold" style={{ color: textOnPrimary }}>{tenantName}</span>
        </div>
        <div className="flex gap-2">
          <div className="w-10 h-1.5 rounded-full bg-white/30" />
          <div className="w-6 h-1.5 rounded-full bg-white/30" />
        </div>
      </div>

      {/* Mini listing card */}
      <div className="p-3">
        <div className="bg-[#141D2B] rounded-lg overflow-hidden">
          <div className="h-16 bg-[#1e2d42] flex items-center justify-center">
            <span className="text-2xl">🚗</span>
          </div>
          <div className="p-2.5">
            <div className="flex items-start justify-between">
              <div>
                <p className="text-white font-semibold text-xs">Toyota Prado</p>
                <p className="text-[#7088A8] text-xs">Nairobi CBD</p>
              </div>
              <div className="text-right">
                <p className="font-bold text-xs" style={{ color: primaryColor }}>KES 4,500</p>
                <p className="text-[#7088A8] text-xs">/day</p>
              </div>
            </div>
            <button
              className="mt-2 w-full py-1.5 rounded-md text-xs font-semibold transition"
              style={{ backgroundColor: primaryColor, color: textOnPrimary }}
            >
              Book Now
            </button>
          </div>
        </div>
        <p className="text-center text-[#7088A8] mt-2" style={{ fontSize: 9 }}>Live preview — updates in real-time</p>
      </div>
    </div>
  );
}

function isColorDark(hex: string): boolean {
  const c = hex.replace("#", "");
  if (c.length !== 6) return true;
  const r = parseInt(c.slice(0, 2), 16);
  const g = parseInt(c.slice(2, 4), 16);
  const b = parseInt(c.slice(4, 6), 16);
  // Perceived brightness formula
  return (r * 299 + g * 587 + b * 114) / 1000 < 128;
}
