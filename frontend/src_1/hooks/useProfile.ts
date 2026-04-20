import { useState } from "react";

export interface UpdateProfilePayload {
    firstName: string;
    lastName: string;
    email?: string;
    gender?: "male" | "female";
    birthDate?: string;
}

interface ApiResponse<T> {
    status: "success" | "error";
    message: string;
    data: T;
}

export const useProfile = () => {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const request = async <T>(
        url: string,
        method: string,
        token: string,
        body?: any
    ): Promise<ApiResponse<T>> => {
        setLoading(true);
        setError(null);

        try {
            const res = await fetch(url, {
                method,
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    Authorization: `Bearer ${token}`,
                },
                body: body ? JSON.stringify(body) : undefined,
            });

            if (res.status === 401) {
                throw new Error("unauthorized");
            }

            const data = await res.json();
            return data;
        } catch (err: any) {
            setError(err.message || "error");
            throw err;
        } finally {
            setLoading(false);
        }
    };

    const updateProfile = (token: string, payload: UpdateProfilePayload) =>
        request<any>(
            "http://185.255.88.111:8000/api/user/profile",
            "POST",
            token,
            payload
        );

    return {
        updateProfile,
        loading,
        error,
    };
};