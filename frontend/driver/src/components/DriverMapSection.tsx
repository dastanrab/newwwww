import { useEffect, useRef, useState, useCallback } from "react";
import {
    Avatar,
    Box,
    Paper,
    Stack,
    Typography,
    Dialog,
    DialogTitle,
    DialogContent,
    Button, Snackbar, Alert, Divider,
} from "@mui/material";
import SmartphoneOutlinedIcon from "@mui/icons-material/SmartphoneOutlined";
import Map from "@neshan-maps-platform/ol/Map";
import View from "@neshan-maps-platform/ol/View";
import { fromLonLat } from "@neshan-maps-platform/ol/proj";
import { Feature } from "@neshan-maps-platform/ol";
import Point from "@neshan-maps-platform/ol/geom/Point";
import Icon from "@neshan-maps-platform/ol/style/Icon";
import { Style } from "@neshan-maps-platform/ol/style";
import VectorLayer from "@neshan-maps-platform/ol/layer/Vector";
import VectorSource from "@neshan-maps-platform/ol/source/Vector";
import { DriverRequest, useDriverRequests } from "../hooks/useDriverRequests";
import { useAuthStore } from "../store/useAuthStore";
import { MapBrowserEvent } from "@neshan-maps-platform/ol";

type DriverMapSectionProps = {
    driverName: string;
    driverPhone: string;
};

export default function DriverMapSection({
                                             driverName,
                                             driverPhone,
                                         }: DriverMapSectionProps) {
    const mapRef = useRef<HTMLDivElement | null>(null);
    const mapInstance = useRef<Map | null>(null);
    const { getDriverRequests, requests,receiveRequest } = useDriverRequests();
    const [selectedRequest, setSelectedRequest] = useState<DriverRequest | null>(null);
    const [openModal, setOpenModal] = useState(false);
    const { accessToken } = useAuthStore();
    const [openSnackbar, setOpenSnackbar] = useState(false);
    const [snackbarMessage, setSnackbarMessage] = useState('');
    type SnackbarSeverity = "error" | "info" | "success" | "warning";

    const [snackbarSeverity, setSnackbarSeverity] = useState<SnackbarSeverity>('success');

    // @ts-ignore
    const handleRegisterWaste = async (requestId)=>{
        console.log(requestId)
    }
    // @ts-ignore
    const handleReceiveRequest = async (requestId) => {
        try {
            // استفاده از متد receiveRequest از هوک برای ارسال درخواست به سرور
            const response = await receiveRequest(requestId, accessToken);  // در اینجا token باید از context یا state گرفته شود

            // بررسی موفقیت درخواست
            if (response.status === "success") {
                setSnackbarMessage('درخواست با موفقیت دریافت شد!');
                setSnackbarSeverity('success');
                await getDriverRequests(accessToken);
                setOpenModal(false)
            } else {
                setSnackbarMessage('خطا در دریافت درخواست!');
                setSnackbarSeverity('error');
                setOpenModal(false)
            }
        } catch (error) {
            console.error('Error receiving request:', error);
            setSnackbarMessage('خطا در برقراری ارتباط با سرور!');
            setSnackbarSeverity('error');
        }

        // نمایش پیام
        setOpenSnackbar(true);
    };
    const fetchRequests = useCallback(() => {
        if (!requests) {
            getDriverRequests(accessToken);
        }
    }, [getDriverRequests, accessToken, requests]);

    useEffect(() => {
        fetchRequests();
    }, [fetchRequests]);

    useEffect(() => {
        if (!mapRef.current || mapInstance.current) return;

        const map = new Map({
            mapType: "neshan",
            target: mapRef.current,
            key: "web.7f11b5c6971d4917a6e9272a522d8b9e",
            poi: true,
            traffic: true,
            layers: [],
            view: new View({
                center: fromLonLat([59.58874, 36.28865]),
                zoom: 14,
            }),
        });

        const vectorSource = new VectorSource();
        const vectorLayer = new VectorLayer({
            source: vectorSource,
        });
        map.addLayer(vectorLayer);

        const defaultLocationFeature = new Feature({
            geometry: new Point(fromLonLat([59.58874, 36.28865])),
        });

        defaultLocationFeature.setStyle(
            new Style({
                image: new Icon({
                    src: '/truc.svg',
                    anchor: [0.5, 1],
                    scale: 0.32,
                }),
            })
        );

        vectorSource.addFeature(defaultLocationFeature);

        // Add requests with trash.svg icon
        requests?.forEach((req) => {
            const requestFeature = new Feature({
                geometry: new Point(fromLonLat([req.location.lng, req.location.lat])),
            });

            requestFeature.set('requestData', req);
            const iconSrc = req.status.value === 2 ? '/received1.svg' : '/rec.svg'; // تغییر آیکون برای وضعیت 2

            requestFeature.setStyle(
                new Style({
                    image: new Icon({
                        src: iconSrc,
                        scale: req.status.value === 2 ? 0.22 :0.09,
                    }),
                })
            );

            vectorSource.addFeature(requestFeature);
        });

        // Handling click event on map to show request details
        map.on('click', (event: MapBrowserEvent<PointerEvent>) => {
            const feature = map.getFeaturesAtPixel(event.pixel)[0];
            if (feature && feature.get('requestData')) {
                const requestData = feature.get('requestData');
                setSelectedRequest(requestData); // Set the selected request details
                setOpenModal(true); // Open the modal
            }
        });

        mapInstance.current = map;

        return () => {
            map.setTarget(undefined);
            mapInstance.current = null;
        };
    }, [requests]);

    return (
        <Box
            sx={{
                position: "relative",
                flex: 1,
                minHeight: 0,
                width: "100%",
                bgcolor: "grey.200",
            }}
        >
            <Box
                ref={mapRef}
                sx={{
                    position: "absolute",
                    inset: 0,
                    "& canvas": { outline: "none" },
                    pb: "90px",
                    width: "100%",
                    height: "100%",
                }}
            />

            {/* Driver info box */}
            <Box
                sx={{
                    position: "absolute",
                    top: 10,
                    right: 10,
                    zIndex: 1,
                    pointerEvents: "none",
                    maxWidth: "calc(100% - 20px)",
                }}
            >
                <Paper
                    elevation={0}
                    sx={{
                        width: "fit-content",
                        maxWidth: 200,
                        px: 1.35,
                        pt: 1.35,
                        pb: 1.15,
                        borderRadius: 2,
                        textAlign: "center",
                        bgcolor: "rgba(255, 255, 255, 0.88)",
                        backdropFilter: "blur(10px)",
                        WebkitBackdropFilter: "blur(10px)",
                        border: "1px solid rgba(255, 255, 255, 0.7)",
                    }}
                >
                    <Stack alignItems="center" spacing={1}>
                        <Box
                            sx={{
                                p: "2px",
                                borderRadius: "50%",
                                background:
                                    "linear-gradient(145deg, #64b5f6 0%, #1565c0 55%, #0d47a1 100%)",
                            }}
                        >
                            <Avatar
                                sx={{
                                    width: 40,
                                    height: 40,
                                    bgcolor: "background.paper",
                                    color: "primary.dark",
                                    fontWeight: 800,
                                }}
                            >
                                {driverName.substring(0, 1)}
                            </Avatar>
                        </Box>

                        <Box sx={{ minWidth: 0, width: "100%" }}>
                            <Typography
                                variant="body2"
                                fontWeight={800}
                                sx={{
                                    lineHeight: 1.3,
                                    color: "primary.dark",
                                    fontSize: "0.8125rem",
                                }}
                            >
                                {driverName}
                            </Typography>
                            <Stack
                                direction="row"
                                alignItems="center"
                                justifyContent="center"
                                spacing={0.5}
                                sx={{ mt: 0.5 }}
                            >
                                <SmartphoneOutlinedIcon
                                    sx={{
                                        fontSize: 14,
                                        color: "primary.main",
                                    }}
                                />
                                <Typography
                                    variant="caption"
                                    color="text.secondary"
                                    fontWeight={500}
                                    sx={{
                                        direction: "ltr",
                                    }}
                                >
                                    {driverPhone}
                                </Typography>
                            </Stack>
                        </Box>
                    </Stack>
                </Paper>
            </Box>

            {/* Modal for selected request */}
            <Dialog open={openModal} onClose={() => setOpenModal(false)} fullWidth maxWidth="sm">
                <DialogTitle sx={{ borderBottom: '1px solid #eee', mb: 2 }}>جزئیات درخواست</DialogTitle>
                <DialogContent>
                    {selectedRequest && (
                        <Box display="flex" flexDirection="column" gap={2}>
                            {/* کارت اطلاعات شخصی */}
                            <Box p={2} borderRadius={2} bgcolor="#f9f9f9" border="1px solid #e0e0e0">
                                <Typography variant="h6" color="primary" gutterBottom>{selectedRequest.name}</Typography>
                                <Typography variant="body1"><b>موبایل:</b> {selectedRequest.mob}</Typography>
                                <Typography variant="body1"><b>آدرس:</b> {selectedRequest.address}</Typography>
                            </Box>

                            <Box display="flex" justifyContent="space-between" alignItems="center">
                                <Typography variant="body2" color="textSecondary"><b>نوع کاربر:</b> {selectedRequest.userType === "citizen" ? "شهروند" : "صنف"}</Typography>
                                <Typography variant="body2" color="textSecondary"><b>فوری:</b> {selectedRequest.immediate ? "بله" : "خیر"}</Typography>
                            </Box>

                            <Typography variant="body2" color="textSecondary"><b>زمان:</b> {selectedRequest.date.day} - {selectedRequest.date.time}</Typography>

                            <Divider sx={{ my: 1 }} />

                            {/* دکمه‌ها */}
                            <Box display="flex" gap={2} mt={1}>
                                {selectedRequest.status.value === 1 && (
                                    <Button variant="contained" color="primary" fullWidth onClick={() => handleReceiveRequest(selectedRequest.id)}>
                                        دریافت درخواست
                                    </Button>
                                )}

                                {selectedRequest.status.value === 2 && (
                                    <Button variant="contained" color="success" fullWidth onClick={() => handleRegisterWaste(selectedRequest.id)}>
                                        ثبت پسماند
                                    </Button>
                                )}
                            </Box>
                        </Box>
                    )}
                </DialogContent>
            </Dialog>

            {/* اسنک‌بار با استایل ارتقا یافته */}
            <Snackbar
                open={openSnackbar}
                autoHideDuration={6000}
                onClose={() => setOpenSnackbar(false)}
                anchorOrigin={{ vertical: 'top', horizontal: 'center' }}
                sx={{ zIndex: 9999, mt: 8 }}
            >
                <Alert
                    onClose={() => setOpenSnackbar(false)}
                    severity={snackbarSeverity}
                    variant="filled"
                    sx={{ width: '100%', boxShadow: 3 }}
                >
                    {snackbarMessage}
                </Alert>
            </Snackbar>

        </Box>
    );
}