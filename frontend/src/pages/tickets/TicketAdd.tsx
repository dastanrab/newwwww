import {
    Button,
    OutlinedInput,
    FormControl,
    InputLabel,
    InputAdornment,
    Grid,
    Box,
} from "@mui/material";
import Notes from '@mui/icons-material/Notes';
import AccountCircle from '@mui/icons-material/AccountCircle';

export default function TicketAdd() {
    return (
        <Box sx={{
            height: '100vh',
        }}>
            <Grid container spacing={2}>
                <Grid size={12}>
                    <FormControl fullWidth>
                        <InputLabel htmlFor="outlined-adornment-amount">عنوان تیکت</InputLabel>
                        <OutlinedInput
                            id="outlined-adornment-amount"
                            startAdornment={<InputAdornment position="start"><AccountCircle/></InputAdornment>}
                            label="عنوان تیکت"
                        />
                    </FormControl>
                </Grid>
                <Grid size={12}>
                    <FormControl fullWidth>
                        <InputLabel htmlFor="outlined-adornment-amount">توضیحات تیکت</InputLabel>
                        <OutlinedInput
                            id="outlined-adornment-amount"
                            startAdornment={<InputAdornment position="start"><Notes/></InputAdornment>}
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
                    <Button
                        variant="contained"
                        size="large"
                        sx={{
                            borderRadius: "300px",
                            px: 5
                        }}
                    >
                        ارسال تیکت
                    </Button>
                </Box>
            </Grid>
        </Box>
    );
}