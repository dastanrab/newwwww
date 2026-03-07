import React, {useEffect, useState} from "react";
import {
    Box,
    Card,
    CardContent,
    Typography,
    List,
    ListItem,
    Skeleton
} from "@mui/material";

import {
    AccountBalanceWallet,
    NorthEast,
    SouthWest
} from "@mui/icons-material";

import {useWallet} from "../hooks/useWallet";
import {useAuthStore} from "../store/useAuthStore";

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
    const {accessToken,setting} = useAuthStore()

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

            {/* Wallet Balance */}

            <Card
                sx={{
                    mb: 3,
                    borderRadius: 4,
                    background: "linear-gradient(135deg,#0ea5e9,#2563eb)",
                    color: "white",
                    boxShadow: 4
                }}
            >

                <CardContent>

                    <Box
                        display="flex"
                        alignItems="center"
                        justifyContent="space-between"
                    >

                        <Box>

                            <Typography variant="body2" sx={{opacity: .8}}>
                                موجودی کیف پول
                            </Typography>

                            {loading ? (
                                <Skeleton width={140}/>
                            ) : (
                                <Typography variant="h5" fontWeight="bold">
                                    {// @ts-ignore
                                        setting.user.balance.toLocaleString("fa-IR")} تومان
                                </Typography>
                            )}

                        </Box>

                        <AccountBalanceWallet sx={{fontSize: 42, opacity: .9}}/>

                    </Box>

                </CardContent>

            </Card>


            {/* Title */}

            <Typography
                variant="h6"
                fontWeight="bold"
                sx={{mb: 2}}
            >
                گردش حساب
            </Typography>


            {/* Empty State */}

            {!loading && transactions.length === 0 && (

                <Card sx={{borderRadius: 3}}>

                    <CardContent sx={{textAlign: "center", py: 5}}>

                        <AccountBalanceWallet sx={{fontSize: 50, color: "grey.400", mb: 1}}/>

                        <Typography color="text.secondary">
                            هیچ تراکنشی ثبت نشده است
                        </Typography>

                    </CardContent>

                </Card>

            )}


            {/* Transactions */}

            <List>

                {loading
                    ? Array.from({length: 4}).map((_, i) => (

                        <ListItem key={i} sx={{p: 0, mb: 2}}>

                            <Card sx={{width: "100%", borderRadius: 3}}>

                                <CardContent>

                                    <Box
                                        display="flex"
                                        justifyContent="space-between"
                                        alignItems="center"
                                    >

                                        <Box display="flex" gap={2} alignItems="center">

                                            <Skeleton
                                                variant="circular"
                                                width={40}
                                                height={40}
                                            />

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

                            <Card
                                sx={{
                                    width: "100%",
                                    borderRadius: 3,
                                    boxShadow: 2
                                }}
                            >

                                <CardContent>

                                    <Box
                                        display="flex"
                                        alignItems="center"
                                        justifyContent="space-between"
                                    >

                                        <Box
                                            display="flex"
                                            alignItems="center"
                                            gap={1.5}
                                        >

                                            {/* Icon */}

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

                                            {/* Details */}

                                            <Box>

                                                <Typography fontWeight={600}>
                                                    {trx.details}
                                                </Typography>

                                                <Typography
                                                    variant="caption"
                                                    color="text.secondary"
                                                >
                                                    {trx.date.day} - {trx.date.time}
                                                </Typography>

                                            </Box>

                                        </Box>


                                        {/* Amount */}

                                        <Box textAlign="right">

                                            <Typography
                                                fontWeight="bold"
                                                color={
                                                    trx.type === "increase"
                                                        ? "success.main"
                                                        : "error.main"
                                                }
                                            >

                                                {trx.amount.toLocaleString("fa-IR")} تومان
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