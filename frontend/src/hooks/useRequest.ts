import { useState } from "react";

const BASE_URL = "http://185.255.88.111/api/user/request";

interface ApiResponse<T = any> {
    status: "success" | "error";
    message: string;
    data: T;
}

export const useRequest = () => {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const request = async <T>(
        endpoint: string,
        method: "GET" | "POST" | "DELETE" = "GET",
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
          Request Methods
    ============================ */

    // زمان‌بندی درخواست
    const getScheduling = (addressId: number | string, token: string) => {
        return request(`/scheduling?addressId=${addressId}`, "GET", undefined, token);
    };

    // ایجاد درخواست جدید
    const createRequest = (
        token: string,
        payload: {
            addressId: number;
            cardId: number;
            payMethod: string;
            scheduling: string;
        }
    ) => {
        return request("", "POST", payload, token);
    };

    // لیست درخواست‌ها
    const getList = (token: string) => {
        return request("/list", "GET", undefined, token);
    };

    // جزئیات یک درخواست
    const getDetail = (id: number | string, token: string) => {
        return request(`/${id}`, "GET", undefined, token);
    };

    // حذف یک درخواست (تمامی درخواست‌ها)
    const deleteRequest = (token: string) => {
        return request("", "DELETE", undefined, token);
    };

    // ثبت بازخورد روی یک درخواست
    const reviewRequest = (
        id: number | string,
        payload: {
            comment: string;
            rate: number;
            tipValues?: {
                good?: string[];
                bad?: string[];
            };
        }
    ) => {
        return request(`/${id}/review`, "POST", payload);
    };

    return {
        getScheduling,
        createRequest,
        getList,
        getDetail,
        deleteRequest,
        reviewRequest,
        loading,
        error,
    };
};