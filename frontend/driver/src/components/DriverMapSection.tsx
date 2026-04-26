import { useEffect, useRef } from "react";
import {
    Avatar,
    Box,
    Paper,
    Stack,
    Typography,
} from "@mui/material";
import SmartphoneOutlinedIcon from "@mui/icons-material/SmartphoneOutlined";
import Map from "@neshan-maps-platform/ol/Map";
import View from "@neshan-maps-platform/ol/View";
import { fromLonLat } from "@neshan-maps-platform/ol/proj";
import { MOCK_DRIVER_DISPLAY } from "../mock/driverProfile";

type DriverMapSectionProps = {
    driverName: string;
    driverPhone: string;
};

/**
 * Full-bleed map (read-only) with driver info overlays.
 * Uses the same Neshan setup as the main app map component.
 */
export default function DriverMapSection({
    driverName,
    driverPhone,
}: DriverMapSectionProps) {
    const mapRef = useRef<HTMLDivElement | null>(null);
    const mapInstance = useRef<Map | null>(null);

    useEffect(() => {
        if (!mapRef.current || mapInstance.current) return;

        const map = new Map({
            mapType: "neshan",
            target: mapRef.current,
            key: "web.7f11b5c6971d4917a6e9272a522d8b9e",
            poi: true,
            traffic: true,
            layers: [],
            view: new View({
                center: fromLonLat([59.58874, 36.28865]),
                zoom: 14,
            }),
        });

        mapInstance.current = map;

        return () => {
            map.setTarget(undefined);
            mapInstance.current = null;
        };
    }, []);

    return (
        <Box
            sx={{
                position: "relative",
                flex: 1,
                minHeight: 0,
                width: "100%",
                bgcolor: "grey.200",
            }}
        >
            <Box
                ref={mapRef}
                sx={{
                    position: "absolute",
                    inset: 0,
                    "& canvas": { outline: "none" },
                }}
            />

            <Box
                sx={{
                    position: "absolute",
                    top: 10,
                    right: 10,
                    zIndex: 1,
                    pointerEvents: "none",
                    maxWidth: "calc(100% - 20px)",
                }}
            >
                <Paper
                    elevation={0}
                    sx={{
                        width: "fit-content",
                        maxWidth: 200,
                        px: 1.35,
                        pt: 1.35,
                        pb: 1.15,
                        borderRadius: 2,
                        textAlign: "center",
                        bgcolor: "rgba(255, 255, 255, 0.88)",
                        backdropFilter: "blur(10px)",
                        WebkitBackdropFilter: "blur(10px)",
                        border: "1px solid rgba(255, 255, 255, 0.7)",
                        boxShadow:
                            "0 4px 18px rgba(13, 71, 161, 0.1), 0 1px 0 rgba(255,255,255,0.85) inset",
                    }}
                >
                    <Stack alignItems="center" spacing={1}>
                        <Box
                            sx={{
                                p: "2px",
                                borderRadius: "50%",
                                background:
                                    "linear-gradient(145deg, #64b5f6 0%, #1565c0 55%, #0d47a1 100%)",
                                boxShadow:
                                    "0 3px 10px rgba(21, 101, 192, 0.28)",
                            }}
                        >
                            <Avatar
                                sx={{
                                    width: 40,
                                    height: 40,
                                    bgcolor: "background.paper",
                                    color: "primary.dark",
                                    fontWeight: 800,
                                    fontSize: "0.95rem",
                                    fontFamily: "Estedad-Bold, Arial, sans-serif",
                                    border: "1px solid rgba(255,255,255,0.95)",
                                }}
                            >
                                {MOCK_DRIVER_DISPLAY.avatarLetter}
                            </Avatar>
                        </Box>

                        <Box sx={{ minWidth: 0, width: "100%" }}>
                            <Typography
                                variant="body2"
                                fontWeight={800}
                                sx={{
                                    lineHeight: 1.3,
                                    color: "primary.dark",
                                    fontSize: "0.8125rem",
                                }}
                            >
                                {driverName}
                            </Typography>
                            <Stack
                                direction="row"
                                alignItems="center"
                                justifyContent="center"
                                spacing={0.5}
                                sx={{ mt: 0.5 }}
                            >
                                <SmartphoneOutlinedIcon
                                    sx={{
                                        fontSize: 14,
                                        color: "primary.main",
                                        opacity: 0.7,
                                    }}
                                />
                                <Typography
                                    variant="caption"
                                    color="text.secondary"
                                    fontWeight={500}
                                    sx={{
                                        direction: "ltr",
                                        unicodeBidi: "plaintext",
                                        letterSpacing: 0.08,
                                        fontSize: "0.7rem",
                                        lineHeight: 1.3,
                                    }}
                                >
                                    {driverPhone}
                                </Typography>
                            </Stack>
                        </Box>
                    </Stack>
                </Paper>
            </Box>
        </Box>
    );
}
