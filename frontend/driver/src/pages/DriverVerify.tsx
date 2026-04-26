import React, { useEffect, useRef, useState } from "react";
import {
    Box,
    Typography,
    TextField,
    Button,
    Stack,
    Snackbar,
    Alert,
} from "@mui/material";
import { useNavigate } from "react-router-dom";
import { LoadingButton } from "@mui/lab";
import { useDriverSession } from "../context/DriverSessionContext";

/**
 * تایید OTP راننده — ساختار و استایل مشابه `src/pages/user/Verify.tsx`، بدون API.
 * هر کد ۵ رقمی برای نمایش پذیرفته می‌شود.
 */
const DriverVerify: React.FC = () => {
    const navigate = useNavigate();
    const { session, pendingPhone, login, clearPendingPhone } =
        useDriverSession();

    const [code, setCode] = useState<string[]>(Array(5).fill(""));
    const inputRefs = useRef<(HTMLInputElement | null)[]>([]);
    const [counter, setCounter] = useState(59);
    const [verifying, setVerifying] = useState(false);

    const [openSnackbar, setOpenSnackbar] = useState(false);
    const [snackbarMessage, setSnackbarMessage] = useState("");
    const [snackbarSeverity, setSnackbarSeverity] = useState<
        "success" | "error" | "warning" | "info"
    >("error");

    useEffect(() => {
        if (session) {
            navigate("/home", { replace: true });
        }
    }, [session, navigate]);

    useEffect(() => {
        if (!pendingPhone) {
            navigate("/login", { replace: true });
        }
    }, [pendingPhone, navigate]);

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

    const handleVerify = async () => {
        const finalCode = code.join("");

        if (finalCode.length !== 5) {
            showSnackbar("کد تایید کامل نیست", "error");
            return;
        }

        if (!pendingPhone) return;

        setVerifying(true);
        await new Promise((r) => setTimeout(r, 400));

        login(pendingPhone);
        showSnackbar("ورود موفقیت‌آمیز بود", "success");
        setVerifying(false);

        setTimeout(() => {
            navigate("/home", { replace: true });
        }, 800);
    };

    const handleResend = async () => {
        setCounter(59);
        setCode(Array(5).fill(""));
        inputRefs.current[4]?.focus();
        showSnackbar("کد مجددا ارسال شد", "success");
    };

    const goEditPhone = () => {
        clearPendingPhone();
        navigate("/login");
    };

    if (!pendingPhone) {
        return null;
    }

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
            <Typography variant="body1" mb={3} textAlign="center">
                کد تایید ارسال شده به شماره{" "}
                <strong>{pendingPhone}</strong> را وارد نمایید
            </Typography>

            <Stack
                direction="row"
                spacing={1}
                mb={3}
                justifyContent="center"
                sx={{ direction: "ltr" }}
            >
                {code.map((digit, index) => (
                    <TextField
                        key={index}
                        inputRef={(el) => {
                            inputRefs.current[index] = el;
                        }}
                        value={digit}
                        onChange={(e) => handleChange(e.target.value, index)}
                        inputProps={{
                            maxLength: 1,
                            style: { textAlign: "center", direction: "ltr" },
                            onKeyDown: (
                                e: React.KeyboardEvent<HTMLInputElement>
                            ) => handleKeyDown(e, index),
                        }}
                        sx={{
                            width: "50px",
                            "& .MuiOutlinedInput-root": {
                                borderRadius: "300px",
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
                loading={verifying}
                sx={{ width: 200, m: "0 auto 15px", borderRadius: "300px" }}
            >
                تایید کد
            </LoadingButton>

            {counter > 0 ? (
                <Typography
                    variant="body2"
                    color="text.secondary"
                    sx={{ mb: 2, textAlign: "center" }}
                >
                    ارسال مجدد کد تا {counter} ثانیه دیگر
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
                onClick={goEditPhone}
                sx={{ width: "200px", m: "0 auto", borderRadius: "300px" }}
            >
                اصلاح شماره موبایل
            </Button>

            <Snackbar
                open={openSnackbar}
                autoHideDuration={3000}
                onClose={() => setOpenSnackbar(false)}
                anchorOrigin={{ vertical: "bottom", horizontal: "center" }}
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

export default DriverVerify;
