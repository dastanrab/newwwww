import {useEffect, useState} from "react";
import {Box, Button, Typography} from "@mui/material";
//import {useNavigate} from "react-router-dom";
import { useLocation } from "react-router-dom";
import {Swiper, SwiperSlide} from "swiper/react";
import 'swiper/swiper-bundle.css';
import { useRequest } from "../hooks/useRequest";
import { useAuthStore } from "../store/useAuthStore";
import { CircularProgress } from "@mui/material";

function CollectSchedule() {

    const [submitLoading, setSubmitLoading] = useState(false);
    const { createRequest } = useRequest();
    const locationState = useLocation().state as {
        addressId?: number;
    };

    const addressId = locationState?.addressId;

    const { accessToken } = useAuthStore();
    const { getScheduling, loading, error } = useRequest();

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
            scheduling: {day : selectedDayData.value , hour:selectedHourData.value},
        };

        try {
            setSubmitLoading(true);

            // @ts-ignore
            const res = await createRequest(accessToken, payload);

            if (res.status === "success") {
                console.log("درخواست با موفقیت ثبت شد", res.data);
                window.location.href='/'
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
                <CircularProgress />
            </Box>
        );
    }

    if (error) {
        return (
            <Typography color="error" align="center">
                {error}
            </Typography>
        );
    }
    return (
        <Box>
            <Typography variant="h5" sx={{mb: 1.5, fontWeight: 'bold', textAlign: 'center'}}>
                انتخاب تاریخ و زمان
            </Typography>
            <Typography variant="h6" sx={{mb: 1.5, fontWeight: 'bold'}}>
                انتخاب روز
            </Typography>
            <Box sx={{mb: 3.5}}>
                <Swiper
                    spaceBetween={15}
                    slidesPerView={3}
                    onSlideChange={handleDaySlideChange}
                    onSwiper={(swiper) => {
                        if (selectedDay !== null) {
                            swiper.slideTo(selectedDay);
                        }
                    }}
                    style={{transform: 'scale(1)'}}
                >
                    {days.map((day, index) => (
                        <SwiperSlide key={index}>
                            <Button
                                fullWidth
                                variant={selectedDay === index ? "contained" : "outlined"}
                                color={!day.enabled ? "error" : (selectedDay === index ? "primary" : "inherit")}
                                disabled={!day.enabled}
                                onClick={() => handleDaySelect(index)}
                            >
                                <Typography variant="h6">{day.weekday}</Typography>
                                <Typography variant="body2">{day.label}</Typography>
                            </Button>
                        </SwiperSlide>
                    ))}
                </Swiper>
            </Box>
            <Typography variant="h6" sx={{mb: 1.5, fontWeight: 'bold'}}>
                انتخاب ساعت
            </Typography>
            <Box sx={{mb: 3.5}}>
                <Swiper
                    spaceBetween={10}
                    slidesPerView={3}
                    onSlideChange={handleHourSlideChange}
                    onSwiper={(swiper) => {
                        if (selectedHour !== null) {
                            swiper.slideTo(selectedHour);
                        }
                    }}
                    style={{transform: 'scale(1)'}}
                >
                    {selectedDay !== null && days[selectedDay]?.hours.map((hour: any, index: number) => (
                        <SwiperSlide key={index}>
                            <Button
                                fullWidth
                                variant={selectedHour === index ? "contained" : "outlined"}
                                color={!hour.enabled ? "error" : (selectedHour === index ? "primary" : "inherit")}
                                disabled={!hour.enabled}
                                onClick={() => handleHourSelect(index)}
                            >
                                <Typography variant="h6">{hour.subLabel}</Typography>
                                <Typography variant="body2">{hour.label}</Typography>
                            </Button>
                        </SwiperSlide>
                    ))}
                </Swiper>
            </Box>
            <Box sx={{width: '100%', position: 'fixed', bottom: 90, right: 0, left: 0, textAlign: 'center'}}>
                <Button
                    onClick={handleFinalSubmit}
                    variant="contained"
                    size="large"
                    disabled={
                        selectedDay === null ||
                        selectedHour === null ||
                        submitLoading
                    }
                    sx={{px: 5, borderRadius: '300px'}}
                >
                    {submitLoading ? <CircularProgress size={24} color="inherit" /> : "تایید نهایی"}
                </Button>
            </Box>
        </Box>
    );
}

export default CollectSchedule;