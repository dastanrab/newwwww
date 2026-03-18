import React, {useEffect, useState} from "react";
import {Box, Button, Typography} from "@mui/material";
//import {useNavigate} from "react-router-dom";
import {useLocation} from "react-router-dom";
import {Swiper, SwiperSlide} from "swiper/react";
import 'swiper/swiper-bundle.css';
import {useRequest} from "../hooks/useRequest";
import {useAuthStore} from "../store/useAuthStore";
import {CircularProgress} from "@mui/material";
import {LoadingButton} from "@mui/lab";

function CollectSchedule() {

    const [submitLoading, setSubmitLoading] = useState(false);
    const {createRequest} = useRequest();
    const locationState = useLocation().state as {
        addressId?: number;
    };

    const addressId = locationState?.addressId;

    const {accessToken} = useAuthStore();
    const {getScheduling, loading, error} = useRequest();

    const [days, setDays] = useState<any[]>([]);

    useEffect(() => {
        if (!addressId) return;

        const fetchSchedule = async () => {
            // @ts-ignore
            const res = await getScheduling(addressId, accessToken);
            if (res.status === "success") {
                // خروجی API: res.data.list
                // @ts-ignore
                setDays(res.data.list || []);
            } else {
                console.error(res.message);
            }
        };

        fetchSchedule();
    }, [addressId, accessToken]);

    const [selectedDay, setSelectedDay] = useState<number | null>(null);
    const [selectedHour, setSelectedHour] = useState<number | null>(null);


    const handleDaySelect = (index: number) => {
        setSelectedDay(index);
    };

    const handleHourSelect = (index: number) => {
        setSelectedHour(index);
    };

    const handleDaySlideChange = (swiper: any) => {
        setSelectedDay(swiper.activeIndex);
    };

    const handleHourSlideChange = (swiper: any) => {
        setSelectedHour(swiper.activeIndex);
    };

    const handleFinalSubmit = async () => {
        if (selectedDay === null || selectedHour === null || !addressId) return;

        const selectedDayData = days[selectedDay];
        const selectedHourData = days[selectedDay].hours[selectedHour];

        const payload = {
            addressId: addressId,
            cardId: null,
            payMethod: "aniroob",
            scheduling: {day: selectedDayData.value, hour: selectedHourData.value},
        };

        try {
            setSubmitLoading(true);

            // @ts-ignore
            const res = await createRequest(accessToken, payload);

            if (res.status === "success") {
                console.log("درخواست با موفقیت ثبت شد", res.data);
                window.location.href = '/'
            } else {
                console.error(res.message);
            }
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
            <Typography color="error" align="center">{error}</Typography>
        );
    }
    return (
        <Box className="zo-collect">
            <Typography variant="h5" sx={{mb: 1, textAlign: 'center'}}>انتخاب تاریخ و زمان</Typography>
            <Typography variant="h6">انتخاب روز</Typography>
            <Box sx={{mb: 1.5}}>
                <Swiper
                    spaceBetween={5}
                    slidesPerView={3}
                    onSlideChange={handleDaySlideChange}
                    onSwiper={(swiper) => {
                        if (selectedDay !== null) {
                            swiper.slideTo(selectedDay);
                        }
                    }}
                >
                    {days.map((day, index) => (
                        <SwiperSlide key={index}>
                            <Button
                                fullWidth
                                variant={selectedDay === index ? "contained" : "outlined"}
                                color={!day.enabled ? "error" : (selectedDay === index ? "secondary" : "inherit")}
                                disabled={!day.enabled}
                                onClick={() => handleDaySelect(index)}
                                sx={{display: 'flex', flexDirection: 'column', gap: 0.5}}
                            >
                                <Typography variant="body1">{day.weekday}</Typography>
                                <Typography variant="caption">{day.label}</Typography>
                            </Button>
                        </SwiperSlide>
                    ))}
                </Swiper>
            </Box>
            <Typography variant="h6">انتخاب ساعت</Typography>
            <Box sx={{mb: 1.5}}>
                <Swiper
                    spaceBetween={5}
                    slidesPerView={3}
                    onSlideChange={handleHourSlideChange}
                    onSwiper={(swiper) => {
                        if (selectedHour !== null) {
                            swiper.slideTo(selectedHour);
                        }
                    }}
                >
                    {selectedDay !== null && days[selectedDay]?.hours.map((hour: any, index: number) => (
                        <SwiperSlide key={index}>
                            <Button
                                fullWidth
                                variant={selectedHour === index ? "contained" : "outlined"}
                                color={!hour.enabled ? "error" : (selectedHour === index ? "secondary" : "inherit")}
                                disabled={!hour.enabled}
                                onClick={() => handleHourSelect(index)}
                                sx={{display: 'flex', flexDirection: 'column', gap: 0.5}}
                            >
                                <Typography variant="body1">{hour.subLabel}</Typography>
                                <Typography variant="caption">{hour.label}</Typography>
                            </Button>
                        </SwiperSlide>
                    ))}
                </Swiper>
            </Box>
            <Box sx={{width: '100%', position: 'fixed', bottom: 90, right: 0, left: 0, textAlign: 'center'}}>
                <LoadingButton
                    type="submit"
                    variant="contained"
                    size="large"
                    onClick={handleFinalSubmit}
                    disabled={
                        selectedDay === null ||
                        selectedHour === null ||
                        submitLoading
                    }
                    sx={{px: 4.5}}
                >
                    ثبت درخواست جمع‌آوری
                </LoadingButton>
            </Box>
        </Box>
    );
}

export default CollectSchedule;