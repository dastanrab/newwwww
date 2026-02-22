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
    Skeleton
} from "@mui/material";
import {useAuthStore} from "../store/useAuthStore.ts";
import {usePrices} from "../hooks/usePrices.ts";

interface WasteItem {
    id: number;
    title: string;
    description: string;
    image: string;
    rateList: { [weight: number]: number };
    maxAmount: number;
}


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
    const { accessToken } = useAuthStore();
    const { getPrices, loading} = usePrices();
    const [wasteItems, setWasteItems] = useState<WasteItem[]>([]);


    const  fetchPrices  = async () => {
        const pricesResponse = await getPrices(accessToken!);
        const formattedItems: WasteItem[] = pricesResponse.data.list.map((item: any) => ({
            id: item.id,
            title: item.title,
            description: item.description,
            image: item.bgImage || item.image,
            rateList: item.rateList,
            maxAmount: item.maxAmount,
        }));
        setWasteItems(formattedItems)
        console.log('prices',pricesResponse)
    }
// گرفتن لیست قیمت‌ها
    useEffect(() => {
        fetchPrices().then(r => console.log(r,'finish'))
    }, []);

    const handleExpand = (id: number) => setExpanded(expanded === id ? null : id);

    const handleOpenDialog = (item: WasteItem) => {
        setSelectedItem(item);
        setOpenDialog(true);
    };
    const handleCloseDialog = () => {
        setOpenDialog(false);
        setSelectedItem(null);
    };

    const getPriceForWeight = (item: WasteItem, weight: number) => {
        if (item.rateList[weight]) return item.rateList[weight];
        return item.maxAmount; // اگر وزن بالاتر از لیست باشد
    };

    const handleSliderChange = (id: number, value: number | number[]) =>
        setWeights(prev => ({ ...prev, [id]: value as number }));

    return (
        <Grid container spacing={2}>
            {loading
                ? Array.from({length: 9}).map((_, index) => (
                    <Grid size={12} key={index}>
                        <WastePriceSkeleton/>
                    </Grid>
                ))
                : wasteItems.map((item) => {
                    const weight = weights[item.id] || 1;
                    const price = getPriceForWeight(item, weight);
                    return (<Grid size={12} key={item.id}>
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
                                            <span><strong>{price.toLocaleString()}</strong> <small>تومان</small></span>
                                        </Typography>

                                        <Typography variant="subtitle1"
                                                    sx={{display: "flex", justifyContent: "space-between"}}>
                                            <span>مجموع</span>
                                            <span><strong>{price.toLocaleString()}</strong> <small>تومان</small></span>
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
                        </Grid>   );
                })
            }


            <Dialog open={openDialog} onClose={handleCloseDialog} fullWidth>
                <DialogTitle>{selectedItem?.title}</DialogTitle>
                <DialogContent>
                    <Typography>{selectedItem?.description}</Typography>
                </DialogContent>
                <DialogActions>
                    <Button onClick={handleCloseDialog}>بستن</Button>
                </DialogActions>
            </Dialog>
        </Grid>
    );
};

export default WastePrices;