import React, {useEffect, useState} from "react";
import {Card, CardContent, Typography, Grid, Box, Skeleton} from "@mui/material";
import {useTicket} from "../hooks/useTicket";
import {useAuthStore} from "../store/useAuthStore.ts";

const MessageSkeleton: React.FC = () => (
    <Card sx={{mb: 1.5, borderRadius: 3, boxShadow: 3}}>
        <CardContent>
            <Box sx={{display: "flex", justifyContent: "space-between", mb: 1}}>
                <Skeleton variant="text" width={120} height={30} animation="wave"/>
                <Skeleton variant="text" width={60} height={20} animation="wave"/>
            </Box>
            <Box sx={{mt: 1}}>
                <Skeleton variant="text" width="100%" height={20} animation="wave"/>
                <Skeleton variant="text" width="90%" height={20} animation="wave"/>
                <Skeleton variant="text" width="80%" height={20} animation="wave"/>
            </Box>
        </CardContent>
    </Card>
);

const Messages: React.FC = () => {
    const {getMessages} = useTicket();
    const [messages, setMessages] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);
    const {accessToken} = useAuthStore();
    useEffect(() => {
        const fetchMessages = async () => {
            if (!accessToken) {
                return
            }
            setLoading(true);
            const res = await getMessages(accessToken);
            if (res.status === "success") {
                // @ts-ignore
                setMessages(res.data.list);
            }
            setLoading(false);
        };

        fetchMessages();
    }, [accessToken]);


    return (
        <Box>
            {loading
                ? Array.from({length: 12}).map((_) => (
                    <MessageSkeleton/>
                ))
                : messages.map((msg, index) => (
                    <Grid size={{xs: 12}} key={index}>
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
                                <Box sx={{display: "flex", justifyContent: "space-between"}}>
                                    <Typography variant="h6" gutterBottom>{msg.title}</Typography>
                                    <Typography variant="caption" color="textSecondary" display="block" gutterBottom>
                                        {msg.date}
                                    </Typography>
                                </Box>
                                <Typography variant="body1" style={{whiteSpace: "pre-line"}}>
                                    {msg.message}
                                </Typography>
                            </CardContent>
                        </Card>
                    </Grid>
                ))}
        </Box>
    );
};

export default Messages;