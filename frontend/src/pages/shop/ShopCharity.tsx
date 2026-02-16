import {useState} from 'react';
import {
    Card,
    CardContent,
    Typography,
    TextField,
    FormControl,
    InputLabel,
    Select,
    MenuItem,
    RadioGroup,
    FormControlLabel,
    Radio,
    Button,
    Box
} from '@mui/material';
import CreditCard from "@mui/icons-material/CreditCard";
import Diversity1 from "@mui/icons-material/Diversity1";
import VolunteerActivism from "@mui/icons-material/VolunteerActivism";
import KeyboardArrowDownIcon from '@mui/icons-material/KeyboardArrowDown';

const ShopCharity = () => {
    const [charity, setCharity] = useState('Charity1');
    const [amount, setAmount] = useState('');
    const [suggestedAmount, setSuggestedAmount] = useState('');
    const [paymentMethod, setPaymentMethod] = useState('');

    const handleSuggestedAmountChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        setSuggestedAmount(event.target.value);
        setAmount(event.target.value);
    };

    const handleSubmit = () => {
        if (!amount || !paymentMethod) {
            alert('لطفاً مبلغ و روش پرداخت را وارد کنید.');
            return;
        }

        alert(`✅ اطلاعات پرداخت:\nموسسه: ${charity}\nمبلغ: ${amount} تومان\nروش پرداخت: ${paymentMethod}`);
    };

    return (
        <Box sx={{pb: 4.5}}>
            <Box sx={{display: "flex", alignItems: "center", gap: 1, mb: 1.5}}>
                <Diversity1/>
                <Typography variant="h6">موسسه خیریه را انتخاب کنید</Typography>
            </Box>
            <Card sx={{width: "100%", mb: 3, borderRadius: 3, boxShadow: 3}}>
                <CardContent>
                    <FormControl fullWidth>
                        <InputLabel id="charity-select-label">موسسه خیریه</InputLabel>
                        <Select
                            labelId="charity-select-label"
                            value={charity}
                            label="موسسه خیریه"
                            IconComponent={KeyboardArrowDownIcon}
                            onChange={(e) => setCharity(e.target.value)}
                        >
                            <MenuItem value="Charity1">بنیاد پیشگیری و کنترل دیابت ایرانیان</MenuItem>
                        </Select>
                    </FormControl>
                </CardContent>
            </Card>

            <Box sx={{display: "flex", alignItems: "center", gap: 1, mb: 1.5}}>
                <VolunteerActivism/>
                <Typography variant="h6">مبلغ را وارد کنید</Typography>
            </Box>
            <Card sx={{width: "100%", mb: 3, borderRadius: 3, boxShadow: 3}}>
                <CardContent>
                    <TextField
                        fullWidth
                        placeholder="مبلغ پرداختی"
                        type="text"
                        inputMode="decimal"
                        value={amount}
                        onChange={(e) => setAmount(e.target.value)}
                        sx={{mb: 2}}
                    />
                    <FormControl component="fieldset">
                        <RadioGroup
                            row
                            value={suggestedAmount}
                            onChange={handleSuggestedAmountChange}
                        >
                            <FormControlLabel
                                value="10000"
                                control={<Radio/>}
                                label={<span><strong>10.000</strong> <small>تومان</small></span>}
                            />
                            <FormControlLabel
                                value="20000"
                                control={<Radio/>}
                                label={<span><strong>20.000</strong> <small>تومان</small></span>}
                            />
                            <FormControlLabel
                                value="50000"
                                control={<Radio/>}
                                label={<span><strong>50.000</strong> <small>تومان</small></span>}
                            />
                            <FormControlLabel
                                value="100000"
                                control={<Radio/>}
                                label={<span><strong>100.000</strong> <small>تومان</small></span>}
                            />
                        </RadioGroup>
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

export default ShopCharity;