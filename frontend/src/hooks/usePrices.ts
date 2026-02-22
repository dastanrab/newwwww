import { useState } from "react";

const BASE_URL = "http://185.255.88.111:8000/api/user";

interface RateList {
    [key: string]: number;
}

export interface PriceItem {
    id: number;
    title: string;
    description: string;
    bgImage: string;
    image: string;
    maxAmount: number;
    unitAmount: number;
    rateList: RateList;
}

interface ApiResponse<T = any> {
    status: "success" | "error";
    message: string;
    data: T;
}

export const usePrices = () => {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const request = async <T>(
        endpoint: string,
        token?: string
    ): Promise<ApiResponse<T>> => {
        setLoading(true);
        setError(null);

        try {
            const res = await fetch(`${BASE_URL}${endpoint}`, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    ...(token && { Authorization: `Bearer ${token}` }),
                },
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
          Price Methods
    ============================ */

    const getPrices = (token?: string) => {
        return request<{ list: PriceItem[] }>("/prices", token);
    };

    return {
        getPrices,
        loading,
        error,
    };
};