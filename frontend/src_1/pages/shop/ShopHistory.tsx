import React, {useState, useEffect} from "react";
import {Box, Card, Typography, Divider, Skeleton} from "@mui/material";

const items = ["بسته اینترنت ایرانسل", "بسته اینترنت همراه اول", "بسته اینترنت رایتل", "کمک به خیریه محک"];

const generateTransactions = () =>
    Array.from({length: 12}, () => {
        const item = items[Math.floor(Math.random() * items.length)];
        return {
            title: item,
            date: `1404/07/${Math.floor(1 + Math.random() * 30)} - ${Math.floor(10 + Math.random() * 14)}:${Math.floor(Math.random() * 60).toString().padStart(2, "0")}`,
            amount: item.includes("کمک")
                ? (Math.floor(Math.random() * 100000) + 5000).toLocaleString()
                : (Math.floor(Math.random() * 100000) + 1000).toLocaleString(),
        };
    });

const ShopHistorySkeleton: React.FC = () => {
    return (
        <Card sx={{p: 2, borderRadius: 3, boxShadow: 3}}>
            {Array.from({length: 12}).map((_, index) => (
                <Box key={index}>
                    <Box
                        sx={{
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "space-between",
                            py: 1.5,
                        }}
                    >
                        <Box>
                            <Skeleton variant="text" width={200} height={30} sx={{mb: 0.5}} animation="wave"/>
                            <Skeleton variant="text" width={120} height={20} animation="wave"/>
                        </Box>
                        <Box>
                            <Skeleton variant="text" width={80} height={30} sx={{mb: 0.5}} animation="wave"/>
                        </Box>
                    </Box>
                    {index < 11 && <Divider/>}
                </Box>
            ))}
        </Card>
    );
};

const ShopHistory: React.FC = () => {
    const [loading, setLoading] = useState(true);
    const [transactions, setTransactions] = useState<Array<{ title: string; date: string; amount: string }>>([]);

    useEffect(() => {
        const timer = setTimeout(() => {
            setTransactions(generateTransactions());
            setLoading(false);
        }, 2000);

        return () => clearTimeout(timer);
    }, []);

    return (
        <Box>
            {loading ? (
                <ShopHistorySkeleton/>
            ) : (
                <Card sx={{p: 2, borderRadius: 3}}>
                    {transactions.map((tx, index) => (
                        <Box key={index}>
                            <Box
                                sx={{
                                    display: "flex",
                                    alignItems: "center",
                                    justifyContent: "space-between",
                                    py: 1.5,
                                }}
                            >
                                <Box>
                                    <Typography variant="h6" sx={{pb: 0.5}}>
                                        {tx.title}
                                    </Typography>
                                    <Typography variant="body2">{tx.date}</Typography>
                                </Box>
                                <Box>
                                    <Typography variant="h6" component="strong" sx={{pr: 0.25}}>
                                        {tx.amount}
                                    </Typography>
                                    <Typography variant="body2" component="small">تومان</Typography>
                                </Box>
                            </Box>
                            {index < transactions.length - 1 && <Divider/>}
                        </Box>
                    ))}
                </Card>
            )}
        </Box>
    );
};

export default ShopHistory;