import {
    Box,
    Button,
    Card,
    CardContent,
    Divider,
    Stack,
    Typography,
} from "@mui/material";
import LocationOnOutlinedIcon from "@mui/icons-material/LocationOnOutlined";
import AccessTimeOutlinedIcon from "@mui/icons-material/AccessTimeOutlined";
import NavigationRoundedIcon from "@mui/icons-material/NavigationRounded";
import AssignmentTurnedInOutlinedIcon from "@mui/icons-material/AssignmentTurnedInOutlined";
import ChatBubbleOutlineRoundedIcon from "@mui/icons-material/ChatBubbleOutlineRounded";
import type { DriverCurrentRequest } from "../mock/currentRequests";

type DriverRequestCardProps = {
    request: DriverCurrentRequest;
};

export default function DriverRequestCard({ request }: DriverRequestCardProps) {
    const noop = () => {
    };

    return (
        <Card
            elevation={0}
            sx={{
                borderRadius: 3,
                overflow: "hidden",
                border: "1px solid",
                borderColor: "rgba(30, 111, 230, 0.12)",
                boxShadow: "0 10px 30px rgba(15, 70, 160, 0.075)",
            }}
        >
            <CardContent sx={{ p: 2.25, "&:last-child": { pb: 2.25 } }}>
                <Stack
                    direction="row"
                    justifyContent="space-between"
                    alignItems="flex-start"
                    spacing={2}
                >
                    <Box sx={{ minWidth: 0, flex: 1 }}>
                        <Typography
                            variant="subtitle1"
                            fontWeight={800}
                            sx={{ lineHeight: 1.35 }}
                        >
                            {request.customerName}
                        </Typography>
                        <Typography
                            variant="body2"
                            color="text.secondary"
                            sx={{ mt: 0.25, letterSpacing: 0.2 }}
                        >
                            {request.phone}
                        </Typography>
                    </Box>
                    <Typography
                        variant="h6"
                        component="span"
                        sx={{
                            fontWeight: 900,
                            color: "primary.dark",
                            flexShrink: 0,
                            bgcolor: "rgba(30, 111, 230, 0.08)",
                            px: 1.25,
                            py: 0.5,
                            borderRadius: 2,
                            lineHeight: 1.2,
                        }}
                    >
                        #{request.id}
                    </Typography>
                </Stack>

                <Divider sx={{ my: 2, borderColor: "rgba(0,0,0,0.06)" }} />

                <Stack spacing={2}>
                    <Stack direction="row" spacing={1.25} alignItems="flex-start">
                        <Box
                            sx={{
                                width: 40,
                                height: 40,
                                borderRadius: 2,
                                display: "flex",
                                alignItems: "center",
                                justifyContent: "center",
                                bgcolor: "rgba(30, 111, 230, 0.1)",
                                color: "primary.main",
                                flexShrink: 0,
                            }}
                        >
                            <LocationOnOutlinedIcon fontSize="small" />
                        </Box>
                        <Box sx={{ minWidth: 0, flex: 1 }}>
                            <Typography
                                variant="caption"
                                fontWeight={800}
                                color="primary"
                                sx={{ letterSpacing: 0.2, display: "block", mb: 0.35 }}
                            >
                                آدرس
                            </Typography>
                            <Typography variant="body2" sx={{ lineHeight: 1.65 }}>
                                {request.address}
                            </Typography>
                        </Box>
                    </Stack>

                    <Stack direction="row" spacing={1.25} alignItems="flex-start">
                        <Box
                            sx={{
                                width: 40,
                                height: 40,
                                borderRadius: 2,
                                display: "flex",
                                alignItems: "center",
                                justifyContent: "center",
                                bgcolor: "rgba(30, 111, 230, 0.1)",
                                color: "primary.main",
                                flexShrink: 0,
                            }}
                        >
                            <AccessTimeOutlinedIcon fontSize="small" />
                        </Box>
                        <Box sx={{ minWidth: 0, flex: 1 }}>
                            <Typography
                                variant="caption"
                                fontWeight={800}
                                color="primary"
                                sx={{ letterSpacing: 0.2, display: "block", mb: 0.35 }}
                            >
                                زمان درخواست
                            </Typography>
                            <Typography variant="body2" sx={{ lineHeight: 1.65 }}>
                                {request.timeLabel}
                            </Typography>
                        </Box>
                    </Stack>
                </Stack>

                <Box sx={{ mt: 2, display: "flex", justifyContent: "flex-end" }}>
                    <Button
                        variant="contained"
                        size="medium"
                        disableElevation
                        startIcon={<NavigationRoundedIcon sx={{ ml: -0.25 }} />}
                        onClick={noop}
                        sx={{
                            borderRadius: 999,
                            px: 2.5,
                            py: 1,
                            fontWeight: 700,
                            textTransform: "none",
                            color: "#fff",
                            boxShadow: "0 4px 14px rgba(18, 150, 105, 0.35)",
                            "&:hover": {
                                background:
                                    "linear-gradient(90deg, rgb(40, 210, 150) 0%, rgb(22, 165, 118) 100%)",
                                boxShadow: "0 6px 18px rgba(18, 150, 105, 0.45)",
                            },
                        }}
                    >
                        مسیریابی
                    </Button>
                </Box>
            </CardContent>

            <Box
                sx={{
                    px: 2,
                    py: 1.75,
                    display: "flex",
                    flexDirection: "row",
                    alignItems: "center",
                    justifyContent: "space-between",
                    gap: 1.5,
                    flexWrap: "wrap",
                    bgcolor: "rgba(245, 245, 245, 0.90)",
                    borderTop: "1px solid rgba(0,0,0,0.05)",
                }}
            >
                <Button
                    variant="contained"
                    color="inherit"
                    size="medium"
                    disableElevation
                    startIcon={<AssignmentTurnedInOutlinedIcon />}
                    onClick={noop}
                    sx={{
                        borderRadius: 999,
                        px: 2,
                        py: 1,
                        fontWeight: 700,
                        textTransform: "none",
                        bgcolor: "#fff",
                        color: "text.primary",
                        border: "1px solid rgba(0,0,0,0.08)",
                        boxShadow: "0 2px 8px rgba(0,0,0,0.06)",
                        "&:hover": { bgcolor: "#fafafa" },
                    }}
                >
                    ثبت پسماند
                </Button>
                <Button
                    variant="contained"
                    size="medium"
                    disableElevation
                    startIcon={<ChatBubbleOutlineRoundedIcon />}
                    onClick={noop}
                    sx={{
                        borderRadius: 999,
                        px: 2,
                        py: 1,
                        minWidth: 0,
                        fontWeight: 700,
                        textTransform: "none",
                    }}
                >
                    پیام
                </Button>
            </Box>
        </Card>
    );
}
