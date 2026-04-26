import {
    Avatar,
    Badge,
    Box,
    Divider,
    Drawer,
    List,
    ListItemButton,
    ListItemIcon,
    ListItemText,
    Typography,
} from "@mui/material";
import Inventory2OutlinedIcon from "@mui/icons-material/Inventory2Outlined";
import PendingActionsOutlinedIcon from "@mui/icons-material/PendingActionsOutlined";
import TaskAltOutlinedIcon from "@mui/icons-material/TaskAltOutlined";
import CampaignOutlinedIcon from "@mui/icons-material/CampaignOutlined";
import LogoutOutlinedIcon from "@mui/icons-material/LogoutOutlined";
import { useNavigate } from "react-router-dom";
import { DRIVER_APP_VERSION } from "../constants";
import { driverDrawerPaperGradient } from "../driverTheme";
import { MOCK_DRIVER_DISPLAY } from "../mock/driverProfile";
import { MOCK_CURRENT_REQUESTS_COUNT } from "../mock/currentRequests";
import { useDriverSession } from "../context/DriverSessionContext";

type DriverDrawerMenuProps = {
    open: boolean;
    onClose: () => void;
    driverPhone: string;
};

const iconSx = { color: "rgb(255, 255, 255)" };

export default function DriverDrawerMenu({
    open,
    onClose,
    driverPhone,
}: DriverDrawerMenuProps) {
    const navigate = useNavigate();
    const { logout } = useDriverSession();

    const go = (path: string) => {
        navigate(path);
        onClose();
    };

    const handleLogout = () => {
        logout();
        onClose();
        navigate("/login", { replace: true });
    };

    return (
        <Drawer
            anchor="left"
            open={open}
            onClose={onClose}
            PaperProps={{
                sx: {
                    background: driverDrawerPaperGradient,
                    backgroundImage: driverDrawerPaperGradient,
                    color: "rgb(255, 255, 255)",
                    borderRadius: "0 60px 60px 0",
                },
            }}
        >
            <Box
                sx={{
                    width: 260,
                    height: "100%",
                    display: "flex",
                    flexDirection: "column",
                }}
                role="presentation"
            >
                <Box sx={{ px: 2, pt: 2, pb: 1 }}>
                    <Avatar
                        sx={{
                            width: 56,
                            height: 56,
                            mb: 1.5,
                            bgcolor: "rgba(255,255,255,0.2)",
                            color: "common.white",
                            fontWeight: 800,
                            border: "2px solid rgba(255,255,255,0.35)",
                        }}
                    >
                        {MOCK_DRIVER_DISPLAY.avatarLetter}
                    </Avatar>
                    <Typography variant="h6" sx={{ color: "rgb(255,255,255)" }}>
                        {MOCK_DRIVER_DISPLAY.name}
                    </Typography>
                    <Typography variant="body2" sx={{ color: "rgb(255,255,255)", opacity: 0.95 }}>
                        {driverPhone}
                    </Typography>
                </Box>

                <Divider sx={{ borderColor: "rgba(255,255,255,0.15)" }} />

                <List sx={{ flex: 1, py: 0, overflowY: "auto" }}>
                    <ListItemButton
                        onClick={() => go("/waste-prices")}
                        sx={{ color: "rgb(255,255,255)" }}
                    >
                        <ListItemIcon sx={{ minWidth: 44, ...iconSx }}>
                            <Inventory2OutlinedIcon />
                        </ListItemIcon>
                        <ListItemText primary="قیمت پسماندها" />
                    </ListItemButton>

                    <ListItemButton
                        onClick={() => go("/current-requests")}
                        sx={{ color: "rgb(255,255,255)" }}
                    >
                        <ListItemIcon sx={{ minWidth: 44, ...iconSx }}>
                            <Badge
                                badgeContent={MOCK_CURRENT_REQUESTS_COUNT}
                                sx={{
                                    "& .MuiBadge-badge": {
                                        bgcolor: "rgba(255,255,255,0.92)",
                                        color: "primary.dark",
                                        fontWeight: 800,
                                    },
                                }}
                                max={99}
                            >
                                <PendingActionsOutlinedIcon />
                            </Badge>
                        </ListItemIcon>
                        <ListItemText
                            primary="درخواست‌های جاری"
                            secondary={`${MOCK_CURRENT_REQUESTS_COUNT} مورد جاری`}
                            secondaryTypographyProps={{
                                sx: { color: "rgba(255,255,255,0.85)" },
                            }}
                        />
                    </ListItemButton>

                    <ListItemButton
                        onClick={() => go("/completed-requests")}
                        sx={{ color: "rgb(255,255,255)" }}
                    >
                        <ListItemIcon sx={{ minWidth: 44, ...iconSx }}>
                            <TaskAltOutlinedIcon />
                        </ListItemIcon>
                        <ListItemText primary="درخواست‌های انجام‌شده" />
                    </ListItemButton>

                    <ListItemButton
                        onClick={() => go("/notifications")}
                        sx={{ color: "rgb(255,255,255)" }}
                    >
                        <ListItemIcon sx={{ minWidth: 44, ...iconSx }}>
                            <CampaignOutlinedIcon />
                        </ListItemIcon>
                        <ListItemText primary="اطلاع‌رسانی" />
                    </ListItemButton>

                    <Divider sx={{ my: 1, borderColor: "rgba(255,255,255,0.15)" }} />

                    <ListItemButton onClick={handleLogout} sx={{ color: "rgb(255,255,255)" }}>
                        <ListItemIcon sx={{ minWidth: 44, color: "#ffcdd2" }}>
                            <LogoutOutlinedIcon />
                        </ListItemIcon>
                        <ListItemText
                            primary="خروج از حساب کاربری"
                            primaryTypographyProps={{
                                sx: { color: "#ffcdd2", fontWeight: 700 },
                            }}
                        />
                    </ListItemButton>
                </List>

                <Box
                    sx={{
                        py: 2,
                        fontSize: "0.85rem",
                        textAlign: "center",
                        color: "rgba(255, 255, 255, 0.25)",
                    }}
                >
                    نسخه نرم‌افزار {DRIVER_APP_VERSION}
                </Box>
            </Box>
        </Drawer>
    );
}
