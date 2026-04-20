import { useState } from "react";

const BASE_URL = "http://185.255.88.111/api/user";

interface Card {
    value: string | number;
    label: string;
    name: string;
    bank: string;
}

interface ApiResponse<T = any> {
    status: "success" | "error";
    message: string;
    data: T;
}

export const useCard = () => {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const request = async <T>(
        endpoint: string,
        method: "GET" | "POST" = "GET",
        body?: any,
        token?: string
    ): Promise<ApiResponse<T>> => {
        setLoading(true);
        setError(null);

        try {
            const res = await fetch(`${BASE_URL}${endpoint}`, {
                method,
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    ...(token && { Authorization: `Bearer ${token}` }),
                },
                ...(body && { body: JSON.stringify(body) }),
            });

            const json: ApiResponse<T> = await res.json();

            if (json.status === "error") {
                setError(json.message);
            }

            return json;
        } catch (err: any) {
            setError("خطا در ارتباط با سرور");
            return {
                status: "error",
                message: "خطا در ارتباط با سرور",
                data: [] as any,
            };
        } finally {
            setLoading(false);
        }
    };

    /* ===========================
          Card Methods
    ============================ */

    // گرفتن لیست کارت‌ها
    const getCards = (token: string) => {
        return request<Card[]>("/cardNumbers", "GET", undefined, token);
    };

    // افزودن کارت جدید
    const addCard = (token: string, cardNumber: string) => {
        return request<Card>("/cardNumber", "POST", { cardNumber }, token);
    };

    return {
        getCards,
        addCard,
        loading,
        error,
    };
};