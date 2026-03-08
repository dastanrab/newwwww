import {
    Grid,
    Box,
    Card,
    CardContent,
    Typography,
    Button,
    Skeleton,
} from "@mui/material";
import {Link} from "react-router-dom";
import KeyboardArrowLeftIcon from '@mui/icons-material/KeyboardArrowLeft';
import KeyboardArrowLeft from "@mui/icons-material/KeyboardArrowLeft";
import {useState, useEffect} from "react";
import {useTicket} from "../../hooks/useTicket";
import {useAuthStore} from "../../store/useAuthStore";

interface TicketType {
    id: number
    refId: number
    title: string
    seen: boolean
    date: {
        day: string
        time: string
    }
}

const TicketSkeleton: React.FC = () => {
    return (
        <Card
            sx={{
                p: 2,
                borderRadius: 3,
                boxShadow: 3,
            }}
        >
            <Skeleton variant="text" width="40%" height={30} animation="wave"/>
            <Box display="flex" justifyContent="space-between" mb={1}>
                <Skeleton variant="text" width="30%" height={20} animation="wave"/>
                <Skeleton variant="text" width="20%" height={20} animation="wave"/>
            </Box>
            <Skeleton variant="rectangular" height={40} sx={{mb: 2}} animation="wave"/>
            <Box display="flex" justifyContent="end">
                <Skeleton variant="circular" width={30} height={30} animation="wave"/>
            </Box>
        </Card>
    );
};

export default function TicketListPage() {

    const {getTickets, loading} = useTicket();
    const {accessToken} = useAuthStore();

    const [tickets, setTickets] = useState<TicketType[]>([]);

    useEffect(() => {

        const fetchTickets = async () => {

            if (!accessToken)
            {
                return
            }
            const res = await getTickets(accessToken);

            if (res.status === "success") {
                // @ts-ignore
                setTickets(res.data.list || []);
            }

        };

        fetchTickets();

    }, []);

    return (
        <Box>

            {/* Ticket List Content */}
            <Box sx={{mb: 7}}>
                <Grid container spacing={2}>
                    <Grid size={12}>
                        <Box display="flex" flexDirection="column" gap={2}>
                            {loading
                                ? Array.from({length: 3}).map((_, i) => <TicketSkeleton key={i}/>)
                                : tickets.map((ticket) => (
                                    <Card
                                        key={ticket.id}
                                        sx={{
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
                                                background: "linear-gradient(90deg, rgb(20, 200, 135) 0%, rgb(15, 160, 105) 100%)",
                                                opacity: 0.15,
                                                transform: "rotate(45deg)",
                                                borderRadius: "50%",
                                                filter: "blur(90px)",
                                                zIndex: 0,
                                            },
                                        }}
                                    >
                                        <CardContent>
                                            <Typography variant="h6" gutterBottom>
                                                {ticket.title}
                                            </Typography>

                                            <Box display="flex" justifyContent="space-between" mb={1}>
                                                <Typography variant="body2" color="text.secondary">
                                                    تاریخ: {ticket.date.day}-{ticket.date.time}
                                                </Typography>

                                                <Typography variant="body2" color="text.secondary">
                                                    کد پیگیری : {ticket.refId}
                                                </Typography>
                                            </Box>

                                            <Box display="flex" justifyContent="end">
                                                <Button
                                                    sx={{p: 0, height: 30, minWidth: 30, borderRadius: '50%'}}
                                                    variant="contained"
                                                    component={Link}
                                                    to={`/tickets/${ticket.id}`}
                                                >
                                                    <KeyboardArrowLeftIcon/>
                                                </Button>
                                            </Box>
                                        </CardContent>
                                    </Card>
                                ))
                            }
                        </Box>
                    </Grid>
                </Grid>
            </Box>

            {/* Support Request Button */}
            <Button
                component={Link}
                to="/tickets/new"
                sx={{
                    width: 200,
                    position: 'fixed',
                    bottom: '90px',
                    right: '0',
                    left: '0',
                    m: 'auto',
                    p: '10px 0',
                    borderRadius: '300px'
                }}
                variant="contained"
                endIcon={<KeyboardArrowLeft/>}
            >
                <span>درخواست پشتیبانی</span>
            </Button>
        </Box>
    );
}