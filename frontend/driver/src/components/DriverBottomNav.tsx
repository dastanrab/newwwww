import React, { useState, useEffect, useCallback } from "react";
import { Box, ButtonBase, Typography, Snackbar, Alert, CircularProgress } from "@mui/material";
import HomeRoundedIcon from "@mui/icons-material/HomeRounded";
import PowerSettingsNewRoundedIcon from "@mui/icons-material/PowerSettingsNewRounded";
import PowerOffRoundedIcon from "@mui/icons-material/PowerOffRounded";
import ListAltRoundedIcon from "@mui/icons-material/ListAltRounded";
import { useNavigate, useLocation } from "react-router-dom";
import { driverAppBarGradient } from "../driverTheme";
import { useAuthStore } from "../store/useAuthStore";
import { useRollCall } from "../hooks/useRollCall";

const navItemSx = {
    flex: 1,
    display: "flex",
    flexDirection: "column",
    alignItems: "center",
    justifyContent: "center",
    gap: 0.25,
    py: 1,
    minWidth: 0,
    color: "common.white",
    "&:hover": {
        bgcolor: "rgba(255,255,255,0.08)",
    },
} as const;

const iconSx = {
    fontSize: 30,
    color: "common.white",
    transition: "all 0.3s ease-in-out",
};

const labelSx = {
    fontSize: "0.7rem",
    fontWeight: 600,
    lineHeight: 1.2,
    color: "common.white",
    textAlign: "center",
} as const;

export default function DriverBottomNav() {
    const { setting, setSetting, accessToken } = useAuthStore();
    const navigate = useNavigate();
    const location = useLocation();
    const [toggleTipOpen, setToggleTipOpen] = useState(false);
    const [loading, setLoading] = useState(false); // Add a loading state
    const [snackbarMessage, setSnackbarMessage] = useState("");
    const [snackbarSeverity, setSnackbarSeverity] = useState<"success" | "error" | "warning" | "info">("error");
    const { updateRollCall, error } = useRollCall();
    const [lat, setLat] = useState(36.29091038457875);
    const [lng, setLng] = useState(59.542967399205395);

    const isHome = location.pathname === "/home" || location.pathname === "/" || location.pathname === "";
    const isRequests = location.pathname.startsWith("/current-requests");

    const showSnackbar = (message: string, severity: "success" | "error" | "warning" | "info") => {
        setSnackbarMessage(message);
        setSnackbarSeverity(severity);
        setToggleTipOpen(true);
    };

    const handleToggleClick = useCallback(async () => {
        setLoading(true); // Start loading
        // @ts-ignore
        const response = await updateRollCall(lat, lng, accessToken);

        if (response.status === "success") {
            const currentStatus = setting?.user?.rollCall?.status;
            const newStatus = response.data.status;

            setSetting((prev: any) => ({
                ...prev,
                user: {
                    ...prev?.user,
                    rollCall: {
                        ...prev?.user?.rollCall,
                        status: newStatus,
                    },
                },
            }));

            showSnackbar("حضور ثبت شد.", "success");
        } else {
            showSnackbar(error || "خطا در ثبت حضور", "error");
        }

        setLoading(false); // Stop loading
    }, [setting, setSetting, updateRollCall, lat, lng, accessToken, error]);

    useEffect(() => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    setLat(position.coords.latitude);
                    setLng(position.coords.longitude);
                },
                () => {
                    console.log("Unable to retrieve your location");
                }
            );
        }
    }, []);

    return (
        <>
            <Box
                component="nav"
                aria-label="ناوبری اصلی راننده"
                sx={{
                    position: "fixed",
                    bottom: 0,
                    left: 0,
                    zIndex: 20000,
                    display: "flex",
                    flexDirection: "row",
                    alignItems: "stretch",
                    justifyContent: "space-around",
                    flexShrink: 0,
                    bgcolor: "transparent",
                    background: driverAppBarGradient,
                    backgroundImage: driverAppBarGradient,
                    borderTop: "1px solid rgba(255,255,255,0.2)",
                    width: "100%",
                }}
            >
                {/* خانه */}
                <ButtonBase
                    focusRipple
                    aria-current={isHome ? "page" : undefined}
                    onClick={() => navigate("/home")}
                    sx={{
                        ...navItemSx,
                        opacity: isHome ? 1 : 0.88,
                        fontWeight: isHome ? 700 : 400,
                        borderTop: isHome ? "3px solid #fff" : "3px solid transparent",
                    }}
                >
                    <HomeRoundedIcon sx={iconSx} />
                    <Typography component="span" sx={labelSx}>
                        خانه
                    </Typography>
                </ButtonBase>

                {/* دکمه سوئیچ فعال/غیرفعال */}
                <Box
                    sx={{
                        flex: 0.8,
                        display: "flex",
                        minWidth: 0,
                        justifyContent: "center",
                        alignItems: "center",
                        position: "relative",
                    }}
                >
                    <ButtonBase
                        focusRipple
                        onClick={handleToggleClick}
                        sx={{
                            width: 70,
                            height: 70,
                            borderRadius: "50%",
                            bgcolor: setting?.user?.rollCall?.status !== "absent" ? "#2ecc71" : "#e74c3c",
                            boxShadow: "0 0 10px rgba(0,0,0,0.3)",
                            transition: "background-color 0.4s, transform 0.2s",
                            "&:hover": {
                                transform: "scale(0.8)",
                            },
                            display: "flex",
                            flexDirection: "column",
                            alignItems: "center",
                            justifyContent: "center",
                            transform: "translateY(-9px)",
                            zIndex: 1000,
                        }}
                    >
                        {loading ? (
                            <CircularProgress size={40} color="inherit" />
                        ) : setting?.user?.rollCall?.status !== "absent" ? (
                            <PowerSettingsNewRoundedIcon sx={iconSx} />
                        ) : (
                            <PowerOffRoundedIcon sx={iconSx} />
                        )}
                        <Typography component="span" sx={{ ...labelSx, color: "white" }}>
                            {setting?.user?.rollCall?.status !== "absent" ? "فعال" : "غیرفعال"}
                        </Typography>
                    </ButtonBase>
                </Box>

                {/* درخواست‌ها */}
                <ButtonBase
                    focusRipple
                    aria-current={isRequests ? "page" : undefined}
                    onClick={() => navigate("/current-requests")}
                    sx={{
                        ...navItemSx,
                        opacity: isRequests ? 1 : 0.88,
                        borderTop: isRequests ? "3px solid #fff" : "3px solid transparent",
                    }}
                >
                    <ListAltRoundedIcon sx={iconSx} />
                    <Typography component="span" sx={labelSx}>
                        درخواست‌ها
                    </Typography>
                </ButtonBase>
            </Box>

            {/* Snackbar for Feedback */}
            <Snackbar
                open={toggleTipOpen}
                autoHideDuration={3000}
                onClose={() => setToggleTipOpen(false)}
                anchorOrigin={{ vertical: "bottom", horizontal: "center" }}
            >
                <Alert severity={snackbarSeverity} onClose={() => setToggleTipOpen(false)}>
                    {snackbarMessage}
                </Alert>
            </Snackbar>
        </>
    );
}