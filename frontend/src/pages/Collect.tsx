import React, {useState, useEffect} from "react";
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
import {useAddress} from "../hooks/useAddress";
import {useAuthStore} from "../store/useAuthStore";
import SubmitAdressMap from "../components/submit/address/SubmitAdressMap.tsx";
import {Snackbar, Alert, type AlertColor} from "@mui/material";
import {LoadingButton} from "@mui/lab";


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
    const [addresses, setAddresses] = useState<{ id: number, title: string }[]>([]);
    const navigate = useNavigate();
    const [openMapModal, setOpenMapModal] = useState(false);
    const [selectedLocation, setSelectedLocation] = useState<SelectedLocation | null>(null);
    const [detailedAddress, setDetailedAddress] = useState("");
    const [isPreferred, setIsPreferred] = useState(false);
    const [showInfo, setShowInfo] = useState(false);
    const [customTitle, setCustomTitle] = useState("");
    const [selectedAddressId, setSelectedAddressId] = useState<number | null>(null);
    const [step, setStep] = useState(0);
    //  const [location, setLocation] = useState<{latitude: number, longitude: number} | null>(null);


    // لیست آدرس‌های از پیش تعیین شده
    const {accessToken} = useAuthStore();

    const {getAddresses, createAddress,reverseAddress} = useAddress();

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
                // const addressesRes = await getAddresses(accessToken);

                // if (addressesRes.status === "success") {
                //     // @ts-ignore
                //     setAddresses(addressesRes.data.map((a: any) => ({id: a.id, title: a.title})));
                // }

                setSnack({
                    open: true,
                    message: "آدرس با موفقیت اضافه شد",
                    type: "success",
                });
                handleCloseMapModal();

                // برو به صفحه بعد با آدرس جدید
                navigate("/collect/schedule", {
                    state: {addressId: newAddressId}
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
    const handleAdressInfo = async (lat:number|null,lan:number|null) => {
        console.log('start')
        if (!lat || !lan) return;
        console.log('end')
        setSavingAddress(true);
        try {
            const res = await reverseAddress(lat, lan);
            if (res.status === "OK") {
                // @ts-ignore
                setDetailedAddress(res.formatted_address)
                setStep(1)
                setShowInfo(true)

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
        setStep(0)
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
                    <CircularProgress/>
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
                fullScreen
            >
                <DialogTitle>انتخاب موقعیت از روی نقشه</DialogTitle>
                <DialogContent sx={{px: 2.5, py: 2}}>

                    {/* 📍 Info Card Map*/}
                    {( detailedAddress !== "" && showInfo && selectedLocation) ? (
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
                                placeholder={detailedAddress}
                                value={detailedAddress}
                                disabled={true}
                                // onChange={(e) => setDetailedAddress(e.target.value)}
                                sx={{mb: 1.5}}
                                InputProps={{
                                    startAdornment: (
                                        <InputAdornment position="start">
                                            <LocationOnIcon fontSize="small"/>
                                        </InputAdornment>
                                    ),
                                }}
                            />
                            {/* ورودی جدید: جزئیات آدرس (فعال) */}
                            <TextField
                                fullWidth
                                size="small"
                                variant="outlined"
                                label="جزئیات آدرس (واحد، پلاک، طبقه)"
                                placeholder="مثلاً: پلاک ۱۰، واحد ۴، زنگ سوم"
                             //   value={addressDetails} // این استیت را در کامپوننت تعریف کنید
                              //  onChange={(e) => setAddressDetails(e.target.value)} // این تابع را تعریف کنید
                                sx={{
                                    mb: 1.5,
                                    '& .MuiInputLabel-root': { fontSize: '0.85rem' }, // کوچک‌تر کردن لیبل برای ظاهر بهتر
                                }}
                            />

                            {/* علاقه‌مندی */}
                            <FormControlLabel
                                sx={{mb: isPreferred ? 1.5 : 0}}
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
                    ) : ( <Box sx={{width: '100%', height: '100%', boxShadow: '0 5px 20px rgba(0,0,0,0.075)'}}>
                        <SubmitAdressMap
                            onSelect={(coords) => {
                                setSelectedLocation({
                                    latitude: coords.lat,
                                    longitude: coords.lon,
                                    isPreferred: false,
                                });
                                if (!coords.lat || !coords.lon) return;
                                // handleAdressInfo(coords.lat,coords.lon)

                            }}
                        />
                    </Box>)}
                </DialogContent>
                <DialogActions sx={{mb: 1.5, justifyContent: 'center'}}>
                    <Button onClick={handleCloseMapModal} color="info" variant="outlined"
                            sx={{
                                py: 1,
                                px: 3.5,
                                borderRadius: '300px'
                            }}
                    >
                        انصراف
                    </Button>

                    {step == 0 ?
                        <> <LoadingButton
                            type="submit"
                            variant="contained"
                            size="large"
                            disabled={!selectedLocation || savingAddress}
                            onClick={()=>{handleAdressInfo(selectedLocation?.latitude ?? null,selectedLocation?.longitude??null).then()}}
                            sx={{py: 1, px: 3.5, borderRadius: '300px'}}
                        >
                            تایید آدرس
                        </LoadingButton></> :
                        <><LoadingButton
                            type="submit"
                            variant="contained"
                            size="large"
                            disabled={!selectedLocation || savingAddress}
                            onClick={handleSaveAddress}
                            sx={{py: 1, px: 3.5, borderRadius: '300px'}}
                        >
                            ذخیره آدرس
                        </LoadingButton></>}


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
                onClose={() => setSnack({...snack, open: false})}
                anchorOrigin={{vertical: "bottom", horizontal: "center"}}
            >
                <Alert
                    onClose={() => setSnack({...snack, open: false})}
                    severity={snack.type}
                    variant="filled"
                    sx={{width: "100%"}}
                >
                    {snack.message}
                </Alert>
            </Snackbar>
        </Box>
    );
}

export default Collect;