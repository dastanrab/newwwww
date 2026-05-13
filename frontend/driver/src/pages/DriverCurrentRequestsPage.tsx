import { Box, Stack, Typography } from "@mui/material";
import DriverRequestCard from "../components/DriverRequestCard";
import {useCallback, useEffect} from "react";
import {useDriverRequests} from "../hooks/useDriverRequests";
import {useAuthStore} from "../store/useAuthStore";

export default function DriverCurrentRequestsPage() {
    const { accessToken } = useAuthStore();
    // اضافه کردن loading و error از هوک
    const { getCurrentDriverRequests, currentRequests, loading, error } = useDriverRequests();

    const fetchRequests = useCallback(() => {
        // اگر توکن وجود دارد و قبلاً داده‌ای نگرفته‌ایم، درخواست بزن
        if (accessToken && !currentRequests) {
            getCurrentDriverRequests(accessToken);
        }
    }, [getCurrentDriverRequests, accessToken, currentRequests]);

    useEffect(() => {
        fetchRequests();
    }, [fetchRequests]);

    // نمایش وضعیت در حال بارگذاری
    if (loading && !currentRequests) return <Typography>در حال دریافت اطلاعات...</Typography>;

    // نمایش خطا در صورت بروز مشکل
    if (error) return <Typography color="error">{error}</Typography>;

    console.log('cur',currentRequests)
    // @ts-ignore
    return (
        <Box sx={{ flex: 1, minHeight: 0, overflow: "auto", px: 2, py: 2.5, pb: 3, bgcolor: "background.default" }}>
            <Stack spacing={0.75} sx={{ mb: 2 }}>
                <Typography variant="body2" color="text.secondary">
                    {/* استفاده از طول آرایه واقعی به جای Mock */}
                    {currentRequests?.length || 0} درخواست فعال برای شما ثبت شده است.
                </Typography>
            </Stack>

            <Stack spacing={2.25}>
                {/* پیمایش روی داده‌های واقعی دریافت شده از سرور */}
                {currentRequests?.map((req) => (
                    <DriverRequestCard key={req.id} request={req} />
                ))}

                {currentRequests?.length === 0 && (
                    <Typography align="center">درخواستی یافت نشد.</Typography>
                )}
            </Stack>
        </Box>
    );
}

