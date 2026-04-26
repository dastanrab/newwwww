export type DriverCurrentRequest = {
    id: string;
    customerName: string;
    phone: string;
    address: string;
    timeLabel: string;
};

/** دادهٔ نمایشی تا اتصال API */
export const MOCK_CURRENT_REQUESTS: DriverCurrentRequest[] = [
    {
        id: "۷۰۹۶۱",
        customerName: "زینب غفوری",
        phone: "۰۹۱۵۲۰۰۷۴۰۳",
        address: "مشهد، توس ۳۳، شقایق ۲، حافظ ۱، پلاک ۳",
        timeLabel: "۲۲ بهمن — ۸:۰۰ تا ۱۱:۰۰",
    },
    {
        id: "۷۰۹۶۲",
        customerName: "علی محمدی",
        phone: "۰۹۱۳۱۲۳۴۵۶۷",
        address: "تهران، ونک، خیابان ملاصدرا، کوچه گلستان، پلاک ۱۸، واحد ۴",
        timeLabel: "۲۳ بهمن — ۱۴:۰۰ تا ۱۷:۰۰",
    },
    {
        id: "۷۰۹۶۳",
        customerName: "مریم کاظمی",
        phone: "۰۹۳۵۹۸۷۶۵۴۳",
        address: "اصفهان، خمینی‌شهر، بلوار امام، نبش کوچه ۱۲",
        timeLabel: "۲۴ بهمن — ۹:۳۰ تا ۱۲:۳۰",
    },
];

export const MOCK_CURRENT_REQUESTS_COUNT = MOCK_CURRENT_REQUESTS.length;
