import { createTheme } from "@mui/material/styles";

/**
 * تم جدا برای بخش راننده: گرادیان آبی و راست‌چین (متفاوت از نسخه کاربر سبز).
 */
export const driverTheme = createTheme({
    direction: "rtl",
    typography: {
        fontFamily: "Estedad-Regular, Arial, sans-serif",
        h6: {
            margin: 0,
            fontFamily: "Estedad-Bold, Arial, sans-serif",
            fontSize: "1rem",
            fontWeight: 700,
        },
        body1: {
            fontFamily: "Estedad-Regular, Arial, sans-serif",
            fontSize: "1rem",
            fontWeight: 400,
        },
        caption: {
            fontSize: "0.8rem",
        },
    },
    palette: {
        mode: "light",
        primary: {
            main: "#1e6fe6",
            contrastText: "#ffffff",
        },
        background: {
            default: "#eef4fc",
            paper: "#ffffff",
        },
    },
    components: {
        MuiButton: {
            styleOverrides: {
                containedPrimary: {
                    backgroundImage:
                        "linear-gradient(90deg, #42a5f5 0%, #1565c0 100%)",
                    color: "#ffffff",
                    borderRadius: "300px",
                    textTransform: "none",
                    boxShadow: "none",
                    "&:hover": {
                        backgroundImage:
                            "linear-gradient(90deg, #5eb8ff 0%, #1976d2 100%)",
                        boxShadow: "0 4px 12px rgba(21,101,192,0.35)",
                    },
                },
            },
        },
    },
});

export const driverAppBarGradient =
    "linear-gradient(135deg, #2196f3 0%, #0d47a1 100%)";

/** همان جهت گرادیان دراور اپ کاربر، با رنگ آبی */
export const driverDrawerPaperGradient =
    "linear-gradient(90deg, rgb(66, 165, 245) 0%, rgb(21, 101, 192) 100%)";
