import type { WasteItem } from "../components/waste/WastePricesGrid";

function buildRateList(base: number, floor: number): { [weight: number]: number } {
    const list: { [weight: number]: number } = {};
    for (let w = 1; w <= 20; w += 1) {
        list[w] = Math.max(floor, Math.round(base - w * 850 + (w % 5) * 120));
    }
    return list;
}

/**
 * تصاویر نمونه (HTTPS) — همان الگوی `bgImage` API؛ فقط آدرس‌هایی که پاسخ ۲۰۰ دارند.
 */
const IMG = {
    cardboard:
        "https://images.unsplash.com/photo-1604187351574-c75ca79f5807?w=900&auto=format&fit=crop&q=80",
    plasticWaste:
        "https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=900&auto=format&fit=crop&q=80",
    bottles:
        "https://images.unsplash.com/photo-1581578731548-c64695cc6952?w=900&auto=format&fit=crop&q=80",
    industry:
        "https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?w=900&auto=format&fit=crop&q=80",
    ewaste:
        "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=900&auto=format&fit=crop&q=80",
    evBattery:
        "https://images.unsplash.com/photo-1593941707874-ef25b8b4a92b?w=900&auto=format&fit=crop&q=80",
    oilPlant:
        "https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=900&auto=format&fit=crop&q=80",
} as const;

/**
 * ۹ قلم مثل اسکلتون لودینگ صفحهٔ کاربر؛ فقط برای نمایش سمت راننده بدون API.
 */
export const DRIVER_WASTE_PRICES_MOCK: WasteItem[] = [
    {
        id: 101,
        title: "کاغذ و مقوا",
        description:
            "کاغذ باطله، جعبه و مقوای تمیز و خشک را جدا از رطوبت و مواد غذایی نگه دارید تا قیمت بهتری دریافت کنید.",
        image: IMG.cardboard,
        maxAmount: 11000,
        rateList: buildRateList(51000, 11000),
    },
    {
        id: 102,
        title: "پت (PET)",
        description:
            "بطری‌های نوشیدنی شفاف و رنگی پس از شستشو و جدا کردن درب؛ بطری‌های له‌شدهٔ تمیز نیز پذیرفته می‌شود.",
        image: IMG.plasticWaste,
        maxAmount: 14000,
        rateList: buildRateList(56000, 14000),
    },
    {
        id: 103,
        title: "پلاستیک نرم",
        description:
            "ظروف یک‌بار مصرف، نایلون تمیز و پلاستیک‌های نرم بدون آلودگی شدید؛ لطفاً خشک تحویل دهید.",
        image: IMG.bottles,
        maxAmount: 13000,
        rateList: buildRateList(47000, 13000),
    },
    {
        id: 104,
        title: "فلزات آهنی",
        description:
            "قوطی کنسرو، درب فلزی و ضایعات آهنی؛ برای ایمنی، لبه‌های تیز را جدا بسته‌بندی کنید.",
        image: IMG.industry,
        maxAmount: 19000,
        rateList: buildRateList(74000, 19000),
    },
    {
        id: 105,
        title: "آلومینیوم",
        description:
            "قوطی نوشابه، فویل آشپزخانهٔ جمع‌شده و قطعات آلومینیومی تمیز؛ رطوبت و بقایای غذا را بگیرید.",
        image: IMG.bottles,
        maxAmount: 22000,
        rateList: buildRateList(82000, 22000),
    },
    {
        id: 106,
        title: "شیشه",
        description:
            "بطری و ظروف شیشه‌ای بدون شکستگی شدید؛ شیشهٔ رنگی و شفاف را در صورت امکان جدا کنید.",
        image: IMG.plasticWaste,
        maxAmount: 8000,
        rateList: buildRateList(28000, 8000),
    },
    {
        id: 107,
        title: "لوازم برقی و الکترونیک",
        description:
            "قطعات برقی غیرقابل استفاده را بدون باز کردن باتری به صورت ایمن تحویل دهید؛ داده‌های شخصی را پاک کنید.",
        image: IMG.ewaste,
        maxAmount: 35000,
        rateList: buildRateList(120000, 35000),
    },
    {
        id: 108,
        title: "باتری",
        description:
            "باتری قلمی و شارژی فرسوده را در ظرف دربسته و جدا از سایر پسماندها تحویل دهید.",
        image: IMG.evBattery,
        maxAmount: 40000,
        rateList: buildRateList(95000, 40000),
    },
    {
        id: 109,
        title: "روغن سوخته",
        description:
            "روغن خوراکی مصرف‌شده را در ظرف دربسته و بدون مخلوط با آب یا سوخت دیگر تحویل دهید.",
        image: IMG.oilPlant,
        maxAmount: 16000,
        rateList: buildRateList(42000, 16000),
    },
];
