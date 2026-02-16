import {useState, useEffect} from "react";
import {
    Box,
    TextField,
    Button,
    RadioGroup,
    FormControlLabel,
    Radio,
    Typography,
    Grid,
    InputAdornment,
    MenuItem,
    Select,
    FormControl,
    InputLabel,
    Dialog,
    DialogActions,
    DialogContent,
    DialogTitle,
    Avatar,
    IconButton,
    Skeleton,
} from "@mui/material";

import type {SelectChangeEvent} from "@mui/material";
import AccountCircle from "@mui/icons-material/AccountCircle";
import PhoneAndroidIcon from "@mui/icons-material/PhoneAndroid";
import EmailIcon from "@mui/icons-material/Email";
import GroupAdd from "@mui/icons-material/GroupAdd";
import CalendarMonthIcon from "@mui/icons-material/CalendarMonth";
import KeyboardArrowDown from "@mui/icons-material/KeyboardArrowDown";
import PhotoCamera from "@mui/icons-material/PhotoCamera";

const ProfileSkeleton: React.FC = () => {
    return (
        <Grid container justifyContent="center" spacing={2}>
            <Grid size={12} textAlign="center" position="relative">
                <IconButton disabled>
                    <Avatar sx={{width: 100, height: 100}}>
                        <Skeleton variant="circular" width={100} height={100} animation="wave"/>
                    </Avatar>
                    <PhotoCamera
                        sx={{
                            position: "absolute",
                            bottom: 5,
                            right: 5,
                            background: "#fff",
                            borderRadius: "50%",
                            padding: "3px"
                        }}
                    />
                </IconButton>
            </Grid>
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
                        <Skeleton variant="rectangular" width={60} height={15} sx={{borderRadius: 30}} animation="wave"/>
                    </Box>
                    <Box sx={{display: "flex", alignItems: "center", gap: "5px"}}>
                        <Skeleton variant="circular" width={30} height={30} animation="wave"/>
                        <Skeleton variant="rectangular" width={60} height={15} sx={{borderRadius: 30}} animation="wave"/>
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

    useEffect(() => {
        const timer = setTimeout(() => setLoading(false), 2000);
        return () => clearTimeout(timer);
    }, []);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const {name, value} = e.target;
        setFormData(prev => ({...prev, [name]: value}));
    };

    const handleTempChange = (e: SelectChangeEvent<string>) => {
        const {name, value} = e.target;
        setTempDate(prev => ({...prev, [name!]: value}));
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        console.log("اطلاعات کاربر:", formData);
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
                    <Grid size={12} textAlign="center">
                        <input accept="image/*" style={{display: "none"}} id="avatar-upload" type="file"
                               onChange={handleAvatarChange}/>
                        <label htmlFor="avatar-upload">
                            <IconButton component="span" sx={{position: "relative"}}>
                                <Avatar src={formData.avatar} sx={{width: 100, height: 100}}/>
                                <PhotoCamera sx={{
                                    position: "absolute",
                                    bottom: 5,
                                    right: 5,
                                    background: "#fff",
                                    borderRadius: "50%",
                                    padding: "3px"
                                }}/>
                            </IconButton>
                        </label>
                    </Grid>

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
                            <Button type="submit" variant="contained" size="large" sx={{borderRadius: "300px", px: 5}}>
                                ویرایش پروفایل کاربری
                            </Button>
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