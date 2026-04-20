import {createTheme} from "@mui/material/styles";

const theme = createTheme({
    typography: {
        fontFamily: "Estedad-Regular, Arial, sans-serif",
        h1: {
            fontFamily: "Estedad-Black, Arial, sans-serif",
            fontSize: "2.5rem",
            fontWeight: 700,
        },
        h2: {
            fontFamily: "Estedad-Bold, Arial, sans-serif",
            fontSize: "2rem",
            fontWeight: 700,
        },
        h3: {
            fontFamily: "Estedad-Bold, Arial, sans-serif",
            fontSize: "1.75rem",
            fontWeight: 700,
        },
        h4: {
            fontFamily: "Estedad-Bold, Arial, sans-serif",
            fontSize: "1.5rem",
            fontWeight: 700,
        },
        h5: {
            fontFamily: "Estedad-Bold, Arial, sans-serif",
            fontSize: "1.25rem",
            fontWeight: 700,
        },
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
            fontSize: ".90rem",
        },
    },

    palette: {
        primary: {
            main: "#14c887",
            contrastText: "#fff",
        },
    },

    direction: "rtl",

    components: {
        MuiCssBaseline: {
            styleOverrides: {
                body: {
                    fontFamily: "Estedad-Regular, Arial, sans-serif",
                    backgroundColor: "rgb(245, 245, 245)",
                    margin: 0,
                    padding: 0,
                },
            },
        },

        MuiButton: {
            styleOverrides: {
                containedPrimary: {
                    backgroundImage: "linear-gradient(90deg, rgb(20,200,135) 0%, rgb(15, 160, 105) 100%)",
                    color: "rgb(255,255,255)",
                    borderRadius: "300px",
                    textTransform: "none",
                    "&:hover": {
                        backgroundImage: "linear-gradient(90deg, rgb(20,200,135) 0%, rgb(15, 160, 105) 100%)",
                    },
                },
                containedSecondary: {
                    backgroundImage: "linear-gradient(90deg, rgb(145, 165, 175) 0%, rgb(95, 125, 140) 100%)",
                    color: "rgb(255,255,255)",
                    borderRadius: "300px",
                    textTransform: "none",
                    "&:hover": {
                        backgroundImage: "linear-gradient(90deg, rgb(145, 165, 175) 0%, rgb(95, 125, 140) 100%)",
                    },
                },
            },
        },
    },
} as any);

export default theme;