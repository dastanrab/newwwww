export type DriverCompletedWasteLine = {
    /** عنوان پسماند */
    title: string;
    /** توضیح کوتاه مثل وزن تخمینی */
    detail?: string;
    /** قیمت کل این قلم (تومان) */
    lineTotal: number;
};

export type DriverCompletedRequest = {
    id: string;
    customerName: string;
    phone: string;
    address: string;
    requestedAtLabel: string;
    completedAtLabel: string;
    wastes: DriverCompletedWasteLine[];
    /** جمع کل تومان */
    grandTotal: number;
};

export const MOCK_COMPLETED_REQUESTS: DriverCompletedRequest[] = [
    {
        id: "۷۰۸۹۱",
        customerName: "سارا نادری",
        phone: "۰۹۱۲۳۴۵۶۷۸۹",
        address: "مشهد، احمدآباد، بلوار سجاد، کوچه یاس ۵، پلاک ۱۲، واحد ۲",
        requestedAtLabel: "۲۰ بهمن ۱۴۰۴ — ۹:۱۵",
        completedAtLabel: "۲۰ بهمن ۱۴۰۴ — ۱۰:۴۲",
        wastes: [
            {
                title: "کاغذ و مقوا",
                detail: "حدود ۸ کیلوگرم",
                lineTotal: 312_000,
            },
            {
                title: "پت (PET)",
                detail: "بطری‌های فشرده",
                lineTotal: 198_000,
            },
            {
                title: "فلزات آهنی",
                detail: "قوطی و درب",
                lineTotal: 145_000,
            },
        ],
        grandTotal: 655_000,
    },
    {
        id: "۷۰۸۸۸",
        customerName: "رضا کریمی",
        phone: "۰۹۳۵۱۱۲۲۳۳۴",
        address: "تهران، نارمک، خیابان دردشت، نبش کوچه شهید رجایی، پلاک ۴۵",
        requestedAtLabel: "۱۹ بهمن ۱۴۰۴ — ۱۴:۰۰",
        completedAtLabel: "۱۹ بهمن ۱۴۰۴ — ۱۵:۲۰",
        wastes: [
            {
                title: "پلاستیک نرم",
                detail: "نایلون و ظروف یک‌بار مصرف",
                lineTotal: 86_500,
            },
            {
                title: "شیشه",
                detail: "بطری نوشیدنی",
                lineTotal: 52_000,
            },
        ],
        grandTotal: 138_500,
    },
    {
        id: "۷۰۸۷۵",
        customerName: "مینا رحیمی",
        phone: "۰۹۱۹۸۷۶۵۴۳۲",
        address: "اصفهان، خیابان چهارباغ عباسی، کوچه نقشینه، پلاک ۳",
        requestedAtLabel: "۱۸ بهمن ۱۴۰۴ — ۱۱:۳۰",
        completedAtLabel: "۱۸ بهمن ۱۴۰۴ — ۱۲:۰۵",
        wastes: [
            {
                title: "آلومینیوم",
                detail: "قوطی نوشابه",
                lineTotal: 224_000,
            },
            {
                title: "لوازم برقی و الکترونیک",
                detail: "یک دستگاه کوچک",
                lineTotal: 380_000,
            },
            {
                title: "باتری",
                detail: "باتری قلمی و شارژی",
                lineTotal: 95_000,
            },
            {
                title: "روغن سوخته",
                detail: "ظرف ۵ لیتری",
                lineTotal: 41_000,
            },
        ],
        grandTotal: 740_000,
    },
];

export const MOCK_COMPLETED_REQUESTS_COUNT = MOCK_COMPLETED_REQUESTS.length;
