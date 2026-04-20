import React, {useState} from "react";
import {
    // Layout / Structure
    AppBar,
    Toolbar,
    Drawer,
    Box,
    IconButton,

    // Lists / Navigation
    List,
    ListItem,
    ListItemButton,
    ListItemText,
    ListItemIcon,
    Divider,
    Chip,
    Typography,
} from "@mui/material";
import MenuIcon from "@mui/icons-material/Menu";
import MessageIcon from "@mui/icons-material/Message";
import WestIcon from "@mui/icons-material/West";
import HomeFilled from "@mui/icons-material/HomeFilled";
import AccountCircle from "@mui/icons-material/AccountCircle";
import AssignmentIcon from "@mui/icons-material/Assignment";
import AccountBalanceWalletIcon from "@mui/icons-material/AccountBalanceWallet";
import Shield from "@mui/icons-material/Shield";
import LockIcon from "@mui/icons-material/Lock";
import Timeline from "@mui/icons-material/Timeline";
import SupportAgentIcon from "@mui/icons-material/SupportAgent";
import CreditCard from "@mui/icons-material/CreditCard";
import LogoutIcon from "@mui/icons-material/Logout";
import {useLocation, useNavigate} from "react-router-dom";

import {useAuthStore} from "../../store/useAuthStore.ts";

const pageTitles: Record<string, string> = {
    "/": "آنی‌روب",
    "/wallet": "کیف پول",
    "/messages": "اعلان ها",
    "/tickets": "پشتیبانی",
    "/tickets/:id": "جزئیات پیام پشتیبانی",
    "/tickets/new": "درخواست پشتیبانی",
    "/profile": "پروفایل کاربری",
    "/rule": "قوانین و مقررات",
    "/privacy": "حریم شخصی",
    "/prices": "تعرفه انواع پسماند",
    "/requests": "درخواست ها",
    "/request": "جزئیات درخواست",
    "/collect": "جمع آوری پسماند",
    "/shop": "اینترنت ، شارژ",
    "/shop/history": "سوابق خرید",
};

const Header: React.FC = () => {
    const [drawerOpen, setDrawerOpen] = useState(false);
    const location = useLocation();
    const navigate = useNavigate();
    const {logout, setting} = useAuthStore();

    const toggleDrawer = (open: boolean) => () => {
        setDrawerOpen(open);
    };

    const isHome = location.pathname === "/";

    return (
        <>
            <AppBar
                position="fixed"
                sx={{
                    width: '95%',
                    m: 'auto',
                    top: 10,
                    right: 0,
                    left: 0,
                    overflow: 'hidden',
                    borderRadius: '300px',
                    background: 'linear-gradient(90deg, rgb(20, 200, 135) 0%, rgb(15, 160, 105) 100%)',
                    '@media (min-width: 550px)': {
                        width: '550px',
                        mx: 'auto',
                        right: 0,
                        left: 0,
                    },
                }}
            >
                <Toolbar sx={{display: 'flex', justifyContent: 'space-between'}}>
                    <IconButton color="inherit" edge="end" onClick={toggleDrawer(true)}>
                        <MenuIcon/>
                    </IconButton>
                    <Typography variant="h6" sx={{flexGrow: 1, color: 'rgb(255, 255, 255)', textAlign: 'center'}}>
                        {pageTitles[location.pathname] || "آنی‌روب"}
                    </Typography>
                    {isHome ? (
                        <IconButton color="inherit" edge="start" onClick={() => navigate("/messages")}>
                            <MessageIcon/>
                        </IconButton>
                    ) : (
                        <IconButton color="inherit" edge="start" onClick={() => navigate(-1)}>
                            <WestIcon/>
                        </IconButton>
                    )}
                </Toolbar>
            </AppBar>
            <Drawer
                anchor="left"
                open={drawerOpen}
                onClose={toggleDrawer(false)}
                PaperProps={{
                    sx: {
                        background: 'linear-gradient(90deg, rgb(20, 200, 135) 0%, rgb(15, 160, 105) 100%)',
                        color: 'rgb(255, 255, 255)',
                        borderRadius: '0 60px 60px 0',
                    },
                }}
            >
                <Box sx={{width: 260, height: '100%', display: 'flex', flexDirection: 'column'}}>
                    <List>
                        <ListItem sx={{display: 'flex', alignItems: 'unset', flexDirection: 'column'}}>
                            <Typography sx={{mb: 0.5, color: "rgb(255,255,255)"}}>
                                {[setting?.user?.firstName, setting?.user?.lastName]
                                    .filter(Boolean)
                                    .join(" ") || "-"}
                            </Typography>
                            <Typography variant="h6" sx={{color: "rgb(255,255,255)"}}>
                                {setting?.user?.mob || '-'}
                            </Typography>
                        </ListItem>
                        <Divider sx={{borderColor: "rgba(255,255,255,0.15)"}}/>
                        {[
                            {text: "آنی‌روب", icon: <HomeFilled/>, path: "/"},
                            {text: "پروفایل کاربری", icon: <AccountCircle/>, path: "/profile"},
                            {text: "درخواست ها", icon: <AssignmentIcon/>, path: "/requests"},
                            {
                                text: "کیف پول",
                                icon: <AccountBalanceWalletIcon/>,
                                path: "/wallet",
                                chip: {amount: setting?.user?.balance ?? 0, currency: "تومان"},
                            },
                            {text: "قوانین و مقررات", icon: <Shield/>, path: "/rule"},
                            {text: "حریم خصوصی", icon: <LockIcon/>, path: "/privacy"},
                            {text: "قیمت پسماندها", icon: <Timeline/>, path: "/prices"},
                            {text: "پشتیبانی", icon: <SupportAgentIcon/>, path: "/tickets"},
                            {text: "فروشگاه", icon: <CreditCard/>, path: "/shop"},
                            {
                                text: "خروج",
                                icon: <LogoutIcon/>,
                                path: "/login",
                                logout: true,
                            }

                        ].map((item, index) => (
                            <ListItem key={index} disablePadding>
                                <ListItemButton
                                    onClick={() => {
                                        if (item.logout) {
                                            logout()
                                            navigate("/login");
                                        } else {
                                            navigate(item.path);
                                        }
                                        setDrawerOpen(false);
                                    }}
                                    sx={{color: 'rgb(255,255,255)'}}
                                >
                                    <ListItemIcon sx={{color: "rgb(255,255,255)"}}>
                                        {item.icon}
                                    </ListItemIcon>
                                    <ListItemText primary={item.text}/>
                                    {item.chip && (
                                        <Chip
                                            size="small"
                                            label={
                                                <Typography sx={{fontSize: '0.8rem', color: 'rgb(255, 255, 255)'}}>
                                                    <strong>{item.chip.amount}</strong>{" "}
                                                    <small>{item.chip.currency}</small>
                                                </Typography>
                                            }
                                            sx={{
                                                height: '25px',
                                                p: '0 5px',
                                                backgroundColor: 'rgba(255, 255, 255, 0.25)',
                                                '& .MuiChip-label': {
                                                    px: 1,
                                                },
                                            }}
                                        />
                                    )}
                                </ListItemButton>
                            </ListItem>
                        ))}
                    </List>
                    <Box
                        sx={{
                            mt: 'auto',
                            py: 2,
                            fontSize: '0.85rem',
                            textAlign: 'center',
                            color: 'rgba(255, 255, 255, 0.25)',
                        }}
                    >
                        1.0.0
                    </Box>
                </Box>
            </Drawer>
        </>
    );
};

export default Header;