import { BrowserRouter } from "react-router-dom";
import DriverRoutes from "./DriverRoutes";

export default function App() {
    return (
        <BrowserRouter>
            <DriverRoutes />
        </BrowserRouter>
    );
}

