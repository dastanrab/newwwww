import React, {useState} from "react";
import {
    Box,
    TextField,
    Checkbox,
    FormControlLabel,
    Typography,
    InputAdornment,
    Link,
    Snackbar,
    Alert,
} from "@mui/material";
import Dock from "@mui/icons-material/Dock";
import logo from "../../assets/logo.svg";
import text from "../../assets/logo-text.svg";
import {Link as RouterLink, useNavigate} from "react-router-dom";

import {useAuth} from "../../hooks/useAuth";
import {useAuthStore} from "../../store/useAuthStore.ts";
import {LoadingButton} from "@mui/lab";

const Login: React.FC = () => {
    const [phone, setPhone] = useState("");
    const [acceptedTerms, setAcceptedTerms] = useState(false);
    const [openSnackbar, setOpenSnackbar] = useState(false);
    const [snackbarMessage, setSnackbarMessage] = useState("");
    const [snackbarSeverity, setSnackbarSeverity] =
        useState<"success" | "error" | "warning" | "info">("error");

    const navigate = useNavigate();
    const {login, loading} = useAuth();
    const setMob = useAuthStore((state) => state.setMob);

    const showSnackbar = (
        message: string,
        severity: "success" | "error" | "warning" | "info"
    ) => {
        setSnackbarMessage(message);
        setSnackbarSeverity(severity);
        setOpenSnackbar(true);
    };

    const handleLogin = async () => {
        if (!acceptedTerms) {
            showSnackbar("لطفا قوانین و مقررات را قبول کنید.", "error");
            return;
        }

        if (!/^09\d{9}$/.test(phone)) {
            showSnackbar("شماره موبایل معتبر نیست.", "error");
            return;
        }

        const response = await login(phone);

        if (response.status === "success") {
            setMob(phone);
            showSnackbar("کد تایید ارسال شد.", "success");

            setTimeout(() => {
                navigate("/verify");
            }, 800);
        } else {
            showSnackbar(response.message || "خطا در ورود", "error");
        }
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
            }}
        >
            <Box sx={{width: "125px", m: "0 auto 15px"}}>
                <img src={logo} alt="logo"/>
                <img src={text} alt="logo"/>
            </Box>

            <TextField
                label="شماره موبایل"
                fullWidth
                value={phone}
                onChange={(e) => setPhone(e.target.value)}
                InputProps={{endAdornment: (<InputAdornment position="end"><Dock/></InputAdornment>),}}
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
                        <Link component={RouterLink} to="/rule" sx={{color: "primary.main", textDecoration: "none"}}>
                            قوانین و مقررات
                        </Link>{" "}
                        آنی‌روب را می‌پذیرم
                    </Typography>
                }
            />
            <LoadingButton
                type="submit"
                variant="contained"
                size="large"
                onClick={handleLogin}
                disabled={loading}
                sx={{mt: 1.5, py: 1.5, borderRadius: '300px'}}
            >
                ورود به حساب کاربری
            </LoadingButton>

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
