import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import { ThemeProvider, CssBaseline } from "@mui/material";

import { CacheProvider } from "@emotion/react";
import createCache from "@emotion/cache";
import rtlPlugin from "@mui/stylis-plugin-rtl";
import { prefixer } from "stylis";

import App from "./App";
import { driverTheme } from "./driverTheme";
import "./assets/style.css";
import "./assets/driver-overrides.css";

const cacheRtl = createCache({
    key: "muirtl",
    stylisPlugins: [prefixer, rtlPlugin],
});

createRoot(document.getElementById("root")!).render(
    <StrictMode>
        <CacheProvider value={cacheRtl}>
            <ThemeProvider theme={driverTheme}>
                <CssBaseline />
                <App />
            </ThemeProvider>
        </CacheProvider>
    </StrictMode>
);

