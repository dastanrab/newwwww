# -------- Base Image --------
FROM node:20-alpine AS base
WORKDIR /app

# -------- Dependencies --------
FROM base AS deps
# فقط package.json و package-lock.json رو کپی می‌کنیم تا cache درست کار کنه
COPY package*.json ./
RUN npm ci

# -------- Build --------
FROM base AS builder
COPY --from=deps /app/node_modules ./node_modules
COPY . .
RUN npm run build

# -------- Production --------
FROM node:20-alpine AS runner
WORKDIR /app
ENV NODE_ENV=production
ENV PORT=3000

# کپی فقط فایل‌های لازم برای اجرای production
COPY --from=builder /app/dist ./dist
COPY --from=builder /app/package.json ./package.json
COPY --from=builder /app/node_modules ./node_modules
COPY --from=builder /app/public ./public

EXPOSE 3000

# اجرای Vite preview برای production
CMD ["npm", "run", "preview"]