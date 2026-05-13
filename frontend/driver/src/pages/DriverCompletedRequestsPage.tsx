import { useEffect, useState } from "react";
import { Box, Stack, Typography, CircularProgress, Alert } from "@mui/material";
import DriverCompletedRequestCard from "../components/DriverCompletedRequestCard";
import { useAuthStore } from "../store/useAuthStore";

type WasteItem = {
    id: number;
    type: {
        value: number;
        label: string;
    };
    image: string;
    weight: number;
    price: number;
    totalPrice: number;
};

type CompletedRequest = {
    id: number;
    name: string;
    mob: string;
    address: string;
    totalWeight: string;
    date: {
        day: string;
        time: string;
    };
    wastes: {
        items: WasteItem[] | null;
        totalPrice: number | null;
    };
    userType: string;
};

const API_BASE = "http://185.255.88.111:8000/api/driver";

export default function DriverCompletedRequestsPage() {
    const { accessToken } = useAuthStore();
    const [requests, setRequests] = useState<CompletedRequest[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        fetchCompletedRequests();
    }, []);

    const fetchCompletedRequests = async () => {
        setLoading(true);
        setError(null);

        try {
            const response = await fetch(`${API_BASE}/requests/history`, {
                headers: {
                    Authorization: `Bearer ${accessToken}`,
                },
            });

            const result = await response.json();

            if (result.status === "success") {
                setRequests(result.data.list);
            } else {
                setError("خطا در دریافت لیست درخواست‌های تکمیل شده");
            }
        } catch (err) {
            setError("خطا در برقراری ارتباط با سرور");
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return (
            <Box
                sx={{
                    flex: 1,
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                    minHeight: 0,
                }}
            >
                <CircularProgress />
            </Box>
        );
    }

    if (error) {
        return (
            <Box
                sx={{
                    flex: 1,
                    minHeight: 0,
                    overflow: "auto",
                    px: 2,
                    py: 2.5,
                    bgcolor: "background.default",
                }}
            >
                <Alert severity="error">{error}</Alert>
            </Box>
        );
    }

    return (
        <Box
            sx={{
                flex: 1,
                minHeight: 0,
                overflow: "auto",
                px: 2,
                py: 2.5,
                pb: 3,
                bgcolor: "background.default",
            }}
        >
            <Stack spacing={0.75} sx={{ mb: 2 }}>
                <Typography variant="body2" color="text.secondary">
                    {requests.length} درخواست تکمیل‌شده در لیست شماست.
                </Typography>
            </Stack>

            {requests.length === 0 ? (
                <Box
                    sx={{
                        textAlign: "center",
                        py: 6,
                    }}
                >
                    <Typography variant="body1" color="text.secondary">
                        درخواست تکمیل شده‌ای وجود ندارد
                    </Typography>
                </Box>
            ) : (
                <Stack spacing={2.25}>
                    {requests.map((req) => (
                        <DriverCompletedRequestCard key={req.id} request={req} />
                    ))}
                </Stack>
            )}
        </Box>
    );
}
