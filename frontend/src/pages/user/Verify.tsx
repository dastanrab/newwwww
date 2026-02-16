import React, {useState, useEffect, useRef} from "react";
import {Box, Typography, TextField, Button, Stack} from "@mui/material";
import {useNavigate} from "react-router-dom";

const Verify: React.FC = () => {
    const navigate = useNavigate();
    const phone = "09303736415";
    const [code, setCode] = useState<string[]>(Array(5).fill(""));
    const inputRefs = useRef<(HTMLInputElement | null)[]>([]);
    const [counter, setCounter] = useState(59);

    useEffect(() => {
        if (counter > 0) {
            const timer = setTimeout(() => setCounter(counter - 1), 1000);
            return () => clearTimeout(timer);
        }
    }, [counter]);

    const handleChange = (value: string, index: number) => {
        if (/^\d?$/.test(value)) {
            const newCode = [...code];
            newCode[index] = value;
            setCode(newCode);
            if (value && index > 0) inputRefs.current[index - 1]?.focus();
        }
    };

    const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>, index: number) => {
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

    const handleResend = () => {
        setCounter(59);
        setCode(Array(5).fill(""));
        inputRefs.current[4]?.focus();
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
            <Typography variant="body1" mb={3} textAlign="center">
                کد تایید ارسال شده به شماره <strong>{phone}</strong> را وارد نمایید
            </Typography>

            <Stack direction="row" spacing={1} mb={3} justifyContent="center">
                {code.map((digit, index) => (
                    <TextField
                        key={index}
                        inputRef={(el) => (inputRefs.current[index] = el)}
                        value={digit}
                        onChange={(e) => handleChange(e.target.value, index)}
                        inputProps={{
                            maxLength: 1,
                            style: { textAlign: "center" },
                            onKeyDown: (e: React.KeyboardEvent<HTMLInputElement>) =>
                                handleKeyDown(e, index),
                        }}
                        sx={{
                            width: "50px",
                            "& .MuiOutlinedInput-root": {borderRadius: "300px"}
                        }}
                    />
                ))}
            </Stack>

            {counter > 0 ? (
                <Typography variant="body2" color="text.secondary" sx={{mb: 2, textAlign: "center"}}>
                    ارسال مجدد کد تا {counter} ثانیه دیگر
                </Typography>
            ) : (
                <Button
                    variant="contained"
                    color="secondary"
                    onClick={handleResend}
                    sx={{width: "200px", m: "0 auto 15px", borderRadius: "300px"}}
                >
                    ارسال مجدد کد تایید
                </Button>
            )}
            <Button
                type="button"
                variant="contained"
                onClick={() => navigate("/login")}
                sx={{width: "200px", m: "0 auto", borderRadius: "300px"}}
            >
                اصلاح شماره موبایل
            </Button>
        </Box>
    );
};

export default Verify;