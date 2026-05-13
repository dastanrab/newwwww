import React, {useEffect, useState} from "react";
import {
    Box,
    Card,
    CardContent,
    Typography,
    List,
    ListItem,
    Skeleton,
    Button,
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    TextField,
    InputAdornment,
    MenuItem,
    Alert
} from "@mui/material";

import {
    NorthEast,
    SouthWest,
    AccountBalanceWalletOutlined
} from "@mui/icons-material";

import {useWallet} from "../hooks/useWallet";
import {useAuthStore} from "../store/useAuthStore";
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

interface BankCard {
    value: number
    label: string
    name: string
    bank: string
}

const WalletTransactionsPage: React.FC = () => {

    const {getTransactions} = useWallet()
    const {accessToken, setting} = useAuthStore()

    const [transactions, setTransactions] = useState<Transaction[]>([])
    const [bankCards, setBankCards] = useState<BankCard[]>([])
    const [loading, setLoading] = useState(true)
    const [withdrawDialogOpen, setWithdrawDialogOpen] = useState(false)
    const [withdrawAmount, setWithdrawAmount] = useState("")
    const [selectedCardId, setSelectedCardId] = useState<number | "">("")
    const [withdrawing, setWithdrawing] = useState(false)
    const [error, setError] = useState("")

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

    const loadBankCards = async () => {
        if (!accessToken) return

        try {
            const response = await fetch(`http://185.255.88.111:8000/api/user/cardNumbers`, {
                headers: {
                    'Authorization': `Bearer ${accessToken}`,
                    'Accept': 'application/json'
                }
            })

            const data = await response.json()

            if (data.status === "success") {
                setBankCards(data.data || [])
            }
        } catch (e) {
            console.error("خطا در دریافت کارت‌های بانکی:", e)
        }
    }

    useEffect(() => {
        loadTransactions()
        loadBankCards()
    }, [accessToken])

    const handleWithdrawClick = () => {
        setWithdrawDialogOpen(true)
        setError("")
    }

    const handleWithdrawClose = () => {
        setWithdrawDialogOpen(false)
        setWithdrawAmount("")
        setSelectedCardId("")
        setError("")
    }

    const handleWithdrawSubmit = async () => {
        if (!accessToken) return

        setError("")
        setWithdrawing(true)

        try {
            const response = await fetch(`http://185.255.88.111:8000/api/user/wallet/withdrawal`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${accessToken}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    amount: Number(withdrawAmount),
                    cardId: selectedCardId
                })
            })

            const data = await response.json()

            if (data.status === "success") {
                // به‌روزرسانی موجودی در store
                if (setting) {
                    useAuthStore.setState({
                        setting: {
                            ...setting,
                            user: {
                                ...setting.user,
                                balance: data.data.balance
                            }
                        }
                    })
                }

                // بارگذاری مجدد تراکنش‌ها
                await loadTransactions()

                handleWithdrawClose()
            } else {
                setError(data.message || "خطا در ثبت درخواست برداشت")
            }
        } catch (e) {
            setError("خطا در برقراری ارتباط با سرور")
        } finally {
            setWithdrawing(false)
        }
    }

    const balance = setting?.user.balance ?? 0
    const minWithdraw = 10000

    return (
        <Box>
            <Box sx={{textAlign: "center"}}>
                <Card sx={{
                    borderRadius: 3,
                    boxShadow: 3,
                    position: "relative",
                    py: 3,
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
                        <AccountBalanceWalletOutlined
                            sx={{
                                fontSize: 64,
                                color: "primary.main",
                                mb: 2
                            }}
                        />
                        <Typography variant="body2" color="text.secondary" sx={{ mb: 1 }}>
                            موجودی کیف پول
                        </Typography>
                        {loading ? (
                            <Skeleton width={200} height={60} sx={{ margin: "0 auto" }} />
                        ) : (
                            <Typography variant="h3" fontWeight={800} sx={{ mb: 0.5 }}>
                                {balance.toLocaleString("fa-IR")}
                                <Typography variant="h6" component="span" sx={{ px: 1, fontWeight: 600 }}>
                                    تومان
                                </Typography>
                            </Typography>
                        )}
                    </CardContent>
                </Card>

                <Box
                    maxWidth="450px"
                    margin="15px auto"
                >
                    <Button
                        type="submit"
                        variant="contained"
                        size="large"
                        fullWidth
                        onClick={handleWithdrawClick}
                        sx={{
                            py: 1.5,
                            fontWeight: 700,
                            fontSize: "1rem"
                        }}
                    >
                        برداشت از کیف پول
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

            <Dialog
                open={withdrawDialogOpen}
                onClose={handleWithdrawClose}
                maxWidth="xs"
                fullWidth
                PaperProps={{
                    sx: { borderRadius: 3 }
                }}
            >
                <DialogTitle sx={{ fontWeight: 700 }}>
                    برداشت از کیف پول
                </DialogTitle>
                <DialogContent>
                    <Box sx={{ pt: 2 }}>
                        {error && (
                            <Alert severity="error" sx={{ mb: 2, borderRadius: 2 }}>
                                {error}
                            </Alert>
                        )}

                        <Typography variant="body2" color="text.secondary" sx={{ mb: 2 }}>
                            موجودی قابل برداشت: {balance.toLocaleString("fa-IR")} تومان
                        </Typography>

                        <TextField
                            fullWidth
                            select
                            label="انتخاب کارت بانکی"
                            value={selectedCardId}
                            onChange={(e) => setSelectedCardId(Number(e.target.value))}
                            sx={{ mb: 2 }}
                        >
                            {bankCards.length === 0 ? (
                                <MenuItem disabled>
                                    کارت بانکی ثبت نشده است
                                </MenuItem>
                            ) : (
                                bankCards.map((card) => (
                                    <MenuItem key={card.value} value={card.value}>
                                        {card.name} - {card.label} ({card.bank})
                                    </MenuItem>
                                ))
                            )}
                        </TextField>

                        <TextField
                            fullWidth
                            label="مبلغ برداشت"
                            type="number"
                            value={withdrawAmount}
                            onChange={(e) => setWithdrawAmount(e.target.value)}
                            InputProps={{
                                endAdornment: (
                                    <InputAdornment position="end">
                                        تومان
                                    </InputAdornment>
                                )
                            }}
                            inputProps={{
                                max: balance,
                                min: minWithdraw
                            }}
                            helperText={
                                withdrawAmount && Number(withdrawAmount) > balance
                                    ? "مبلغ وارد شده بیشتر از موجودی است"
                                    : withdrawAmount && Number(withdrawAmount) < minWithdraw
                                        ? `حداقل مبلغ برداشت ${minWithdraw.toLocaleString("fa-IR")} تومان است`
                                        : ""
                            }
                            error={
                                withdrawAmount !== "" &&
                                (Number(withdrawAmount) > balance || Number(withdrawAmount) < minWithdraw)
                            }
                        />
                    </Box>
                </DialogContent>
                <DialogActions sx={{ px: 3, pb: 2.5 }}>
                    <Button
                        onClick={handleWithdrawClose}
                        variant="outlined"
                        sx={{ borderRadius: 2 }}
                        disabled={withdrawing}
                    >
                        انصراف
                    </Button>
                    <Button
                        onClick={handleWithdrawSubmit}
                        variant="contained"
                        disabled={
                            !withdrawAmount ||
                            !selectedCardId ||
                            Number(withdrawAmount) < minWithdraw ||
                            Number(withdrawAmount) > balance ||
                            withdrawing
                        }
                        sx={{ borderRadius: 2 }}
                    >
                        {withdrawing ? "در حال ثبت..." : "تایید برداشت"}
                    </Button>
                </DialogActions>
            </Dialog>
        </Box>
    )
}

export default WalletTransactionsPage
