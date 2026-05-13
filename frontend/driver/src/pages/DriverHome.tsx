import { Box } from "@mui/material";
import DriverMapSection from "../components/DriverMapSection";
import { useDriverSession } from "../context/DriverSessionContext";
import { MOCK_DRIVER_DISPLAY } from "../mock/driverProfile";
import {useAuthStore} from "../store/useAuthStore";
import {useEffect} from "react";

export default function DriverHome() {
    const { setting } = useAuthStore();
    const user = setting?.user;


    return (
        <Box
            sx={{
                flex: 1,
                minHeight: 0,
                display: "flex",
                flexDirection: "column",
            }}
        >
            <DriverMapSection
                driverName={user?.firstName ?? '-' + user?.lastName ?? 'S'}
                driverPhone={user?.mob ?? '-'}
            />
        </Box>
    );
}
