import { useEffect, useMemo, useState } from "react";
import {
    Box,
    Button,
    Dialog,
    DialogActions,
    DialogContent,
    DialogTitle,
    Stack,
    TextField,
    Typography,
} from "@mui/material";
import type { WasteItem } from "./waste/WastePricesGrid";
import { DRIVER_WASTE_PRICES_MOCK } from "../mock/driverWastePricesMock";

export type RecordedWasteLine = {
    wasteId: number;
    title: string;
    weightKg: number;
    amountTomans: number;
};

type DriverRecordWasteDialogProps = {
    open: boolean;
    onClose: () => void;
    /** آخرین ثبت روی کارت؛ هنگام باز شدن دیالوگ در فیلدها نمایش داده می‌شود */
    recordedWaste: RecordedWasteLine[];
    onSubmit: (lines: RecordedWasteLine[]) => void;
};

function clampWeightKg(raw: number): number {
    if (!Number.isFinite(raw) || raw <= 0) return 0;
    return Math.min(20, raw);
}

function tierRateTomansPerKg(item: WasteItem, weightKg: number): number {
    const w = Math.max(1, Math.min(20, Math.round(weightKg)));
    return item.rateList[w] ?? item.rateList[1] ?? item.maxAmount;
}

function estimateTotalTomans(item: WasteItem, weightKg: number): number {
    const rate = tierRateTomansPerKg(item, weightKg);
    return Math.round(rate * weightKg);
}

function buildDraftFromRecorded(recorded: RecordedWasteLine[]): Record<number, string> {
    const draft: Record<number, string> = {};
    for (const item of DRIVER_WASTE_PRICES_MOCK) {
        draft[item.id] = "";
    }
    for (const line of recorded) {
        draft[line.wasteId] = String(line.weightKg);
    }
    return draft;
}

export default function DriverRecordWasteDialog({
    open,
    onClose,
    recordedWaste,
    onSubmit,
}: DriverRecordWasteDialogProps) {
    const [draftKg, setDraftKg] = useState<Record<number, string>>(() =>
        buildDraftFromRecorded([]),
    );

    useEffect(() => {
        if (open) {
            setDraftKg(buildDraftFromRecorded(recordedWaste));
        }
    }, [open, recordedWaste]);

    const summary = useMemo(() => {
        let total = 0;
        let count = 0;
        for (const item of DRIVER_WASTE_PRICES_MOCK) {
            const raw = parseFloat(String(draftKg[item.id] ?? "").replace(/,/g, "."));
            const kg = clampWeightKg(raw);
            if (kg <= 0) continue;
            total += estimateTotalTomans(item, kg);
            count += 1;
        }
        return { total, count };
    }, [draftKg]);

    const handleChange = (id: number, value: string) => {
        setDraftKg((prev) => ({ ...prev, [id]: value }));
    };

    const handleConfirm = () => {
        const lines: RecordedWasteLine[] = [];
        for (const item of DRIVER_WASTE_PRICES_MOCK) {
            const raw = parseFloat(String(draftKg[item.id] ?? "").replace(/,/g, "."));
            const kg = clampWeightKg(raw);
            if (kg <= 0) continue;
            lines.push({
                wasteId: item.id,
                title: item.title,
                weightKg: kg,
                amountTomans: estimateTotalTomans(item, kg),
            });
        }
        onSubmit(lines);
        onClose();
    };

    return (
        <Dialog
            open={open}
            onClose={onClose}
            fullWidth
            maxWidth="sm"
            scroll="paper"
            slotProps={{
                paper: {
                    sx: { borderRadius: 3 },
                },
            }}
        >
            <DialogTitle sx={{ fontWeight: 800, pb: 1 }}>ثبت پسماند</DialogTitle>
            <DialogContent dividers sx={{ px: 2, py: 2 }}>
                <Typography variant="body2" color="text.secondary" sx={{ mb: 2 }}>
                    برای هر قلم وزن (کیلوگرم) را وارد کنید؛ قیمت هر کیلو مثل صفحهٔ تعرفه‌ها
                    نمایش داده شده است.
                </Typography>
                <Stack spacing={2}>
                    {DRIVER_WASTE_PRICES_MOCK.map((item) => {
                        const price1kg = item.rateList?.[1] ?? item.maxAmount;
                        const raw = parseFloat(
                            String(draftKg[item.id] ?? "").replace(/,/g, "."),
                        );
                        const kg = clampWeightKg(raw);
                        const preview =
                            kg > 0
                                ? estimateTotalTomans(item, kg).toLocaleString("fa-IR")
                                : "—";

                        return (
                            <Box
                                key={item.id}
                                sx={{
                                    display: "flex",
                                    gap: 1.5,
                                    alignItems: "flex-start",
                                    flexWrap: "wrap",
                                }}
                            >
                                <Box
                                    sx={{
                                        width: 56,
                                        height: 56,
                                        borderRadius: 2,
                                        flexShrink: 0,
                                        backgroundImage: `url(${item.image})`,
                                        backgroundSize: "cover",
                                        backgroundPosition: "center",
                                        border: "1px solid rgba(0,0,0,0.08)",
                                    }}
                                />
                                <Box sx={{ flex: 1, minWidth: 0 }}>
                                    <Typography variant="subtitle2" fontWeight={800}>
                                        {item.title}
                                    </Typography>
                                    <Typography variant="caption" color="text.secondary">
                                        {price1kg.toLocaleString("fa-IR")} تومان هر کیلو (۱
                                        کیلو) — جمع این قلم: {preview} تومان
                                    </Typography>
                                    <TextField
                                        size="small"
                                        fullWidth
                                        margin="dense"
                                        label="وزن (کیلوگرم)"
                                        placeholder="۰"
                                        value={draftKg[item.id] ?? ""}
                                        onChange={(e) => handleChange(item.id, e.target.value)}
                                        inputProps={{
                                            inputMode: "decimal",
                                            dir: "ltr",
                                            sx: { textAlign: "right" },
                                        }}
                                        sx={{ mt: 0.75 }}
                                    />
                                </Box>
                            </Box>
                        );
                    })}
                </Stack>
                <Box
                    sx={{
                        mt: 2,
                        pt: 2,
                        borderTop: "1px solid rgba(0,0,0,0.08)",
                        display: "flex",
                        justifyContent: "space-between",
                        gap: 2,
                        flexWrap: "wrap",
                    }}
                >
                    <Typography variant="body2" color="text.secondary">
                        تعداد اقلام با وزن: {summary.count.toLocaleString("fa-IR")}
                    </Typography>
                    <Typography variant="subtitle2" fontWeight={800}>
                        جمع تقریبی: {summary.total.toLocaleString("fa-IR")} تومان
                    </Typography>
                </Box>
            </DialogContent>
            <DialogActions sx={{ px: 2, py: 1.5, gap: 1 }}>
                <Button onClick={onClose} color="inherit" sx={{ fontWeight: 700 }}>
                    انصراف
                </Button>
                <Button
                    variant="contained"
                    onClick={handleConfirm}
                    disabled={summary.count === 0}
                    sx={{ fontWeight: 800, textTransform: "none" }}
                >
                    ثبت پسماند
                </Button>
            </DialogActions>
        </Dialog>
    );
}
