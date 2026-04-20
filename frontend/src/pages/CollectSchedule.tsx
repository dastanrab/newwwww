import {useEffect, useState} from "react";
import {Box, Button, Typography, Grid, CircularProgress} from "@mui/material";
import {useLocation} from "react-router-dom";
import {LoadingButton} from "@mui/lab";
import 'swiper/swiper-bundle.css';

import {useRequest} from "../hooks/useRequest";
import {useAuthStore} from "../store/useAuthStore";

function CollectSchedule() {
    const location = useLocation();

    // دریافت اطلاعات از استیت مسیر
    const locationState = location.state as { addressId?: number };
    const addressId = locationState?.addressId;

    const {accessToken} = useAuthStore();

    // تجمیع متدها و وضعیت‌ها از یک هوک واحد
    const {getScheduling, createRequest, loading, error} = useRequest();

    const [days, setDays] = useState<any[]>([]);
    const [submitLoading, setSubmitLoading] = useState(false);
    const [selectedDay, setSelectedDay] = useState<number | null>(null);
    const [selectedHour, setSelectedHour] = useState<number | null>(null);

    // واکشی داده‌ها - این افکت فقط با تغییر addressId یا accessToken اجرا می‌شود
    useEffect(() => {
        if (!addressId || !accessToken) return;

        let isMounted = true; // برای جلوگیری از آپدیت استیت اگر کامپوننت آنماونت شد

        const fetchSchedule = async () => {
            try {
                const res = await getScheduling(addressId, accessToken);
                if (isMounted && res.status === "success") {
                    // @ts-ignore
                    setDays(res.data.list || []);
                    // اگر می‌خواهید به صورت پیش‌فرض روز اول انتخاب شود:
                    // if (res.data.list?.length > 0) setSelectedDay(0);
                }
            } catch (err) {
                console.error("خطا در دریافت زمان‌بندی:", err);
            }
        };

        fetchSchedule();

        return () => {
            isMounted = false;
        };
    }, [addressId, accessToken]); // وابستگی‌های دقیق

    // مدیریت تغییر روز
    const handleDaySelect = (index: number) => {
        setSelectedDay(index);
        setSelectedHour(null); // حیاتی: با تغییر روز، ساعت باید دوباره انتخاب شود
    };

    const handleHourSelect = (index: number) => {
        setSelectedHour(index);
    };

    // const handleDaySlideChange = (swiper: any) => {
    //     setSelectedDay(swiper.activeIndex);
    //     setSelectedHour(null); // ریست کردن ساعت در تغییر اسلاید
    // };

    const handleFinalSubmit = async () => {
        if (selectedDay === null || selectedHour === null || !addressId) return;

        const selectedDayData = days[selectedDay];
        const selectedHourData = selectedDayData.hours[selectedHour];

        const payload = {
            addressId: addressId,
            cardId: null,
            payMethod: "aniroob",
            scheduling: {
                day: selectedDayData.value,
                hour: selectedHourData.value
            },
        };

        try {
            setSubmitLoading(true);
            // @ts-ignore
            const res = await createRequest(accessToken, payload);
            if (res.status === "success") {
                window.location.href='/'
            }
        } catch (err) {
            console.error("خطا در ثبت درخواست:", err);
        } finally {
            setSubmitLoading(false);
        }
    };

    if (loading) {
        return (
            <Box sx={{height: '60vh', display: 'flex', alignItems: 'center', justifyContent: 'center'}}>
                <CircularProgress/>
            </Box>
        );
    }

    if (error) {
        return (
            <Typography color="error" align="center" sx={{mt: 4}}>{error}</Typography>
        );
    }


    return (
        <Box className="zo-collect" sx={{pb: 15}}>
            <Typography variant="h5" sx={{mb: 3, textAlign: 'center', fontWeight: 'bold'}}>
                انتخاب تاریخ و زمان
            </Typography>

            <Typography variant="h6" sx={{mb: 1}}>انتخاب روز</Typography>
            <Box sx={{mb: 3}}>
                <Grid container spacing={1}>
                    {days.map((day, index) => (
                        <Grid size={{xs: 6, md: 3}} key={index}>
                            <Button
                                fullWidth
                                variant={selectedDay === index ? "contained" : "outlined"}
                                // @ts-ignore
                                color={!day.enabled ? "inherit" : (selectedDay === index ? "primary" : "outlined")}
                                disabled={!day.enabled}
                                onClick={() => handleDaySelect(index)}
                                sx={{
                                    display: 'flex',
                                    flexDirection: 'column',
                                    gap: 0.5,
                                    borderRadius: 2,
                                    opacity: day.enabled ? 1 : 0.5
                                }}
                            >
                                <Typography variant="body1" sx={{fontWeight: 700}}>{day.weekday}</Typography>
                                <Typography variant="caption">{day.label}</Typography>
                            </Button>
                        </Grid>
                    ))}
                </Grid>
            </Box>

            <Typography variant="h6" sx={{mt: 2}}>انتخاب ساعت</Typography>
            <Grid container spacing={1}>
                {selectedDay !== null &&
                    days[selectedDay]?.hours.map((hour: any, index: number) => (
                        <Grid size={{xs: 6, md: 3}} key={index}>
                            <Button
                                key={index}
                                fullWidth
                                variant={selectedHour === index ? "contained" : "outlined"}
                                // @ts-ignore
                                color={!hour.enabled ? "inherit" : (selectedHour === index ? "primary" : "outlined")}
                                disabled={!hour.enabled}
                                onClick={() => handleHourSelect(index)}
                                sx={{
                                    display: 'flex',
                                    flexDirection: 'column',
                                    borderRadius: 2,
                                    padding: '10px 5px',
                                }}
                            >
                                <Typography variant="body2" sx={{fontWeight: 700}}>{hour.subLabel}</Typography>
                                <Typography variant="caption">{hour.label}</Typography>
                            </Button>
                        </Grid>
                    ))}
            </Grid>

            {/* بخش دکمه نهایی */}
            <Box
                sx={{
                    width: 300,
                    margin: '0 auto',
                    position: 'fixed',
                    right: 0,
                    bottom: 90,
                    left: 0,
                    textAlign: 'center',
                    zIndex: 15
                }}
            >
                <LoadingButton
                    fullWidth
                    loading={submitLoading}
                    variant="contained"
                    size="large"
                    onClick={handleFinalSubmit}
                    disabled={selectedDay === null || selectedHour === null}
                    sx={{py: 1.5, borderRadius: 300, boxShadow: 3}}
                >
                    ثبت درخواست جمع‌آوری
                </LoadingButton>
            </Box>
        </Box>
    );
}

export default CollectSchedule;