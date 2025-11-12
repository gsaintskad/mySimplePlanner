# --- ETAP 1: Instalacja zależności ---
# Używamy pełnego obrazu Node.js, aby mieć dostęp do npm
FROM node:24.8-alpine AS deps
WORKDIR /app

# Kopiujemy tylko pliki potrzebne do instalacji zależności
COPY package.json package-lock.json* ./
RUN npm install

# --- ETAP 2: Budowanie aplikacji ---
# Używamy tego samego obrazu do budowania
FROM node:24.8-alpine AS builder
WORKDIR /app

# Kopiujemy zainstalowane zależności z poprzedniego etapu
COPY --from=deps /app/node_modules ./node_modules
# Kopiujemy resztę kodu źródłowego
COPY . .

# Uruchamiamy skrypt budujący (npm run build)
RUN npm run build

# --- ETAP 3: Obraz produkcyjny ---
# Używamy lekkiego obrazu Alpine dla Node.js
FROM node:24.8-alpine AS runner
WORKDIR /app

ENV NODE_ENV=production

# Kopiujemy tylko te pliki, które są niezbędne do uruchomienia
# aplikacji w trybie produkcyjnym
COPY --from=builder /app/public ./public
COPY --from=builder /app/.next ./.next
COPY --from=builder /app/node_modules ./node_modules
COPY --from=builder /app/package.json ./package.json

# Next.js będzie działać na porcie 3000 wewnątrz kontenera
EXPOSE 3000

# Polecenie uruchamiające serwer Next.js
CMD ["npm", "start"]