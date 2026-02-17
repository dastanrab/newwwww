import {Grid, Card, Box, Typography, Stack, Button} from "@mui/material";
import { ShoppingCart, RequestPage, PriceChange, People } from "@mui/icons-material";
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
        <Box className="zo-page" >
            <Swiper
                spaceBetween={-12}
                slidesPerView={1.15}
                centeredSlides
                loop
                pagination={{ clickable: true }}
                style={{
                    paddingTop: 8,
                    paddingBottom: 12,
                }}
            >
                {[slide1, slide2, slide3, slide1, slide2, slide3].map((img, i) => (
                    <SwiperSlide key={i}>
                        <Box
                            sx={{
                                height: { xs: 150, sm: 180 },
                                borderRadius: 3,
                                overflow: "hidden",
                                boxShadow: "0 6px 15px rgba(0,0,0,0.08)",
                            }}
                        >
                            <img
                                src={img}
                                alt=""
                                style={{
                                    width: "100%",
                                    height: "100%",
                                    objectFit: "cover",
                                }}
                            />
                        </Box>
                    </SwiperSlide>
                ))}
            </Swiper>
            <Box sx={{mb: 3}}>
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
            <Box >
                <Grid container spacing={2}>
                    <Grid size={4} sx={{ textAlign: "center" }}>
                        <Card
                            sx={{
                                p: 1.5,
                                borderRadius: 4,
                                cursor: "pointer",
                                backdropFilter: "blur(10px)",
                                background: "rgba(255,255,255,0.6)",
                                border: "1px solid rgba(255,255,255,0.3)",
                                boxShadow: "0 8px 20px rgba(0,0,0,0.05)",
                                transition: "0.2s",
                                "&:hover": {
                                    boxShadow: "0 10px 25px rgba(0,0,0,0.1)",
                                },
                            }}
                            onClick={() => navigate("/prices")}
                        >
                            <Stack spacing={1.5} alignItems="center">
                                <Box
                                    sx={{
                                        width: 50,
                                        height: 50,
                                        borderRadius: "50%",
                                        display: "flex",
                                        alignItems: "center",
                                        justifyContent: "center",
                                        background:
                                            "linear-gradient(90deg, rgb(20, 200, 135) 0%, rgb(15, 160, 105) 100%)",
                                        color: "#fff",
                                    }}
                                >
                                    <PriceChange />
                                </Box>
                                <Typography variant="h6" sx={{ fontSize: "0.9rem" }}>
                                     پسماندها
                                </Typography>
                            </Stack>
                        </Card>
                    </Grid>

                    <Grid size={4} sx={{ textAlign: "center" }}>
                        <Card
                            sx={{
                                p: 1.5,
                                borderRadius: 4,
                                cursor: "pointer",
                                backdropFilter: "blur(10px)",
                                background: "rgba(255,255,255,0.6)",
                                border: "1px solid rgba(255,255,255,0.3)",
                                boxShadow: "0 8px 20px rgba(0,0,0,0.05)",
                                transition: "0.2s",
                                "&:hover": {
                                    boxShadow: "0 10px 25px rgba(0,0,0,0.1)",
                                },
                            }}
                            onClick={() => navigate("/shop")}
                        >
                            <Stack spacing={1.5} alignItems="center">
                                <Box
                                    sx={{
                                        width: 50,
                                        height: 50,
                                        borderRadius: "50%",
                                        display: "flex",
                                        alignItems: "center",
                                        justifyContent: "center",
                                        background:
                                            "linear-gradient(90deg, rgb(20, 200, 135) 0%, rgb(15, 160, 105) 100%)",
                                        color: "#fff",
                                    }}
                                >
                                    <ShoppingCart />
                                </Box>
                                <Typography variant="h6" sx={{ fontSize: "0.9rem" }}>
                                    فروشگاه
                                </Typography>
                            </Stack>
                        </Card>
                    </Grid>

                    <Grid size={4} sx={{ textAlign: "center" }}>
                        <Card
                            sx={{
                                p: 1.5,
                                borderRadius: 4,
                                cursor: "pointer",
                                backdropFilter: "blur(10px)",
                                background: "rgba(255,255,255,0.6)",
                                border: "1px solid rgba(255,255,255,0.3)",
                                boxShadow: "0 8px 20px rgba(0,0,0,0.05)",
                                transition: "0.2s",
                                "&:hover": {
                                    boxShadow: "0 10px 25px rgba(0,0,0,0.1)",
                                },
                            }}
                            onClick={() => navigate("/requests")}
                        >
                            <Stack spacing={1.5} alignItems="center">
                                <Box
                                    sx={{
                                        width: 50,
                                        height: 50,
                                        borderRadius: "50%",
                                        display: "flex",
                                        alignItems: "center",
                                        justifyContent: "center",
                                        background:
                                            "linear-gradient(90deg, rgb(20, 200, 135) 0%, rgb(15, 160, 105) 100%)",
                                        color: "#fff",
                                    }}
                                >
                                    <RequestPage />
                                </Box>
                                <Typography variant="h6" sx={{ fontSize: "0.9rem" }}>
                                    درخواست‌ها
                                </Typography>
                            </Stack>
                        </Card>
                    </Grid>

                    <Grid size={4} sx={{ textAlign: "center" }}>
                        <Card
                            sx={{
                                p: 1.5,
                                borderRadius: 4,
                                cursor: "pointer",
                                backdropFilter: "blur(10px)",
                                background: "rgba(255,255,255,0.6)",
                                border: "1px solid rgba(255,255,255,0.3)",
                                boxShadow: "0 8px 20px rgba(0,0,0,0.05)",
                                transition: "0.2s",
                                "&:hover": {
                                    boxShadow: "0 10px 25px rgba(0,0,0,0.1)",
                                },
                            }}
                            onClick={() => navigate("/tickets")}
                        >
                            <Stack spacing={1.5} alignItems="center">
                                <Box
                                    sx={{
                                        width: 50,
                                        height: 50,
                                        borderRadius: "50%",
                                        display: "flex",
                                        alignItems: "center",
                                        justifyContent: "center",
                                        background:
                                            "linear-gradient(90deg, rgb(20, 200, 135) 0%, rgb(15, 160, 105) 100%)",
                                        color: "#fff",
                                    }}
                                >
                                    <People />
                                </Box>
                                <Typography variant="h6" sx={{ fontSize: "0.9rem" }}>
                                    معرفی دوستان
                                </Typography>
                            </Stack>
                        </Card>
                    </Grid>
                </Grid>
            </Box>
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


        </Box>
    );
}