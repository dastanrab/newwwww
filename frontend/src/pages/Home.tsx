import {Grid, Card, Box, Typography, Button} from "@mui/material";
import {Swiper, SwiperSlide} from "swiper/react";
import 'swiper/swiper-bundle.css';
import slide1 from "../assets/slide1.png";
import slide2 from "../assets/slide2.jpg";
import slide3 from "../assets/slide3.webp";
import recycling from "../assets/recycling.png";
import credit_card from "../assets/credit-card.png";
import leaf from "../assets/leaf.png";
import box from "../assets/box.png";

import West from '@mui/icons-material/West';
import {useNavigate} from "react-router-dom";

export default function Home() {

    const navigate = useNavigate();

    return (
        <Box className="zo-page">
            <Swiper
                spaceBetween={-25}
                slidesPerView={1.25}
                centeredSlides={true}
                loop={true}
                pagination={{clickable: true}}
            >
                <SwiperSlide>
                    <img src={slide1} alt=""/>
                </SwiperSlide>
                <SwiperSlide>
                    <img src={slide2} alt=""/>
                </SwiperSlide>
                <SwiperSlide>
                    <img src={slide3} alt=""/>
                </SwiperSlide>
                <SwiperSlide>
                    <img src={slide1} alt=""/>
                </SwiperSlide>
                <SwiperSlide>
                    <img src={slide2} alt=""/>
                </SwiperSlide>
                <SwiperSlide>
                    <img src={slide3} alt=""/>
                </SwiperSlide>
                <SwiperSlide>
                    <img src={slide1} alt=""/>
                </SwiperSlide>
                <SwiperSlide>
                    <img src={slide3} alt=""/>
                </SwiperSlide>
            </Swiper>
            <Box
                sx={{
                    display: "flex",
                    flexDirection: "column",
                    alignItems: "center",
                    justifyContent: "center",
                    gap: 0,
                    textAlign: "center"
                }}>
                <Box sx={{
                    width: "50px",
                    mt: 3,
                }}>
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

            <Box sx={{mb: 7}}>
                <Grid container spacing={2}>
                    <Grid size={4} sx={{textAlign: "center"}}>
                        <Box sx={{
                            width: "35px",
                            m: "auto"
                        }}>
                            <img src={leaf}/>
                        </Box>
                        <Typography variant="body1" sx={{mb: 0.75}}>درخواست‌ها</Typography>
                        <Typography variant="h1" sx={{fontSize: "1.25rem"}}>
                            12
                        </Typography>
                    </Grid>
                    <Grid size={4} sx={{textAlign: "center"}}>
                        <Box sx={{
                            width: "35px",
                            m: "auto"
                        }}>
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
                    <Grid size={4} sx={{textAlign: "center"}}>
                        <Box sx={{
                            width: "35px",
                            m: "auto"
                        }}>
                            <img src={box}/>
                        </Box>
                        <Typography variant="body1" sx={{mb: 0.75}}>پسماندها</Typography>
                        <Typography variant="h1" sx={{fontSize: "1.25rem"}}>
                            0.6
                            <Typography component="span"
                                        sx={{pl: .25, fontSize: "0.90rem", fontWeight: 400}}>تن</Typography>
                        </Typography>
                    </Grid>
                </Grid>
            </Box>
            <Box sx={{mb: 3}}>
                <Grid container spacing={2}>
                    <Grid size={4} sx={{textAlign: "center", position: "relative"}}>
                        <Card
                            sx={{
                                p: 2,
                                overflow: "visible",
                                borderRadius: 3,
                                cursor: "pointer",
                                transition: "0.2s",
                                "&:hover": {
                                    boxShadow: 3,
                                },
                                "@media (max-width: 475px)": {
                                    p: "16px 4px",
                                },
                            }}

                            onClick={() => navigate("/tickets")}
                        >
                            <Typography variant="h6" sx={{
                                "@media (max-width: 475px)": {
                                    fontSize: "0.875rem",
                                },
                            }}>
                                معرفی دوستان
                            </Typography>
                            <Button color="secondary" variant="contained" size="small"
                                    sx={{
                                        minWidth: "25px",
                                        height: "25px",
                                        p: 0,
                                        position: "absolute",
                                        bottom: "-15px",
                                        right: "15px",
                                    }}>
                                <West fontSize="inherit"/>
                            </Button>
                        </Card>
                    </Grid>
                    <Grid size={4} sx={{textAlign: "center", position: "relative"}}>
                        <Card
                            sx={{
                                p: 2,
                                overflow: "visible",
                                borderRadius: 3,
                                cursor: "pointer",
                                transition: "0.2s",
                                "&:hover": {
                                    boxShadow: 3,
                                },
                                "@media (max-width: 475px)": {
                                    p: "16px 4px",
                                },
                            }}

                            onClick={() => navigate("/prices")}
                        >
                            <Typography variant="h6" sx={{
                                "@media (max-width: 475px)": {
                                    fontSize: "0.875rem",
                                },
                            }}>
                                قیمت پسماندها
                            </Typography>
                            <Button color="secondary" variant="contained" size="small"
                                    sx={{
                                        minWidth: "25px",
                                        height: "25px",
                                        p: 0,
                                        position: "absolute",
                                        bottom: "-15px",
                                        right: "15px",
                                    }}>
                                <West fontSize="inherit"/>
                            </Button>
                        </Card>
                    </Grid>
                    <Grid size={4} sx={{textAlign: "center", position: "relative"}}>
                        <Card
                            sx={{
                                p: 2,
                                overflow: "visible",
                                borderRadius: 3,
                                cursor: "pointer",
                                transition: "0.2s",
                                "&:hover": {
                                    boxShadow: 3,
                                },
                                "@media (max-width: 475px)": {
                                    p: "16px 4px",
                                },
                            }}

                            onClick={() => navigate("/shop")}
                        >
                            <Typography variant="h6" sx={{
                                "@media (max-width: 475px)": {
                                    fontSize: "0.875rem",
                                },
                            }}>
                                اینترنت ، شارژ
                            </Typography>
                            <Button color="secondary" variant="contained" size="small"
                                    sx={{
                                        minWidth: "25px",
                                        height: "25px",
                                        p: 0,
                                        position: "absolute",
                                        bottom: "-15px",
                                        right: "15px",
                                    }}>
                                <West fontSize="inherit"/>
                            </Button>
                        </Card>
                    </Grid>
                    <Grid size={4} sx={{textAlign: "center", position: "relative"}}>
                        <Card
                            sx={{
                                p: 2,
                                overflow: "visible",
                                borderRadius: 3,
                                cursor: "pointer",
                                transition: "0.2s",
                                "&:hover": {
                                    boxShadow: 3,
                                },
                                "@media (max-width: 475px)": {
                                    p: "16px 4px",
                                },
                            }}

                            onClick={() => navigate("/tickets")}
                        >
                            <Typography variant="h6" sx={{
                                "@media (max-width: 475px)": {
                                    fontSize: "0.875rem",
                                },
                            }}>
                                پشتیبانی زی پاک
                            </Typography>
                            <Button color="secondary" variant="contained" size="small"
                                    sx={{
                                        minWidth: "25px",
                                        height: "25px",
                                        p: 0,
                                        position: "absolute",
                                        bottom: "-15px",
                                        right: "15px",
                                    }}>
                                <West fontSize="inherit"/>
                            </Button>
                        </Card>
                    </Grid>
                    <Grid size={4} sx={{textAlign: "center", position: "relative"}}>
                        <Card
                            sx={{
                                p: 2,
                                overflow: "visible",
                                borderRadius: 3,
                                cursor: "pointer",
                                transition: "0.2s",
                                "&:hover": {
                                    boxShadow: 3,
                                },
                                "@media (max-width: 475px)": {
                                    p: "16px 4px",
                                },
                            }}

                            onClick={() => navigate("/profile")}
                        >
                            <Typography variant="h6" sx={{
                                "@media (max-width: 475px)": {
                                    fontSize: "0.875rem",
                                },
                            }}>
                                پروفایل کاربری
                            </Typography>
                            <Button color="secondary" variant="contained" size="small"
                                    sx={{
                                        minWidth: "25px",
                                        height: "25px",
                                        p: 0,
                                        position: "absolute",
                                        bottom: "-15px",
                                        right: "15px",
                                    }}>
                                <West fontSize="inherit"/>
                            </Button>
                        </Card>
                    </Grid>
                    <Grid size={4} sx={{textAlign: "center", position: "relative"}}>
                        <Card
                            sx={{
                                p: 2,
                                overflow: "visible",
                                borderRadius: 3,
                                cursor: "pointer",
                                transition: "0.2s",
                                "&:hover": {
                                    boxShadow: 3,
                                },
                                "@media (max-width: 475px)": {
                                    p: "16px 4px",
                                },
                            }}

                            onClick={() => navigate("/requests")}
                        >
                            <Typography variant="h6" sx={{
                                "@media (max-width: 475px)": {
                                    fontSize: "0.875rem",
                                },
                            }}>
                                درخواست‌ها
                            </Typography>
                            <Button color="secondary" variant="contained" size="small"
                                    sx={{
                                        minWidth: "25px",
                                        height: "25px",
                                        p: 0,
                                        position: "absolute",
                                        bottom: "-15px",
                                        right: "15px",
                                    }}>
                                <West fontSize="inherit"/>
                            </Button>
                        </Card>
                    </Grid>
                </Grid>
            </Box>
        </Box>
    );
}