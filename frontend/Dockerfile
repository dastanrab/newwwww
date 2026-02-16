# -------- Base Image --------
FROM node:20-alpine AS base
WORKDIR /app

# -------- Install dependencies --------
FROM base AS deps
COPY package*.json ./
RUN npm ci

# -------- Build project --------
FROM base AS builder
COPY --from=deps /app/node_modules ./node_modules
COPY . .
RUN npm run build

# -------- Production --------
FROM node:20-alpine AS runner
WORKDIR /app
ENV NODE_ENV=production
ENV PORT=3000

# فقط فایل‌های build شده و public
COPY --from=builder /app/dist ./dist
COPY --from=builder /app/public ./public

# سرور سبک برای سرو کردن فایل‌های static
RUN npm install -g serve

EXPOSE 3000
CMD ["serve", "-s", "dist", "-l", "3000"]