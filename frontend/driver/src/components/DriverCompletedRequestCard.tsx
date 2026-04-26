import { type ReactNode } from "react";
import {
    Box,
    Card,
    CardContent,
    Divider,
    Stack,
    Typography,
    type TypographyProps,
} from "@mui/material";
import LocationOnOutlinedIcon from "@mui/icons-material/LocationOnOutlined";
import AccessTimeOutlinedIcon from "@mui/icons-material/AccessTimeOutlined";
import TaskAltOutlinedIcon from "@mui/icons-material/TaskAltOutlined";
import Inventory2OutlinedIcon from "@mui/icons-material/Inventory2Outlined";
import PaymentsOutlinedIcon from "@mui/icons-material/PaymentsOutlined";
import type { DriverCompletedRequest } from "../mock/completedRequests";

function PriceToman({
    amount,
    numberVariant,
    numberFontWeight,
}: {
    amount: number;
    numberVariant: TypographyProps["variant"];
    numberFontWeight: number;
}) {
    return (
        <Box
            component="span"
            sx={{
                display: "inline-flex",
                alignItems: "baseline",
                gap: 0.35,
                flexShrink: 0,
                direction: "ltr",
                unicodeBidi: "plaintext",
            }}
        >
            <Typography
                component="span"
                variant={numberVariant}
                fontWeight={numberFontWeight}
                color="primary.dark"
            >
                {amount.toLocaleString("fa-IR")}
            </Typography>
            <Typography
                component="span"
                variant="caption"
                fontWeight={400}
                color="primary.dark"
                sx={{ fontSize: "0.7rem", opacity: 0.9 }}
            >
                تومان
            </Typography>
        </Box>
    );
}

type DriverCompletedRequestCardProps = {
    request: DriverCompletedRequest;
};

function IconTile({ children }: { children: ReactNode }) {
    return (
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
            {children}
        </Box>
    );
}

export default function DriverCompletedRequestCard({
    request,
}: DriverCompletedRequestCardProps) {
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
                        <IconTile>
                            <LocationOnOutlinedIcon fontSize="small" />
                        </IconTile>
                        <Box sx={{ minWidth: 0, flex: 1 }}>
                            <Typography
                                variant="caption"
                                fontWeight={800}
                                color="primary"
                                sx={{
                                    letterSpacing: 0.2,
                                    display: "block",
                                    mb: 0.35,
                                }}
                            >
                                آدرس
                            </Typography>
                            <Typography variant="body2" sx={{ lineHeight: 1.65 }}>
                                {request.address}
                            </Typography>
                        </Box>
                    </Stack>

                    <Stack direction="row" spacing={1.25} alignItems="flex-start">
                        <IconTile>
                            <AccessTimeOutlinedIcon fontSize="small" />
                        </IconTile>
                        <Box sx={{ minWidth: 0, flex: 1 }}>
                            <Typography
                                variant="caption"
                                fontWeight={800}
                                color="primary"
                                sx={{
                                    letterSpacing: 0.2,
                                    display: "block",
                                    mb: 0.35,
                                }}
                            >
                                زمان درخواست
                            </Typography>
                            <Typography variant="body2" sx={{ lineHeight: 1.65 }}>
                                {request.requestedAtLabel}
                            </Typography>
                        </Box>
                    </Stack>

                    <Stack direction="row" spacing={1.25} alignItems="flex-start">
                        <IconTile>
                            <TaskAltOutlinedIcon fontSize="small" />
                        </IconTile>
                        <Box sx={{ minWidth: 0, flex: 1 }}>
                            <Typography
                                variant="caption"
                                fontWeight={800}
                                color="primary"
                                sx={{
                                    letterSpacing: 0.2,
                                    display: "block",
                                    mb: 0.35,
                                }}
                            >
                                زمان انجام
                            </Typography>
                            <Typography variant="body2" sx={{ lineHeight: 1.65 }}>
                                {request.completedAtLabel}
                            </Typography>
                        </Box>
                    </Stack>
                </Stack>

                <Divider sx={{ my: 2, borderColor: "rgba(0,0,0,0.06)" }} />

                <Stack direction="row" spacing={1} alignItems="center" sx={{ mb: 1.5 }}>
                    <Inventory2OutlinedIcon
                        sx={{ fontSize: 20, color: "primary.main", opacity: 0.9 }}
                    />
                    <Typography variant="subtitle2" fontWeight={800} color="primary.dark">
                        پسماندها
                    </Typography>
                </Stack>

                <Stack spacing={1.25}>
                    {request.wastes.map((w, i) => (
                        <Box
                            key={`${request.id}-${i}`}
                            sx={{
                                borderRadius: 2,
                                px: 1.5,
                                py: 1.25,
                                bgcolor: "rgba(30, 111, 230, 0.04)",
                                border: "1px solid rgba(30, 111, 230, 0.1)",
                            }}
                        >
                            <Stack
                                direction="row"
                                alignItems="flex-start"
                                justifyContent="space-between"
                                spacing={1.5}
                            >
                                <Box sx={{ minWidth: 0, flex: 1 }}>
                                    <Typography
                                        variant="body2"
                                        fontWeight={700}
                                        sx={{ lineHeight: 1.45 }}
                                    >
                                        {w.title}
                                    </Typography>
                                    {w.detail ? (
                                        <Typography
                                            variant="caption"
                                            color="text.secondary"
                                            sx={{ display: "block", mt: 0.35 }}
                                        >
                                            {w.detail}
                                        </Typography>
                                    ) : null}
                                </Box>
                                <Box sx={{ flexShrink: 0, textAlign: "left" }}>
                                    <PriceToman
                                        amount={w.lineTotal}
                                        numberVariant="body2"
                                        numberFontWeight={800}
                                    />
                                </Box>
                            </Stack>
                        </Box>
                    ))}
                </Stack>
            </CardContent>

            <Box
                sx={{
                    px: 2.25,
                    py: 2,
                    display: "flex",
                    flexDirection: "row",
                    alignItems: "center",
                    justifyContent: "space-between",
                    gap: 2,
                    flexWrap: "wrap",
                    background:
                        "linear-gradient(135deg, rgba(30, 111, 230, 0.1) 0%, rgba(21, 101, 192, 0.14) 100%)",
                    borderTop: "1px solid rgba(30, 111, 230, 0.15)",
                }}
            >
                <Stack direction="row" spacing={1} alignItems="center">
                    <Box
                        sx={{
                            width: 36,
                            height: 36,
                            borderRadius: "50%",
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "center",
                            bgcolor: "rgba(255,255,255,0.85)",
                            color: "primary.dark",
                        }}
                    >
                        <PaymentsOutlinedIcon sx={{ fontSize: 20 }} />
                    </Box>
                    <Typography variant="subtitle2" fontWeight={800} color="primary.dark">
                        مجموع پرداختی
                    </Typography>
                </Stack>
                <PriceToman
                    amount={request.grandTotal}
                    numberVariant="h6"
                    numberFontWeight={900}
                />
            </Box>
        </Card>
    );
}
