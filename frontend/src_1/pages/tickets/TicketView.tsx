import {
    Box,
    Button,
    Card,
    CardContent,
    Typography,
    TextField,
    Snackbar,
    Alert
} from "@mui/material";
import {useEffect, useRef, useState} from "react";
import {useParams} from "react-router-dom";
import {useTicket} from "../../hooks/useTicket";
import {useAuthStore} from "../../store/useAuthStore";

interface MessageType {
    type: "admin" | "user"
    name: string
    message: string
    date: {
        day: string
        time: string
    }
}

export default function TicketViewPage() {

    const {id} = useParams();
    const {getTicketDetail, replyTicket} = useTicket();
    const {accessToken} = useAuthStore();
    const [snackbarOpen, setSnackbarOpen] = useState(false);

    const [messages, setMessages] = useState<MessageType[]>([]);
    const [message, setMessage] = useState("");

    const bottomRef = useRef<HTMLDivElement | null>(null);

    const fetchTicket = async () => {
        if (!id || !accessToken) return;

        const res = await getTicketDetail(id, accessToken);

        if (res.status === "success") {
            // @ts-ignore
            setMessages(res.data.messages || []);
        }
    };

    /* دریافت اولیه */
    useEffect(() => {
        fetchTicket();
    }, [id]);

    /* realtime polling */
    useEffect(() => {

        const interval = setInterval(() => {
            fetchTicket();
        }, 8000);

        return () => clearInterval(interval);

    }, [id]);

    /* اسکرول به آخر */
    useEffect(() => {
        bottomRef.current?.scrollIntoView({behavior: "smooth"});
    }, [messages]);

    /* ارسال پیام */
    const sendMessage = async () => {

        if (!message.trim() || !id ||!accessToken) return;

        const text = message;

        setMessage("");

        const res = await replyTicket(id, accessToken, text);

        if (res.status === "success") {
            setSnackbarOpen(true);
            fetchTicket();
        }

    };

    return (
        <Box sx={{pb: 18}}>

            {messages.map((msg, index) => {

                const isUser = msg.type === "user";

                return (
                    <Box
                        key={index}
                        sx={{
                            display: "flex",
                            justifyContent: isUser ? "start" : "end",
                            mb: 1.5,
                        }}
                    >
                        <Card
                            sx={{
                                width: "90%",
                                backgroundColor: isUser
                                    ? "rgba(160, 15, 70,.10)"
                                    : "rgba(20, 200, 135,.10)",
                                borderRadius: 3,
                            }}
                        >
                            <CardContent>

                                <Box
                                    sx={{
                                        display: "flex",
                                        justifyContent: "space-between",
                                        mb: 0.5,
                                    }}
                                >
                                    <Typography variant="h6">
                                        {msg.name}
                                    </Typography>

                                    <Typography variant="body2">
                                        {msg.date.day} {msg.date.time}
                                    </Typography>
                                </Box>

                                <Typography>
                                    {msg.message}
                                </Typography>

                            </CardContent>
                        </Card>
                    </Box>
                );
            })}

            <div ref={bottomRef}/>
            <Box
                sx={{
                    position: "fixed",
                    bottom: 90,
                    left: 0,
                    right: 0,
                    width: "100%",
                    maxWidth: 500,
                    mx: "auto",
                    display: "flex",
                    gap: 1,
                    p: 1,
                }}
            >

                <TextField
                    fullWidth
                    size="small"
                    placeholder="پیام خود را بنویسید..."
                    value={message}
                    onChange={(e) => setMessage(e.target.value)}
                />

                <Button
                    variant="contained"
                    sx={{
                        borderRadius: "300px",
                        px: 3,
                        whiteSpace: "nowrap"
                    }}
                    onClick={sendMessage}
                >
                    ارسال
                </Button>

            </Box>
            <Snackbar
                open={snackbarOpen}
                autoHideDuration={3000}
                onClose={() => setSnackbarOpen(false)}
                anchorOrigin={{ vertical: "bottom", horizontal: "center" }}
            >
                <Alert
                    onClose={() => setSnackbarOpen(false)}
                    severity="success"
                    variant="filled"
                    sx={{ width: "100%" }}
                >
                    پاسخ شما با موفقیت ارسال شد
                </Alert>
            </Snackbar>
        </Box>
    );
}