import { Box } from "@mui/material";
import { Outlet, useLocation, useNavigate } from "react-router-dom";
import { useEffect, useMemo } from "react";
import DriverAppBar from "../components/DriverAppBar";
import DriverBottomNav from "../components/DriverBottomNav";
import { useDriverSession } from "../context/DriverSessionContext";
import {useAuthStore} from "../store/useAuthStore";

function getPageTitle(pathname: string): string {
    if (pathname.startsWith("/waste-prices")) return "قیمت پسماندها";
    if (pathname.startsWith("/current-requests")) return "درخواست‌های جاری";
    if (pathname.startsWith("/completed-requests")) return "درخواست‌های انجام‌شده";
    if (pathname.startsWith("/notifications")) return "اطلاع‌رسانی";
    return "خانه";
}

export default function DriverLayout() {
    const location = useLocation();
    const navigate = useNavigate();
    const { accessToken, setting, refreshSettings } = useAuthStore();
    const user = setting?.user;

    useEffect(() => {
        if (!accessToken) {
            console.log('no location')
            navigate("/login", { replace: true, state: { from: location } });
        } else {
            console.log('refresh setting')
            refreshSettings();
        }
    }, [accessToken, navigate, location, refreshSettings]);

    const { title, showBack } = useMemo(() => {
        const onHome =
            location.pathname === "/home" ||
            location.pathname === "/" ||
            location.pathname === "";

        return { title: getPageTitle(location.pathname), showBack: !onHome };
    }, [location.pathname]);

    const handleBack = () => {
        navigate(-1);
    };

    if (!accessToken) {
        return null;
    }


    return (
        <Box
            sx={{
                height: "100vh",
                display: "flex",
                flexDirection: "column",
                bgcolor: "background.default",
            }}
        >
            <DriverAppBar
                title={title}
                showBack={showBack}
                onBack={handleBack}
                driverPhone={user?.mob ?? '-'}
            />
            <Box
                sx={{
                    flex: 1,
                    display: "flex",
                    flexDirection: "column",
                }}
            >
                <Outlet />
            </Box>
            <DriverBottomNav />
        </Box>
    );
}
