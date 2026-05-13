import { AppBar, IconButton, Toolbar, Typography, Box } from "@mui/material";
import MenuIcon from "@mui/icons-material/Menu";
import ArrowBackIosIcon from "@mui/icons-material/ArrowBackIos";
import { useState, type MouseEvent } from "react";
import DriverDrawerMenu from "./DriverDrawerMenu";

type DriverAppBarProps = {
    title: string;
    showBack: boolean;
    onBack: () => void;
    driverPhone: string;
};

export default function DriverAppBar({
    title,
    showBack,
    onBack,
    driverPhone,
}: DriverAppBarProps) {
    const [drawerOpen, setDrawerOpen] = useState(false);

    const handleMenuOpen = (_e: MouseEvent<HTMLButtonElement>) => {
        setDrawerOpen(true);
    };

    return (
        <>
            <AppBar
                position="static"
                elevation={0}
                sx={{
                    backgroundImage: 'linear-gradient(90deg,rgb(0, 160, 180) 0%, rgb(0, 125, 140) 100%)',
                }}
            >
                <Toolbar sx={{ direction: "rtl", gap: 1, minHeight: 55, backgroundImage: 'linear-gradient(90deg,rgb(0, 160, 180) 0%, rgb(0, 125, 140) 100%)', }}>
                    <Box
                        sx={{
                            width: 40,
                            display: "flex",
                            justifyContent: "flex-start",
                        }}
                    >
                        {showBack ? (
                            <IconButton
                                edge="start"
                                sx={{ color: "common.white" }}
                                aria-label="بازگشت"
                                onClick={onBack}
                                size="small"
                            >
                                <ArrowBackIosIcon fontSize="small" />
                            </IconButton>
                        ) : null}
                    </Box>

                    <Typography
                        variant="h6"
                        component="div"
                        sx={{
                            flex: 1,
                            textAlign: "center",
                            fontWeight: 800,
                            overflow: "hidden",
                            textOverflow: "ellipsis",
                            whiteSpace: "nowrap",
                            color: "common.white",
                        }}
                    >
                        {title}
                    </Typography>

                    <Box
                        sx={{
                            width: 40,
                            display: "flex",
                            justifyContent: "flex-end",
                        }}
                    >
                        <IconButton
                            edge="end"
                            sx={{ color: "common.white" }}
                            aria-label="منو"
                            onClick={handleMenuOpen}
                            size="small"
                        >
                            <MenuIcon />
                        </IconButton>
                    </Box>
                </Toolbar>
            </AppBar>

            <DriverDrawerMenu
                open={drawerOpen}
                onClose={() => setDrawerOpen(false)}
                driverPhone={driverPhone}
            />
        </>
    );
}
