import { useState } from "react";

const BASE_URL = "http://185.255.88.111:8000/api/user";

interface ApiResponse<T = any> {
    status: "success" | "error";
    message: string;
    data: T;
}

export const useAddress = () => {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const request = async <T>(
        endpoint: string,
        method: "GET" | "POST" | "DELETE" = "GET",
        body?: any,
        token?: string | null
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
          Address Methods
    ============================ */

    // گرفتن لیست آدرس‌ها
    const getAddresses = (token: string | null) => {
        return request("/addresses", "GET", undefined, token);
    };

    // ثبت آدرس جدید
    const createAddress = (
        token: string,
        payload: {
            title: string;
            address: string;
            lat: number;
            lng: number;
            isFavorite?: boolean;
        }
    ) => {
        return request("/address", "POST", payload, token);
    };

    // حذف آدرس
    const deleteAddress = (token: string, addressId: number | string) => {
        return request(`/address/${addressId}`, "DELETE", undefined, token);
    };

    // جستجوی آدرس
    const searchAddress = (token: string, query: string) => {
        return request("/search", "POST", { address: query }, token);
    };
    const reverseAddress = async (lat: number, lng: number) => {
        setLoading(true);
        setError(null);

        try {
            const res = await fetch(`https://api.neshan.org/v5/reverse?lat=${lat}&lng=${lng}`, {
               method:"GET",
                headers: {
                    "Api-Key": "service.b28eded11be548d198058478e5296f16"
                }
            });
            const json = await res.json();

            if (!json.status || json.status !== "ok") {
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

    return {
        reverseAddress,
        getAddresses,
        createAddress,
        deleteAddress,
        searchAddress,
        loading,
        error,
    };
};