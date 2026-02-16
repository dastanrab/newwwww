import React, {useEffect, useState} from "react";
import {Link} from "react-router-dom";

import {
    Box,
    Card,
    CardContent,
    Typography,
    Button,
    List,
    ListItem,
    Divider,
    Skeleton,
} from "@mui/material";

interface Request {
    id: number;
    status: "جمع آوری" | "لغو کاربر" | "لغو اپراتور";
    status_color: "primary" | "error" | "warning";
    date: string;
    ambassador: string;
    weight: number;
    amount: number;
}

const requests: Request[] = [
    {
        id: 101,
        status: "جمع آوری",
        status_color: "primary",
        date: "1404/06/20",
        ambassador: "علی رضایی",
        weight: 12,
        amount: 85000,
    },
    {
        id: 102,
        status: "لغو کاربر",
        status_color: "error",
        date: "1404/06/19",
        ambassador: "مریم کاظمی",
        weight: 8,
        amount: 50000,
    },
    {
        id: 103,
        status: "لغو اپراتور",
        status_color: "warning",
        date: "1404/06/18",
        ambassador: "حسین محمدی",
        weight: 5,
        amount: 32000,
    },
];

const RequestSkeleton: React.FC = () => {
    return (
        <Card
            sx={{
                width: "100%",
                position: "relative",
                borderRadius: 3,
                boxShadow: 3,
                overflow: "hidden",
            }}
        >
            <CardContent>
                <List sx={{p: 0}}>
                    {Array.from({length: 5}).map((_, idx) => (
                        <React.Fragment key={idx}>
                            <ListItem
                                sx={{
                                    px: 0,
                                    py: 0.5,
                                    display: "flex",
                                    justifyContent: "space-between",
                                }}
                            >
                                <Skeleton variant="text" width={90} animation="wave"/>
                                <Skeleton variant="text" width={120} animation="wave"/>
                            </ListItem>
                            {idx < 4 && (
                                <Divider sx={{borderColor: "rgba(225,225,225,0.5)"}}/>
                            )}
                        </React.Fragment>
                    ))}
                </List>
                <Box
                    sx={{
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "space-between",
                        mt: 2,
                    }}
                >
                    <Skeleton variant="text" width={80} height={30} animation="wave"/>
                    <Skeleton
                        variant="rectangular"
                        width={120}
                        height={36}
                        sx={{borderRadius: "300px"}}
                        animation="wave"
                    />
                </Box>
            </CardContent>
        </Card>
    );
};

const RequestsPage: React.FC = () => {
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        const timer = setTimeout(() => setIsLoading(false), 2000);
        return () => clearTimeout(timer);
    }, []);

    return (
        <Box>
            <List>
                {isLoading
                    ? Array.from({length: 3}).map((_, i) => (
                        <ListItem key={i} sx={{p: 0, mb: 2}}>
                            <RequestSkeleton/>
                        </ListItem>
                    ))
                    : requests.map((req) => (
                        <ListItem key={req.id} sx={{p: 0, mb: 2}}>
                            <Card
                                sx={{
                                    width: "100%",
                                    position: "relative",
                                    borderRadius: 3,
                                    boxShadow: 3,
                                    "&::before": {
                                        content: '""',
                                        width: "250px",
                                        height: "250px",
                                        display: "block",
                                        position: "absolute",
                                        top: "-90px",
                                        left: "-90px",
                                        background:
                                            req.status_color === "primary"
                                                ? "linear-gradient(90deg, rgb(20, 200, 135) 0%, rgb(15, 160, 105) 100%)"
                                                : req.status_color === "error"
                                                    ? "linear-gradient(90deg, rgb(240, 85, 80) 0%, rgb(210, 45, 45) 100%)"
                                                    : "linear-gradient(90deg, rgb(255, 150, 0) 0%, rgb(235, 110, 0) 100%)",
                                        opacity: ".10",
                                        transform: "rotate(45deg)",
                                        borderRadius: "50%",
                                        filter: "blur(90px)",
                                    },
                                }}
                            >
                                <CardContent>
                                    <List sx={{p: 0}}>
                                        <ListItem
                                            sx={{
                                                px: 0,
                                                py: 0.5,
                                                display: "flex",
                                                justifyContent: "space-between",
                                            }}
                                        >
                                            <Typography variant="body2">شماره درخواست</Typography>
                                            <Typography><strong>{req.id}</strong></Typography>
                                        </ListItem>
                                        <Divider sx={{borderColor: "rgba(225,225,225,0.5)"}}/>
                                        <ListItem
                                            sx={{
                                                px: 0,
                                                py: 0.5,
                                                display: "flex",
                                                justifyContent: "space-between",
                                            }}
                                        >
                                            <Typography variant="body2">تاریخ</Typography>
                                            <Typography>{req.date}</Typography>
                                        </ListItem>
                                        <Divider sx={{borderColor: "rgba(225,225,225,0.5)"}}/>
                                        <ListItem
                                            sx={{
                                                px: 0,
                                                py: 0.5,
                                                display: "flex",
                                                justifyContent: "space-between",
                                            }}
                                        >
                                            <Typography variant="body2">نام سفیر</Typography>
                                            <Typography>{req.ambassador}</Typography>
                                        </ListItem>
                                        <Divider sx={{borderColor: "rgba(225,225,225,0.5)"}}/>
                                        <ListItem
                                            sx={{
                                                px: 0,
                                                py: 0.5,
                                                display: "flex",
                                                justifyContent: "space-between",
                                            }}
                                        >
                                            <Typography variant="body2">وزن</Typography>
                                            <Typography>
                                                <strong>{req.weight}</strong>
                                                <Box component="small" sx={{pl: 0.5}}>کیلوگرم</Box>
                                            </Typography>
                                        </ListItem>
                                        <Divider sx={{borderColor: "rgba(225,225,225,0.5)"}}/>
                                        <ListItem
                                            sx={{
                                                px: 0,
                                                py: 0.5,
                                                display: "flex",
                                                justifyContent: "space-between",
                                            }}
                                        >
                                            <Typography variant="body2">مبلغ</Typography>
                                            <Typography>
                                                <strong>
                                                    {req.amount.toLocaleString("fa-IR")}
                                                </strong>
                                                <Box component="small" sx={{pl: 0.5}}>تومان</Box>
                                            </Typography>
                                        </ListItem>
                                    </List>
                                    <Box
                                        sx={{
                                            display: "flex",
                                            alignItems: "center",
                                            justifyContent: "space-between",
                                            mt: 2,
                                        }}
                                    >
                                        <Typography color={req.status_color}>
                                            {req.status}
                                        </Typography>
                                        <Button
                                            variant="contained"
                                            component={Link}
                                            sx={{
                                                borderRadius: "300px",
                                                px: 4,
                                            }}
                                            to={`/request/${req.id}`}
                                        >
                                            جزئیات درخواست
                                        </Button>
                                    </Box>
                                </CardContent>
                            </Card>
                        </ListItem>
                    ))}
            </List>
        </Box>
    );
};

export default RequestsPage;