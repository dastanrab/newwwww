import { Box, Stack, Typography } from "@mui/material";
import DriverCompletedRequestCard from "../components/DriverCompletedRequestCard";
import {
    MOCK_COMPLETED_REQUESTS,
    MOCK_COMPLETED_REQUESTS_COUNT,
} from "../mock/completedRequests";

export default function DriverCompletedRequestsPage() {
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
                    {MOCK_COMPLETED_REQUESTS_COUNT} درخواست تکمیل‌شده در لیست شماست.
                </Typography>
            </Stack>

            <Stack spacing={2.25}>
                {MOCK_COMPLETED_REQUESTS.map((req) => (
                    <DriverCompletedRequestCard key={req.id} request={req} />
                ))}
            </Stack>
        </Box>
    );
}
