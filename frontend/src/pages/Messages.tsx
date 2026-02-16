import React, {useState, useEffect} from "react";
import {Card, CardContent, Typography, Grid, Box, Skeleton} from "@mui/material";

interface Message {
    id: number;
    title: string;
    date: string;
    content: string;
}

const messages: Message[] = [
    {
        id: 1,
        title: "خرید پسماند",
        date: "2025-09-22",
        content: `کارتن تا 9,108😳😱
پسماند های شمارو با بالاترین قیمت خریداریم.🤑
پس همین الان درخواستتو ثبت کن و منتظرمون باش😉
☎️05138722223
زی پاک، همیار محیط زیست♻️`,
    },
    {
        id: 2,
        title: "بالاترین قیمت",
        date: "2025-09-21",
        content: `کارتن تا  7,520؟😳😱
پسماند های شمارو با بالاترین قیمت خریداریم.🤑
پس همین الان درخواستتو ثبت کن و منتظرمون باش😉
☎️05138766666
♻️زی پاک، همیار محیط زیست♻️`,
    },
    {
        id: 3,
        title: "خرید پسماند",
        date: "2025-09-22",
        content: `کارتن تا 9,108😳😱
پسماند های شمارو با بالاترین قیمت خریداریم.🤑
پس همین الان درخواستتو ثبت کن و منتظرمون باش😉
☎️05138722223
زی پاک، همیار محیط زیست♻️`,
    },
    {
        id: 4,
        title: "بالاترین قیمت",
        date: "2025-09-21",
        content: `کارتن تا  7,520؟😳😱
پسماند های شمارو با بالاترین قیمت خریداریم.🤑
پس همین الان درخواستتو ثبت کن و منتظرمون باش😉
☎️05138766666
♻️زی پاک، همیار محیط زیست♻️`,
    },
];

const MessageSkeleton: React.FC = () => {
    return (
        <Card sx={{borderRadius: 3, boxShadow: 3}}>
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
};

const Messages: React.FC = () => {
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const timer = setTimeout(() => setLoading(false), 2000);
        return () => clearTimeout(timer);
    }, []);

    return (
        <Grid container spacing={2}>
            {loading
                ? Array.from({length: 4}).map((_, index) => (
                    <Grid size={12} key={index}>
                        <MessageSkeleton/>
                    </Grid>
                ))
                : messages.map((msg) => (
                    <Grid size={12} key={msg.id}>
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
                                background: "linear-gradient(90deg, rgb(20, 200, 135) 0%, rgb(15, 160, 105) 100%)",
                                opacity: 0.15,
                                transform: "rotate(45deg)",
                                borderRadius: "50%",
                                filter: "blur(90px)",
                                zIndex: 1,
                            },
                        }}
                        >
                            <CardContent>
                                <Box sx={{display: "flex", justifyContent: "space-between"}}>
                                    <Typography variant="h6" gutterBottom>{msg.title}</Typography>
                                    <Typography variant="caption" color="textSecondary" display="block"
                                                gutterBottom>{msg.date}</Typography>
                                </Box>
                                <Typography variant="body1" style={{whiteSpace: "pre-line"}}>
                                    {msg.content}
                                </Typography>
                            </CardContent>
                        </Card>
                    ))
                </Grid>
            ))}
        </Grid>
    );
};

export default Messages;