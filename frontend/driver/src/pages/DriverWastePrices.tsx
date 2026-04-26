import { type FC } from "react";
import { Box } from "@mui/material";
import WastePricesGrid from "../components/waste/WastePricesGrid";
import { DRIVER_WASTE_PRICES_MOCK } from "../mock/driverWastePricesMock";

const DriverWastePrices: FC = () => {
    return (
        <Box
            sx={{
                flex: 1,
                minHeight: 0,
                overflow: "auto",
                px: 2,
                py: 2.5,
                pb: 3,
                bgcolor: "background.default",
            }}
        >
            <WastePricesGrid items={DRIVER_WASTE_PRICES_MOCK} loading={false} />
        </Box>
    );
};

export default DriverWastePrices;
