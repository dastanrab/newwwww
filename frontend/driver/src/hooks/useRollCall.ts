import { useState } from "react";

const BASE_URL = "http://185.255.88.111:8000/api/driver";

interface RollCallResponse {
    status: "success" | "error";
    message: string;
    data: {
        status: "present" | "absent";
        enabled: {
            value: boolean;
            warning: string;
        };
    };
}

export const useRollCall = () => {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    // تابع برای ارسال درخواست حضور به سرور
    const updateRollCall = async (lat: number, lng: number, token: string) => {
        setLoading(true);
        setError(null);

        try {
            const res = await fetch(`${BASE_URL}/rollCall`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify({ lat, lng }), // ارسال مختصات
            });

            const json: RollCallResponse = await res.json();

            if (json.status === "error") {
                setError(json.message);
            }

            return json; // وضعیت جدید حضور و اطلاعات
        } catch (err: any) {
            setError("خطا در ارتباط با سرور");
            return {
                status: "error",
                message: "خطا در ارتباط با سرور",
                data: { status: "absent", enabled: { value: false, warning: "" } },
            };
        } finally {
            setLoading(false);
        }
    };

    return {
        updateRollCall,
        loading,
        error,
    };
};