# -----------------------------
# Build stage
# -----------------------------
FROM node:23 AS builder

# Set working directory
WORKDIR /app

# Copy package.json and package-lock.json first
COPY package.json package-lock.json ./

# Install dependencies
RUN npm install --frozen-lockfile

# Copy the rest of the project
COPY . .

# Build the React app with Vite
RUN npm run build

# -----------------------------
# Production stage
# -----------------------------
FROM nginx:alpine

LABEL com.example.dashboard.part="frontend"
LABEL com.example.dashboard.version="1.0.0"

# Expose port 90
EXPOSE 90

# Copy built files from builder
COPY --from=builder /app/dist /usr/share/nginx/html

# Copy custom nginx config
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Optional: clean default Nginx HTML files
RUN rm -rf /usr/share/nginx/html/*.html /usr/share/nginx/html/*.js /usr/share/nginx/html/*.css || true
