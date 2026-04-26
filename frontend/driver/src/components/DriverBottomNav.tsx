import { Box, ButtonBase, Tooltip, Typography } from "@mui/material";
import HomeRoundedIcon from "@mui/icons-material/HomeRounded";
import ToggleOffRoundedIcon from "@mui/icons-material/ToggleOffRounded";
import ListAltRoundedIcon from "@mui/icons-material/ListAltRounded";
import { useNavigate, useLocation } from "react-router-dom";
import { useCallback, useState } from "react";
import { driverAppBarGradient } from "../driverTheme";

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

const iconSx = { fontSize: 25, color: "common.white" };

const labelSx = {
    fontSize: "0.7rem",
    fontWeight: 600,
    lineHeight: 1.2,
    color: "common.white",
    textAlign: "center",
} as const;

export default function DriverBottomNav() {
    const navigate = useNavigate();
    const location = useLocation();
    const [toggleTipOpen, setToggleTipOpen] = useState(false);

    const isHome =
        location.pathname === "/home" ||
        location.pathname === "/" ||
        location.pathname === "";
    const isRequests = location.pathname.startsWith("/current-requests");

    const handleToggleClick = useCallback(() => {
        setToggleTipOpen(true);
        window.setTimeout(() => setToggleTipOpen(false), 2000);
    }, []);

    return (
        <Box
            component="nav"
            aria-label="ناوبری اصلی راننده"
            sx={{
                display: "flex",
                flexDirection: "row",
                alignItems: "stretch",
                justifyContent: "space-around",
                flexShrink: 0,
                bgcolor: "transparent",
                background: driverAppBarGradient,
                backgroundImage: driverAppBarGradient,
                borderTop: "1px solid rgba(255,255,255,0.2)",
            }}
        >
            {/* در RTL اولین فرزند سمت راست قرار می‌گیرد → خانه */}
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

            <Tooltip
                title="ابتدا فعال کنید!"
                open={toggleTipOpen}
                disableHoverListener
                disableFocusListener
                disableTouchListener
                slotProps={{ popper: { sx: { direction: "rtl" } } }}
            >
                <Box component="span" sx={{ flex: 1, display: "flex", minWidth: 0 }}>
                    <ButtonBase
                        focusRipple
                        aria-label="تغییر وضعیت حضور"
                        onClick={handleToggleClick}
                        sx={{
                            ...navItemSx,
                            width: "100%",
                            opacity: 0.88,
                            borderTop: "3px solid transparent",
                        }}
                    >
                        <ToggleOffRoundedIcon sx={iconSx} />
                        <Typography component="span" sx={labelSx}>
                            تغییر وضعیت
                        </Typography>
                    </ButtonBase>
                </Box>
            </Tooltip>

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
    );
}
