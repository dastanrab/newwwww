import type { WasteItem } from "../components/waste/WastePricesGrid";

/** قیمت هر کیلو بر اساس پلهٔ وزنی (۱…۲۰)، مثل اسلایدر صفحهٔ قیمت پسماند. */
export function getUnitPricePerKgToman(item: WasteItem, weightKg: number): number {
    if (!Number.isFinite(weightKg) || weightKg <= 0) {
        return item.rateList?.[1] ?? item.maxAmount;
    }
    const tier = Math.min(20, Math.max(1, Math.round(weightKg)));
    if (item.rateList?.[tier] != null) return item.rateList[tier];
    return item.maxAmount;
}

export function getLineTotalToman(item: WasteItem, weightKg: number): number {
    if (!Number.isFinite(weightKg) || weightKg <= 0) return 0;
    return Math.round(weightKg * getUnitPricePerKgToman(item, weightKg));
}
