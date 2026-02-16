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

const Tickets = [
    {
        id: 1,
        title: "مشکل ورود",
        date: "1404/06/24-19:09",
        track: "432",
        content: "هنگام ثبت درخواست جمع‌آوری..."
    },
    {
        id: 2,
        title: "خطای پرداخت",
        date: "1404/09/26-07:25",
        track: "756",
        content: "پرداخت با خطای نامشخص مواجه شد."
    },
    {
        id: 3,
        title: "عدم دریافت پیامک",
        date: "1404/07/10-14:32",
        track: "981",
        content: "پس از ثبت‌نام، هیچ پیامک تأیید برای من ارسال نشده است."
    },
    {
        id: 4,
        title: "لغو درخواست",
        date: "1404/08/02-11:47",
        track: "125",
        content: "می‌خواهم درخواست جمع‌آوری قبلی را لغو کنم."
    },
    {
        id: 5,
        title: "تاخیر در جمع‌آوری",
        date: "1404/08/15-16:20",
        track: "348",
        content: "زمان جمع‌آوری اعلام شده رعایت نشده است."
    },
    {
        id: 6,
        title: "مشکل کیف پول",
        date: "1404/09/01-09:58",
        track: "642",
        content: "موجودی کیف پول به درستی نمایش داده نمی‌شود."
    },
    {
        id: 7,
        title: "بروز خطا در اپلیکیشن",
        date: "1404/09/12-20:33",
        track: "877",
        content: "هنگام باز کردن صفحه پروفایل برنامه بسته می‌شود."
    },
    {
        id: 8,
        title: "نیاز به راهنمایی",
        date: "1404/09/19-13:10",
        track: "509",
        content: "لطفا توضیح دهید چگونه می‌توانم تعرفه‌ها را مشاهده کنم."
    }
];

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
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const timer = setTimeout(() => setLoading(false), 2000);
        return () => clearTimeout(timer);
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
                                : Tickets.map((ticket) => (
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
                                                    تاریخ: {ticket.date}
                                                </Typography>
                                                <Typography variant="body2" color="text.secondary">
                                                    کد پیگیری : {ticket.track}
                                                </Typography>
                                            </Box>
                                            <Typography variant="caption" mb={2}>
                                                {ticket.content}
                                            </Typography>
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