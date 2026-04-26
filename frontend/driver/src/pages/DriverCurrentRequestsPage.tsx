import { Box, Stack, Typography } from "@mui/material";
import DriverRequestCard from "../components/DriverRequestCard";
import {
    MOCK_CURRENT_REQUESTS,
    MOCK_CURRENT_REQUESTS_COUNT,
} from "../mock/currentRequests";

export default function DriverCurrentRequestsPage() {
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
            <Stack spacing={0.75} sx={{ mb: 2 }}>
                <Typography variant="body2" color="text.secondary">
                    {MOCK_CURRENT_REQUESTS_COUNT} درخواست فعال برای شما ثبت شده است.
                </Typography>
            </Stack>

            <Stack spacing={2.25}>
                {MOCK_CURRENT_REQUESTS.map((req) => (
                    <DriverRequestCard key={req.id} request={req} />
                ))}
            </Stack>
        </Box>
    );
}
