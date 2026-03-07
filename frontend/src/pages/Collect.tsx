import {useState, useEffect} from "react";
import {
    Box,
    TextField,
    InputAdornment,
    Button,
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    Checkbox,
    FormControlLabel,
    Typography,
    CircularProgress
} from "@mui/material";
import LocationOnIcon from "@mui/icons-material/LocationOn";
import StarIcon from '@mui/icons-material/Star';
import MapIcon from '@mui/icons-material/Map';
import {useNavigate} from "react-router-dom";
import { useAddress } from "../hooks/useAddress";
import { useAuthStore } from "../store/useAuthStore";
import SubmitAdressMap from "../components/submit/address/SubmitAdressMap.tsx";
import { Snackbar, Alert, type AlertColor } from "@mui/material";


interface SelectedLocation {
    latitude: number;
    longitude: number;
    detailedAddress?: string;
    isPreferred: boolean;
    customTitle?: string;
}

function Collect() {
    const [snack, setSnack] = useState<{
        open: boolean;
        message: string;
        type: AlertColor;
    }>({
        open: false,
        message: "",
        type: "success",
    });
    const [savingAddress, setSavingAddress] = useState(false);
    const [loadingAddresses, setLoadingAddresses] = useState(true);
    const [addresses, setAddresses] = useState<{id: number, title: string}[]>([]);
    const navigate = useNavigate();
    const [openMapModal, setOpenMapModal] = useState(false);
    const [selectedLocation, setSelectedLocation] = useState<SelectedLocation | null>(null);
    const [detailedAddress, setDetailedAddress] = useState("");
    const [isPreferred, setIsPreferred] = useState(false);
    const [customTitle, setCustomTitle] = useState("");
    const [selectedAddressId, setSelectedAddressId] = useState<number | null>(null);
  //  const [location, setLocation] = useState<{latitude: number, longitude: number} | null>(null);


    // لیست آدرس‌های از پیش تعیین شده
    const { accessToken} = useAuthStore();

    const { getAddresses, createAddress } = useAddress();

    useEffect(() => {
        async function fetchAddresses() {
            setLoadingAddresses(true);
            const res = await getAddresses(accessToken);
            if (res.status === "success") {
                // @ts-ignore
                setAddresses(res.data.map((a: any) => ({id: a.id, title: a.title})));
            } else {
                setSnack({
                    open: true,
                    message: res.message || "خطا در دریافت آدرس‌ها",
                    type: "error",
                });
            }
            setLoadingAddresses(false);
        }
        fetchAddresses();
    }, [accessToken]);
    const handleSelectAddress = (addressId: number) => {
        setSelectedAddressId(addressId);
    };

    const handleSaveAddress = async () => {
        if (!selectedLocation || !accessToken) return;

        try {
            setSavingAddress(true);
            const payload = {
                title: isPreferred ? customTitle || "آدرس منتخب" : detailedAddress || "آدرس جدید",
                address: detailedAddress || "",
                lat: selectedLocation.latitude,
                lng: selectedLocation.longitude,
                isFavorite: isPreferred,
            };

            const res = await createAddress(accessToken, payload);

            if (res.status === "success") {
                // @ts-ignore
                const newAddressId = res.data.id; // ← آیدی آدرس جدید
                const addressesRes = await getAddresses(accessToken);

                if (addressesRes.status === "success") {
                    // @ts-ignore
                    setAddresses(addressesRes.data.map((a: any) => ({ id: a.id, title: a.title })));
                }

                setSnack({
                    open: true,
                    message: "آدرس با موفقیت اضافه شد",
                    type: "success",
                });
                handleCloseMapModal();

                // برو به صفحه بعد با آدرس جدید
                navigate("/collect/schedule", {
                    state: { addressId: newAddressId }
                });
            } else {
                setSnack({
                    open: true,
                    message: res.message || "خطا در ثبت آدرس",
                    type: "error",
                });
            }
        } catch (err) {
            setSnack({
                open: true,
                message: "خطا در ارتباط با سرور",
                type: "error",
            });
        } finally {
            setSavingAddress(false);
        }
    };


    const handleOpenMapModal = () => {
        setOpenMapModal(true);
        setSelectedLocation(null);
        setDetailedAddress("");
        setIsPreferred(false);
        setCustomTitle("");
    };

    const handleCloseMapModal = () => {
        setOpenMapModal(false);
        setSelectedLocation(null);
        setDetailedAddress("");
        setIsPreferred(false);
        setCustomTitle("");
    };



    // مدیریت تغییر چک‌باکس
    const handlePreferredChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        setIsPreferred(event.target.checked);
        if (!event.target.checked) {
            setCustomTitle("");
        }
    };

    // ذخیره آدرس انتخاب شده


    const handleFinalSubmit = () => {
        if (!selectedAddressId) {
            setSnack({
                open: true,
                message: "لطفاً یک آدرس انتخاب کنید",
                type: "warning",
            });
            return;
        }
        navigate("/collect/schedule", {
            state: {
                addressId: selectedAddressId,
            }
        });
    };

    // @ts-ignore
    return (
        <Box>
            <Box sx={{display: 'flex', gap: 1, mb: 2, flexWrap: 'wrap', justifyContent: 'center'}}>
                {loadingAddresses ? (
                    <CircularProgress />
                ) : (
                    addresses.map((address) => (
                        <Button
                            key={address.id}
                            color={selectedAddressId === address.id ? "primary" : "secondary"}
                            variant={selectedAddressId === address.id ? "contained" : "outlined"}
                            startIcon={<StarIcon/>}
                            onClick={() => handleSelectAddress(address.id)}
                            sx={{flex: '1 1 auto'}}
                        >
                            {address.title}
                        </Button>
                    ))
                )}
            </Box>
            <Box sx={{mb: 3}}>
                <Button
                    variant="contained"
                    fullWidth
                    startIcon={<MapIcon/>}
                    onClick={handleOpenMapModal}
                    sx={{py: 1.5, fontSize: '1rem', borderRadius: 2}}
                >
                    انتخاب از روی نقشه
                </Button>
            </Box>
            <Dialog
                open={openMapModal}
                onClose={handleCloseMapModal}
                fullWidth
                maxWidth="md"
                PaperProps={{sx: {borderRadius: 3}}}
            >
                <DialogTitle>
                    انتخاب موقعیت از روی نقشه
                </DialogTitle>
                <DialogContent sx={{ px: 2.5, py: 2, background: "#fafafa" }}>
                    {/* 🗺️ Map */}
                    <Box
                        sx={{
                            width: '100%',
                            height: 320,
                            mb: 2,
                            borderRadius: 3,
                            overflow: 'hidden',
                            border: '1px solid #eee',
                            boxShadow: '0 6px 20px rgba(0,0,0,0.08)'
                        }}
                    >
                        <SubmitAdressMap
                            onSelect={(coords) => {
                                setSelectedLocation({
                                    latitude: coords.lat,
                                    longitude: coords.lon,
                                    isPreferred: false,
                                });
                            }}
                        />
                    </Box>

                    {/* 📍 Info Card */}
                    {selectedLocation && (
                        <Box
                            sx={{
                                p: 2,
                                borderRadius: 3,
                                background: "#fff",
                                boxShadow: '0 2px 10px rgba(0,0,0,0.06)',
                                border: '1px solid #f0f0f0'
                            }}
                        >
                            {/* مختصات */}
                            <Typography
                                variant="body2"
                                sx={{
                                    mb: 1.5,
                                    color: 'success.main',
                                    fontWeight: 500,
                                    textAlign: 'center'
                                }}
                            >
                                📍 {selectedLocation.latitude.toFixed(5)} , {selectedLocation.longitude.toFixed(5)}
                            </Typography>

                            {/* آدرس */}
                            <TextField
                                fullWidth
                                size="small"
                                variant="outlined"
                                placeholder="آدرس دقیق‌تر..."
                                value={detailedAddress}
                                onChange={(e) => setDetailedAddress(e.target.value)}
                                sx={{ mb: 1.5 }}
                                InputProps={{
                                    startAdornment: (
                                        <InputAdornment position="start">
                                            <LocationOnIcon fontSize="small" />
                                        </InputAdornment>
                                    ),
                                }}
                            />

                            {/* علاقه‌مندی */}
                            <FormControlLabel
                                sx={{ mb: isPreferred ? 1.5 : 0 }}
                                control={
                                    <Checkbox
                                        size="small"
                                        checked={isPreferred}
                                        onChange={handlePreferredChange}
                                    />
                                }
                                label={
                                    <Typography variant="body2">
                                        ذخیره به عنوان آدرس منتخب ⭐
                                    </Typography>
                                }
                            />

                            {/* عنوان سفارشی */}
                            {isPreferred && (
                                <TextField
                                    fullWidth
                                    size="small"
                                    variant="outlined"
                                    placeholder="مثلاً: خانه، محل کار..."
                                    value={customTitle}
                                    onChange={(e) => setCustomTitle(e.target.value)}
                                />
                            )}
                        </Box>
                    )}
                </DialogContent>
                <DialogActions sx={{px: 3, pb: 2}}>
                    <Button onClick={handleCloseMapModal} variant="outlined">
                        انصراف
                    </Button>
                    <Button
                        onClick={handleSaveAddress}
                        variant="contained"
                        disabled={!selectedLocation || savingAddress}
                    >
                        {savingAddress ? <CircularProgress size={20} color="inherit" /> : "ذخیره آدرس"}
                    </Button>
                </DialogActions>
            </Dialog>

            <Box sx={{width: '100%', position: 'fixed', bottom: 90, right: 0, left: 0, textAlign: 'center'}}>
                <Button
                    onClick={handleFinalSubmit}
                    variant="contained"
                    size="large"
                    disabled={!selectedAddressId}
                    sx={{borderRadius: '300px', px: 5}}
                >
                    تایید نهایی
                </Button>
            </Box>
            <Snackbar
                open={snack.open}
                autoHideDuration={3000}
                onClose={() => setSnack({ ...snack, open: false })}
                anchorOrigin={{ vertical: "bottom", horizontal: "center" }}
            >
                <Alert
                    onClose={() => setSnack({ ...snack, open: false })}
                    severity={snack.type}
                    variant="filled"
                    sx={{ width: "100%" }}
                >
                    {snack.message}
                </Alert>
            </Snackbar>
        </Box>
    );
}

export default Collect;