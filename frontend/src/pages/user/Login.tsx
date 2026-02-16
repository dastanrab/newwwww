import React, {useState} from "react";
import {
    Box,
    TextField,
    Button,
    Checkbox,
    FormControlLabel,
    Typography,
    InputAdornment,
    Link,
    Snackbar,
    Alert,
} from "@mui/material";
import logo from "../../assets/logo.png";
import Dock from "@mui/icons-material/Dock";
import {Link as RouterLink, useNavigate } from "react-router-dom";

const Login: React.FC = () => {
    const [phone, setPhone] = useState("");
    const [acceptedTerms, setAcceptedTerms] = useState(false);

    const [openSnackbar, setOpenSnackbar] = useState(false);
    const [snackbarMessage, setSnackbarMessage] = useState("");
    const [snackbarSeverity, setSnackbarSeverity] = useState<"success" | "error" | "warning" | "info">("error");

    const navigate = useNavigate();

    const handleLogin = () => {
        if (!acceptedTerms) {
            setSnackbarMessage("لطفا قوانین و مقررات را قبول کنید.");
            setSnackbarSeverity("error");
            setOpenSnackbar(true);
            return;
        }
        if (!/^09\d{9}$/.test(phone)) {
            setSnackbarMessage("شماره موبایل معتبر نیست.");
            setSnackbarSeverity("error");
            setOpenSnackbar(true);
            return;
        }

        setSnackbarMessage("ورود موفقیت‌آمیز بود!");
        setSnackbarSeverity("success");
        setOpenSnackbar(true);

        console.log("شماره موبایل:", phone);

        navigate("/verify", {state: {phone}});
    };

    return (
        <Box
            sx={{
                width: "100%",
                height: "100vh",
                display: "flex",
                flexDirection: "column",
                justifyContent: "center",
                px: 3,
                position: "relative",
                "&::before": {
                    content: '""',
                    width: "250px",
                    height: "250px",
                    display: "block",
                    position: "absolute",
                    top: "0",
                    left: "-90px",
                    background: "linear-gradient(90deg, rgb(20, 200, 135) 0%, rgb(15, 160, 105) 100%)",
                    opacity: 0.25,
                    transform: "rotate(45deg)",
                    borderRadius: "50%",
                    filter: "blur(90px)",
                    zIndex: 1,
                },
            }}
        >
            <Box sx={{width: "125px", m: "0 auto 15px"}}>
                <img src={logo}/>
            </Box>

            <TextField
                label="شماره موبایل"
                fullWidth
                value={phone}
                onChange={(e) => setPhone(e.target.value)}
                InputProps={{
                    endAdornment: (
                        <InputAdornment position="end">
                            <Dock/>
                        </InputAdornment>
                    ),
                }}
            />

            <FormControlLabel
                control={
                    <Checkbox
                        checked={acceptedTerms}
                        onChange={(e) => setAcceptedTerms(e.target.checked)}
                    />
                }
                sx={{mt: 0.5}}
                label={
                    <Typography variant="body2">
                        تمامی{" "}
                        <Link
                            component={RouterLink}
                            to="/rule"
                            sx={{color: "primary.main", textDecoration: "none"}}
                        >
                            قوانین و مقررات
                        </Link>{" "}
                        زی پاک را می‌پذیرم
                    </Typography>
                }
            />

            <Button
                fullWidth
                type="button"
                variant="contained"
                onClick={handleLogin}
                sx={{mt: 1.5, py: 1.5, borderRadius: "300px"}}
            >
                ورود به حساب کاربری
            </Button>

            <Snackbar
                open={openSnackbar}
                autoHideDuration={3000}
                onClose={() => setOpenSnackbar(false)}
                anchorOrigin={{vertical: "bottom", horizontal: "center"}}
            >
                <Alert severity={snackbarSeverity} onClose={() => setOpenSnackbar(false)}>
                    {snackbarMessage}
                </Alert>
            </Snackbar>
        </Box>
    );
};

export default Login;