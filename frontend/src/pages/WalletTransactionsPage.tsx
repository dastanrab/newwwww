import React, {useEffect, useState} from "react";
import {
    Box,
    Card,
    CardContent,
    Typography,
    List,
    ListItem,
    Skeleton, Button
} from "@mui/material";

import {
    NorthEast,
    SouthWest
} from "@mui/icons-material";

import {useWallet} from "../hooks/useWallet";
import {useAuthStore} from "../store/useAuthStore";
import top from "../assets/top.png";
import empty from "../assets/empty-1.svg";

interface Transaction {
    id: number
    amount: number
    refCode: number
    details: string
    type: "increase" | "decrease"
    date: {
        day: string
        time: string
    }
}

const WalletTransactionsPage: React.FC = () => {

    const {getTransactions} = useWallet()
    const {accessToken, setting} = useAuthStore()

    const [transactions, setTransactions] = useState<Transaction[]>([])
    const [loading, setLoading] = useState(true)

    const loadTransactions = async () => {

        if (!accessToken) {
            setLoading(false)
            return
        }

        try {

            const res = await getTransactions(accessToken)

            if (res.status === "success") {
                // @ts-ignore
                setTransactions(res.data.list)
            }

        } catch (e) {

        } finally {
            setLoading(false)
        }

    }

    useEffect(() => {
        loadTransactions()
    }, [accessToken])


    return (
        <Box>
            <Box sx={{textAlign: "center"}}>
                <Card sx={{
                    borderRadius: 3,
                    boxShadow: 3,
                    position: "relative",
                    "&::before": {
                        content: '""',
                        width: "250px",
                        height: "250px",
                        display: "block",
                        position: "absolute",
                        top: "0",
                        right: "-90px",
                        background: "linear-gradient(90deg, rgb(20, 200, 135 ,.5) 0%, rgb(15, 160, 105 ,.5) 100%)",
                        opacity: 0.15,
                        transform: "rotate(45deg)",
                        borderRadius: "50%",
                        filter: "blur(90px)",
                        zIndex: 1,
                    },
                }}>
                    <CardContent>
                        <Box display="flex" alignItems="center" justifyContent="space-between">
                            <Typography variant="h6">
                                موجودی کیف پول
                            </Typography>
                            {loading ? (
                                <Skeleton width={150}/>
                            ) : (
                                <Typography variant="h6">
                                    {setting?.user.balance.toLocaleString("fa-IR") ?? 0}
                                    <Typography variant="caption" sx={{px: 0.5}}>تومان</Typography>
                                </Typography>
                            )}
                        </Box>
                        <Box sx={{maxWidth: '100px', margin: '0 auto 1.5px'}}><img src={top} alt="تاپ"/></Box>
                        <Typography variant="h6">موجودی آپ</Typography>
                        <Typography sx={{mb: 1.5}}>برای مشاهده کیف پول آپ می بایست به آنی روب دسترسی بدهید.</Typography>
                        <Button
                            variant="outlined"
                            sx={{
                                padding: '7.5px 35px',
                                backgroundColor: 'rgb(240, 75, 35)',
                                color: 'rgb(255, 255, 255)',
                                border: 0,
                                borderRadius: '300px',
                                boxShadow: '0 3px 1px -2px rgba(0, 0, 0, 0.2), 0 2px 2px 0px rgba(0, 0, 0, 0.15), 0 1px 5px 0px rgba(0, 0, 0, 0.10)',
                                '&:hover': {opacity: '0.90'},
                            }}
                        >
                            دریافت مجوز از آپ
                        </Button>
                    </CardContent>
                </Card>
                <Box
                    maxWidth="450px"
                    display="flex"
                    alignItems="center"
                    justifyContent="space-between"
                    gap="15px"
                    margin="15px auto"
                >
                    <Button type="submit" variant="contained" size="large" fullWidth>
                        برداشت از کیف پول
                    </Button>
                    <Button type="submit" variant="contained" size="large" fullWidth>
                        انتقال به آپ
                    </Button>
                </Box>
            </Box>
            {!loading && transactions.length === 0 && (
                <Box sx={{textAlign: "center"}}>
                    <Box sx={{maxWidth: '450px', margin: 'auto'}}>
                        <img src={empty} alt="هیچ تراکنشی ثبت نشده است."/>
                    </Box>
                    <Typography variant="h6" color="text.secondary">
                        هیچ تراکنشی ثبت نشده است.
                    </Typography>
                </Box>
            )}
            <List>
                {loading
                    ? Array.from({length: 4}).map((_, i) => (
                        <ListItem key={i} sx={{p: 0, mb: 2}}>
                            <Card sx={{width: "100%", borderRadius: 3}}>
                                <CardContent>
                                    <Box display="flex" justifyContent="space-between" alignItems="center">
                                        <Box display="flex" gap={2} alignItems="center">
                                            <Skeleton variant="circular" width={45} height={45}/>
                                            <Box>
                                                <Skeleton width={120}/>
                                                <Skeleton width={90}/>
                                            </Box>
                                        </Box>
                                        <Skeleton width={80}/>
                                    </Box>
                                </CardContent>
                            </Card>
                        </ListItem>
                    ))
                    : transactions.map((trx) => (
                        <ListItem key={trx.id} sx={{p: 0, mb: 2}}>
                            <Card sx={{width: "100%", borderRadius: 3, boxShadow: 2}}>
                                <CardContent>
                                    <Box display="flex" alignItems="center" justifyContent="space-between">
                                        <Box display="flex" alignItems="center" gap={1.5}>
                                            <Box
                                                sx={{
                                                    width: 40,
                                                    height: 40,
                                                    borderRadius: 2,
                                                    display: "flex",
                                                    alignItems: "center",
                                                    justifyContent: "center",
                                                    background:
                                                        trx.type === "increase"
                                                            ? "rgba(16,185,129,.15)"
                                                            : "rgba(239,68,68,.15)"
                                                }}
                                            >
                                                {trx.type === "increase"
                                                    ? <NorthEast sx={{color: "#10b981"}}/>
                                                    : <SouthWest sx={{color: "#ef4444"}}/>
                                                }
                                            </Box>
                                            <Box>
                                                <Typography fontWeight={600}>{trx.details}</Typography>
                                                <Typography variant="caption" color="text.secondary">
                                                    {trx.date.day} - {trx.date.time}
                                                </Typography>
                                            </Box>
                                        </Box>
                                        <Box textAlign="right">
                                            <Typography
                                                fontWeight="bold"
                                                color={trx.type === "increase" ? "success.main" : "error.main"}
                                            >

                                                {trx.amount.toLocaleString("fa-IR")}
                                                <Typography variant="caption" sx={{px: 0.5}}>تومان</Typography>
                                                {trx.type === "increase" ? " +" : " -"}
                                            </Typography>
                                        </Box>
                                    </Box>
                                </CardContent>
                            </Card>
                        </ListItem>
                    ))}
            </List>
        </Box>
    )
}

export default WalletTransactionsPage