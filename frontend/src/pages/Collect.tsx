import {useState, useRef, useEffect} from "react";
import {Box, TextField, InputAdornment, IconButton, Button, Typography, Grid} from "@mui/material";
import {Map} from "@neshan-maps-platform/ol";
import NeshanMap from "@neshan-maps-platform/react-openlayers";
import SearchIcon from "@mui/icons-material/Search";
import LocationOnIcon from "@mui/icons-material/LocationOn";
import StarIcon from '@mui/icons-material/Star';

function Collect() {
    const mapRef = useRef<Map | null>(null);
    const [searchText, setSearchText] = useState("");

    const mapKey = "web.5aa1ec24bac34fde98d10c1a5215165d";

    const handleSearch = () => {
        console.log("Search for:", searchText);
    };

    useEffect(() => {
        if (mapRef.current) {
            console.log("Map instance:", mapRef.current);
        }
    }, []);

    const days = [
        {name: "امروز", date: "1404/07/07"},
        {name: "فردا", date: "1404/07/08"},
        {name: "یکشنبه", date: "1404/07/09"},
        {name: "دوشنبه", date: "1404/07/10"},
        {name: "سه شنبه", date: "1404/07/11"},
        {name: "چهارشنبه", date: "1404/07/12"},
    ];

    const hours = [
        {name: "صبح", date: "11 الی 14"},
        {name: "ظهر", date: "14 الی 15"},
        {name: "عصر", date: "15 الی 16"},
        {name: "عصر", date: "16 الی 18"},
        {name: "شب", date: "18 الی 19"},
        {name: "شب", date: "19 الی 21"},
    ];

    return (
        <Box>
            <Box
                sx={{
                    height: "450px",
                    width: "100%",
                    position: "relative",
                }}
            >
                <NeshanMap
                    //ref={mapRef}
                    mapKey={mapKey}
                    center={{latitude: 36.2974945, longitude: 59.6059232}}
                    zoom={14}
                    style={{
                        height: "100%",
                        width: "100%",
                        overflow: "hidden",
                        borderRadius: 12,
                    }}
                />
            </Box>
            <Box sx={{my: 2}}>
                <TextField
                    fullWidth
                    variant="outlined"
                    placeholder="جستجو آدرس ..."
                    value={searchText}
                    onChange={(e) => setSearchText(e.target.value)}
                    InputProps={{
                        startAdornment: (
                            <InputAdornment position="start">
                                <LocationOnIcon/>
                            </InputAdornment>
                        ),
                        endAdornment: (
                            <InputAdornment position="end">
                                <IconButton onClick={handleSearch}>
                                    <SearchIcon/>
                                </IconButton>
                            </InputAdornment>
                        ),
                    }}
                />
            </Box>
            <Box
                sx={{
                    display: "flex",
                    gap: 1,
                    mb: 2
                }}
            >
                <Button color="secondary" variant="contained" startIcon={<StarIcon/>}>شرکت</Button>
                <Button color="secondary" variant="contained" startIcon={<StarIcon/>}>منزل</Button>
            </Box>
            <Grid container spacing={1} sx={{pb: 2}}>
                {days.map((day, index) => (
                    <Grid size={4} key={index}>
                        <Button
                            fullWidth
                            variant="outlined"
                            sx={{
                                display: "flex",
                                flexDirection: "column",
                                p: 2,
                                textAlign: "center",
                                height: "100%",
                                borderRadius: 2,
                                justifyContent: "center",
                            }}
                        >
                            <Typography variant="h6" sx={{pb: 0.5}}>
                                {day.name}
                            </Typography>
                            <Typography variant="body2">{day.date}</Typography>
                        </Button>
                    </Grid>
                ))}
            </Grid>
            <Grid container spacing={1}>
                {hours.map((day, index) => (
                    <Grid size={4} key={index}>
                        <Button
                            fullWidth
                            variant="outlined"
                            sx={{
                                display: "flex",
                                flexDirection: "column",
                                p: 2,
                                textAlign: "center",
                                height: "100%",
                                borderRadius: 2,
                                justifyContent: "center",
                            }}
                        >
                            <Typography variant="h6" sx={{pb: 0.5}}>
                                {day.name}
                            </Typography>
                            <Typography variant="body2">{day.date}</Typography>
                        </Button>
                    </Grid>
                ))}
            </Grid>
            <Box
                sx={{width: "100%", position: "fixed", bottom: 90, right: 0, left: 0, textAlign: "center"}}>
                <Button type="submit" variant="contained" size="large" sx={{borderRadius: "300px", px: 5}}>
                    تایید نهایی
                </Button>
            </Box>
        </Box>
    );
}

export default Collect;