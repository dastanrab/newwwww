import {useState, useRef, useEffect} from "react";
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
    FormControlLabel
} from "@mui/material";
import {Map} from "@neshan-maps-platform/ol";
import NeshanMap from "@neshan-maps-platform/react-openlayers";
import LocationOnIcon from "@mui/icons-material/LocationOn";
import StarIcon from '@mui/icons-material/Star';
import MapIcon from '@mui/icons-material/Map';
import {useNavigate} from "react-router-dom";

interface Address {
    id: number;
    title: string;
    address: string;
    latitude?: number;
    longitude?: number;
}

interface SelectedLocation {
    latitude: number;
    longitude: number;
    detailedAddress?: string;
    isPreferred: boolean;
    customTitle?: string;
}

function Collect() {
    const navigate = useNavigate();
    const modalMapRef = useRef<Map | null>(null);
    const mapClickHandlerRef = useRef<((event: any) => void) | null>(null);
    const [openMapModal, setOpenMapModal] = useState(false);
    const [selectedLocation, setSelectedLocation] = useState<SelectedLocation | null>(null);
    const [detailedAddress, setDetailedAddress] = useState("");
    const [isPreferred, setIsPreferred] = useState(false);
    const [customTitle, setCustomTitle] = useState("");
    const [selectedAddressId, setSelectedAddressId] = useState<number | null>(null);

    // لیست آدرس‌های از پیش تعیین شده
    const [predefinedAddresses] = useState<Address[]>([
        {id: 1, title: "منزل", address: "تهران، خیابان ولیعصر، پلاک 123"},
        {id: 2, title: "شرکت", address: "تهران، میدان ونک، برج میلاد"},
        {id: 3, title: "دفتر کار", address: "تهران، خیابان انقلاب، دانشگاه تهران"},
    ]);

    // آدرس‌های منتخب (شرکت و منزل)
    const preferredAddresses = predefinedAddresses.filter(addr =>
        addr.title === "شرکت" || addr.title === "منزل"
    );

    const handleSelectAddress = (addressId: number) => {
        setSelectedAddressId(addressId);
    };

    const mapKey = "web.5aa1ec24bac34fde98d10c1a5215165d";

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

    // پاکسازی event listener هنگام بسته شدن مودال و fallback برای attach کردن event listener
    useEffect(() => {
        if (!openMapModal) return;

        // Fallback: اگر onReady کار نکرد، بعد از یک تاخیر event listener را attach می‌کنیم
        const timer = setTimeout(() => {
            if (modalMapRef.current && !mapClickHandlerRef.current) {
                const map = modalMapRef.current;
                const view = map.getView();

                const clickHandler = (event: any) => {
                    const coordinate = event.coordinate;
                    if (coordinate && view) {
                        const lonlat = view.getCoordinateFromPixel(event.pixel);
                        if (lonlat) {
                            const latitude = lonlat[1];
                            const longitude = lonlat[0];
                            setSelectedLocation({
                                latitude,
                                longitude,
                                isPreferred: false,
                            });
                        }
                    }
                };

                mapClickHandlerRef.current = clickHandler;
                map.on('click', clickHandler);
            }
        }, 1000);

        return () => {
            clearTimeout(timer);
            if (modalMapRef.current && mapClickHandlerRef.current) {
                modalMapRef.current.un('click', mapClickHandlerRef.current);
                mapClickHandlerRef.current = null;
            }
        };
    }, [openMapModal]);

    // مدیریت تغییر چک‌باکس
    const handlePreferredChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        setIsPreferred(event.target.checked);
        if (!event.target.checked) {
            setCustomTitle("");
        }
    };

    // ذخیره آدرس انتخاب شده
    const handleSaveAddress = () => {
        if (selectedLocation) {
            const addressToSave = {
                ...selectedLocation,
                detailedAddress: detailedAddress || undefined,
                isPreferred,
                customTitle: isPreferred ? customTitle : undefined,
            };
            console.log("آدرس ذخیره شده:", addressToSave);
            // در اینجا می‌توانید آدرس را به لیست اضافه کنید یا به API ارسال کنید
            handleCloseMapModal();
        }
    };

    const handleFinalSubmit = () => {
        // هدایت به صفحه انتخاب تاریخ و زمان
        navigate("/collect/schedule");
    };

    return (
        <Box>
            <Box sx={{display: 'flex', gap: 1, mb: 2}}>
                {preferredAddresses.map((address) => (
                    <Button
                        key={address.id}
                        color={selectedAddressId === address.id ? "primary" : "secondary"}
                        variant={selectedAddressId === address.id ? "contained" : "outlined"}
                        startIcon={<StarIcon/>}
                        onClick={() => handleSelectAddress(address.id)}
                        sx={{flex: 1}}
                    >
                        {address.title}
                    </Button>
                ))}
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
                <DialogContent>
                    <Box
                        sx={{
                            width: '100%',
                            height: '400px',
                            mb: 2,
                            position: 'relative',
                            overflow: 'hidden',
                            borderRadius: 2
                        }}
                    >
                        <NeshanMap
                            ref={modalMapRef}
                            mapKey={mapKey}
                            center={{latitude: 36.2974945, longitude: 59.6059232}}
                            zoom={14}
                            style={{
                                height: '100%',
                                width: '100%',
                            }}
                            onReady={(map: Map) => {
                                if (map && modalMapRef.current) {
                                    if (mapClickHandlerRef.current) {
                                        map.un('click', mapClickHandlerRef.current);
                                    }

                                    const view = map.getView();
                                    const clickHandler = (event: any) => {
                                        const coordinate = event.coordinate;
                                        if (coordinate && view) {
                                            const lonlat = view.getCoordinateFromPixel(event.pixel);
                                            if (lonlat) {
                                                const latitude = lonlat[1];
                                                const longitude = lonlat[0];
                                                setSelectedLocation({
                                                    latitude,
                                                    longitude,
                                                    isPreferred: false,
                                                });
                                            }
                                        }
                                    };

                                    mapClickHandlerRef.current = clickHandler;
                                    map.on('click', clickHandler);
                                }
                            }}
                        />
                    </Box>

                    {selectedLocation && (
                        <Box sx={{mt: 2}}>
                            <Typography variant="body2" sx={{mb: 1, color: 'success.main'}}>
                                موقعیت انتخاب
                                شده: {selectedLocation.latitude.toFixed(6)}, {selectedLocation.longitude.toFixed(6)}
                            </Typography>

                            <TextField
                                fullWidth
                                variant="outlined"
                                placeholder="آدرس دقیق‌تر (اختیاری)"
                                value={detailedAddress}
                                onChange={(e) => setDetailedAddress(e.target.value)}
                                sx={{mb: 2}}
                                InputProps={{
                                    startAdornment: (
                                        <InputAdornment position="start">
                                            <LocationOnIcon/>
                                        </InputAdornment>
                                    ),
                                }}
                            />

                            <FormControlLabel
                                control={
                                    <Checkbox
                                        checked={isPreferred}
                                        onChange={handlePreferredChange}
                                        color="primary"
                                    />
                                }
                                label="انتخاب به عنوان آدرس منتخب"
                                sx={{mb: 2}}
                            />

                            {isPreferred && (
                                <TextField
                                    fullWidth
                                    variant="outlined"
                                    placeholder="عنوان دلخواه برای این آدرس"
                                    value={customTitle}
                                    onChange={(e) => setCustomTitle(e.target.value)}
                                    sx={{mb: 2}}
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
                        disabled={!selectedLocation}
                    >
                        ذخیره آدرس
                    </Button>
                </DialogActions>
            </Dialog>

            <Box sx={{width: '100%', position: 'fixed', bottom: 90, right: 0, left: 0, textAlign: 'center'}}>
                <Button
                    onClick={handleFinalSubmit}
                    variant="contained"
                    size="large"
                    sx={{borderRadius: '300px', px: 5}}
                >
                    تایید نهایی
                </Button>
            </Box>
        </Box>
    );
}

export default Collect;