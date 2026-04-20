import {Grid, Card, Box, Typography, Stack, Button, CircularProgress, IconButton} from "@mui/material";
import {ShoppingCart,  PriceChange} from "@mui/icons-material";
import WalletIcon from '@mui/icons-material/Wallet';
import {Swiper, SwiperSlide} from "swiper/react";
import 'swiper/swiper-bundle.css';
import slide1 from "../assets/slide1.png";
import slide2 from "../assets/slide2.jpg";
import slide3 from "../assets/slide3.webp";
import credit_card from "../assets/credit-card.png";
import leaf from "../assets/leaf.png";
import box from "../assets/box.png";
import recycling from "../assets/recycling.png"
import {useRequest} from "../hooks/useRequest";
import {Snackbar, Alert} from "@mui/material";
import DeleteIcon from '@mui/icons-material/Delete'; // Import the Delete icon


import {useNavigate} from "react-router-dom";
import {useAuthStore} from "../store/useAuthStore";
import {useEffect, useState} from "react";

export default function Home() {

    type SnackType = "success" | "error" | "warning" | "info";

    const [snack, setSnack] = useState<{
        open: boolean;
        message: string;
        type: SnackType;
    }>({
        open: false,
        message: "",
        type: "success",
    });
    const [listLoading, setLoading] = useState(true);
    const [requests, setRequests] = useState([]);
    const {deleteRequest, loading, getList} = useRequest();
    const navigate = useNavigate();
    let total_weights = 0;
    const [total_requests, setTotalRequests] = useState(0);
    const {setting, accessToken, setSetting} = useAuthStore();
    console.log(setting)
    const handleCancelRequest = async () => {
        if (!setting?.currentRequest?.id) return;

        // @ts-ignore
        const res = await deleteRequest(accessToken);

        if (res.status === "success") {
            console.log("درخواست با موفقیت لغو شد");
            setSetting((prev: any) => ({
                ...prev,
                currentRequest: null,
            }));
            setTotalRequests(total_requests + 1)
            setSnack({
                open: true,
                message: "درخواست با موفقیت لغو شد",
                type: "success",
            });
        } else {
            console.log(res.message);
            setSnack({
                open: true,
                message: res.message || "خطا در لغو درخواست",
                type: "error",
            });
        }
    };
    const getRequests = async () => {
        if (!accessToken) {
            setLoading(false);
            return;
        }

        try {
            const res = await getList(accessToken);
            // @ts-ignore
            const request = res.data.list
            // @ts-ignore
            request.map((req) => {
                total_weights += req.status.value == 3 ? req.weight : 0
            })
            setTotalRequests(request.length)
            // @ts-ignore
            setRequests(request)
        } catch (error: any) {

        } finally {
            setLoading(false);
        }
    };
    useEffect(() => {
        getRequests()
    }, [accessToken]);

    if (listLoading) {
        return (
            <Box
                sx={{
                    height: "100vh",
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                }}
            >
                <CircularProgress size={50}/>
            </Box>
        );
    }
    console.log('requests', requests)
    // @ts-ignore
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
            <Box sx={{my: 0.5}}>
                <Grid container spacing={2}>
                    {/* Requests */}
                    <Grid
                        size={4}
                        sx={{textAlign: 'center', cursor: "pointer"}}
                        onClick={() => navigate("/requests")}
                    >
                        <Box sx={{width: '25px', m: 'auto'}}>
                            <img src={leaf}/>
                        </Box>
                        <Typography variant="body1" sx={{mt: -0.5, mb: 0.25}}>درخواست‌ها</Typography>
                        <Typography variant="h1" sx={{fontSize: '1rem'}}>
                            {total_requests}
                        </Typography>
                    </Grid>

                    {/* Salary */}
                    <Grid
                        size={4}
                        sx={{textAlign: 'center', cursor: "pointer"}}
                        onClick={() => navigate("/wallet")}
                    >
                        <Box sx={{width: '25px', m: 'auto'}}>
                            <img src={credit_card}/>
                        </Box>
                        <Typography variant="body1" sx={{mt: -0.5, mb: 0.25}}>درآمد</Typography>
                        <Typography variant="h1" sx={{fontSize: '1rem'}}>
                            {setting?.user?.balance ?? 0}
                            <Typography component="span" sx={{pl: .25, fontSize: '0.75rem', fontWeight: 400}}>
                                تومان
                            </Typography>
                        </Typography>
                    </Grid>

                    {/* Waste */}
                    <Grid size={4} sx={{textAlign: 'center'}}>
                        <Box sx={{width: '25px', m: 'auto'}}>
                            <img src={box}/>
                        </Box>
                        <Typography variant="body1" sx={{mt: -0.5, mb: 0.25}}>پسماندها</Typography>
                        <Typography variant="h1" sx={{fontSize: '1rem'}}>
                            {total_weights}
                            <Typography component="span" sx={{pl: .25, fontSize: '0.75rem', fontWeight: 400}}>
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
                            p: 1.25,
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
                        <Stack spacing={0.5} alignItems="center">
                            <Box
                                sx={{
                                    width: 35,
                                    height: 35,
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    background: 'linear-gradient(90deg, rgb(20, 200, 135) 0%, rgb(15, 160, 105) 100%)',
                                    color: 'rgb(255, 255, 255)',
                                    borderRadius: '50%',
                                }}
                            >
                                <PriceChange sx={{fontSize: '1rem'}}/>
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
                            p: 1.25,
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
                        <Stack spacing={0.5} alignItems="center">
                            <Box
                                sx={{
                                    width: 35,
                                    height: 35,
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    background: 'linear-gradient(90deg, rgb(20, 200, 135) 0%, rgb(15, 160, 105) 100%)',
                                    color: 'rgb(255, 255, 255)',
                                    borderRadius: '50%',
                                }}
                            >
                                <ShoppingCart sx={{fontSize: '1rem'}}/>
                            </Box>
                            <Typography variant="h6" sx={{fontSize: '0.90rem'}}>
                                فروشگاه
                            </Typography>
                        </Stack>
                    </Card>
                </Grid>
                <Grid size={4}>
                    <Card
                        sx={{
                            p: 1.25,
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
                        onClick={() => navigate("/wallet")}
                    >
                        <Stack spacing={0.5} alignItems="center">
                            <Box
                                sx={{
                                    width: 35,
                                    height: 35,
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    background: 'linear-gradient(90deg, rgb(20, 200, 135) 0%, rgb(15, 160, 105) 100%)',
                                    color: 'rgb(255, 255, 255)',
                                    borderRadius: '50%',
                                }}
                            >
                                <WalletIcon sx={{fontSize: '1rem'}}/>
                            </Box>
                            <Typography variant="h6" sx={{fontSize: '0.90rem'}}>
                                کیف پول
                            </Typography>
                        </Stack>
                    </Card>
                </Grid>
            </Grid>
            {setting?.currentRequest ? (
                <Card
                    sx={{
                        mb: 3,
                        p: 2,
                        border: '2px solid rgb(15, 160, 105)',
                        borderRadius: 4,
                        boxShadow: '0 10px 25px rgba(0,0,0,0.08)',
                    }}
                >
                    {setting.currentRequest.cancelable && (
                        <Stack
                            direction="row"
                            alignItems="center"
                            justifyContent="space-between"
                            spacing={1}
                            sx={{ mb: 1 }}
                        >
                            <Typography variant="h6" sx={{ fontWeight: 600 }}>
                                درخواست فعال شما
                            </Typography>

                            <IconButton
                                onClick={handleCancelRequest}
                                sx={{
                                    color: 'rgb(255, 0, 0)',
                                    p: 0.5

                                }}
                                disabled={loading}
                            >
                                <DeleteIcon fontSize="large" />
                            </IconButton>
                        </Stack>
                    )}

                    <Stack spacing={1.5}>
                        <Typography variant="body2">
                            📍 {setting.currentRequest.address}
                        </Typography>

                        <Typography variant="body2">
                            📅 {setting.currentRequest.requestDate.day}
                        </Typography>

                        <Typography variant="body2">
                            ⏰ {setting.currentRequest.requestDate.range}
                        </Typography>

                        <Typography variant="body2">
                            وضعیت: {setting.currentRequest.status.label}
                        </Typography>
                    </Stack>
                </Card>
            ) : (
                <Box
                    sx={{
                        display: 'flex',
                        alignItems: 'center',
                        flexDirection: 'column',
                        justifyContent: 'center',
                        textAlign: 'center'
                    }}
                >
                    <Box sx={{width: 30}}>
                        <img src={recycling}/>
                    </Box>

                    <Typography variant="h6">
                        درخواست فعالی ندارین!
                    </Typography>

                    <Typography variant="body1" sx={{py: 0.25}}>
                        طبیعت هنوز به شما احتیاج داره، سهم‌ِتون رو ادا کنین
                    </Typography>

                    <Button
                        variant="contained"
                        size="large"
                        sx={{borderRadius: "300px", mt: 1.5, mb: 3, px: 6}}
                        onClick={() => navigate("/collect")}
                    >
                        درخواست جمع آوری
                    </Button>
                </Box>
            )}
            <Snackbar
                open={snack.open}
                autoHideDuration={3000}
                onClose={() => setSnack({...snack, open: false})}
                anchorOrigin={{vertical: "bottom", horizontal: "center"}}
            >
                <Alert
                    onClose={() => setSnack({...snack, open: false})}
                    severity={snack.type || 'success'}
                    variant="filled"
                    sx={{width: "100%"}}
                >
                    {snack.message}
                </Alert>
            </Snackbar>
        </Box>
    );
}