import { Box } from "@mui/material";
import DriverMapSection from "../components/DriverMapSection";
import { useDriverSession } from "../context/DriverSessionContext";
import { MOCK_DRIVER_DISPLAY } from "../mock/driverProfile";

export default function DriverHome() {
    const { session } = useDriverSession();
    const phone = session?.phone ?? "—";

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
                driverName={MOCK_DRIVER_DISPLAY.name}
                driverPhone={phone}
            />
        </Box>
    );
}
