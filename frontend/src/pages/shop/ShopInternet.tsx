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
    Button
} from '@mui/material';
import CreditCard from "@mui/icons-material/CreditCard";
import SettingsCell from '@mui/icons-material/SettingsCell';
import KeyboardArrowDownIcon from '@mui/icons-material/KeyboardArrowDown';

import charge1 from "../../assets/img/charge-1.png";
import charge2 from "../../assets/img/charge-2.png";
import charge3 from "../../assets/img/charge-3.png";

const ShopInternet = () => {
    const [phone, setPhone] = useState('');
    const [operator, setOperator] = useState('');
    const [packageType, setPackageType] = useState('');
    const [paymentMethod, setPaymentMethod] = useState('');

    const detectOperator = (number: string): string => {
        if (/^093\d*/.test(number)) return 'ایرانسل';
        if (/^0915\d*/.test(number)) return 'همراه اول';
        if (/^092\d*/.test(number)) return 'رایتل';
        return '';
    };

    const handlePhoneChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = e.target.value;
        setPhone(value);
        const op = detectOperator(value);
        setOperator(op);
    };

    const handleSubmit = () => {
        alert(`
      شماره: ${phone}
      اپراتور: ${operator}
      بسته انتخابی: ${packageType}
      روش پرداخت: ${paymentMethod}
    `);
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
                            sx={{
                                width: 90,
                                filter: operator === 'همراه اول' ? 'none' : 'grayscale(100%)',
                                transition: 'filter 0.3s'
                            }}
                        />
                        <Box
                            component="img"
                            src={charge2}
                            alt="ایرانسل"
                            sx={{
                                width: 90,
                                filter: operator === 'ایرانسل' ? 'none' : 'grayscale(100%)',
                                transition: 'filter 0.3s'
                            }}
                        />
                        <Box
                            component="img"
                            src={charge3}
                            alt="رایتل"
                            sx={{
                                width: 90,
                                filter: operator === 'رایتل' ? 'none' : 'grayscale(100%)',
                                transition: 'filter 0.3s'
                            }}
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
                            <FormControlLabel value="آپ" control={<Radio/>} label="کیف پول آپ"/>
                            <FormControlLabel value="زی پاک" control={<Radio/>} label="کیف پول زی پاک"/>
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
                >
                    تایید نهایی
                </Button>
            </Box>
        </Box>
    );
};

export default ShopInternet;