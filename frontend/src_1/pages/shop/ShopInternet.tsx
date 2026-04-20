import {useState} from 'react';
import {
    Box,
    Card,
    CardContent,
    TextField,
    InputAdornment,
    Typography,
    Select,
    MenuItem,
    FormControl,
    InputLabel,
    Radio,
    RadioGroup,
    FormControlLabel,
    Button,
    Snackbar,
    Alert
} from '@mui/material';
import CreditCard from "@mui/icons-material/CreditCard";
import SettingsCell from '@mui/icons-material/SettingsCell';
import KeyboardArrowDownIcon from '@mui/icons-material/KeyboardArrowDown';

import charge1 from "../../assets/img/charge-1.png";
import charge2 from "../../assets/img/charge-2.png";
import charge3 from "../../assets/img/charge-3.png";

import { useShop } from '../../hooks/useShop';
import { useAuthStore } from '../../store/useAuthStore';

const ShopInternet = () => {
    const [phone, setPhone] = useState('');
    const [operator, setOperator] = useState('');
    const [packageType, setPackageType] = useState('');
    const [paymentMethod, setPaymentMethod] = useState('');

    const [snackbar, setSnackbar] = useState({open:false, message:'', severity:'success'});

    const { accessToken ,refreshSettings} = useAuthStore();
    const { buyInternet, loading } = useShop();

    const detectOperator = (number: string): string => {
        if (/^093\d*/.test(number)) return 'ایرانسل';
        if (/^0915\d*/.test(number)) return 'همراه اول';
        if (/^092\d*/.test(number)) return 'رایتل';
        return '';
    };

    const handlePhoneChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = e.target.value;
        setPhone(value);
        setOperator(detectOperator(value));
    };

    const handleSubmit = async () => {
        if(!phone || !operator || !packageType || !paymentMethod || !accessToken) {
            setSnackbar({open:true, message:'لطفا همه فیلدها را تکمیل کنید', severity:'error'});
            return;
        }

        const productMap: Record<string,string> = {
            "روزانه": "daily",
            "هفتگی": "weekly",
            "ماهانه": "monthly",
            "سالانه": "yearly"
        };

        try {
            const res = await buyInternet(accessToken, {
                productId: productMap[packageType],
                operator: operator === 'همراه اول' ? 'MCI' : operator === 'ایرانسل' ? 'MTN' : 'RTL',
                mobile: phone,
                internetType: 'data', // فرضی
                simType: 'prepaid', // فرضی
                payMethod: paymentMethod === 'آپ' ? 'aap' : 'aniroob'
            });

            if(res.status === 'success') {
                await refreshSettings()
                setSnackbar({open:true, message: res.message || 'خرید اینترنت با موفقیت انجام شد', severity:'success'});
                setPhone('');
                setOperator('');
                setPackageType('');
                setPaymentMethod('');

            } else {
                setSnackbar({open:true, message: res.message || 'خطا در خرید اینترنت', severity:'error'});
            }

        } catch(err:any) {
            setSnackbar({open:true, message: err?.message || 'خطا در اتصال به سرور', severity:'error'});
        }
    };

    return (
        <Box sx={{pb: 4.5}}>
            <Box sx={{display: "flex", alignItems: "center", gap: 1, mb: 1.5}}>
                <SettingsCell/>
                <Typography variant="h6">شماره موبایل را وارد کنید</Typography>
            </Box>
            <Card sx={{width: "100%", mb: 3, borderRadius: 3, boxShadow: 3}}>
                <CardContent>
                    <TextField
                        fullWidth
                        label="شماره موبایل"
                        value={phone}
                        onChange={handlePhoneChange}
                        margin="normal"
                        InputProps={{
                            startAdornment: (
                                <InputAdornment position="start">
                                    <SettingsCell/>
                                </InputAdornment>
                            ),
                        }}
                    />
                    <Box sx={{display: 'flex', justifyContent: 'center', gap: 3}}>
                        <Box
                            component="img"
                            src={charge1}
                            alt="همراه اول"
                            sx={{width: 90, filter: operator === 'همراه اول' ? 'none' : 'grayscale(100%)', transition: 'filter 0.3s'}}
                        />
                        <Box
                            component="img"
                            src={charge2}
                            alt="ایرانسل"
                            sx={{width: 90, filter: operator === 'ایرانسل' ? 'none' : 'grayscale(100%)', transition: 'filter 0.3s'}}
                        />
                        <Box
                            component="img"
                            src={charge3}
                            alt="رایتل"
                            sx={{width: 90, filter: operator === 'رایتل' ? 'none' : 'grayscale(100%)', transition: 'filter 0.3s'}}
                        />
                    </Box>
                    {operator && (
                        <Typography color="text.secondary" sx={{mb: 2}}>
                            اپراتور: {operator}
                        </Typography>
                    )}
                    <FormControl fullWidth margin="normal">
                        <InputLabel>بسته اینترنت</InputLabel>
                        <Select
                            value={packageType}
                            onChange={(e) => setPackageType(e.target.value)}
                            label="بسته اینترنت"
                            IconComponent={KeyboardArrowDownIcon}
                        >
                            <MenuItem value="روزانه">روزانه</MenuItem>
                            <MenuItem value="هفتگی">هفتگی</MenuItem>
                            <MenuItem value="ماهانه">ماهانه</MenuItem>
                            <MenuItem value="سالانه">سالانه</MenuItem>
                        </Select>
                    </FormControl>
                </CardContent>
            </Card>

            <Box sx={{display: "flex", alignItems: "center", gap: 1, mb: 1.5}}>
                <CreditCard/>
                <Typography variant="h6">نحوه پرداخت را انتخاب کنید</Typography>
            </Box>
            <Card sx={{width: "100%", mb: 3, borderRadius: 3, boxShadow: 3}}>
                <CardContent>
                    <FormControl component="fieldset">
                        <RadioGroup
                            row
                            value={paymentMethod}
                            onChange={(e) => setPaymentMethod(e.target.value)}
                        >
                            <FormControlLabel value="آنیرُوب" control={<Radio/>} label="کیف پول آنیرُوب"/>
                        </RadioGroup>
                    </FormControl>
                </CardContent>
            </Card>

            <Box sx={{width: "100%", position: "fixed", bottom: 90, right: 0, left: 0, textAlign: "center"}}>
                <Button
                    type="submit"
                    variant="contained"
                    size="large"
                    onClick={handleSubmit}
                    sx={{borderRadius: "300px", px: 5}}
                    disabled={loading}
                >
                    {loading ? 'در حال پردازش...' : 'تایید نهایی'}
                </Button>
            </Box>

            <Snackbar
                open={snackbar.open}
                autoHideDuration={4000}
                onClose={() => setSnackbar({...snackbar, open:false})}
                anchorOrigin={{ vertical:'bottom', horizontal:'center' }}
            >
                <Alert severity={snackbar.severity as any} sx={{ width: '100%' }}>
                    {snackbar.message}
                </Alert>
            </Snackbar>
        </Box>
    );
};

export default ShopInternet;