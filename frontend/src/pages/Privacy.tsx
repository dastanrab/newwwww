import {Typography, Box} from "@mui/material";

export default function Privacy() {
    return (
        <Box>
            <Typography variant="body1" component="div" sx={{lineHeight: 2, textAlign: "justify"}}>
                <Box sx={{display: "flex", alignItems: "center", gap: 1, mb: 2}}>
                    <Box
                        component="span"
                        sx={{
                            bgcolor: "primary.main",
                            color: "white",
                            width: 30,
                            height: 30,
                            borderRadius: "50%",
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "center",
                            boxShadow: 2,
                            fontWeight: "bold",
                            flexShrink: 0
                        }}
                    >
                        1
                    </Box>
                    <Typography variant="h6" component="strong">
                        چه اطلاعاتی جمع‌آوری می‌شود
                    </Typography>
                </Box>
                در زمان ثبت‌نام، شماره تماس، اسم و فامیل از كاربر گرفته می‌شود و در انجام پروسه كاری،
                موقعیت مكانی كه كاربر برای ارسال ماشین مشخص می‌كند، گردآوری می‌شود.
                <br/>
                در صورت بروز خطا، مشخصات سیستم‌عامل و مرورگر شما برای مطالعه بروز خطا و عیب‌یابی،
                جمع‌آوری می‌شود.
                <br/>
                <br/>
                <Box sx={{display: "flex", alignItems: "center", gap: 1, mb: 2}}>
                    <Box
                        component="span"
                        sx={{
                            bgcolor: "primary.main",
                            color: "white",
                            width: 30,
                            height: 30,
                            borderRadius: "50%",
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "center",
                            boxShadow: 2,
                            fontWeight: "bold",
                            flexShrink: 0
                        }}
                    >
                        2
                    </Box>
                    <Typography variant="h6" component="strong">
                        چگونه از اطلاعات استفاده می‌شود
                    </Typography>
                </Box>
                از اطلاعات دریافتی، در جهت ارایه بهتر خدمات به كاربر و كیف پول الكترونیكی (PSP) برای
                تسویه مبالغ با كاربر انجام می‌شود.
                <br/>
                <br/>
                <Box sx={{display: "flex", alignItems: "center", gap: 1, mb: 2}}>
                    <Box
                        component="span"
                        sx={{
                            bgcolor: "primary.main",
                            color: "white",
                            width: 30,
                            height: 30,
                            borderRadius: "50%",
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "center",
                            boxShadow: 2,
                            fontWeight: "bold",
                            flexShrink: 0
                        }}
                    >
                        3
                    </Box>
                    <Typography variant="h6" component="strong">
                        امنیت اطلاعات شخصی
                    </Typography>
                </Box>
                الف) در تمام قسمت‌های برنامه، از پروتکل HTTPS استفاده می‌شود.
                <br/>
                ب) برای هر بار ورود به حساب کاربری از رمز عبور یک‌بار مصرف (OTP) استفاده می‌شود که تنها
                با دسترسی به شماره تلفن همراهی که از طریق آن، حساب کاربری خود را ایجاد کرده‌اید،
                امکان‌پذیر است.
                <br/>
                <br/>
                <Box sx={{display: "flex", alignItems: "center", gap: 1, mb: 2}}>
                    <Box
                        component="span"
                        sx={{
                            bgcolor: "primary.main",
                            color: "white",
                            width: 30,
                            height: 30,
                            borderRadius: "50%",
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "center",
                            boxShadow: 2,
                            fontWeight: "bold",
                            flexShrink: 0
                        }}
                    >
                        4
                    </Box>
                    <Typography variant="h6" component="strong">
                        استفاده از کوکی‌ها
                    </Typography>
                </Box>
                ممکن است از کوکی‌ها در جمع‌آوری اطلاعات استفاده کنیم. کوکی فایلی است که به درخواست یک
                سایت، توسط مرورگر ایجاد می‌شود و به سایت امکان ذخیره بازدید‌های شما و مناسب‌سازی آنها را
                فراهم می‌نماید.
                <br/>
                <br/>
                <Box sx={{display: "flex", alignItems: "center", gap: 1, mb: 2}}>
                    <Box
                        component="span"
                        sx={{
                            bgcolor: "primary.main",
                            color: "white",
                            width: 30,
                            height: 30,
                            borderRadius: "50%",
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "center",
                            boxShadow: 2,
                            fontWeight: "bold",
                            flexShrink: 0
                        }}
                    >
                        5
                    </Box>
                    <Typography variant="h6" component="strong">
                        چگونه یک کاربر می‌تواند از جمع‌آوری یا استفاده از داده انصراف دهد
                    </Typography>
                </Box>
                كاربر می‌تواند با تماس با پشتیبانی و پس از احراز هویت، انصراف خود را انجام دهد.
                <br/>
                <br/>
                <Box sx={{display: "flex", alignItems: "center", gap: 1, mb: 2}}>
                    <Box
                        component="span"
                        sx={{
                            bgcolor: "primary.main",
                            color: "white",
                            width: 30,
                            height: 30,
                            borderRadius: "50%",
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "center",
                            boxShadow: 2,
                            fontWeight: "bold",
                            flexShrink: 0
                        }}
                    >
                        6
                    </Box>
                    <Typography variant="h6" component="strong">
                        اطلاعات تماس شرکت
                    </Typography>
                </Box>
                مشهد – سناباد 42 پلاک 123 طبقه 2
                <br/>
                ایمیل: Info@Zipak.com
            </Typography>
        </Box>
    );
}