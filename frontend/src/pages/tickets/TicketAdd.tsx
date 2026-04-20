import {
    // Layout
    Box,
    Grid,

    // Inputs
    FormControl,
    InputLabel,
    OutlinedInput,
    InputAdornment,

    // Controls

    // Feedback
    Snackbar,
    Alert,
} from "@mui/material";
import { LoadingButton } from "@mui/lab";

import {useState} from "react";
import {useNavigate} from "react-router-dom";

import Notes from "@mui/icons-material/Notes";
import AccountCircle from "@mui/icons-material/AccountCircle";

import {useTicket} from "../../hooks/useTicket";
import {useAuthStore} from "../../store/useAuthStore";

export default function TicketAdd() {

    const [title, setTitle] = useState("");
    const [description, setDescription] = useState("");

    const [loading, setLoading] = useState(false);
    const [snackbarOpen, setSnackbarOpen] = useState(false);

    const {createTicket} = useTicket();
    const {accessToken} = useAuthStore();

    const navigate = useNavigate();

    const submitTicket = async () => {

        if (!title.trim() || !description.trim() || !accessToken) return;

        setLoading(true);

        const res = await createTicket(accessToken, {
            subject:title,
            message:description
        });

        setLoading(false);

        if (res.status === "success") {
            setSnackbarOpen(true);

            setTimeout(() => {
                navigate("/tickets");
            }, 1200);
        }
    };

    return (
        <Box sx={{height: "100vh"}}>

            <Grid container spacing={2}>

                <Grid size={12}>
                    <FormControl fullWidth>
                        <InputLabel>عنوان تیکت</InputLabel>
                        <OutlinedInput
                            value={title}
                            onChange={(e) => setTitle(e.target.value)}
                            startAdornment={
                                <InputAdornment position="start">
                                    <AccountCircle/>
                                </InputAdornment>
                            }
                            label="عنوان تیکت"
                        />
                    </FormControl>
                </Grid>

                <Grid size={12}>
                    <FormControl fullWidth>
                        <InputLabel>توضیحات تیکت</InputLabel>
                        <OutlinedInput
                            value={description}
                            onChange={(e) => setDescription(e.target.value)}
                            startAdornment={
                                <InputAdornment position="start">
                                    <Notes/>
                                </InputAdornment>
                            }
                            label="توضیحات تیکت"
                            multiline
                            rows={4}
                        />
                    </FormControl>
                </Grid>

                <Box
                    sx={{
                        position: "fixed",
                        bottom: 90,
                        left: "50%",
                        transform: "translateX(-50%)",
                    }}
                >
                    <LoadingButton
                        variant="contained"
                        size="large"
                        loading={loading}
                        sx={{minWidth: 150, px: 3.5, borderRadius: '300px'}}
                        onClick={submitTicket}
                    >
                        ارسال تیکت
                    </LoadingButton>
                </Box>

            </Grid>

            <Snackbar
                open={snackbarOpen}
                autoHideDuration={3000}
                onClose={() => setSnackbarOpen(false)}
                anchorOrigin={{vertical: "bottom", horizontal: "center"}}
            >
                <Alert
                    severity="success"
                    variant="filled"
                    sx={{width: "100%"}}
                >
                    تیکت با موفقیت ثبت شد
                </Alert>
            </Snackbar>

        </Box>
    );
}