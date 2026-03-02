import {useState} from "react";
import {Box, Button, Typography} from "@mui/material";
import {useNavigate} from "react-router-dom";
import {Swiper, SwiperSlide} from "swiper/react";
import 'swiper/swiper-bundle.css';

function CollectSchedule() {
    const navigate = useNavigate();
    const [selectedDay, setSelectedDay] = useState<number | null>(null);
    const [selectedHour, setSelectedHour] = useState<number | null>(null);

    const days = [
        {name: "امروز", date: "1404/07/07"},
        {name: "فردا", date: "1404/07/08"},
        {name: "یکشنبه", date: "1404/07/09"},
        {name: "دوشنبه", date: "1404/07/10"},
        {name: "سه شنبه", date: "1404/07/11"},
        {name: "چهارشنبه", date: "1404/07/12"},
    ];

    const hours = [
        {name: "صبح", date: "11 الی 14"},
        {name: "ظهر", date: "14 الی 15"},
        {name: "عصر", date: "15 الی 16"},
        {name: "عصر", date: "16 الی 18"},
        {name: "شب", date: "18 الی 19"},
        {name: "شب", date: "19 الی 21"},
    ];

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

    const handleFinalSubmit = () => {
        console.log("روز انتخاب شده:", selectedDay !== null ? days[selectedDay] : null);
        console.log("ساعت انتخاب شده:", selectedHour !== null ? hours[selectedHour] : null);
    };

    const fullDays = [1, 3];
    const fullHours = [2, 5];

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
                        <SwiperSlide key={index} style={{transform: 'scale(1)'}}>
                            <Button
                                fullWidth
                                variant={selectedDay === index ? "contained" : "outlined"}
                                color={fullDays.includes(index) ? "error" : (selectedDay === index ? "primary" : "inherit")}
                                disabled={fullDays.includes(index)}
                                onClick={() => handleDaySelect(index)}
                                sx={{
                                    height: '120px',
                                    display: 'flex',
                                    flexDirection: 'column',
                                    justifyContent: 'center',
                                    textAlign: 'center',
                                    borderRadius: 3,
                                    boxShadow: selectedDay === index ? 3 : 1,
                                }}
                            >
                                <Typography variant="h6" sx={{pb: 0.5, fontWeight: 'bold'}}>
                                    {day.name}
                                </Typography>
                                <Typography variant="body2">{day.date}</Typography>
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
                    {hours.map((hour, index) => (
                        <SwiperSlide key={index} style={{transform: 'scale(1)'}}>
                            <Button
                                fullWidth
                                variant={selectedHour === index ? "contained" : "outlined"}
                                color={fullHours.includes(index) ? "error" : (selectedHour === index ? "primary" : "inherit")}
                                disabled={fullHours.includes(index)}
                                onClick={() => handleHourSelect(index)}
                                sx={{
                                    height: '120px',
                                    display: 'flex',
                                    flexDirection: 'column',
                                    justifyContent: 'center',
                                    p: 3,
                                    textAlign: 'center',
                                    borderRadius: 3,
                                    boxShadow: selectedHour === index ? 3 : 1,
                                }}
                            >
                                <Typography variant="h6" sx={{pb: 0.5, fontWeight: 'bold'}}>
                                    {hour.name}
                                </Typography>
                                <Typography variant="body2">{hour.date}</Typography>
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
                    disabled={selectedDay === null || selectedHour === null}
                    sx={{px: 5, borderRadius: '300px'}}
                >
                    تایید نهایی
                </Button>
            </Box>
        </Box>
    );
}

export default CollectSchedule;