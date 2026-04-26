import { Navigate, Route, Routes } from "react-router-dom";
import { ThemeProvider } from "@mui/material/styles";
import { driverTheme } from "./driverTheme";
import { DriverSessionProvider } from "./context/DriverSessionContext";
import DriverLogin from "./pages/DriverLogin";
import DriverVerify from "./pages/DriverVerify";
import DriverLayout from "./layout/DriverLayout";
import DriverHome from "./pages/DriverHome";
import DriverCurrentRequestsPage from "./pages/DriverCurrentRequestsPage";
import DriverWastePrices from "./pages/DriverWastePrices";
import DriverNotificationsPage from "./pages/DriverNotificationsPage";
import DriverCompletedRequestsPage from "./pages/DriverCompletedRequestsPage";

export default function DriverRoutes() {
    return (
        <ThemeProvider theme={driverTheme}>
            <DriverSessionProvider>
                <Routes>
                    <Route path="/login" element={<DriverLogin />} />
                    <Route path="/verify" element={<DriverVerify />} />
                    <Route element={<DriverLayout />}>
                        <Route index element={<Navigate to="/home" replace />} />
                        <Route path="/home" element={<DriverHome />} />
                        <Route path="/waste-prices" element={<DriverWastePrices />} />
                        <Route
                            path="/current-requests"
                            element={<DriverCurrentRequestsPage />}
                        />
                        <Route
                            path="/completed-requests"
                            element={<DriverCompletedRequestsPage />}
                        />
                        <Route
                            path="/notifications"
                            element={<DriverNotificationsPage />}
                        />
                    </Route>
                    <Route path="*" element={<Navigate to="/login" replace />} />
                </Routes>
            </DriverSessionProvider>
        </ThemeProvider>
    );
}
