import {useState, useEffect} from "react";
import {
    // Layout
    Box,
    Grid,

    // Inputs
    TextField,
    Select,
    MenuItem,
    InputLabel,
    InputAdornment,
    FormControl,

    // Controls
    Button,
    Radio,
    RadioGroup,
    FormControlLabel,

    // Feedback
    CircularProgress,
    Skeleton,

    // Surfaces / Overlays
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,

    // Typography
    Typography,
} from "@mui/material";
import { LoadingButton } from "@mui/lab";

import type {SelectChangeEvent} from "@mui/material";
import AccountCircle from "@mui/icons-material/AccountCircle";
import PhoneAndroidIcon from "@mui/icons-material/PhoneAndroid";
import EmailIcon from "@mui/icons-material/Email";
import GroupAdd from "@mui/icons-material/GroupAdd";
import CalendarMonthIcon from "@mui/icons-material/CalendarMonth";
import KeyboardArrowDown from "@mui/icons-material/KeyboardArrowDown";
import PhotoCamera from "@mui/icons-material/PhotoCamera";
import {useAuthStore} from "../store/useAuthStore.ts";
import {useProfile} from "../hooks/useProfile.ts";

const ProfileSkeleton: React.FC = () => {
    return (
        <Grid container justifyContent="center" spacing={2}>
            <Grid size={12}>
                <Box sx={{display: "flex", gap: "10px"}}>
                    <Box sx={{display: "flex", alignItems: "center", gap: "5px"}}>
                        <Skeleton variant="circular" width={30} height={30} animation="wave"/>
                        <Skeleton variant="rectangular" width={60} height={15} sx={{borderRadius: 3}} animation="wave"/>
                    </Box>
                    <Box sx={{display: "flex", alignItems: "center", gap: "5px"}}>
                        <Skeleton variant="circular" width={30} height={30} animation="wave"/>
                        <Skeleton variant="rectangular" width={60} height={15} sx={{borderRadius: 3}} animation="wave"/>
                    </Box>
                </Box>
            </Grid>
            <Grid size={12}>
                <Skeleton variant="rectangular" height={45} sx={{borderRadius: 30}} animation="wave"/>
            </Grid>
            <Grid size={12}>
                <Skeleton variant="rectangular" height={45} sx={{borderRadius: 30}} animation="wave"/>
            </Grid>
            <Grid size={12}>
                <Box sx={{display: "flex", gap: "10px"}}>
                    <Box sx={{display: "flex", alignItems: "center", gap: "5px"}}>
                        <Skeleton variant="circular" width={30} height={30} animation="wave"/>
                        <Skeleton variant="rectangular" width={60} height={15} sx={{borderRadius: 30}}
                                  animation="wave"/>
                    </Box>
                    <Box sx={{display: "flex", alignItems: "center", gap: "5px"}}>
                        <Skeleton variant="circular" width={30} height={30} animation="wave"/>
                        <Skeleton variant="rectangular" width={60} height={15} sx={{borderRadius: 30}}
                                  animation="wave"/>
                    </Box>
                </Box>
            </Grid>
            <Grid size={12}>
                <Skeleton variant="rectangular" height={45} sx={{borderRadius: 30}} animation="wave"/>
            </Grid>
            <Grid size={12}>
                <Skeleton variant="rectangular" height={45} sx={{borderRadius: 30}} animation="wave"/>
            </Grid>
            <Grid size={12}>
                <Skeleton variant="rectangular" height={45} sx={{borderRadius: 30}} animation="wave"/>
            </Grid>
            <Grid size={12}>
                <Skeleton variant="rectangular" height={45} sx={{borderRadius: 30}} animation="wave"/>
            </Grid>
            <Grid size={12}>
                <Skeleton variant="rectangular" height={45} sx={{borderRadius: 30}} animation="wave"/>
            </Grid>
            <Grid size={12} textAlign="center">
                <Skeleton
                    variant="rectangular"
                    width={220}
                    height={50}
                    sx={{borderRadius: "300px", mx: "auto"}}
                    animation="wave"
                />
            </Grid>
        </Grid>
    );
};

export default function Profile() {

    const [formData, setFormData] = useState({
        userType: "citizen",
        firstName: "",
        lastName: "",
        gender: "male",
        birthYear: "",
        birthMonth: "",
        birthDay: "",
        phone: "",
        email: "",
        referralCode: "",
        avatar: ""
    });
    const [open, setOpen] = useState(false);
    const [tempDate, setTempDate] = useState({birthYear: "", birthMonth: "", birthDay: ""});
    const [loading, setLoading] = useState(true);
    const {updateProfile, loading: apiLoading} = useProfile();
    const {accessToken, setting, setSetting} = useAuthStore();
    const user = setting?.user;

    useEffect(() => {
        setLoading(true)
        if (user) {
            setLoading(false)
            setFormData(prev => ({
                ...prev,
                userType: user.userType || "citizen",
                firstName: user.firstName || "",
                lastName: user.lastName || "",
                gender: user.gender || "male",
                phone: user.mob || "",
                email: user.email || "",
                referralCode: user.referralCode || "",
                avatar: "", // اگر بعداً از API گرفتی اینجا ست کن
            }));

            // اگر birthDate داشتی (مثلاً "1375-05-12")
            if (user.birthDate) {
                const [year, month, day] = user.birthDate.split("-");
                setFormData(prev => ({
                    ...prev,
                    birthYear: year,
                    birthMonth: month,
                    birthDay: day,
                }));
            }
        }

    }, [user]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const {name, value} = e.target;
        setFormData(prev => ({...prev, [name]: value}));
    };

    const handleTempChange = (e: SelectChangeEvent<string>) => {
        const {name, value} = e.target;
        setTempDate(prev => ({...prev, [name!]: value}));
    };

    const getBirthDate = () => {
        if (!formData.birthYear || !formData.birthMonth || !formData.birthDay) return undefined;

        // اگر ماه اسمه باید تبدیلش کنی (اینجا ساده گذاشتم)
        const monthIndex = months.indexOf(formData.birthMonth) + 1;

        return `${formData.birthYear}/${String(monthIndex).padStart(2, "0")}/${String(formData.birthDay).padStart(2, "0")}`;
    };
    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        if (!accessToken) return;

        try {
            const payload = {
                firstName: formData.firstName,
                lastName: formData.lastName,
                email: formData.email || undefined,
                gender: formData.gender as "male" | "female",
                birthDate: getBirthDate(),
            };

            const res = await updateProfile(accessToken, payload);

            // ✅ آپدیت Zustand
            if (res.status === "success") {
                setSetting((prev: any) => ({
                    ...prev,
                    user: res.data,
                }));
            }

        } catch (err) {
            console.error(err);
        }
    };
    const handleAvatarChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            const reader = new FileReader();
            reader.onloadend = () => setFormData(prev => ({...prev, avatar: reader.result as string}));
            reader.readAsDataURL(file);
        }
    };

    const years = Array.from({length: 1404 - 1350 + 1}, (_, i) => 1350 + i);
    const months = ["فروردین", "اردیبهشت", "خرداد", "تیر", "مرداد", "شهریور", "مهر", "آبان", "آذر", "دی", "بهمن", "اسفند"];
    const days = Array.from({length: 31}, (_, i) => i + 1);

    const handleOpenDialog = () => {
        setTempDate({birthYear: formData.birthYear, birthMonth: formData.birthMonth, birthDay: formData.birthDay});
        setOpen(true);
    };
    const handleCloseDialog = () => setOpen(false);
    const handleConfirmDate = () => {
        setFormData(prev => ({
            ...prev,
            birthYear: tempDate.birthYear,
            birthMonth: tempDate.birthMonth,
            birthDay: tempDate.birthDay
        }));
        setOpen(false);
    };

    const formattedDate = formData.birthYear && formData.birthMonth && formData.birthDay
        ? `${formData.birthDay} ${formData.birthMonth} ${formData.birthYear}`
        : "";

    return (
        <Box component="form" onSubmit={handleSubmit} sx={{mb: 7}}>
            {loading ? (
                <ProfileSkeleton/>
            ) : (
                <Grid container justifyContent="center">

                    <Grid size={12}>
                        <Typography variant="h6">نوع کاربر</Typography>
                        <RadioGroup row name="userType" value={formData.userType} onChange={handleChange}>
                            <FormControlLabel value="citizen" control={<Radio/>} label="شهروندی"/>
                            <FormControlLabel value="business" control={<Radio/>} label="صنفی"/>
                        </RadioGroup>
                    </Grid>

                    <Grid size={12}>
                        <TextField fullWidth margin="normal" label="نام" name="firstName" value={formData.firstName}
                                   onChange={handleChange} InputProps={{
                            startAdornment: <InputAdornment position="start"><AccountCircle/></InputAdornment>
                        }} sx={{"& .MuiOutlinedInput-root": {borderRadius: "300px"}}}/>
                    </Grid>

                    <Grid size={12}>
                        <TextField fullWidth margin="normal" label="نام خانوادگی" name="lastName"
                                   value={formData.lastName} onChange={handleChange} InputProps={{
                            startAdornment: <InputAdornment position="start"><AccountCircle/></InputAdornment>
                        }} sx={{"& .MuiOutlinedInput-root": {borderRadius: "300px"}}}/>
                    </Grid>

                    <Grid size={12}>
                        <Typography sx={{mt: 2, fontWeight: "bold"}}>جنسیت</Typography>
                        <RadioGroup row name="gender" value={formData.gender} onChange={handleChange}>
                            <FormControlLabel value="male" control={<Radio/>} label="مرد"/>
                            <FormControlLabel value="female" control={<Radio/>} label="زن"/>
                        </RadioGroup>
                    </Grid>

                    <Grid size={12}>
                        <TextField fullWidth margin="normal" label="تاریخ تولد" value={formattedDate}
                                   onClick={handleOpenDialog} InputProps={{
                            readOnly: true,
                            startAdornment: <InputAdornment position="start"><CalendarMonthIcon/></InputAdornment>,
                            endAdornment: <InputAdornment position="end"><KeyboardArrowDown/></InputAdornment>
                        }} sx={{"& .MuiOutlinedInput-root": {borderRadius: "300px"}}}/>
                    </Grid>

                    <Grid size={12}>
                        <TextField fullWidth margin="normal" label="شماره موبایل" name="phone" value={formData.phone}
                                   onChange={handleChange} InputProps={{
                            startAdornment: <InputAdornment position="start"><PhoneAndroidIcon/></InputAdornment>
                        }} sx={{"& .MuiOutlinedInput-root": {borderRadius: "300px"}}}/>
                    </Grid>

                    <Grid size={12}>
                        <TextField fullWidth margin="normal" label="ایمیل" name="email" type="email"
                                   value={formData.email} onChange={handleChange} InputProps={{
                            startAdornment: <InputAdornment position="start"><EmailIcon/></InputAdornment>
                        }} sx={{"& .MuiOutlinedInput-root": {borderRadius: "300px"}}}/>
                    </Grid>

                    <Grid size={12}>
                        <TextField fullWidth margin="normal" label="کد معرف" name="referralCode"
                                   value={formData.referralCode} onChange={handleChange} InputProps={{
                            startAdornment: <InputAdornment position="start"><GroupAdd/></InputAdornment>
                        }} sx={{"& .MuiOutlinedInput-root": {borderRadius: "300px"}}}/>
                    </Grid>

                    <Grid size={12}>
                        <Box
                            sx={{width: "100%", position: "fixed", bottom: 90, right: 0, left: 0, textAlign: "center"}}>
                            <LoadingButton
                                type="submit"
                                variant="contained"
                                size="large"
                                loading={apiLoading}
                                sx={{ px: 3.5, borderRadius: '300px' }}
                            >
                                ویرایش پروفایل کاربری
                            </LoadingButton>
                        </Box>
                    </Grid>
                </Grid>
            )}
            <Dialog open={open} onClose={handleCloseDialog} fullWidth maxWidth="sm">
                <DialogTitle>انتخاب تاریخ تولد</DialogTitle>
                <DialogContent>
                    <Grid container spacing={2}>
                        <Grid size={12}>
                            <FormControl fullWidth>
                                <InputLabel id="year-label">سال</InputLabel>
                                <Select labelId="year-label" name="birthYear" value={tempDate.birthYear}
                                        onChange={handleTempChange}>
                                    {years.map(year => <MenuItem key={year} value={year}>{year}</MenuItem>)}
                                </Select>
                            </FormControl>
                        </Grid>
                        <Grid size={12}>
                            <FormControl fullWidth>
                                <InputLabel id="month-label">ماه</InputLabel>
                                <Select labelId="month-label" name="birthMonth" value={tempDate.birthMonth}
                                        onChange={handleTempChange}>
                                    {months.map((month, index) => <MenuItem key={index}
                                                                            value={month}>{month}</MenuItem>)}
                                </Select>
                            </FormControl>
                        </Grid>
                        <Grid size={12}>
                            <FormControl fullWidth>
                                <InputLabel id="day-label">روز</InputLabel>
                                <Select labelId="day-label" name="birthDay" value={tempDate.birthDay}
                                        onChange={handleTempChange}>
                                    {days.map(day => <MenuItem key={day} value={day}>{day}</MenuItem>)}
                                </Select>
                            </FormControl>
                        </Grid>
                    </Grid>
                </DialogContent>
                <DialogActions sx={{justifyContent: "center"}}>
                    <Button variant="outlined" onClick={handleCloseDialog}>انصراف</Button>
                    <Button variant="contained" onClick={handleConfirmDate}>تایید</Button>
                </DialogActions>
            </Dialog>
        </Box>
    );
}