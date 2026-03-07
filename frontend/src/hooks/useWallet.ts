import { useState } from "react";

const BASE_URL = "http://185.255.88.111:8000/api/user/wallet";

interface ApiResponse<T = any> {
    status: "success" | "error";
    message: string;
    data: T;
}

export const useWallet = () => {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const request = async <T>(
        endpoint: string,
        method: "GET" | "POST" | "PUT" = "GET",
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
          Ticket Methods
    ============================ */



    // لیست تراکنش ها
    const getTransactions = (token: string) => {
        return request("/transactions", "GET", undefined, token);
    };



    return {
        getTransactions,
        loading,
        error,
    };
};