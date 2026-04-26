import { Box, Card, CardContent, Skeleton, Typography } from "@mui/material";

export interface WasteItem {
  id: number;
  title: string;
  description: string;
  image: string;
  rateList: { [weight: number]: number };
  maxAmount: number;
}

export default function WastePricesGrid({
  items,
  loading,
}: {
  items: WasteItem[];
  loading: boolean;
}) {
  if (loading) {
    return (
      <Box sx={{ display: "grid", gridTemplateColumns: "1fr", gap: 2 }}>
        {Array.from({ length: 6 }).map((_, idx) => (
          <Card key={idx} sx={{ borderRadius: 3, boxShadow: 3 }}>
            <CardContent>
              <Skeleton variant="text" width="40%" height={44} />
              <Skeleton variant="text" width="80%" height={20} />
            </CardContent>
          </Card>
        ))}
      </Box>
    );
  }

  return (
    <Box sx={{ display: "grid", gridTemplateColumns: "1fr", gap: 2 }}>
      {items.map((item) => {
        const price1kg = item.rateList?.[1] ?? item.maxAmount;
        return (
          <Card key={item.id} sx={{ borderRadius: 3, boxShadow: 3 }}>
            <Box
              sx={{
                display: "flex",
                alignItems: "center",
                minHeight: 80,
                backgroundImage: `url(${item.image})`,
                backgroundSize: "contain",
                backgroundPosition: "right",
                backgroundRepeat: "no-repeat",
              }}
            >
              <CardContent sx={{ pr: 14 }}>
                <Typography variant="h6" sx={{ pb: 0.5 }}>
                  {item.title}
                </Typography>
                <Typography variant="body2" color="text.secondary">
                  {price1kg.toLocaleString()} تومان (۱ کیلو)
                </Typography>
              </CardContent>
            </Box>
          </Card>
        );
      })}
    </Box>
  );
}

