import { useEffect, useMemo, useState } from "react";
import {
    Box,
    Button,
    Dialog,
    DialogActions,
    DialogContent,
    DialogTitle,
    Divider,
    Stack,
    TextField,
    Typography,
} from "@mui/material";
import type { WasteItem } from "./waste/WastePricesGrid";
import { getLineTotalToman, getUnitPricePerKgToman } from "../utils/wastePricing";

export type RegisteredWasteLine = {
    wasteId: number;
    title: string;
    kg: number;
    lineTotal: number;
};

type DriverRegisterWasteDialogProps = {
    open: boolean;
    onClose: () => void;
    requestId: number;
    items: WasteItem[];
    onSubmit: (lines: RegisteredWasteLine[]) => void;
};

function parseKg(raw: string): number {
    const n = parseFloat(raw.replace(/,/g, ".").trim());
    return Number.isFinite(n) && n > 0 ? n : 0;
}

export default function DriverRegisterWasteDialog({
    open,
    onClose,
    requestId,
    items,
    onSubmit,
}: DriverRegisterWasteDialogProps) {
    const [weights, setWeights] = useState<Record<number, string>>({});

    useEffect(() => {
        if (open) setWeights({});
    }, [open]);

    const previewLines = useMemo(() => {
        return items.map((item) => {
            const kg = parseKg(weights[item.id] ?? "");
            const unit = getUnitPricePerKgToman(item, kg);
            const lineTotal = getLineTotalToman(item, kg);
            return { item, kg, unit, lineTotal };
        });
    }, [items, weights]);

    const anyPositive = previewLines.some((p) => p.kg > 0);

    const handleSubmit = () => {
        if (!anyPositive) return;
        const lines: RegisteredWasteLine[] = previewLines
            .filter((p) => p.kg > 0)
            .map((p) => ({
                wasteId: p.item.id,
                title: p.item.title,
                kg: p.kg,
                lineTotal: p.lineTotal,
            }));
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
            aria-labelledby="register-waste-title"
        >
            <DialogTitle id="register-waste-title" sx={{ pb: 0.5 }}>
                ثبت پسماند
            </DialogTitle>
            <Typography variant="body2" color="text.secondary" sx={{ px: 3, pb: 1 }}>
                درخواست #{requestId} — وزن هر قلم را به کیلوگرم وارد کنید.
            </Typography>
            <DialogContent dividers sx={{ py: 1.5 }}>
                <Stack spacing={0} divider={<Divider flexItem sx={{ my: 1.25 }} />}>
                    {previewLines.map(({ item, kg, unit, lineTotal }) => (
                        <Stack key={item.id} spacing={1.25}>
                            <Stack direction="row" spacing={1.5} alignItems="flex-start">
                                <Box
                                    sx={{
                                        width: 52,
                                        height: 52,
                                        borderRadius: 2,
                                        flexShrink: 0,
                                        backgroundImage: `url(${item.image})`,
                                        backgroundSize: "cover",
                                        backgroundPosition: "center",
                                        border: "1px solid rgba(0,0,0,0.08)",
                                    }}
                                />
                                <Box sx={{ minWidth: 0, flex: 1 }}>
                                    <Typography variant="subtitle2" fontWeight={800}>
                                        {item.title}
                                    </Typography>
                                </Box>
                            </Stack>
                            <Stack direction="row" spacing={1.5} alignItems="center" flexWrap="wrap">
                                <TextField
                                    label="وزن (کیلوگرم)"
                                    type="number"
                                    size="small"
                                    value={weights[item.id] ?? ""}
                                    onChange={(e) =>
                                        setWeights((prev) => ({
                                            ...prev,
                                            [item.id]: e.target.value,
                                        }))
                                    }
                                    inputProps={{ min: 0, step: 0.1 }}
                                    sx={{ width: 160 }}
                                />
                                {kg > 0 ? (
                                    <Box sx={{ flex: 1, minWidth: 140 }}>
                                        <Typography variant="caption" color="text.secondary">
                                            قیمت هر کیلو: {unit.toLocaleString("fa-IR")} تومان
                                        </Typography>
                                        <Typography variant="body2" fontWeight={800}>
                                            جمع: {lineTotal.toLocaleString("fa-IR")} تومان
                                        </Typography>
                                    </Box>
                                ) : (
                                    <Typography variant="caption" color="text.secondary">
                                        در صورت عدم تحویل این قلم، خالی بگذارید.
                                    </Typography>
                                )}
                            </Stack>
                        </Stack>
                    ))}
                </Stack>
            </DialogContent>
            <DialogActions sx={{ px: 2.5, py: 2, gap: 1 }}>
                <Button onClick={onClose} color="inherit" sx={{ fontWeight: 700 }}>
                    انصراف
                </Button>
                <Button
                    variant="contained"
                    onClick={handleSubmit}
                    disabled={!anyPositive}
                    sx={{ fontWeight: 800, textTransform: "none" }}
                >
                    ثبت پسماند
                </Button>
            </DialogActions>
        </Dialog>
    );
}
