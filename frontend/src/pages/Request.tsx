import {useEffect, useState} from "react";
import {
    Box,
    Card,
    CardContent,
    Divider,
    List,
    ListItem,
    Skeleton,
    Typography,
} from "@mui/material";
import AllInbox from '@mui/icons-material/AllInbox';
import Portrait from '@mui/icons-material/Portrait';
import Recycling from '@mui/icons-material/Recycling';
import {useParams} from "react-router-dom";
const itemStyle = {
    px: 0,
    py: 0.5,
    display: "flex",
    justifyContent: "space-between",
};

const dividerStyle = {
    borderColor: "rgba(225,225,225,0.5)"
};

const SkeletonCard = () => (
    <Card
        sx={{
            width: "100%",
            mb: 3,
            borderRadius: 3,
            boxShadow: 3
        }}
    >
        <CardContent>
            {[...Array(5)].map((_, index) => (
                <Box key={index}>
                    <Box
                        sx={{
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "space-between",
                            gap: 1.5,
                        }}
                    >
                        <Skeleton variant="text" width={150} height={30}/>
                        <Skeleton variant="text" width={100} height={30}/>
                    </Box>
                    <Divider sx={{borderColor: "rgba(225,225,225,0.5)"}}/>
                </Box>
            ))}
        </CardContent>
    </Card>
);

export default function Request() {
    const [loading, setLoading] = useState(true);
    const {id} = useParams();


    useEffect(() => {
        console.log('id is ',id);
        const timer = setTimeout(() => setLoading(false), 2000);
        return () => clearTimeout(timer);
    }, []);

    return (
        <Box>
            <Box sx={{display: "flex", alignItems: "center", gap: 1, mb: 1.5}}>
                <AllInbox/>
                <Typography variant="h6">جزئیات درخواست</Typography>
            </Box>
            {loading ? <SkeletonCard/> : (
                <Card sx={{width: "100%", mb: 3, borderRadius: 3, boxShadow: 3}}>
                    <CardContent>
                        <List sx={{p: 0}}>
                            <ListItem sx={itemStyle}>
                                <Typography>شماره درخواست</Typography>
                                <Typography><strong>101</strong></Typography>
                            </ListItem>
                            <Divider sx={dividerStyle}/>
                            <ListItem sx={itemStyle}>
                                <Typography>تاریخ</Typography>
                                <Typography>1404/06/20</Typography>
                            </ListItem>
                            <Divider sx={dividerStyle}/>
                            <ListItem sx={itemStyle}>
                                <Typography>سفیر</Typography>
                                <Typography>علی رضایی</Typography>
                            </ListItem>
                            <Divider sx={dividerStyle}/>
                            <ListItem sx={itemStyle}>
                                <Typography>وزن</Typography>
                                <Typography component="span"><strong>12</strong>
                                    <Box component="small" sx={{pl: 0.5}}>کیلوگرم</Box>
                                </Typography>
                            </ListItem>
                            <Divider sx={dividerStyle}/>
                            <ListItem sx={itemStyle}>
                                <Typography>مبلغ</Typography>
                                <Typography component="span"><strong>۸۵٬۰۰۰</strong>
                                    <Box component="small" sx={{pl: 0.5}}>تومان</Box>
                                </Typography>
                            </ListItem>
                            <Divider sx={dividerStyle}/>
                            <ListItem sx={itemStyle}>
                                <Typography>پلاک خودرو</Typography>
                                <Typography>ایران 85 - 371 ط 43</Typography>
                            </ListItem>
                        </List>
                    </CardContent>
                </Card>
            )}

            <Box sx={{display: "flex", alignItems: "center", gap: 1, mb: 1.5}}>
                <Portrait/>
                <Typography variant="h6">اطلاعات مشتری</Typography>
            </Box>
            {loading ? <SkeletonCard/> : (
                <Card sx={{width: "100%", mb: 3, borderRadius: 3, boxShadow: 3}}>
                    <CardContent>
                        <List sx={{p: 0}}>
                            <ListItem sx={itemStyle}>
                                <Typography>نام مشتری</Typography>
                                <Typography>علی مظلوم</Typography>
                            </ListItem>
                            <Divider sx={dividerStyle}/>
                            <ListItem sx={itemStyle}>
                                <Typography>شماره موبایل</Typography>
                                <Typography>09332775003</Typography>
                            </ListItem>
                            <Divider sx={dividerStyle}/>
                            <ListItem sx={itemStyle}>
                                <Typography>آدرس</Typography>
                                <Typography>سناباد 21 پلاک 40 زنگ 1</Typography>
                            </ListItem>
                        </List>
                    </CardContent>
                </Card>
            )}

            <Box sx={{display: "flex", alignItems: "center", gap: 1, mb: 1.5}}>
                <Recycling/>
                <Typography variant="h6">اطلاعات ضایعات</Typography>
            </Box>
            {loading ? <SkeletonCard/> : (
                <Card sx={{width: "100%", borderRadius: 3, boxShadow: 3}}>
                    <CardContent>
                        <List sx={{p: 0}}>
                            <ListItem sx={itemStyle}>
                                <Typography>ضایعات مخلوط 6 <small>کیلوگرم</small></Typography>
                                <Typography component="span">
                                    <strong>176٬250</strong>
                                    <Box component="small" sx={{pl: 0.5}}>تومان</Box>
                                </Typography>
                            </ListItem>
                            <Divider sx={dividerStyle}/>
                            <ListItem sx={itemStyle}>
                                <Typography>شیشه 6 <small>کیلوگرم</small></Typography>
                                <Typography component="span">
                                    <strong>85٬129</strong>
                                    <Box component="small" sx={{pl: 0.5}}>تومان</Box>
                                </Typography>
                            </ListItem>
                            <Divider sx={dividerStyle}/>
                            <ListItem sx={itemStyle}>
                                <Typography>مس 12 <small>کیلوگرم</small></Typography>
                                <Typography component="span">
                                    <strong>197٬376</strong>
                                    <Box component="small" sx={{pl: 0.5}}>تومان</Box>
                                </Typography>
                            </ListItem>
                            <Divider sx={dividerStyle}/>
                            <ListItem sx={itemStyle}>
                                <Typography>آهن و چدن 9 <small>کیلوگرم</small></Typography>
                                <Typography component="span">
                                    <strong>76٬450</strong>
                                    <Box component="small" sx={{pl: 0.5}}>تومان</Box>
                                </Typography>
                            </ListItem>
                        </List>
                    </CardContent>
                </Card>
            )}
        </Box>
    );
}