import * as React from "react";
import {
    BottomNavigation,
    BottomNavigationAction,
    Paper
} from "@mui/material";
import {darken} from "@mui/material/styles";
import HomeFilled from "@mui/icons-material/HomeFilled";
import AccountCircleIcon from "@mui/icons-material/AccountCircle";
import GroupsIcon from "@mui/icons-material/Groups";
import Compost from "@mui/icons-material/Compost";
import {useNavigate} from "react-router-dom";
import {useAuthStore} from "../../store/useAuthStore.ts";

export default function Footer() {
    const [value, setValue] = React.useState(0);
    const navigate = useNavigate();
    const {setting} = useAuthStore();
    const mainColor = '#14C887';
    const darkerColor = darken(mainColor, 0.5);

    const actionStyle = {
        color: "rgb(255, 255, 255)",
        '&.Mui-selected': {color: darkerColor},
        '& .MuiBottomNavigationAction-label': {
            fontSize: '.875rem',
            whiteSpace: "nowrap",
        },
    };

    const handleChange = (_: React.SyntheticEvent, newValue: number) => {
        setValue(newValue);
        switch (newValue) {
            case 0:
                navigate("/");
                break;
            case 1:
                navigate("/profile");
                break;
            case 2:
                navigate("/requests");
                break;
            case 3:
                navigate("/collect");
                break;
        }
    };

    return (
        <Paper
            sx={{
                width: '95%',
                maxWidth: '550px',
                m: 'auto',
                position: 'fixed',
                bottom: 10,
                right: 0,
                left: 0,
                zIndex: 15,
                overflow: 'hidden',
                borderRadius: '300px',
                '@media (min-width: 550px)': {
                    mx: 'auto',
                },
            }}
            elevation={3}
        >
            <BottomNavigation
                showLabels
                value={value}
                onChange={handleChange}
                sx={{
                    background: "linear-gradient(90deg, rgb(20, 200, 135) 0%, rgb(15, 160, 105) 100%)",
                }}
            >
                <BottomNavigationAction  label="آنی‌روب" icon={<HomeFilled/>} sx={actionStyle}/>
                <BottomNavigationAction label="پروفایل کاربری" icon={<AccountCircleIcon/>} sx={actionStyle}/>
                <BottomNavigationAction label="درخواست ها" icon={<GroupsIcon/>} sx={actionStyle}/>
                <BottomNavigationAction disabled={setting?.currentRequest} label="جمع‌آوری" icon={<Compost/>} sx={actionStyle}/>
            </BottomNavigation>
        </Paper>
    );
}