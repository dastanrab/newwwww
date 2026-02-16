import { useState, useEffect } from 'react';
import {
    Card,
    CardContent,
    Typography,
    Button,
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    Grid,
    Slider,
    Collapse,
    Box,
    Skeleton,
} from "@mui/material";
import recyclables1 from "../assets/img/recyclables-1.png";
import recyclables2 from "../assets/img/recyclables-2.png";
import recyclables3 from "../assets/img/recyclables-3.png";
import recyclables4 from "../assets/img/recyclables-4.png";
import recyclables5 from "../assets/img/recyclables-5.png";
import recyclables6 from "../assets/img/recyclables-6.png";
import recyclables7 from "../assets/img/recyclables-7.png";
import recyclables8 from "../assets/img/recyclables-8.png";
import recyclables10 from "../assets/img/recyclables-10.png";

interface WasteItem {
    id: number;
    title: string;
    subtitle: string;
    image: string;
    pricePerKg: number;
    details: string;
}

const wasteData: WasteItem[] = [
    {
        id: 1,
        title: "کاغذ و کارتن",
        subtitle: "کاغذ تحریر، کاغذ چاپی، کارتن سه لایه، مقوای پشت طوسی و ...",
        image: recyclables1,
        pricePerKg: 20000,
        details: "پلاستیک‌های تمیز شامل بطری و ظروف بازیافتی."
    },
    {
        id: 2,
        title: "پلاستیک",
        subtitle: "ظروف ماست و لبنیات، کیسه های پلاستیکی، سلفون و ...",
        image: recyclables2,
        pricePerKg: 10000,
        details: "انواع بطری و ظروف شیشه‌ای بدون آلودگی."
    },
    {
        id: 3,
        title: "لاک",
        subtitle: "لاک معمولی، اکلیلی، تقویتی و ...",
        image: recyclables3,
        pricePerKg: 5000,
        details: "روزنامه، مجله و کارتن‌های بازیافتی."
    },
    {
        id: 4,
        title: "پت",
        subtitle: "بطری نوشیدنی‌ها، فیلم و ورق، ظروف بسته بندی مواد خوراکی و ...",
        image: recyclables4,
        pricePerKg: 5000,
        details: "روزنامه، مجله، کارتن و کاغذهای بازیافتی."
    },
    {
        id: 5,
        title: "شیشه",
        subtitle: "شیشه شفاف، ساختمانی، سکوریت، رنگی سبز و ...",
        image: recyclables5,
        pricePerKg: 5000,
        details: "روزنامه، مجله، کارتن و کاغذهای بازیافتی."
    },
    {
        id: 6,
        title: "آلومینیوم و روی",
        subtitle: "قوطی نوشابه، پروفیل و ورق آلومینیومی، قطعات صنعتی و ...",
        image: recyclables6,
        pricePerKg: 5000,
        details: "روزنامه، مجله، کارتن و کاغذهای بازیافتی."
    },
    {
        id: 7,
        title: "مس",
        subtitle: "سیم و کابل مسی، روکش مسی، قطعات صنعتی و الکترونیکی و ...",
        image: recyclables7,
        pricePerKg: 5000,
        details: "روزنامه، مجله، کارتن و کاغذهای بازیافتی."
    },
    {
        id: 8,
        title: "آهن و چدن",
        subtitle: "ورق و میلگرد آهنی، سیم و میله فولادی، لوله اتصالات چدنی و ...",
        image: recyclables8,
        pricePerKg: 5000,
        details: "روزنامه، مجله، کارتن و کاغذهای بازیافتی."
    },
    {
        id: 9,
        title: "ضایعات الکترونیکی",
        subtitle: "کامپیوتر و لپ‌تاپ، دستگاه های صوتی و تصویری و ...",
        image: recyclables10,
        pricePerKg: 5000,
        details: "روزنامه، مجله، کارتن و کاغذهای بازیافتی."
    },
];

const WastePriceSkeleton: React.FC = () => {
    return (
        <Card sx={{borderRadius: 3, boxShadow: 3}}>
            <CardContent>
                <Skeleton variant="text" width="30%" height={45} animation="wave"/>
                <Skeleton variant="text" width="80%" height={20} animation="wave"/>
            </CardContent>
        </Card>
    );
};

const WastePrices: React.FC = () => {
    const [expanded, setExpanded] = useState<number | null>(null);
    const [weights, setWeights] = useState<{ [key: number]: number }>({});
    const [openDialog, setOpenDialog] = useState(false);
    const [selectedItem, setSelectedItem] = useState<WasteItem | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const timer = setTimeout(() => setLoading(false), 2000);
        return () => clearTimeout(timer);
    }, []);

    const handleExpand = (id: number) => setExpanded(expanded === id ? null : id);
    const handleSliderChange = (id: number, value: number | number[]) => setWeights(prev => ({
        ...prev,
        [id]: value as number
    }));
    const handleOpenDialog = (item: WasteItem) => {
        setSelectedItem(item);
        setOpenDialog(true);
    };
    const handleCloseDialog = () => {
        setOpenDialog(false);
        setSelectedItem(null);
    };

    return (
        <Grid container spacing={2}>
            {loading
                ? Array.from({length: 9}).map((_, index) => (
                    <Grid size={12} key={index}>
                        <WastePriceSkeleton/>
                    </Grid>
                ))
                : wasteData.map((item) => (
                    <Grid size={12} key={item.id}>
                        <Card
                            onClick={() => handleExpand(item.id)}
                            sx={{
                                cursor: "pointer",
                                border: expanded === item.id ? "2px solid" : "2px solid transparent",
                                borderColor: expanded === item.id ? "primary.main" : "transparent",
                                borderRadius: 2,
                            }}
                        >
                            <Box sx={{
                                display: "flex",
                                alignItems: "center",
                                backgroundImage: `url(${item.image})`,
                                backgroundSize: "contain",
                                backgroundPosition: "right",
                                backgroundRepeat: "no-repeat"
                            }}>
                                <CardContent>
                                    <Typography variant="h6" sx={{pb: 0.25}}>{item.title}</Typography>
                                    <Typography variant="body2">{item.subtitle}</Typography>
                                </CardContent>
                            </Box>

                            <Collapse in={expanded === item.id} timeout="auto" unmountOnExit>
                                <CardContent>
                                    <Slider
                                        value={weights[item.id] || 1}
                                        min={1}
                                        max={20}
                                        step={1}
                                        valueLabelDisplay="auto"
                                        onClick={(e) => e.stopPropagation()}
                                        onChange={(_, value) => handleSliderChange(item.id, value)}
                                    />
                                    <Typography sx={{display: "flex", justifyContent: "space-between"}}>
                                        <span>وزن انتخابی</span>
                                        <span><strong>{weights[item.id] || 1}</strong> <small>کیلوگرم</small></span>
                                    </Typography>

                                    <Typography sx={{display: "flex", justifyContent: "space-between"}}>
                                        <span>قیمت هر کیلوگرم</span>
                                        <span><strong>{item.pricePerKg.toLocaleString()}</strong> <small>تومان</small></span>
                                    </Typography>

                                    <Typography variant="subtitle1"
                                                sx={{display: "flex", justifyContent: "space-between"}}>
                                        <span>مجموع</span>
                                        <span><strong>{((weights[item.id] || 1) * item.pricePerKg).toLocaleString()}</strong> <small>تومان</small></span>
                                    </Typography>

                                    <Button fullWidth variant="contained" size="large" sx={{mt: 3}}
                                            onClick={(e) => {
                                                e.stopPropagation();
                                                handleOpenDialog(item);
                                            }}>
                                        اطلاعات بیشتر
                                    </Button>
                                </CardContent>
                            </Collapse>
                        </Card>
                    </Grid>
                ))
            }

            <Dialog open={openDialog} onClose={handleCloseDialog} fullWidth>
                <DialogTitle>{selectedItem?.title}</DialogTitle>
                <DialogContent>
                    <Typography>{selectedItem?.details}</Typography>
                </DialogContent>
                <DialogActions>
                    <Button onClick={handleCloseDialog}>بستن</Button>
                </DialogActions>
            </Dialog>
        </Grid>
    );
};

export default WastePrices;