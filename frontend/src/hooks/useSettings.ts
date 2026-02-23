import { useState } from "react";

const BASE_URL = "http://185.255.88.111:8000/api/user";

interface ApiResponse<T = any> {
    status: "success" | "error";
    message: string;
    data: T;
}

/* ===========================
        Types (مهم)
=========================== */

export interface UserProfile {
    birthDate: string;
    referralCode: string;
    id: number;
    firstName: string;
    lastName: string;
    mob: string;
    email: string;
    gender: "male" | "female";
    userType: "citizen" | "guild";
    balance: number;
}

export interface SettingsData {
    user: UserProfile;
    badgeCounters: {
        messages: number;
        tickets: number;
    };
    cities: any[];
    currentRequest: any;
    cached: any;
    versioning: any;
}

/* ===========================
        Hook
=========================== */

export const useSettings = () => {
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
          Settings Methods
    ============================ */

    const getSettings = (token: string) => {
        return request<SettingsData>("/settings", token);
    };

    return {
        getSettings,
        loading,
        error,
    };
};