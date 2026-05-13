import { useEffect, useState } from "react";
import {
    Autocomplete,
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
    Alert,
    CircularProgress,
    IconButton,
} from "@mui/material";
import DeleteIcon from "@mui/icons-material/Delete";
import { useAuthStore } from "../store/useAuthStore";
import { useDriverWastesStore, type RegisteredWasteLine } from "../store/useDriverWastesStore";

type WastePrice = {
    id: number;
    title: string;
    description: string;
    image: string;
    amount: {
        guild: { max: number; min: number };
        citizen: { max: number; min: number };
        driver: null | { max: number; min: number };
    };
};

type DriverRegisterWasteDialogProps = {
    open: boolean;
    onClose: () => void;
    requestId: number;
};

function parseKg(raw: string): number {
    const n = parseFloat(raw.replace(/,/g, ".").trim());
    return Number.isFinite(n) && n > 0 ? n : 0;
}

const API_BASE = "http://185.255.88.111:8000/api/driver";

export default function DriverRegisterWasteDialog({
                                                      open,
                                                      onClose,
                                                      requestId,
                                                  }: DriverRegisterWasteDialogProps) {
    const { accessToken } = useAuthStore();
    const { getWastes, setWastes } = useDriverWastesStore();

    const [wastePrices, setWastePrices] = useState<WastePrice[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [registeredLines, setRegisteredLines] = useState<RegisteredWasteLine[]>([]);

    const [selectedWaste, setSelectedWaste] = useState<WastePrice | null>(null);
    const [weight, setWeight] = useState("");
    const [submitting, setSubmitting] = useState(false);

    useEffect(() => {
        if (open) {
            const savedWastes = getWastes(requestId);
            setRegisteredLines(savedWastes);
            setSelectedWaste(null);
            setWeight("");
            setError(null);
            fetchWastePrices();
        }
    }, [open, requestId]);

    const fetchWastePrices = async () => {
        setLoading(true);
        setError(null);
        try {
            const response = await fetch(`${API_BASE}/prices`, {
                headers: {
                    Authorization: `Bearer ${accessToken}`,
                },
            });
            const result = await response.json();
            if (result.status === "success") {
                setWastePrices(result.data.list);
            } else {
                setError("خطا در دریافت لیست پسماندها");
            }
        } catch (err) {
            setError("خطا در برقراری ارتباط با سرور");
        } finally {
            setLoading(false);
        }
    };

    const kg = parseKg(weight);
    const unitPrice = selectedWaste
        ? (selectedWaste.amount.citizen.max + selectedWaste.amount.citizen.min) / 2
        : 0;
    const lineTotal = kg * unitPrice;

    const handleAddLine = async () => {
        if (!selectedWaste || kg <= 0) return;

        setSubmitting(true);
        setError(null);

        try {
            const response = await fetch(`${API_BASE}/request/${requestId}/waste`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${accessToken}`,
                },
                body: JSON.stringify({
                    id: selectedWaste.id,
                    weight: kg,
                }),
            });

            const result = await response.json();

            if (result.status === "success") {
                const newLines: RegisteredWasteLine[] = result.data.items.map((item: any) => ({
                    wasteId: item.type.value,
                    title: item.type.label,
                    kg: item.weight,
                    unitPrice: item.price,
                    lineTotal: item.totalPrice,
                    receiveId: item.id,
                }));

                setRegisteredLines(newLines);
                setWastes(requestId, newLines);
                setSelectedWaste(null);
                setWeight("");
            } else {
                setError(result.message || "خطا در ثبت پسماند");
            }
        } catch (err) {
            setError("خطا در برقراری ارتباط با سرور");
        } finally {
            setSubmitting(false);
        }
    };

    const handleRemoveLine = async (receiveId: number) => {
        setSubmitting(true);
        setError(null);

        try {
            const response = await fetch(`${API_BASE}/request/waste/${receiveId}`, {
                method: "DELETE",
                headers: {
                    Authorization: `Bearer ${accessToken}`,
                },
            });

            const result = await response.json();

            if (result.status === "success") {
                if (result.data.items) {
                    const newLines: RegisteredWasteLine[] = result.data.items.map((item: any) => ({
                        wasteId: item.type.value,
                        title: item.type.label,
                        kg: item.weight,
                        unitPrice: item.price,
                        lineTotal: item.totalPrice,
                        receiveId: item.id,
                    }));
                    setRegisteredLines(newLines);
                    setWastes(requestId, newLines);
                } else {
                    setRegisteredLines([]);
                    setWastes(requestId, []);
                }
            } else {
                setError(result.message || "خطا در حذف پسماند");
            }
        } catch (err) {
            setError("خطا در برقراری ارتباط با سرور");
        } finally {
            setSubmitting(false);
        }
    };

    const handleDone = async () => {
        if (registeredLines.length === 0) return;

        setSubmitting(true);
        setError(null);

        try {
            const response = await fetch(`${API_BASE}/request/${requestId}/done`, {
                method: "POST",
                headers: {
                    Authorization: `Bearer ${accessToken}`,
                },
            });

            const result = await response.json();

            if (result.status === "success") {
                onClose();
                window.location.href = "/current-requests";
            } else {
                setError(result.message || "خطا در تکمیل درخواست");
            }
        } catch (err) {
            setError("خطا در برقراری ارتباط با سرور");
        } finally {
            setSubmitting(false);
        }
    };

    const totalAmount = registeredLines.reduce((sum, line) => sum + line.lineTotal, 0);

    return (
        <Dialog
            open={open}
            onClose={onClose}
            fullWidth
            maxWidth="sm"
            scroll="paper"
            aria-labelledby="register-waste-title"
            PaperProps={{
                sx: {
                    borderRadius: 3,
                    mb: 8,
                },
            }}
        >
            <DialogTitle id="register-waste-title" sx={{ pb: 1, pt: 3, px: 3 }}>
                <Typography variant="h6" fontWeight={800}>
                    ثبت پسماند
                </Typography>
            </DialogTitle>
            <Typography variant="body2" color="text.secondary" sx={{ px: 3, pb: 2 }}>
                درخواست #{requestId}
            </Typography>

            <Divider />

            <DialogContent sx={{ py: 3, px: 3 }}>
                {loading ? (
                    <Box sx={{ display: "flex", justifyContent: "center", py: 4 }}>
                        <CircularProgress />
                    </Box>
                ) : (
                    <Stack spacing={3}>
                        {error && (
                            <Alert severity="error" onClose={() => setError(null)}>
                                {error}
                            </Alert>
                        )}

                        <Stack spacing={2}>
                            <Autocomplete
                                value={selectedWaste}
                                onChange={(_, newValue) => setSelectedWaste(newValue)}
                                options={wastePrices}
                                getOptionLabel={(option) => option.title}
                                disabled={submitting}
                                renderOption={(props, option) => {
                                    const { key, ...otherProps } = props;
                                    return (
                                        <Box
                                            component="li"
                                            key={key}
                                            {...otherProps}
                                            sx={{ gap: 1.5 }}
                                        >
                                            <Box
                                                sx={{
                                                    width: 40,
                                                    height: 40,
                                                    borderRadius: 1.5,
                                                    backgroundImage: `url(${option.image})`,
                                                    backgroundSize: "cover",
                                                    backgroundPosition: "center",
                                                    border: "1px solid rgba(0,0,0,0.08)",
                                                    flexShrink: 0,
                                                }}
                                            />
                                            <Typography variant="body2">{option.title}</Typography>
                                        </Box>
                                    );
                                }}
                                renderInput={(params) => (
                                    <TextField {...params} label="انتخاب نوع پسماند" />
                                )}
                                noOptionsText="موردی یافت نشد"
                            />

                            <TextField
                                label="وزن (کیلوگرم)"
                                type="number"
                                value={weight}
                                onChange={(e) => setWeight(e.target.value)}
                                inputProps={{ min: 0, step: 0.1 }}
                                disabled={!selectedWaste || submitting}
                                fullWidth
                            />

                            {selectedWaste && kg > 0 && (
                                <Box
                                    sx={{
                                        p: 2,
                                        bgcolor: "primary.50",
                                        borderRadius: 2,
                                        border: "1px solid",
                                        borderColor: "primary.200",
                                    }}
                                >
                                    <Stack spacing={0.5}>
                                        <Typography variant="caption" color="text.secondary">
                                            قیمت هر کیلو: {unitPrice.toLocaleString("fa-IR")} تومان
                                        </Typography>
                                        <Typography variant="body1" fontWeight={700} color="primary.main">
                                            جمع: {lineTotal.toLocaleString("fa-IR")} تومان
                                        </Typography>
                                    </Stack>
                                </Box>
                            )}

                            <Button
                                variant="outlined"
                                onClick={handleAddLine}
                                disabled={!selectedWaste || kg <= 0 || submitting}
                                sx={{
                                    fontWeight: 700,
                                    textTransform: "none",
                                    py: 1.25,
                                    borderRadius: 2,
                                }}
                            >
                                {submitting ? <CircularProgress size={20} /> : "افزودن به لیست"}
                            </Button>
                        </Stack>

                        {registeredLines.length > 0 && (
                            <>
                                <Divider />
                                <Stack spacing={1.5}>
                                    <Typography variant="subtitle2" fontWeight={700}>
                                        لیست پسماندهای ثبت شده
                                    </Typography>
                                    {registeredLines.map((line) => (
                                        <Box
                                            key={line.receiveId}
                                            sx={{
                                                p: 2,
                                                bgcolor: "grey.50",
                                                borderRadius: 2,
                                                border: "1px solid",
                                                borderColor: "grey.200",
                                            }}
                                        >
                                            <Stack
                                                direction="row"
                                                justifyContent="space-between"
                                                alignItems="center"
                                            >
                                                <Box sx={{ flex: 1 }}>
                                                    <Typography variant="body2" fontWeight={700}>
                                                        {line.title}
                                                    </Typography>
                                                    <Typography variant="caption" color="text.secondary">
                                                        {line.kg.toLocaleString("fa-IR")} کیلوگرم •{" "}
                                                        {line.lineTotal.toLocaleString("fa-IR")} تومان
                                                    </Typography>
                                                </Box>
                                                <IconButton
                                                    size="small"
                                                    color="error"
                                                    onClick={() => handleRemoveLine(line.receiveId)}
                                                    disabled={submitting}
                                                >
                                                    <DeleteIcon fontSize="small" />
                                                </IconButton>
                                            </Stack>
                                        </Box>
                                    ))}
                                    <Box
                                        sx={{
                                            p: 2,
                                            bgcolor: "success.50",
                                            borderRadius: 2,
                                            border: "1px solid",
                                            borderColor: "success.200",
                                        }}
                                    >
                                        <Stack direction="row" justifyContent="space-between">
                                            <Typography variant="body1" fontWeight={700}>
                                                جمع کل:
                                            </Typography>
                                            <Typography
                                                variant="body1"
                                                fontWeight={800}
                                                color="success.dark"
                                            >
                                                {totalAmount.toLocaleString("fa-IR")} تومان
                                            </Typography>
                                        </Stack>
                                    </Box>
                                </Stack>
                            </>
                        )}
                    </Stack>
                )}
            </DialogContent>

            <Divider />

            <DialogActions sx={{ px: 3, py: 2.5, gap: 1.5 }}>
                <Button
                    onClick={onClose}
                    color="inherit"
                    sx={{
                        fontWeight: 700,
                        textTransform: "none",
                        px: 3,
                        py: 1,
                        borderRadius: 2,
                    }}
                    disabled={submitting}
                >
                    انصراف
                </Button>
                <Button
                    variant="contained"
                    onClick={handleDone}
                    disabled={registeredLines.length === 0 || submitting}
                    sx={{
                        fontWeight: 800,
                        textTransform: "none",
                        px: 3,
                        py: 1,
                        borderRadius: 2,
                        minWidth: 120,
                    }}
                >
                    {submitting ? <CircularProgress size={24} color="inherit" /> : "تکمیل درخواست"}
                </Button>
            </DialogActions>
        </Dialog>
    );
}
