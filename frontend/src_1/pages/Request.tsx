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
import {useRequest} from "../hooks/useRequest.ts";
import {useAuthStore} from "../store/useAuthStore.ts";
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
type RequestType = {
    id: number
    amount: number
    weight: number

    requestDate: {
        day: string
        range: string
    }

    collectedDate: {
        day: string
        time: string
    }

    status: {
        label: string
        value: number
    }

    customer: {
        name: string
        mob: string
        address: string
        location: {
            lat: number
            lng: number
        }
    }

    driver: {
        name: string
        mob: string
        avatar: string
        plaque: {
            part1: number
            part2: string
            part3: number
            part4: number
        }
    }

    wasteItems: {
        id: number
        title: string
        weight: string
        amount: string
        image: string
    }[]
}
export default function Request() {
    const [loading, setLoading] = useState(true);
    const {id} = useParams();
    const [request, setRequest] = useState<RequestType | null>(null);
    const {  getDetail } = useRequest();


    const {accessToken} = useAuthStore();
    const getRequest = async () => {
        if (!accessToken || !id) {
            setLoading(false);
            return;
        }

        try {
            const res = await getDetail(id,accessToken);
            // @ts-ignore
            const response=res.data
            // @ts-ignore
            setRequest(response)
        } catch (error: any) {

        } finally {
            setLoading(false);
        }
    };
    useEffect(() => {
        getRequest()
    }, [accessToken]);
    console.log('request',request)

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
                                <Typography><strong>{request?.id ?? '-'}</strong></Typography>
                            </ListItem>

                            <Divider sx={dividerStyle}/>

                            <ListItem sx={itemStyle}>
                                <Typography>تاریخ</Typography>
                                <Typography>{request?.requestDate?.day ?? '-'}</Typography>
                            </ListItem>

                            <Divider sx={dividerStyle}/>

                            <ListItem sx={itemStyle}>
                                <Typography>سفیر</Typography>
                                <Typography>{request?.driver?.name ?? '-'}</Typography>
                            </ListItem>

                            <Divider sx={dividerStyle}/>

                            <ListItem sx={itemStyle}>
                                <Typography>وزن</Typography>
                                <Typography component="span">
                                    <strong>{request?.weight ?? '-'}</strong>
                                    <Box component="small" sx={{pl: 0.5}}>کیلوگرم</Box>
                                </Typography>
                            </ListItem>

                            <Divider sx={dividerStyle}/>

                            <ListItem sx={itemStyle}>
                                <Typography>مبلغ</Typography>
                                <Typography component="span">
                                    <strong>{request?.amount?.toLocaleString()}</strong>
                                    <Box component="small" sx={{pl: 0.5}}>تومان</Box>
                                </Typography>
                            </ListItem>

                            <Divider sx={dividerStyle}/>

                            <ListItem sx={itemStyle}>
                                <Typography>پلاک خودرو</Typography>
                                <Typography>
                                    ایران {request?.driver?.plaque?.part4} - {request?.driver?.plaque?.part3} {request?.driver?.plaque?.part2} {request?.driver?.plaque?.part1}
                                </Typography>
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
                        <ListItem sx={itemStyle}>
                            <Typography>نام مشتری</Typography>
                            <Typography>{request?.customer?.name}</Typography>
                        </ListItem>

                        <Divider sx={dividerStyle}/>

                        <ListItem sx={itemStyle}>
                            <Typography>شماره موبایل</Typography>
                            <Typography>{request?.customer?.mob}</Typography>
                        </ListItem>

                        <Divider sx={dividerStyle}/>

                        <ListItem sx={itemStyle}>
                            <Typography>آدرس</Typography>
                            <Typography>{request?.customer?.address}</Typography>
                        </ListItem>
                    </CardContent>
                </Card>
            )}

            {request?.wasteItems ? <><Box sx={{display: "flex", alignItems: "center", gap: 1, mb: 1.5}}>
                <Recycling/>
                <Typography variant="h6">اطلاعات ضایعات</Typography>
            </Box>
                {loading ? <SkeletonCard/> : (
                    <Card sx={{width: "100%", borderRadius: 3, boxShadow: 3}}>
                        <CardContent>
                            <List sx={{p: 0}}>
                                {request?.wasteItems?.map((item, index) => (
                                    <Box key={item.id}>
                                        <ListItem sx={itemStyle}>
                                            <Typography>
                                                {item.title} <small>{item.weight}</small>
                                            </Typography>

                                            <Typography component="span">
                                                <strong>{item.amount}</strong>
                                            </Typography>
                                        </ListItem>

                                        {index !== request.wasteItems.length - 1 && (
                                            <Divider sx={dividerStyle}/>
                                        )}
                                    </Box>
                                ))}
                            </List>
                        </CardContent>
                    </Card>
                )}</>: null }

        </Box>
    );
}