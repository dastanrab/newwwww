import {Box, Typography, Button, Card} from "@mui/material";
import {useNavigate} from "react-router-dom";
import West from "@mui/icons-material/West";

import internet from "../assets/img/internet.png";
import charge from "../assets/img/charge.png";
import charity from "../assets/img/charity.png";

export default function Shop() {

    const navigate = useNavigate();

    return (
        <Box
            sx={{
                position: "relative",
                "&::before": {
                    content: '""',
                    width: "250px",
                    height: "250px",
                    display: "block",
                    position: "absolute",
                    top: "90px",
                    left: "-90px",
                    background: "linear-gradient(90deg, rgb(20, 200, 135) 0%, rgb(15, 160, 105) 100%)",
                    opacity: ".10",
                    transform: "rotate(45deg)",
                    borderRadius: "50%",
                    filter: "blur(90px)",
                },
            }}
        >
            <Box sx={{width: "100%", position: "fixed", bottom: 90, right: 0, left: 0, textAlign: "center"}}>
                <Button type="submit" variant="contained" size="large"
                        sx={{
                            borderRadius: "300px",
                            px: 5
                        }}
                        onClick={() => navigate("/shop/history")}>
                    سوابق خرید
                </Button>
            </Box>
            <Card
                sx={{
                    p: 4,
                    mb: 2.5,
                    position: "relative",
                    overflow: "visible",
                    borderRadius: 3,
                    cursor: "pointer",
                    transition: "0.2s",
                    backgroundImage: `url(${internet})`,
                    backgroundSize: "contain",
                    backgroundPosition: "right",
                    backgroundRepeat: "no-repeat",
                    "&:hover": {
                        boxShadow: 3,
                    },
                }}
                onClick={() => navigate("/shop/internet")}
            >
                <Typography variant="h6">خرید بسته اینترنت</Typography>
                <Typography variant="body2">اینترنت همراه ایرانسل، همراه اول و رایتل</Typography>
                <Button color="secondary" variant="contained" size="small"
                        sx={{
                            minWidth: "25px",
                            height: "25px",
                            p: 0,
                            position: "absolute",
                            bottom: "-15px",
                            right: "15px",
                        }}>
                    <West fontSize="inherit"/>
                </Button>
            </Card>
            <Card
                sx={{
                    p: 4,
                    mb: 2.5,
                    position: "relative",
                    overflow: "visible",
                    borderRadius: 3,
                    cursor: "pointer",
                    transition: "0.2s",
                    backgroundImage: `url(${charge})`,
                    backgroundSize: "contain",
                    backgroundPosition: "right",
                    backgroundRepeat: "no-repeat",
                    "&:hover": {
                        boxShadow: 3,
                    },
                }}
                onClick={() => navigate("/requests")}
            >
                <Typography variant="h6">خرید شارژ مکالمه</Typography>
                <Typography variant="body2">خرید شارژ مکالمه ایرانسل، همراه اول و رایتل</Typography>
                <Button color="secondary" variant="contained" size="small"
                        sx={{
                            minWidth: "25px",
                            height: "25px",
                            p: 0,
                            position: "absolute",
                            bottom: "-15px",
                            right: "15px",
                        }}>
                    <West fontSize="inherit"/>
                </Button>
            </Card>
            <Card
                sx={{
                    p: 4,
                    mb: 2.5,
                    position: "relative",
                    overflow: "visible",
                    borderRadius: 3,
                    cursor: "pointer",
                    transition: "0.2s",
                    backgroundImage: `url(${charity})`,
                    backgroundSize: "contain",
                    backgroundPosition: "right",
                    backgroundRepeat: "no-repeat",
                    "&:hover": {
                        boxShadow: 3,
                    },
                }}
                onClick={() => navigate("/shop/charity")}
            >
                <Typography variant="h6">کمک به بنیادهای خیریه</Typography>
                <Typography variant="body2">کمک به موسسه امام علی (ع)، شکوه مهر و ...</Typography>
                <Button color="secondary" variant="contained" size="small"
                        sx={{
                            minWidth: "25px",
                            height: "25px",
                            p: 0,
                            position: "absolute",
                            bottom: "-15px",
                            right: "15px",
                        }}>
                    <West fontSize="inherit"/>
                </Button>
            </Card>
        </Box>
    );
}