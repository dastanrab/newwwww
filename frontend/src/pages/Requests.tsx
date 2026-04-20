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
import {useRequest} from "../hooks/useRequest.ts";
import {useAuthStore} from "../store/useAuthStore.ts";
import empty from "../assets/empty-1.svg";

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
    const [listLoading, setLoading] = useState(true);
    const [requests, setRequests] = useState<any[]>([]);
    const {getList} = useRequest();

    const {accessToken} = useAuthStore();
    const getRequests = async () => {
        if (!accessToken) {
            setLoading(false);
            return;
        }

        try {
            const res = await getList(accessToken);

            // @ts-ignore
            const list = res?.data?.list;

            if (Array.isArray(list)) {
                // @ts-ignore
                setRequests(list);
            } else {
                setRequests([]); // fallback
                console.error("list is not array:", list);
            }

        } catch (error: any) {
            console.error(error);
            setRequests([]);
        } finally {
            setLoading(false);
        }
    };
    useEffect(() => {
        getRequests()
    }, [accessToken]);

    console.log('requests', requests)


    return (
        <Box>
            <List>
                {listLoading
                    ? Array.from({length: 3}).map((_, i) => (
                        <ListItem key={i} sx={{p: 0, mb: 2}}>
                            <RequestSkeleton/>
                        </ListItem>
                    ))
                    : requests.length === 0 ? (
                        <Box sx={{textAlign: "center"}}>
                            <Box sx={{maxWidth: '450px', margin: 'auto'}}>
                                <img src={empty} alt="درخواست جمع آوری ثبت نشده است."/>
                            </Box>
                            <Typography variant="h6" color="text.secondary">درخواست جمع آوری ثبت نشده است.</Typography>
                        </Box>
                    ) : (requests.map((req) => (
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
                                            req?.status?.value === 3
                                                ? "linear-gradient(90deg, rgb(20, 200, 135) 0%, rgb(15, 160, 105) 100%)"
                                                : req?.status?.value === 4
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
                                            <Typography><strong>{// @ts-ignore
                                                req.id}</strong></Typography>
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
                                            <Typography>{// @ts-ignore
                                                req?.requestDate ? req?.requestDate?.day : '-'}</Typography>
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
                                            <Typography>{// @ts-ignore
                                                req?.driver?.name ?? '-'}</Typography>
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
                                                <strong>{// @ts-ignore
                                                    req.weight ? req.weight : 0}</strong>
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
                                                    {// @ts-ignore
                                                        req.amount ? req.amount.toLocaleString("fa-IR") : 0}
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

                                        <Typography color={// @ts-ignore
                                            req?.status?.value === 3 ? 'primary' : 'error'}>
                                            {// @ts-ignore
                                                req.status.label}
                                        </Typography>
                                        <Button
                                            variant="contained"
                                            component={Link}
                                            sx={{
                                                borderRadius: "300px",
                                                px: 4,
                                            }}
                                            // @ts-ignore
                                            to={`/request/${req.id}`}
                                        >
                                            جزئیات درخواست
                                        </Button>
                                    </Box>
                                </CardContent>
                            </Card>
                        </ListItem>
                    )))
                }
            </List>
        </Box>
    );
};

export default RequestsPage;