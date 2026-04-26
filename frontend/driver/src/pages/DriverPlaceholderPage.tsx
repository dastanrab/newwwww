import { Box, Typography } from "@mui/material";

type DriverPlaceholderPageProps = {
    title: string;
    subtitle?: string;
};

export default function DriverPlaceholderPage({
    title,
    subtitle = "این بخش در فاز بعدی به API متصل می‌شود.",
}: DriverPlaceholderPageProps) {
    return (
        <Box sx={{ flex: 1, p: 2, overflow: "auto" }}>
            <Typography variant="h6" gutterBottom>
                {title}
            </Typography>
            <Typography variant="body2" color="text.secondary">
                {subtitle}
            </Typography>
        </Box>
    );
}
