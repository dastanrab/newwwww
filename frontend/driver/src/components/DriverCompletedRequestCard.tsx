import { type ReactNode } from "react";
import {
    Box,
    Card,
    Divider,
    Stack,
    Typography,
    type TypographyProps,
} from "@mui/material";
import LocationOnOutlinedIcon from "@mui/icons-material/LocationOnOutlined";
import AccessTimeOutlinedIcon from "@mui/icons-material/AccessTimeOutlined";
import PersonOutlineOutlinedIcon from "@mui/icons-material/PersonOutlineOutlined";
import ScaleOutlinedIcon from "@mui/icons-material/ScaleOutlined";

type WasteItem = {
    id: number;
    type: {
        value: number;
        label: string;
    };
    image: string;
    weight: number;
    price: number;
    totalPrice: number;
};

type CompletedRequest = {
    id: number;
    name: string;
    mob: string;
    address: string;
    totalWeight: string;
    date: {
        day: string;
        time: string;
    };
    wastes: {
        items: WasteItem[] | null;
        totalPrice: number | null;
    };
    userType: string;
};

function PriceToman({
                        amount,
                        variant = "body2",
                        fontWeight = 700,
                    }: {
    amount: number;
    variant?: TypographyProps["variant"];
    fontWeight?: number;
}) {
    return (
        <Box
            component="span"
            sx={{
                display: "inline-flex",
                alignItems: "baseline",
                gap: 0.3,
                direction: "ltr",
            }}
        >
            <Typography
                component="span"
                variant={variant}
                fontWeight={fontWeight}
                color="primary.dark"
            >
                {amount.toLocaleString("fa-IR")}
            </Typography>
            <Typography
                component="span"
                variant="caption"
                color="text.secondary"
                sx={{ fontSize: "0.65rem" }}
            >
                تومان
            </Typography>
        </Box>
    );
}

type DriverCompletedRequestCardProps = {
    request: CompletedRequest;
};

function InfoRow({ icon, children }: { icon: ReactNode; children: ReactNode }) {
    return (
        <Stack direction="row" spacing={0.75} alignItems="center">
            <Box sx={{ color: "primary.main", opacity: 0.7, fontSize: 18 }}>
                {icon}
            </Box>
            <Typography variant="body2" color="text.secondary" sx={{ fontSize: "0.8rem" }}>
                {children}
            </Typography>
        </Stack>
    );
}

export default function DriverCompletedRequestCard({
                                                       request,
                                                   }: DriverCompletedRequestCardProps) {
    return (
        <Card
            elevation={0}
            sx={{
                borderRadius: 2,
                border: "1px solid",
                borderColor: "divider",
                mb:15
            }}
        >
            <Box sx={{ p: 1.75 }}>
                <Stack
                    direction="row"
                    justifyContent="space-between"
                    alignItems="center"
                    spacing={1.5}
                    sx={{ mb: 1.25 }}
                >
                    <Box sx={{ minWidth: 0, flex: 1 }}>
                        <Typography variant="subtitle2" fontWeight={700}>
                            {request.name}
                        </Typography>
                        <Typography variant="caption" color="text.secondary">
                            {request.mob}
                        </Typography>
                    </Box>
                    <Typography
                        variant="body2"
                        fontWeight={700}
                        color="primary.main"
                        sx={{
                            bgcolor: "rgba(30, 111, 230, 0.08)",
                            px: 1,
                            py: 0.35,
                            borderRadius: 1,
                        }}
                    >
                        #{request.id}
                    </Typography>
                </Stack>

                <Stack
                    direction="row"
                    spacing={2}
                    sx={{
                        flexWrap: "wrap",
                        rowGap: 0.75,
                        mb: 1.25,
                    }}
                >
                    <InfoRow icon={<PersonOutlineOutlinedIcon fontSize="inherit" />}>
                        {request.userType}
                    </InfoRow>
                    <InfoRow icon={<AccessTimeOutlinedIcon fontSize="inherit" />}>
                        {request.date.day} • {request.date.time}
                    </InfoRow>
                    <InfoRow icon={<ScaleOutlinedIcon fontSize="inherit" />}>
                        {request.totalWeight}
                    </InfoRow>
                </Stack>

                <InfoRow icon={<LocationOnOutlinedIcon fontSize="inherit" />}>
                    {request.address}
                </InfoRow>

                {request.wastes.items && request.wastes.items.length > 0 && (
                    <>
                        <Divider sx={{ my: 1.5 }} />

                        <Stack spacing={0.75}>
                            {request.wastes.items.map((w) => (
                                <Stack
                                    key={w.id}
                                    direction="row"
                                    alignItems="center"
                                    justifyContent="space-between"
                                    spacing={1}
                                    sx={{
                                        px: 1,
                                        py: 0.75,
                                        bgcolor: "rgba(0, 0, 0, 0.02)",
                                        borderRadius: 1,
                                    }}
                                >
                                    <Stack direction="row" spacing={1} alignItems="center" sx={{ minWidth: 0, flex: 1 }}>
                                        <Box
                                            sx={{
                                                width: 28,
                                                height: 28,
                                                borderRadius: 1,
                                                backgroundImage: `url(${w.image})`,
                                                backgroundSize: "cover",
                                                backgroundPosition: "center",
                                                flexShrink: 0,
                                            }}
                                        />
                                        <Box sx={{ minWidth: 0 }}>
                                            <Typography variant="body2" fontWeight={600} sx={{ fontSize: "0.8rem" }}>
                                                {w.type.label}
                                            </Typography>
                                            <Typography variant="caption" color="text.secondary" sx={{ fontSize: "0.7rem" }}>
                                                {w.weight.toLocaleString("fa-IR")} کیلو × {w.price.toLocaleString("fa-IR")}
                                            </Typography>
                                        </Box>
                                    </Stack>
                                    <PriceToman amount={w.totalPrice} variant="body2" fontWeight={700} />
                                </Stack>
                            ))}
                        </Stack>
                    </>
                )}
            </Box>

            {request.wastes.totalPrice && (
                <Box
                    sx={{
                        px: 1.75,
                        py: 1.25,
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "space-between",
                        bgcolor: "rgba(30, 111, 230, 0.06)",
                        borderTop: "1px solid",
                        borderColor: "divider",
                    }}
                >
                    <Typography variant="body2" fontWeight={700} color="primary.dark">
                        مجموع پرداختی
                    </Typography>
                    <PriceToman amount={request.wastes.totalPrice} variant="subtitle2" fontWeight={800} />
                </Box>
            )}
        </Card>
    );
}
