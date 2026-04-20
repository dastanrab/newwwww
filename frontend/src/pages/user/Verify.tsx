import React, {useState, useEffect, useRef} from "react";
import {
    Box,
    Typography,
    TextField,
    Button,
    Stack,
    Snackbar,
    Alert,
} from "@mui/material";
import {useNavigate} from "react-router-dom";
import {LoadingButton} from "@mui/lab";

import {useAuth} from "../../hooks/useAuth";
import {useAuthStore} from "../../store/useAuthStore";
import logo from "../../assets/logo.svg";
import text from "../../assets/logo-text.svg";;

const Verify: React.FC = () => {
    const navigate = useNavigate();

    const {verify, login, loading} = useAuth();
    const {mob, setAccessToken, setSetting} = useAuthStore();

    const [code, setCode] = useState<string[]>(Array(5).fill(""));
    const inputRefs = useRef<(HTMLInputElement | null)[]>([]);
    const [counter, setCounter] = useState(59);

    const [openSnackbar, setOpenSnackbar] = useState(false);
    const [snackbarMessage, setSnackbarMessage] = useState("");
    const [snackbarSeverity, setSnackbarSeverity] =
        useState<"success" | "error" | "warning" | "info">("error");

    /* =========================
        Redirect if no mob
    ========================== */
    useEffect(() => {
        if (!mob) {
            navigate("/login");
        }
    }, [mob, navigate]);

    /* =========================
        Timer
    ========================== */
    useEffect(() => {
        if (counter > 0) {
            const timer = setTimeout(() => setCounter(counter - 1), 1000);
            return () => clearTimeout(timer);
        }
    }, [counter]);

    const showSnackbar = (
        message: string,
        severity: "success" | "error" | "warning" | "info"
    ) => {
        setSnackbarMessage(message);
        setSnackbarSeverity(severity);
        setOpenSnackbar(true);
    };

    /* =========================
        Input handlers
    ========================== */
    const handleChange = (value: string, index: number) => {
        if (/^\d?$/.test(value)) {
            const newCode = [...code];
            newCode[index] = value;
            setCode(newCode);

            if (value && index > 0) {
                inputRefs.current[index - 1]?.focus();
            }
        }
    };

    const handleKeyDown = (
        e: React.KeyboardEvent<HTMLInputElement>,
        index: number
    ) => {
        if (e.key === "Backspace") {
            if (code[index]) {
                const newCode = [...code];
                newCode[index] = "";
                setCode(newCode);
            } else if (index < 4) {
                inputRefs.current[index + 1]?.focus();
            }
        }
    };

    /* =========================
        Verify Submit
    ========================== */
    const handleVerify = async () => {
        const finalCode = code.join("");

        if (finalCode.length !== 5) {
            showSnackbar("کد تایید کامل نیست", "error");
            return;
        }

        if (!mob) return;

        const response = await verify(mob, finalCode.split('').reverse().join(''));

        if (response.status === "success") {
            console.log(response.data)
            // @ts-ignore
            const token = response.data?.accessToken;
            // @ts-ignore
            const setting = response.data?.settings;

            if (token) {
                setAccessToken(token);
                setSetting(setting)
                showSnackbar("ورود موفقیت‌آمیز بود", "success");

                setTimeout(() => {
                    navigate("/");
                }, 800);
            } else {
                showSnackbar("توکن دریافت نشد", "error");
            }
        } else {
            showSnackbar(response.message || "کد اشتباه است", "error");
        }
    };

    /* =========================
        Resend Code
    ========================== */
    const handleResend = async () => {
        if (!mob) return;

        await login(mob);
        setCounter(59);
        setCode(Array(5).fill(""));
        inputRefs.current[4]?.focus();
        showSnackbar("کد مجددا ارسال شد", "success");
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
            }}
        >
            <Box sx={{width: "125px", m: "0 auto 15px"}}>
                <img src={logo} alt="logo"/>
                <img src={text} alt="logo"/>
            </Box>
            <Typography variant="body1" mb={3} textAlign="center">
                کد تایید ارسال شده به شماره <strong>{mob}</strong> را وارد نمایید
            </Typography>

            <Stack direction="row" spacing={1} mb={3} justifyContent="center" sx={{direction: "ltr"}}>
                {code.map((digit, index) => (
                    <TextField
                        key={index}
                        inputRef={(el) => (inputRefs.current[index] = el)}
                        value={digit}
                        onChange={(e) =>
                            handleChange(e.target.value, index)
                        }
                        inputProps={{
                            maxLength: 1,
                            style: {textAlign: "center", direction: "ltr"},
                            onKeyDown: (
                                e: React.KeyboardEvent<HTMLInputElement>
                            ) => handleKeyDown(e, index),
                        }}
                        sx={{
                            width: "50px",
                            "& .MuiOutlinedInput-root": {
                                borderRadius: "20px",
                            },
                        }}
                    />
                ))}
            </Stack>

            <LoadingButton
                type="submit"
                variant="contained"
                size="large"
                onClick={handleVerify}
                disabled={loading}
                sx={{width: 200, m: '0 auto 15px', borderRadius: '300px'}}
            >
                تایید کد
            </LoadingButton>

            {counter > 0 ? (
                <Typography
                    variant="body2"
                    color="text.secondary"
                    sx={{mb: 2, textAlign: "center"}}
                >
                    ارسال مجدد کد تا <strong>{counter}</strong> ثانیه دیگر
                </Typography>
            ) : (
                <Button
                    variant="outlined"
                    onClick={handleResend}
                    sx={{
                        width: "200px",
                        m: "0 auto 15px",
                        borderRadius: "300px",
                    }}
                >
                    ارسال مجدد کد
                </Button>
            )}

            <Button
                variant="text"
                onClick={() => navigate("/login")}
                sx={{width: "200px", m: "0 auto", borderRadius: "300px"}}
            >
                اصلاح شماره موبایل
            </Button>

            <Snackbar
                open={openSnackbar}
                autoHideDuration={3000}
                onClose={() => setOpenSnackbar(false)}
                anchorOrigin={{vertical: "bottom", horizontal: "center"}}
            >
                <Alert
                    severity={snackbarSeverity}
                    onClose={() => setOpenSnackbar(false)}
                >
                    {snackbarMessage}
                </Alert>
            </Snackbar>
        </Box>
    );
};

export default Verify;
