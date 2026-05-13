import { useMemo, useState } from "react";
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
import Inventory2OutlinedIcon from "@mui/icons-material/Inventory2Outlined";
import type { DriverRequest } from "../hooks/useDriverRequests";
import DriverRegisterWasteDialog from "./DriverRegisterWasteDialog";
import { useDriverWastesStore, type RegisteredWasteLine } from "../store/useDriverWastesStore";

type DriverRequestCardProps = {
    request: DriverRequest;
};

export default function DriverRequestCard({ request }: DriverRequestCardProps) {
    const noop = () => {};
    const [wasteDialogOpen, setWasteDialogOpen] = useState(false);
    const { getWastes } = useDriverWastesStore();

    const mergedWasteLines = useMemo(() => {
        let apiLines: RegisteredWasteLine[] = [];
        const fromApi = request.wastes?.items;
        if (Array.isArray(fromApi) && fromApi.length > 0) {
            apiLines = fromApi
                .map((row: Record<string, unknown>, idx: number) => {
                    const title = String(row.title ?? row.name ?? "");
                    const kg = Number(row.kg ?? row.weight ?? row.amount ?? 0);
                    const lineTotal = Number(row.lineTotal ?? row.price ?? row.total ?? 0);
                    const wasteId = Number(row.wasteId ?? row.id ?? idx);
                    const receiveId = Number(row.receiveId ?? row.id ?? idx);
                    if (!title || !Number.isFinite(lineTotal)) return null;
                    return {
                        wasteId: Number.isFinite(wasteId) ? wasteId : idx,
                        title,
                        kg: Number.isFinite(kg) ? kg : 0,
                        unitPrice: 0,
                        lineTotal,
                        receiveId,
                    } as RegisteredWasteLine;
                })
                .filter((row): row is RegisteredWasteLine => row != null);
        }

        const storedWastes = getWastes(request.id);
        return [...apiLines, ...storedWastes];
    }, [request.wastes?.items, request.id, getWastes]);

    const grandTotal = useMemo(
        () => mergedWasteLines.reduce((s, w) => s + w.lineTotal, 0),
        [mergedWasteLines]
    );

    const timeLabel = `${request.date.day} - ${request.date.time}`;

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
                            {request.name}
                        </Typography>
                        <Typography
                            variant="body2"
                            color="text.secondary"
                            sx={{ mt: 0.25, letterSpacing: 0.2 }}
                        >
                            {request.mob}
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

                <Stack spacing={1.5}>
                    <Stack direction="row" spacing={1} alignItems="center">
                        <LocationOnOutlinedIcon
                            fontSize="small"
                            sx={{ color: "text.secondary" }}
                        />
                        <Typography variant="body2" color="text.secondary">
                            {request.address}
                        </Typography>
                    </Stack>

                    <Stack direction="row" spacing={1} alignItems="center">
                        <AccessTimeOutlinedIcon
                            fontSize="small"
                            sx={{ color: "text.secondary" }}
                        />
                        <Typography variant="body2" color="text.secondary">
                            {timeLabel}
                        </Typography>
                    </Stack>
                </Stack>

                {mergedWasteLines.length > 0 && (
                    <>
                        <Divider sx={{ my: 2, borderColor: "rgba(0,0,0,0.06)" }} />
                        <Stack spacing={1}>
                            <Stack
                                direction="row"
                                alignItems="center"
                                spacing={0.75}
                                sx={{ mb: 0.5 }}
                            >
                                <Inventory2OutlinedIcon
                                    fontSize="small"
                                    sx={{ color: "primary.main" }}
                                />
                                <Typography
                                    variant="subtitle2"
                                    fontWeight={700}
                                    color="primary.main"
                                >
                                    پسماندهای ثبت شده
                                </Typography>
                            </Stack>
                            {mergedWasteLines.map((line, idx) => (
                                <Box
                                    key={`${line.receiveId}-${idx}`}
                                    sx={{
                                        p: 1.5,
                                        bgcolor: "grey.50",
                                        borderRadius: 1.5,
                                        border: "1px solid",
                                        borderColor: "grey.200",
                                    }}
                                >
                                    <Stack
                                        direction="row"
                                        justifyContent="space-between"
                                        alignItems="center"
                                    >
                                        <Typography variant="body2" fontWeight={600}>
                                            {line.title}
                                        </Typography>
                                        <Typography
                                            variant="body2"
                                            color="text.secondary"
                                            sx={{ fontSize: "0.8125rem" }}
                                        >
                                            {line.kg.toLocaleString("fa-IR")} کیلو •{" "}
                                            {line.lineTotal.toLocaleString("fa-IR")} تومان
                                        </Typography>
                                    </Stack>
                                </Box>
                            ))}
                            <Box
                                sx={{
                                    p: 1.5,
                                    bgcolor: "success.50",
                                    borderRadius: 1.5,
                                    border: "1px solid",
                                    borderColor: "success.200",
                                }}
                            >
                                <Stack
                                    direction="row"
                                    justifyContent="space-between"
                                    alignItems="center"
                                >
                                    <Typography variant="body2" fontWeight={700}>
                                        جمع کل:
                                    </Typography>
                                    <Typography
                                        variant="body1"
                                        fontWeight={800}
                                        color="success.dark"
                                    >
                                        {grandTotal.toLocaleString("fa-IR")} تومان
                                    </Typography>
                                </Stack>
                            </Box>
                        </Stack>
                    </>
                )}

                <Divider sx={{ my: 2, borderColor: "rgba(0,0,0,0.06)" }} />

                <Stack direction="row" spacing={1.25}>
                    <Button
                        variant="outlined"
                        size="small"
                        startIcon={<NavigationRoundedIcon />}
                        onClick={noop}
                        sx={{
                            flex: 1,
                            fontWeight: 700,
                            textTransform: "none",
                            borderRadius: 2,
                            py: 0.875,
                        }}
                    >
                        مسیریابی
                    </Button>
                    <Button
                        variant="outlined"
                        size="small"
                        startIcon={<ChatBubbleOutlineRoundedIcon />}
                        onClick={noop}
                        sx={{
                            flex: 1,
                            fontWeight: 700,
                            textTransform: "none",
                            borderRadius: 2,
                            py: 0.875,
                        }}
                    >
                        چت
                    </Button>
                </Stack>

                <Button
                    variant="contained"
                    fullWidth
                    startIcon={<AssignmentTurnedInOutlinedIcon />}
                    onClick={() => setWasteDialogOpen(true)}
                    sx={{
                        mt: 1.25,
                        fontWeight: 800,
                        textTransform: "none",
                        borderRadius: 2,
                        py: 1.125,
                        boxShadow: "0 4px 14px rgba(30, 111, 230, 0.25)",
                    }}
                >
                    ثبت پسماند
                </Button>
            </CardContent>

            <DriverRegisterWasteDialog
                open={wasteDialogOpen}
                onClose={() => setWasteDialogOpen(false)}
                requestId={request.id}
            />
        </Card>
    );
}
