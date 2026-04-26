import { Box, Card, CardContent, Stack, Typography } from "@mui/material";
import { MOCK_DRIVER_NOTIFICATIONS } from "../mock/driverNotifications";

function formatNotificationDate(iso: string): string {
    try {
        return new Date(iso).toLocaleDateString("fa-IR", {
            year: "numeric",
            month: "long",
            day: "numeric",
        });
    } catch {
        return iso;
    }
}

export default function DriverNotificationsPage() {
    return (
        <Box
            sx={{
                flex: 1,
                minHeight: 0,
                overflow: "auto",
                px: 2,
                py: 2.5,
                pb: 3,
                bgcolor: "background.default",
            }}
        >
            <Stack spacing={2}>
                {MOCK_DRIVER_NOTIFICATIONS.map((item) => (
                    <Card
                        key={item.id}
                        elevation={0}
                        sx={{
                            borderRadius: 2,
                            border: "1px solid",
                            borderColor: "divider",
                            bgcolor: "background.paper",
                            boxShadow: "0 1px 4px rgba(13, 71, 161, 0.06)",
                        }}
                    >
                        <CardContent sx={{ py: 2, "&:last-child": { pb: 2 } }}>
                            <Typography variant="body1" sx={{ lineHeight: 1.7 }}>
                                {item.body}
                            </Typography>
                            <Typography
                                variant="caption"
                                color="text.secondary"
                                sx={{ display: "block", mt: 1.5 }}
                            >
                                {formatNotificationDate(item.at)}
                            </Typography>
                        </CardContent>
                    </Card>
                ))}
            </Stack>
        </Box>
    );
}
