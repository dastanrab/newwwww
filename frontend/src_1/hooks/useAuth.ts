import { useState } from "react";

const BASE_URL = "http://185.255.88.111:8000/api/user";

interface ApiResponse<T = any> {
    status: "success" | "error";
    message: string;
    data: T;
}

export const useAuth = () => {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const request = async <T>(
        endpoint: string,
        method: "POST" | "GET" = "POST",
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
          Auth Methods
    ============================ */

    const login = (mob: string) => {
        return request("/login", "POST", { mob });
    };

    const verify = (mob: string, code: string) => {
        return request("/login/verify", "POST", { mob, code });
    };

    const logout = (token: string) => {
        return request("/logout", "POST", undefined, token);
    };

    const register = (
        token: string,
        payload: {
            userType: "citizen" | "guild";
            guildMarket?: number;
            guildTitle?: string;
            gender: "male" | "female";
            firstName: string;
            lastName: string;
            birthDate: string;
            email: string;
        }
    ) => {
        return request("/register", "POST", payload, token);
    };

    return {
        login,
        verify,
        logout,
        register,
        loading,
        error,
    };
};
