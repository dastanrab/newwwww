import { createTheme } from "@mui/material/styles";

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
            main: "rgb(0, 160, 180)",
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
                        "linear-gradient(90deg, rgb(0, 160, 180) 0%, rgb(0, 125, 140) 100%)",
                    color: "#ffffff",
                    borderRadius: "300px",
                    textTransform: "none",
                    boxShadow: "none",
                    "&:hover": {
                        backgroundImage:
                            "linear-gradient(90deg,rgb(0, 160, 180) 0%, rgb(0, 125, 140) 100%)",
                        boxShadow: "0 5px 10px rgba(20, 100, 190, 0.35)",
                    },
                },
            },
        },
    },
});

export const driverAppBarGradient =
    "linear-gradient(90deg, rgb(0, 160, 180) 0%, rgb(0, 125, 140) 100%)";

export const driverDrawerPaperGradient =
    "linear-gradient(90deg, rgb(0, 160, 180) 0%, rgb(0, 125, 140) 100%)";
