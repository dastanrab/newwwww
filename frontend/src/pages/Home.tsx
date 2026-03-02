import {Grid, Card, Box, Typography, Stack, Button} from "@mui/material";
import {ShoppingCart, RequestPage, PriceChange, People} from "@mui/icons-material";
import {Swiper, SwiperSlide} from "swiper/react";
import 'swiper/swiper-bundle.css';
import slide1 from "../assets/slide1.png";
import slide2 from "../assets/slide2.jpg";
import slide3 from "../assets/slide3.webp";
import credit_card from "../assets/credit-card.png";
import leaf from "../assets/leaf.png";
import box from "../assets/box.png";
import recycling from "../assets/recycling.png"

import {useNavigate} from "react-router-dom";

export default function Home() {

    const navigate = useNavigate();

    return (
        <Box className="zo-page">
            <Swiper
                spaceBetween={-12}
                slidesPerView={1.15}
                centeredSlides
                loop
                pagination={{clickable: true}}
            >
                {[slide1, slide2, slide3, slide1, slide2, slide3].map((img, i) => (
                    <SwiperSlide key={i}>
                        <Box
                            sx={{
                                height: {xs: 175, sm: 200},
                                mb: 1,
                                overflow: 'hidden',
                                borderRadius: 3,
                                boxShadow: '0 5px 15px rgba(0,0,0,0.075)',
                            }}
                        >
                            <img
                                src={img}
                                alt=""
                                style={{width: '100%', height: '100%', objectFit: 'cover'}}
                            />
                        </Box>
                    </SwiperSlide>
                ))}
            </Swiper>
            <Box sx={{my: 1.5}}>
                <Grid container spacing={2}>
                    <Grid size={4} sx={{textAlign: 'center'}}>
                        <Box sx={{width: '35px', m: 'auto'}}>
                            <img src={leaf}/>
                        </Box>
                        <Typography variant="body1" sx={{mb: 0.75}}>درخواست‌ها</Typography>
                        <Typography variant="h1" sx={{fontSize: "1.25rem"}}>
                            12
                        </Typography>
                    </Grid>
                    <Grid size={4} sx={{textAlign: 'center'}}>
                        <Box sx={{width: '35px', m: 'auto'}}>
                            <img src={credit_card}/>
                        </Box>
                        <Typography variant="body1" sx={{mt: -0.5, mb: 0.75}}>درآمد</Typography>
                        <Typography variant="h1" sx={{fontSize: "1.25rem"}}>
                            350,600
                            <Typography component="span" sx={{pl: .25, fontSize: '0.90rem', fontWeight: 400}}>
                                تومان
                            </Typography>
                        </Typography>
                    </Grid>
                    <Grid size={4} sx={{textAlign: 'center'}}>
                        <Box sx={{width: '35px', m: 'auto'}}>
                            <img src={box}/>
                        </Box>
                        <Typography variant="body1" sx={{mb: 0.75}}>پسماندها</Typography>
                        <Typography variant="h1" sx={{fontSize: "1.25rem"}}>
                            0.6
                            <Typography component="span" sx={{pl: .25, fontSize: "0.90rem", fontWeight: 400}}>
                                تن
                            </Typography>
                        </Typography>
                    </Grid>
                </Grid>
            </Box>
            <Grid container spacing={2} sx={{my: 3}}>
                <Grid size={4}>
                    <Card
                        sx={{
                            p: 1.5,
                            background: 'rgba(255, 255, 255)',
                            border: '1px solid rgba(255,255,255,0.25)',
                            borderRadius: 4,
                            boxShadow: '0 10px 20px rgba(0,0,0,0.05)',
                            cursor: 'pointer',
                            backdropFilter: 'blur(10px)',
                            transition: "0.25s",
                            '&:hover': {
                                boxShadow: '0 10px 25px rgba(0,0,0,0.25)',
                            },
                        }}
                        onClick={() => navigate("/prices")}
                    >
                        <Stack spacing={1.5} alignItems="center">
                            <Box
                                sx={{
                                    width: 50,
                                    height: 50,
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    background: 'linear-gradient(90deg, rgb(20, 200, 135) 0%, rgb(15, 160, 105) 100%)',
                                    color: 'rgb(255, 255, 255)',
                                    borderRadius: '50%',
                                }}
                            >
                                <PriceChange/>
                            </Box>
                            <Typography variant="h6" sx={{fontSize: '0.90rem'}}>
                                پسماندها
                            </Typography>
                        </Stack>
                    </Card>
                </Grid>
                <Grid size={4}>
                    <Card
                        sx={{
                            p: 1.5,
                            background: 'rgba(255, 255, 255)',
                            border: '1px solid rgba(255,255,255,0.25)',
                            borderRadius: 4,
                            boxShadow: '0 10px 20px rgba(0,0,0,0.05)',
                            cursor: 'pointer',
                            backdropFilter: 'blur(10px)',
                            transition: "0.25s",
                            '&:hover': {
                                boxShadow: '0 10px 25px rgba(0,0,0,0.25)',
                            },
                        }}
                        onClick={() => navigate("/shop")}
                    >
                        <Stack spacing={1.5} alignItems="center">
                            <Box
                                sx={{
                                    width: 50,
                                    height: 50,
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    background: 'linear-gradient(90deg, rgb(20, 200, 135) 0%, rgb(15, 160, 105) 100%)',
                                    color: 'rgb(255, 255, 255)',
                                    borderRadius: '50%',
                                }}
                            >
                                <ShoppingCart/>
                            </Box>
                            <Typography variant="h6" sx={{fontSize: '0.90rem'}}>
                                اینترنت ، شارژ
                            </Typography>
                        </Stack>
                    </Card>
                </Grid>
                <Grid size={4}>
                    <Card
                        sx={{
                            p: 1.5,
                            background: 'rgba(255, 255, 255)',
                            border: '1px solid rgba(255,255,255,0.25)',
                            borderRadius: 4,
                            boxShadow: '0 10px 20px rgba(0,0,0,0.05)',
                            cursor: 'pointer',
                            backdropFilter: 'blur(10px)',
                            transition: "0.25s",
                            '&:hover': {
                                boxShadow: '0 10px 25px rgba(0,0,0,0.25)',
                            },
                        }}
                        onClick={() => navigate("/requests")}
                    >
                        <Stack spacing={1.5} alignItems="center">
                            <Box
                                sx={{
                                    width: 50,
                                    height: 50,
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    background: 'linear-gradient(90deg, rgb(20, 200, 135) 0%, rgb(15, 160, 105) 100%)',
                                    color: 'rgb(255, 255, 255)',
                                    borderRadius: '50%',
                                }}
                            >
                                <RequestPage/>
                            </Box>
                            <Typography variant="h6" sx={{fontSize: '0.90rem'}}>
                                درخواست‌ها
                            </Typography>
                        </Stack>
                    </Card>
                </Grid>
            </Grid>
            <Box
                sx={{
                    display: 'flex',
                    alignItems: 'center',
                    flexDirection: 'column',
                    justifyContent: 'center',
                    gap: 0,
                    textAlign: 'center'
                }}>
                <Box sx={{width: '50px'}}>
                    <img src={recycling}/>
                </Box>
                <Typography variant="h6">درخواست فعالی ندارین!</Typography>
                <Typography variant="body1" sx={{py: 0.25}}>
                    طبیعت هنوز به شما احتیاج داره، سهم‌ِتون رو ادا کنین
                </Typography>
                <Button
                    variant="contained"
                    size="large"
                    sx={{
                        borderRadius: "300px",
                        mt: 1.5,
                        mb: 3,
                        px: 6,
                    }}
                    onClick={() => navigate("/collect")}
                >
                    درخواست جمع آوری
                </Button>
            </Box>
        </Box>
    );
}