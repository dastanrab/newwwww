import { useState } from "react";

const BASE_URL = "http://185.255.88.111:8000/api/driver";

interface ApiResponse<T = any> {
    status: "success" | "error";
    message: string;
    data: T;
}

/* ===========================
        Types (مهم)
=========================== */

export interface DriverRequest {
    id: number;
    status: {
        value: number;
        label: string;
        color: string;
    };
    mob: string;
    location: {
        lat: number;
        lng: number;
    };
    name: string;
    address: string;
    userType: "citizen" | "guild";
    wastes: {
        items: any | null;
        totalPrice: any | null;
    };
    date: {
        day: string;
        time: string;
    };
    messages: {
        badgeCount: number;
        list: any[];
    };
    immediate: boolean;
    firstRequest: boolean;
}

/* ===========================
        Hook
=========================== */

export const useDriverRequests = () => {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [requests, setRequests] = useState<DriverRequest[] | null>(null);
    const [currentRequests, setCurrentRequests] = useState<DriverRequest[] | null>(null);

    const request = async <T>(endpoint: string, token?: string | null, method: string | null = 'POST'): Promise<ApiResponse<T>> => {
        setLoading(true);
        setError(null);

        try {
            const res = await fetch(`${BASE_URL}${endpoint}`, {
                method: method ?? 'POST',
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
        } catch (err) {
            setError("خطا در ارتباط با سرور");
            return {
                status: "error",
                message: "خطا در ارتباط با سرور",
                data: {} as any,
            };
        } finally {
            setLoading(false);
        }
    };

    /* ===========================
        Driver Requests Methods
    ============================ */

    const getDriverRequests = async (token: string | null) => {
        const response = await request<DriverRequest[]>("/requests/map", token);
        if (response.status === "success") {
            setRequests(response.data);
        }
        return response.data;
    };
    const getCurrentDriverRequests = async (token: string | null) => {
        const response = await request<DriverRequest[]>("/requests/current", token,"GET");
        if (response.status === "success") {
            // @ts-ignore
            setCurrentRequests(response.data?.list ?? []);
        }
        return response.data;
    };


    // New method to handle receiving request
    const receiveRequest = async (submitId: string, token: string | null) => {
        const response = await request<ApiResponse<DriverRequest>>(`/request/${submitId}/receive`, token);

        if (response.status === "success") {
            // Optional: Update the requests list or trigger a refresh
            setRequests((prevRequests) =>
                prevRequests ? prevRequests.map((req) =>
                    req.id === parseInt(submitId) ? { ...req, status: { ...req.status, value: 2, label: 'دریافت شده' } } : req
                ) : []
            );
        }

        return response;
    };

    return {
        getCurrentDriverRequests,
        getDriverRequests,
        receiveRequest,
        loading,
        error,
        requests,
        currentRequests
    };
};